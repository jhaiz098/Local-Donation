<?php
include 'db_connect.php';
$province_id = intval($_GET['province_id']);
$result = $conn->query("SELECT id, name FROM cities WHERE province_id = $province_id ORDER BY name");
$cities = [];
while($row = $result->fetch_assoc()) $cities[] = $row;
echo json_encode($cities);
?>
