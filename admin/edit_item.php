<?php
// edit_item.php

include('../db_connect.php');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the POST request
    $action = $_POST['action'];
    $item_id = $_POST['item_id'];
    $type = $_POST['type'];
    $new_item_name = $_POST['new_item_name']; // Get the new item name from the POST data

    // Validate that the required fields are provided
    if ($action === 'edit' && $item_id && $new_item_name) {
        // Sanitize the new item name to prevent SQL injection
        $new_item_name = htmlspecialchars($new_item_name);

        // Perform the editing logic: Update item details in the database
        $stmt = $conn->prepare("UPDATE items SET item_name = ? WHERE item_id = ?");
        $stmt->bind_param("si", $new_item_name, $item_id);
        
        // Execute the query
        if ($stmt->execute()) {
            // Return a success response
            echo json_encode(["status" => "success", "message" => "Item has been updated."]);
        } else {
            // If query fails, return an error response
            echo json_encode(["status" => "error", "message" => "Failed to update item."]);
        }
    } else {
        // Return an error response if required fields are missing
        echo json_encode(["status" => "error", "message" => "Invalid input data."]);
    }
} else {
    // Return an error response for invalid request method
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
