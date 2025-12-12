<?php
// add_item.php

include('../db_connect.php');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the POST request
    $action = $_POST['action'];
    $item_name = $_POST['item_name'];

    // Validate if the required fields are provided
    if ($action === 'add' && $item_name) {
        // Sanitize the item name to prevent SQL injection
        $item_name = htmlspecialchars($item_name);

        // Insert the new item into the database
        $stmt = $conn->prepare("INSERT INTO items (item_name) VALUES (?)");
        $stmt->bind_param("s", $item_name);
        
        // Execute the query
        if ($stmt->execute()) {
            // Return a success response
            echo json_encode(["status" => "success", "message" => "Item has been added."]);
        } else {
            // If query fails, return an error response
            echo json_encode(["status" => "error", "message" => "Failed to add item."]);
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
