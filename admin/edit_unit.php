<?php
include('../db_connect.php');

// Check if action is 'edit' and all necessary data is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $itemId = $_POST['item_id'];
    $oldUnitName = $_POST['old_unit_name'];
    $newUnitName = $_POST['new_unit_name'];

    // Prepare and sanitize inputs
    $itemId = (int)$itemId;
    $oldUnitName = $conn->real_escape_string($oldUnitName);
    $newUnitName = $conn->real_escape_string($newUnitName);

    // Update the unit name in the database
    $updateQuery = "UPDATE item_units SET unit_name = '$newUnitName' WHERE item_id = $itemId AND unit_name = '$oldUnitName'";

    if ($conn->query($updateQuery) === TRUE) {
        // If update was successful
        echo json_encode(["status" => "success", "message" => "Unit name updated"]);
    } else {
        // If an error occurred
        echo json_encode(["status" => "error", "message" => "Failed to update unit"]);
    }

    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
