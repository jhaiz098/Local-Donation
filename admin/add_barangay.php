<?php
// add_barangay.php
include '../db_connect.php'; // Your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $barangay_name = trim($_POST['barangay_name']);
    $city_id = intval($_POST['city_id']); // Assuming city_id is passed

    if (empty($barangay_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Barangay name cannot be empty']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO barangays (name, city_id) VALUES (?, ?)");
    $stmt->bind_param('si', $barangay_name, $city_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Barangay added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add barangay']);
    }

    $stmt->close();
    $conn->close();
}
?>
