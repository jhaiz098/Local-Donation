<?php
header('Content-Type: application/json');
require '../db_connect.php';

$pendingId = intval($_POST['pending_item_id'] ?? 0);

if ($pendingId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid pending donation.']);
    exit;
}

$stmt = $conn->prepare("
    DELETE FROM pending_donation_items
    WHERE pending_item_id = ?
");
$stmt->bind_param("i", $pendingId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error.']);
}

$stmt->close();
