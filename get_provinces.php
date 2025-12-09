<?php
include 'db_connect.php';
$region_id = intval($_GET['region_id']);
$result = $conn->query("SELECT id, name FROM provinces WHERE region_id = $region_id ORDER BY name");
$provinces = [];
while($row = $result->fetch_assoc()) $provinces[] = $row;
echo json_encode($provinces);
?>
