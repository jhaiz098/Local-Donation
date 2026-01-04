<?php
header('Content-Type: application/json');
require '../db_connect.php';

// Error reporting (dev only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check payload
if (!isset($_POST['donation_items'])) {
    echo json_encode(['status' => 'error', 'message' => 'No donation data received.']);
    exit;
}

$donationItems = json_decode($_POST['donation_items'], true);

if (!is_array($donationItems)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid donation data format.']);
    exit;
}

$conn->begin_transaction();

try {
    foreach ($donationItems as $item) {

        $quantity = intval($item['quantity'] ?? 0);
        $requestEntryId = intval($item['recipient_entry_id'] ?? 0);
        $request_item_entry_id = intval($item['recipient_item_entry_id'] ?? 0); // request item row
        $offerEntryId = intval($item['donator_entry_id'] ?? 0);
        $offer_item_entry_id = intval($item['donator_item_entry_id'] ?? 0);   // donor item row
        $donor_profile_id = intval($item['donator_profile_id'] ?? 0);
        $item_id = intval($item['recipient_item_id'] ?? 0);
        $unit = trim($item['unit'] ?? 'pcs');

        // Basic validation
        if ($quantity <= 0 || $requestEntryId <= 0 || $donor_profile_id <= 0 || $item_id <= 0) {
            continue;
        }

        // --- Insert into pending donations ---
        $stmt = $conn->prepare("
            INSERT INTO pending_donation_items
                (entry_id, donor_profile_id, item_id, quantity, unit_name)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iiiis",
            $requestEntryId,
            $donor_profile_id,
            $item_id,
            $quantity,
            $unit
        );
        $stmt->execute();
        $stmt->close();

        // --- Deduct quantity from donor offer ---
        if ($offerEntryId > 0 && $offer_item_entry_id > 0) {
            $stmt = $conn->prepare("
                UPDATE donation_entry_items
                SET quantity = quantity - ?
                WHERE entry_id = ? AND item_entry_id = ?
            ");
            $stmt->bind_param("iii", $quantity, $offerEntryId, $offer_item_entry_id);
            $stmt->execute();
            $stmt->close();

            // Remove item if quantity <= 0
            $stmt = $conn->prepare("
                DELETE FROM donation_entry_items
                WHERE entry_id = ? AND item_entry_id = ? AND quantity <= 0
            ");
            $stmt->bind_param("ii", $offerEntryId, $offer_item_entry_id);
            $stmt->execute();
            $stmt->close();
        }

        // --- Deduct quantity from request entry ---
        if ($request_item_entry_id > 0) {
            $stmt = $conn->prepare("
                UPDATE donation_entry_items
                SET quantity = quantity - ?
                WHERE entry_id = ? AND item_entry_id = ?
            ");
            $stmt->bind_param("iii", $quantity, $requestEntryId, $request_item_entry_id);
            $stmt->execute();
            $stmt->close();

            // Remove item if quantity <= 0
            $stmt = $conn->prepare("
                DELETE FROM donation_entry_items
                WHERE entry_id = ? AND item_entry_id = ? AND quantity <= 0
            ");
            $stmt->bind_param("ii", $requestEntryId, $request_item_entry_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Pending donation created and quantities deducted.'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Pending donation failed: " . $e->getMessage());

    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to submit pending donation.'
    ]);
}
