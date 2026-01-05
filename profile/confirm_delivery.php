<?php
header('Content-Type: application/json');
require '../db_connect.php';

if (!isset($_POST['pending_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing pending ID']);
    exit;
}

$pendingId = (int)$_POST['pending_id'];

$conn->begin_transaction();

// Function to insert activity log
function logActivity($conn, $profileId, $description, $displayText) {
    $stmt = $conn->prepare("
        INSERT INTO activities (profile_id, description, display_text)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iss", $profileId, $description, $displayText);
    $stmt->execute();
    $stmt->close();
}

try {
    /* ===============================
       1️⃣ Get pending donation details
       =============================== */
    $stmt = $conn->prepare("
        SELECT 
            pdi.entry_id AS recipient_entry_id,
            pdi.item_id,
            pdi.quantity,
            pdi.donor_profile_id,
            de.profile_id AS recipient_profile_id
        FROM pending_donation_items pdi
        JOIN donation_entries de ON pdi.entry_id = de.entry_id
        WHERE pdi.pending_item_id = ?
    ");
    $stmt->bind_param("i", $pendingId);
    $stmt->execute();
    $pendingData = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$pendingData) {
        throw new Exception("Pending donation not found.");
    }

    $recipientEntryId   = (int)$pendingData['recipient_entry_id'];
    $itemId             = (int)$pendingData['item_id'];
    $quantity           = (int)$pendingData['quantity'];
    $donorProfileId     = (int)$pendingData['donor_profile_id'];
    $recipientProfileId = (int)$pendingData['recipient_profile_id'];

    /* ===============================
       1.5️⃣ Get unit_name
       =============================== */
    $stmt = $conn->prepare("
        SELECT unit_name
        FROM donation_entry_items
        WHERE entry_id = ? AND item_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $recipientEntryId, $itemId);
    $stmt->execute();
    $unitData = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $unit = $unitData['unit_name'] ?? '';

    /* ===============================
       1.6️⃣ Get item name
       =============================== */
    $stmt = $conn->prepare("
        SELECT item_name
        FROM items
        WHERE item_id = ?
    ");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $itemData = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $itemName = $itemData['item_name'] ?? 'item';

    /* ===============================
       1.7️⃣ Get donor & recipient names
       =============================== */
    $stmt = $conn->prepare("
        SELECT profile_id, profile_name
        FROM profiles
        WHERE profile_id IN (?, ?)
    ");
    $stmt->bind_param("ii", $donorProfileId, $recipientProfileId);
    $stmt->execute();
    $result = $stmt->get_result();

    $donorName = 'Donor';
    $recipientName = 'Recipient';

    while ($row = $result->fetch_assoc()) {
        if ((int)$row['profile_id'] === $donorProfileId) {
            $donorName = $row['profile_name'];
        }
        if ((int)$row['profile_id'] === $recipientProfileId) {
            $recipientName = $row['profile_name'];
        }
    }
    $stmt->close();

    /* ===============================
       2️⃣ Reduce recipient item quantity
       =============================== */
    $stmt = $conn->prepare("
        UPDATE donation_entry_items
        SET quantity = quantity - ?
        WHERE entry_id = ? AND item_id = ?
    ");
    $stmt->bind_param("iii", $quantity, $recipientEntryId, $itemId);
    $stmt->execute();
    $stmt->close();

    /* ===============================
       Remove item if quantity <= 0
       =============================== */
    $stmt = $conn->prepare("
        DELETE FROM donation_entry_items
        WHERE entry_id = ? AND item_id = ? AND quantity <= 0
    ");
    $stmt->bind_param("ii", $recipientEntryId, $itemId);
    $stmt->execute();
    $stmt->close();

    /* ===============================
       3️⃣ Delete pending donation row
       =============================== */
    $stmt = $conn->prepare("
        DELETE FROM pending_donation_items
        WHERE pending_item_id = ?
    ");
    $stmt->bind_param("i", $pendingId);
    $stmt->execute();
    $stmt->close();

    /* ===============================
       4️⃣ Delete donation entry if empty
       =============================== */
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS item_count
        FROM donation_entry_items
        WHERE entry_id = ?
    ");
    $stmt->bind_param("i", $recipientEntryId);
    $stmt->execute();
    $itemCount = (int)($stmt->get_result()->fetch_assoc()['item_count'] ?? 0);
    $stmt->close();

    if ($itemCount === 0) {
        $stmt = $conn->prepare("
            DELETE FROM donation_entries
            WHERE entry_id = ?
        ");
        $stmt->bind_param("i", $recipientEntryId);
        $stmt->execute();
        $stmt->close();
    }

    /* ===============================
       5️⃣ Insert donation log
       =============================== */
    $stmt = $conn->prepare("
        INSERT INTO donation_logs
        (donor_profile_id, recipient_profile_id, item_id, quantity, unit_name)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "iiiis",
        $donorProfileId,
        $recipientProfileId,
        $itemId,
        $quantity,
        $unit
    );
    $stmt->execute();
    $stmt->close();

    /* ===============================
       6️⃣ Activity logs (with names)
       =============================== */
    logActivity(
        $conn,
        $donorProfileId,
        "donation_completed",
        "You donated {$quantity} {$unit} of {$itemName} to {$recipientName}"
    );

    logActivity(
        $conn,
        $recipientProfileId,
        "donation_received",
        "You received {$quantity} {$unit} of {$itemName} from {$donorName}"
    );

    $conn->commit();

    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Confirm delivery failed: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
