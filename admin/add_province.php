<?php
// add_province.php
include '../db_connect.php'; // Your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $province_name = trim($_POST['province_name']);
    $region_id = intval($_POST['region_id']); // Assuming region_id is passed

    if (empty($province_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Province name cannot be empty']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO provinces (name, region_id) VALUES (?, ?)");
    $stmt->bind_param('si', $province_name, $region_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Province added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add province']);
    }

    $stmt->close();
    $conn->close();
}
?>
