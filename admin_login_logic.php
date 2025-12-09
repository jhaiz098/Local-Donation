<?php
session_start(); // Start session
include 'db_connect.php'; // Include your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // -------------------------
    // Get and sanitize input
    // -------------------------
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // -------------------------
    // Basic validation
    // -------------------------
    if (empty($email) || empty($password)) {
        header("Location: admin_login.php?status=error&message=" . urlencode("Email and password are required."));
        exit();
    }

    // -------------------------
    // Check if admin exists
    // Only allow Staff, Admin, Superuser
    // -------------------------
    $stmt = $conn->prepare("
        SELECT user_id, password, first_name, role 
        FROM users 
        WHERE email = ? AND role IN ('Staff', 'Admin', 'Superuser')
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password, $first_name, $role);
        $stmt->fetch();

        // -------------------------
        // Verify password
        // -------------------------
        if (password_verify($password, $hashed_password)) {
            // -------------------------
            // Set session variables
            // -------------------------
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['role'] = $role;

            // -------------------------
            // Record login activity
            // -------------------------
            $activity_stmt = $conn->prepare("
                INSERT INTO activities (user_id, profile_id, description, display_text) 
                VALUES (?, NULL, ?, ?)
            ");
            $description = "Admin logged in (ID: $user_id, Role: $role)";
            $display_text = "You logged in successfully.";
            $activity_stmt->bind_param("iss", $user_id, $description, $display_text);
            $activity_stmt->execute();
            $activity_stmt->close();

            // -------------------------
            // Redirect all admin roles to admin dashboard
            // -------------------------
            header("Location: admin/admin_dashboard.php?status=success&message=" . urlencode("Welcome back, $first_name!"));
            exit();

        } else {
            // -------------------------
            // Log failed login due to wrong password
            // -------------------------
            $audit_stmt = $conn->prepare("
                INSERT INTO audit_logs (user_id, profile_id, description) 
                VALUES (NULL, NULL, ?)
            ");
            $desc = "Failed admin login attempt for email '$email': incorrect password";
            $audit_stmt->bind_param("s", $desc);
            $audit_stmt->execute();
            $audit_stmt->close();

            header("Location: admin_login.php?status=error&message=" . urlencode("Incorrect password."));
            exit();
        }

    } else {
        // -------------------------
        // Email not found or not an admin
        // -------------------------
        header("Location: admin_login.php?status=error&message=" . urlencode("Admin account not found."));
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
