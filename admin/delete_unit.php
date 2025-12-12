<?php
include('../db_connect.php');

// Check if action is 'delete' and all necessary data is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $itemId = $_POST['item_id'];
    $unitName = $_POST['unit_name'];

    // Prepare and sanitize inputs
    $itemId = (int)$itemId;
    $unitName = $conn->real_escape_string($unitName);

    // Delete the unit from the database
    $deleteQuery = "DELETE FROM item_units WHERE item_id = $itemId AND unit_name = '$unitName'";

    if ($conn->query($deleteQuery) === TRUE) {
        // If delete was successful
        echo json_encode(["status" => "success", "message" => "Unit deleted"]);
    } else {
        // If an error occurred
        echo json_encode(["status" => "error", "message" => "Failed to delete unit"]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
