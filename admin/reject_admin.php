<?php
include '../db_connect.php';

// Get the pending admin ID from the request
$pendingAdminId = $_POST['pending_admin_id'];  // Assuming this is passed via POST

// Step 1: Delete from `pending_admins` table (reject the admin)
$deleteSql = "DELETE FROM pending_admins WHERE pending_admin_id = ?";
$deleteStmt = $conn->prepare($deleteSql);
$deleteStmt->bind_param("i", $pendingAdminId);
$deleteStmt->execute();

if ($deleteStmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Admin rejected successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to reject admin."]);
}
?>
