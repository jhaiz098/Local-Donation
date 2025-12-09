<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic validation
    if (empty($email) || empty($password)) {
        header("Location: login.php?status=error&message=" . urlencode("Email and password are required."));
        exit();
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT user_id, password, first_name, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $hashed_password, $first_name, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Login successful
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['role'] = $role;

            // Record login activity
            $activity_stmt = $conn->prepare("INSERT INTO activities (user_id, profile_id, description, display_text) VALUES (?, NULL, ?, ?)");
            $description = "User logged in (ID: $user_id)";
            $display_text = "You logged in successfully.";
            $activity_stmt->bind_param("iss", $user_id, $description, $display_text);
            $activity_stmt->execute();
            $activity_stmt->close();

            header("Location: dashboard.php?status=success&message=" . urlencode("Welcome back, $first_name!"));
            exit();
        } else {

            // Log failed login due to incorrect password
            $audit_stmt = $conn->prepare("INSERT INTO audit_logs (user_id, profile_id, description) VALUES (NULL, NULL, ?)");
            $desc = "Failed login attempt for email '$email': incorrect password";
            $audit_stmt->bind_param("s", $desc);
            $audit_stmt->execute();
            $audit_stmt->close();

            header("Location: login.php?status=error&message=" . urlencode("Incorrect password."));
            exit();
        }
    } else {
        header("Location: login.php?status=error&message=" . urlencode("Email not found."));
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
