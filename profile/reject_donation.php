<?php
header('Content-Type: application/json');
require '../db_connect.php';

if (!isset($_POST['pending_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing pending ID']);
    exit;
}

$pendingId = intval($_POST['pending_id']);

$stmt = $conn->prepare("
    DELETE FROM pending_donation_items
    WHERE pending_item_id = ?
");

$stmt->bind_param("i", $pendingId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
}

$stmt->close();
