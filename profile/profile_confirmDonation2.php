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
        $donor_profile_id = intval($item['donator_profile_id'] ?? 0);
        $item_id = intval($item['recipient_item_id'] ?? 0);
        $unit = trim($item['unit'] ?? 'pcs');

        // Basic validation
        if ($quantity <= 0 || $requestEntryId <= 0 || $donor_profile_id <= 0 || $item_id <= 0) {
            continue;
        }

        /**
         * IMPORTANT:
         * We do NOT deduct quantities
         * We do NOT log donations
         * We ONLY create pending donation records
         */

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
    }

    $conn->commit();
    echo json_encode([
        'status' => 'success',
        'message' => 'Donation items sent for requester confirmation.'
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Pending donation failed: " . $e->getMessage());

    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to submit pending donation.'
    ]);
}
