<?php
// add_region.php
include '../db_connect.php'; // Your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $region_name = trim($_POST['region_name']);
    
    if (empty($region_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Region name cannot be empty']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO regions (name) VALUES (?)");
    $stmt->bind_param('s', $region_name);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Region added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add region']);
    }

    $stmt->close();
    $conn->close();
}
?>
