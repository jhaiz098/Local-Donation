<?php
include "db_connect.php";

$city_id = $_GET['city_id'] ?? 0;

$stmt = $conn->prepare("SELECT id, name FROM barangays WHERE city_id = ? ORDER BY name ASC");
$stmt->bind_param("i", $city_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
