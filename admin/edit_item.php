<?php
// edit_item.php

include('../db_connect.php');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $item_id = $_POST['item_id'];
    $type = $_POST['type'];

    if ($action === 'edit' && $item_id && $type) {
        // Perform the editing logic here
        // Example: Update item details in the database
        $stmt = $conn->prepare("UPDATE items SET item_name = ? WHERE item_id = ?");
        $stmt->bind_param("si", $newItemName, $item_id);
        $newItemName = "Updated Item Name";  // Change as per your logic
        $stmt->execute();

        // Return a success response
        echo json_encode(["status" => "success", "message" => "Item has been updated."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
