<?php
// delete_profile.php
require 'db_connect.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Profile ID missing']);
    exit;
}

$profile_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'] ?? 0;

// Optional: check if the profile belongs to this user
$stmtCheck = $conn->prepare("SELECT * FROM profile_members WHERE profile_id = ? AND user_id = ?");
$stmtCheck->bind_param("ii", $profile_id, $user_id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Profile not found or access denied']);
    exit;
}

// Delete from profile_members first (if needed)
$stmtDeleteMembers = $conn->prepare("DELETE FROM profile_members WHERE profile_id = ?");
$stmtDeleteMembers->bind_param("i", $profile_id);
$stmtDeleteMembers->execute();

// Delete from profiles table
$stmtDeleteProfile = $conn->prepare("DELETE FROM profiles WHERE profile_id = ?");
$stmtDeleteProfile->bind_param("i", $profile_id);
$deleted = $stmtDeleteProfile->execute();

if ($deleted) {
    echo json_encode(['success' => true, 'message' => 'Profile deleted']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete profile']);
}
?>
