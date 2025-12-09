<?php
require 'db_connect.php'; // your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Get POST values and sanitize
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if ($password !== $confirm_password) {
        header("Location: admin_register.php?status=error&message=Passwords+do+not+match");
        exit;
    }

    if (strlen($password) < 6) {
        header("Location: admin_register.php?status=error&message=Password+must+be+at+least+6+characters");
        exit;
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Prepare the insert statement
        $stmt = $conn->prepare("INSERT INTO pending_admins 
            (first_name, middle_name, last_name, date_of_birth, gender, email, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $first_name, $middle_name, $last_name, $date_of_birth, $gender, $email, $password_hash);

        $stmt->execute();

        // Redirect to login page with success message
        header("Location: admin_register.php?status=success&message=Pending+approval+registration+submitted");
        exit;

    } catch (Exception $e) {
        // Handle duplicate email or other errors
        header("Location: admin_register.php?status=error&message=" . urlencode($e->getMessage()));
        exit;
    }
}
?>
