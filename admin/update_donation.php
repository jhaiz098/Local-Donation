<?php
// update_donation.php

// Include your database connection
require_once '../db_connect.php';

// Check if data is sent via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Check if 'items' data is received and properly decoded
    if (isset($_POST['items'])) {
        $items = json_decode($_POST['items'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Error decoding JSON: " . json_last_error_msg());
        }
    } else {
        die("No items data received.");
    }

    // Get the posted data
    $entry_id = $_POST['entry_id'];
    $entry_type = $_POST['entry_type'];
    $details = $_POST['details'];
    $target_location = $_POST['target_location'];

    // Update the main donation record
    $sql = "UPDATE donation_entries SET entry_type = ?, details = ?, target_area = ? WHERE entry_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL error in update query: " . $conn->error); // Output detailed SQL error
    }
    $stmt->bind_param("sssi", $entry_type, $details, $target_location, $entry_id);

    if ($stmt->execute()) {
        // Now update the associated items
        // Delete existing items first
        $delete_sql = "DELETE FROM donation_entry_items WHERE entry_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        if (!$delete_stmt) {
            die("SQL error in delete query: " . $conn->error); // Output detailed SQL error
        }
        $delete_stmt->bind_param("i", $entry_id);
        $delete_stmt->execute();

        // Insert the updated items into the donation_items table
        $insert_sql = "INSERT INTO donation_entry_items (entry_id, item_id, unit_name, quantity) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        if (!$insert_stmt) {
            die("SQL error in insert query: " . $conn->error); // Output detailed SQL error
        }

        foreach ($items as $item) {
            $insert_stmt->bind_param("iisi", $entry_id, $item['item_id'], $item['unit_name'], $item['quantity']);
            $insert_stmt->execute();
        }

        // Return success response
        echo json_encode(['success' => true]);
    } else {
        // Return error response
        echo json_encode(['success' => false, 'message' => 'Failed to update the donation']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
