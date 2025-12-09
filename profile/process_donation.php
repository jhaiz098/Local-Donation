<?php
require '../db_connect.php';

// Check if donation data is sent via POST
if (isset($_POST['donationItems']) && isset($_POST['entry_id'])) {
    $donationItems = json_decode($_POST['donationItems'], true);
    $entryId = intval($_POST['entry_id']);

    // Begin transaction to ensure all changes happen atomically
    $conn->begin_transaction();

    try {
        // Update the item quantities for each donated item
        foreach ($donationItems as $item) {
            $itemName = $item['item_name'];
            $donationQuantity = $item['donation_quantity'];

            // Update the available quantity for the item (decrease it)
            $updateStmt = $conn->prepare("UPDATE donation_entry_items 
                                          SET quantity = quantity - ? 
                                          WHERE entry_id = ? AND item_name = ?");
            $updateStmt->bind_param("iis", $donationQuantity, $entryId, $itemName);
            $updateStmt->execute();
            $updateStmt->close();
        }

        // Commit the transaction
        $conn->commit();
        echo json_encode(["status" => "success", "message" => "Donation processed successfully."]);
    } catch (Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Error processing donation: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}

$conn->close();
?>
