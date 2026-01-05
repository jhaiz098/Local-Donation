<?php
header('Content-Type: application/json');
require '../db_connect.php';

if (!isset($_POST['pending_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing pending ID']);
    exit;
}

$pendingId = intval($_POST['pending_id']);

$conn->begin_transaction();

// Function to insert activity log
function logActivity($conn, $profileId, $description, $displayText) {
    $stmt = $conn->prepare("INSERT INTO activities (profile_id, description, display_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $profileId, $description, $displayText);
    $stmt->execute();
    $stmt->close();
}

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

    // 2️⃣ Reduce quantity from recipient's request entry
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

    // 3️⃣ Delete the pending donation row
    $stmt = $conn->prepare("
        DELETE FROM pending_donation_items
        WHERE pending_item_id = ?
    ");
    $stmt->bind_param("i", $pendingId);
    $stmt->execute();
    $stmt->close();

    // 4️⃣ Delete the donation entry if it has no more items
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS item_count
        FROM donation_entry_items
        WHERE entry_id = ?
    ");
    $stmt->bind_param("i", $recipientEntryId);
    $stmt->execute();
    $itemCount = $stmt->get_result()->fetch_assoc()['item_count'] ?? 0;
    $stmt->close();

    if ($itemCount == 0) {
        $stmt = $conn->prepare("
            DELETE FROM donation_entries
            WHERE entry_id = ?
        ");
        $stmt->bind_param("i", $recipientEntryId);
        $stmt->execute();
        $stmt->close();
    }

    $conn->commit();

    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Confirm delivery failed: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
