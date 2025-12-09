<?php
include "db_connect.php";

$province_id = $_GET['province_id'] ?? 0;

$stmt = $conn->prepare("SELECT id, name FROM cities WHERE province_id = ? ORDER BY name ASC");
$stmt->bind_param("i", $province_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
