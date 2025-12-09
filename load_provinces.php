<?php
include "db_connect.php";

$region_id = $_GET['region_id'] ?? 0;

$stmt = $conn->prepare("SELECT id, name FROM provinces WHERE region_id = ? ORDER BY name ASC");
$stmt->bind_param("i", $region_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
