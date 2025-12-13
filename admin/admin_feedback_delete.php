<?php
include '../db_connect.php';

// Only Admins and Superusers can delete
$user_id = $_SESSION['user_id'] ?? null;
$roleSql = "SELECT role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($roleSql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$roleResult = $stmt->get_result();
$roleRow = $roleResult->fetch_assoc();
$currentRole = $roleRow['role'] ?? 'User';

if (!in_array($currentRole, ['Admin', 'Superuser'])) {
    die("Unauthorized action.");
}

// Check if feedback_id is set
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$feedback_id = intval($_GET['id']);

// Delete the feedback
$deleteSql = "DELETE FROM feedback WHERE feedback_id = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("i", $feedback_id);
$deleteStmt->execute();

// Redirect back to feedback page
header("Location: admin_feedback.php");
exit();
