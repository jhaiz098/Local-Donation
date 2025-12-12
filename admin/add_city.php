<?php
// add_city.php
include '../db_connect.php'; // Your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $city_name = trim($_POST['city_name']);
    $province_id = intval($_POST['province_id']); // Assuming province_id is passed

    if (empty($city_name)) {
        echo json_encode(['status' => 'error', 'message' => 'City name cannot be empty']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO cities (name, province_id) VALUES (?, ?)");
    $stmt->bind_param('si', $city_name, $province_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'City/Municipality added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add city/municipality']);
    }

    $stmt->close();
    $conn->close();
}
?>
