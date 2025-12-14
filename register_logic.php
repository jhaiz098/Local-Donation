<?php
include 'db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // -------------------------
    // Sanitize input
    // -------------------------
    $first_name   = trim($_POST['first_name']);
    $middle_name  = trim($_POST['middle_name'] ?? '');
    $last_name    = trim($_POST['last_name']);
    $dob          = $_POST['date_of_birth'];
    $gender       = $_POST['gender'];
    $email        = trim($_POST['email']);
    $password     = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $role         = 'User'; // default role

    // -------------------------
    // Validation
    // -------------------------
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($dob)) $errors[] = "Date of birth is required.";
    if (empty($gender)) $errors[] = "Gender is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
    if (empty($password)) $errors[] = "Password is required.";
    if ($password !== $confirm_pass) $errors[] = "Passwords do not match.";

    // Password length check
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // Age check (18+)
    if (!empty($dob)) {
        $birthDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        if ($age < 18) $errors[] = "You must be at least 18 years old to register.";
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = "Email already registered.";
    $stmt->close();

    // -------------------------
    // Handle validation errors
    // -------------------------
    if (!empty($errors)) {
        $desc = "Failed registration attempt for email '$email': " . implode(", ", $errors);

        // Log audit for failed registration
        $auditStmt = $conn->prepare("CALL log_audit(NULL, NULL, ?)");
        $auditStmt->bind_param("s", $desc);
        $auditStmt->execute();
        $auditStmt->close();

        $conn->close();
        header("Location: register.php?status=error&message=" . urlencode($errors[0]));
        exit();
    }

    // -------------------------
    // Hash password
    // -------------------------
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // -------------------------
    // Insert new user via stored procedure
    // -------------------------
    $stmt = $conn->prepare("CALL sp_insert_user(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssssssssss",
        $first_name, $middle_name, $last_name, $dob, $gender,
        $zip_code, $phone_number, $email, $hashed_password, $role,
        $region_id, $province_id, $city_id, $barangay_id
    );

    if ($stmt->execute()) {
        $stmt->close();

        // -------------------------
        // Log successful registration as activity
        // -------------------------
        $user_id_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $user_id_stmt->bind_param("s", $email);
        $user_id_stmt->execute();
        $user_id_stmt->bind_result($new_user_id);
        $user_id_stmt->fetch();
        $user_id_stmt->close();

        $description = "New user registered (ID: $new_user_id, Email: $email)";
        $display_text = "You have successfully registered.";

        $activityStmt = $conn->prepare("CALL log_activity(?, NULL, ?, ?)");
        $activityStmt->bind_param("iss", $new_user_id, $description, $display_text);
        $activityStmt->execute();
        $activityStmt->close();

        $conn->close();
        header("Location: register.php?status=success&message=" . urlencode("Registration successful!"));
        exit();

    } else {
        // -------------------------
        // Log insertion failure
        // -------------------------
        $desc = "Database error during registration for email '$email': " . $stmt->error;
        $auditStmt = $conn->prepare("CALL log_audit(NULL, NULL, ?)");
        $auditStmt->bind_param("s", $desc);
        $auditStmt->execute();
        $auditStmt->close();

        $stmt->close();
        $conn->close();
        header("Location: register.php?status=error&message=" . urlencode("Registration failed, please try again."));
        exit();
    }
}
?>
