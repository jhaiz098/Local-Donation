<?php
header('Content-Type: application/json');
require '../db_connect.php';

if (!isset($_POST['pending_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing pending ID']);
    exit;
}

$pendingId = intval($_POST['pending_id']);

$conn->begin_transaction();

try {
    // 1️⃣ Get pending donation details
    $stmt = $conn->prepare("
        SELECT entry_id AS recipient_entry_id, item_id, quantity
        FROM pending_donation_items
        WHERE pending_item_id = ?
    ");
    $stmt->bind_param("i", $pendingId);
    $stmt->execute();
    $pendingData = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$pendingData) {
        throw new Exception("Pending donation not found.");
    }

    $recipientEntryId = intval($pendingData['recipient_entry_id']);
    $itemId = intval($pendingData['item_id']);
    $quantity = intval($pendingData['quantity']);
    // $requestItemEntryId = intval($pendingData['request_item_entry_id'] ?? 0);

    // 2️⃣ Reduce quantity from recipient's request entry
    // if ($requestItemEntryId > 0) {
        $stmt = $conn->prepare("
            UPDATE donation_entry_items
            SET quantity = quantity - ?
            WHERE entry_id = ? AND item_id = ?
        ");
        $stmt->bind_param("iii", $quantity, $recipientEntryId, $itemId);
        $stmt->execute();
        $stmt->close();

        // Remove item if quantity <= 0
        $stmt = $conn->prepare("
            DELETE FROM donation_entry_items
            WHERE entry_id = ? AND item_id = ? AND quantity <= 0
        ");
        $stmt->bind_param("ii", $recipientEntryId, $itemId);
        $stmt->execute();
        $stmt->close();
    // }

    // 3️⃣ Delete the pending donation row
    $stmt = $conn->prepare("
        DELETE FROM pending_donation_items
        WHERE pending_item_id = ?
    ");
    $stmt->bind_param("i", $pendingId);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Confirm delivery failed: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
