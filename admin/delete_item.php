<?php
// delete_item.php

include('../db_connect.php');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $item_id = $_POST['item_id'];
    $type = $_POST['type'];

    if ($action === 'delete' && $item_id && $type) {
        // Perform the deletion logic here
        // Example: Delete item from the database
        $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

        // Return a success response
        echo json_encode(["status" => "success", "message" => "Item has been deleted."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>
