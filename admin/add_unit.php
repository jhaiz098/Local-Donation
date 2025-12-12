<?php
include('../db_connect.php');

// Check if the request contains the required parameters
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    // Retrieve the data from POST
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $unit_name = $_POST['unit_name'];

    // Validate the data
    if (empty($item_id) || empty($unit_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Item ID and unit name are required.']);
        exit;
    }

    // Insert the new unit into the database
    $stmt = $conn->prepare("INSERT INTO item_units (item_id, unit_name) VALUES (?, ?)");
    $stmt->bind_param("is", $item_id, $unit_name);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add unit.']);
    }

    $stmt->close();
}
?>
