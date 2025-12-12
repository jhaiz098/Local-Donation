<?php
include('../db_connect.php');

// Check if action, item_id, and unit_name are set
if (isset($_POST['action']) && $_POST['action'] === 'add' && isset($_POST['item_id']) && isset($_POST['unit_name'])) {
    $item_id = $_POST['item_id'];
    $unit_name = $_POST['unit_name'];

    // Sanitize inputs
    $item_id = $conn->real_escape_string($item_id);
    $unit_name = $conn->real_escape_string($unit_name);

    // Insert the new unit into the database
    $query = "INSERT INTO item_units (item_id, unit_name) VALUES ('$item_id', '$unit_name')";
    if ($conn->query($query) === TRUE) {
        // Return success response
        echo json_encode(['status' => 'success', 'message' => 'Unit added successfully.']);
    } else {
        // Return error response
        echo json_encode(['status' => 'error', 'message' => 'Error adding unit: ' . $conn->error]);
    }
} else {
    // Return error if the required data is missing
    echo json_encode(['status' => 'error', 'message' => 'Missing required data.']);
}
?>
