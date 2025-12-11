<?php

error_reporting(0); // Hide warnings/notices
ob_start();         // Prevent any accidental output
session_start();
include 'db_connect.php';
header('Content-Type: application/json');

// Check user is logged in
$user_id = $_POST['user_id'] ?? $_SESSION['user_id'] ?? null;
if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "No user specified"]);
    exit;
}

// Collect form data safely
$first     = $_POST['first_name'] ?? '';
$middle    = $_POST['middle_name'] ?? '';
$last      = $_POST['last_name'] ?? '';
$dob       = $_POST['date_of_birth'] ?? '';
$gender    = $_POST['gender'] ?? '';

$region    = $_POST['region_id'] ?? null;
$province  = $_POST['province_id'] ?? null;
$city      = $_POST['city_id'] ?? null;
$barangay  = $_POST['barangay_id'] ?? null;

$zip       = $_POST['zip_code'] ?? '';
$phone     = $_POST['phone_number'] ?? '';
$email     = $_POST['email'] ?? '';

$password  = $_POST['password'] ?? '';
$confPass  = $_POST['confirm_password'] ?? '';

// Verify password match if password is entered
if (!empty($password) && $password !== $confPass) {
    echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
    exit;
}

// ========================================
// IMAGE UPLOAD
// ========================================
$profilePicPath = null;

if (!empty($_FILES['profile_pic']['name'])) {

    // Allowed types
    $allowedTypes = ["image/jpeg", "image/png"];

    if (!in_array($_FILES['profile_pic']['type'], $allowedTypes)) {
        echo json_encode(["status" => "error", "message" => "Invalid image format"]);
        exit;
    }

    // Max size = 2MB
    if ($_FILES['profile_pic']['size'] > 2 * 1024 * 1024) {
        echo json_encode(["status" => "error", "message" => "Image too large (max 2MB)"]);
        exit;
    }

    // File path
    $fileName   = time() . "_" . basename($_FILES['profile_pic']['name']);
    $targetPath = "uploads/" . $fileName;

    // Upload file
    if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
        $profilePicPath = $targetPath;
    }
}

// ========================================
// BUILD UPDATE QUERY
// ========================================
$query = "
    UPDATE users SET 
        first_name=?, 
        middle_name=?, 
        last_name=?, 
        date_of_birth=?, 
        gender=?,
        region_id=?, 
        province_id=?, 
        city_id=?, 
        barangay_id=?, 
        zip_code=?,
        phone_number=?, 
        email=?
";

// Add image if uploaded
if ($profilePicPath !== null) {
    $query .= ", profile_pic='" . $profilePicPath . "' ";
}

// Hash the password before saving
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query .= ", password=? ";
}

$query .= " WHERE user_id=?";

$stmt = $conn->prepare($query);

// ========================================
// BIND PARAMETERS
// ========================================

// WITHOUT password
if (empty($password)) {
    $stmt->bind_param(
        "sssssiiiisssi",
        $first, $middle, $last, $dob, $gender,
        $region, $province, $city, $barangay,
        $zip, $phone, $email,
        $user_id
    );
}

// WITH password
else {
    $stmt->bind_param(
        "sssssiiiissssi",
        $first, $middle, $last, $dob, $gender,
        $region, $province, $city, $barangay,
        $zip, $phone, $email,
        $hashedPassword,
        $user_id
    );
}

// ========================================
// EXECUTE QUERY
// ========================================
if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

ob_end_flush();
?>
