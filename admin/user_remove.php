<?php
header('Content-Type: application/json');
include '../db_connect.php';

if (!isset($_POST['user_id'])) {
    echo json_encode(["status" => "error", "message" => "No user ID provided."]);
    exit;
}

$user_id = intval($_POST['user_id']);

// CHECK IF USER EXISTS
$check = $conn->query("SELECT * FROM users WHERE user_id = $user_id");
if ($check->num_rows == 0) {
    echo json_encode(["status" => "error", "message" => "User not found."]);
    exit;
}

// DELETE USER
$delete = $conn->query("DELETE FROM users WHERE user_id = $user_id");

if ($delete) {
    echo json_encode(["status" => "success", "message" => "User removed successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to remove user.", "error" => $conn->error]);
}

?>
