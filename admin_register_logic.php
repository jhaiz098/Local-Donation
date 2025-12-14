<?php
require 'db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize input
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Required fields
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($date_of_birth)) $errors[] = "Date of birth is required.";
    if (empty($gender)) $errors[] = "Gender is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($password)) $errors[] = "Password is required.";

    // Email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Password rules
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        }
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }
    }

    // ✅ Age validation (18+)
    if (!empty($date_of_birth)) {
        $birthDate = new DateTime($date_of_birth);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;

        if ($age < 18) {
            $errors[] = "You must be at least 18 years old to register as staff.";
        }
    }

    // ✅ Check email in BOTH tables
    $stmt = $conn->prepare("
        SELECT email FROM users WHERE email = ?
        UNION
        SELECT email FROM pending_admins WHERE email = ?
    ");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $errors[] = "Email already exists.";
    }
    $stmt->close();

    if (!empty($errors)) {
        $desc = "Failed staff registration attempt for email '$email': " . implode("; ", $errors);
        $auditStmt = $conn->prepare("CALL log_audit(NULL, NULL, ?)");
        $auditStmt->bind_param("s", $desc);
        $auditStmt->execute();
        $auditStmt->close();

        header("Location: admin_register.php?status=error&message=" . urlencode(implode("\n", $errors)));
        exit;
    }


    // ✅ Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // ✅ Insert into pending_admins
    $stmt = $conn->prepare("
        INSERT INTO pending_admins 
        (first_name, middle_name, last_name, date_of_birth, gender, email, password) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssssss",
        $first_name,
        $middle_name,
        $last_name,
        $date_of_birth,
        $gender,
        $email,
        $password_hash
    );
    $stmt->execute();
    $stmt->close();

    // After inserting into pending_admins successfully
    $activity_description = "New staff registration submitted for approval: "
                . $first_name . " " . $last_name
                . " (" . $email . ")";

    $activityStmt = $conn->prepare("CALL log_activity(NULL, NULL, ?, ?)");
    $display_text = "Staff registration submitted for approval.";
    $activityStmt->bind_param("ss", $activity_description, $display_text);
    $activityStmt->execute();
    $activityStmt->close();



    header("Location: admin_register.php?status=success&message=Registration+submitted+for+approval");
    exit;
}
?>
