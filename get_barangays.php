<?php
include 'db_connect.php';
$city_id = intval($_GET['city_id']);
$result = $conn->query("SELECT id, name FROM barangays WHERE city_id = $city_id ORDER BY name");
$barangays = [];
while($row = $result->fetch_assoc()) $barangays[] = $row;
echo json_encode($barangays);
?>
