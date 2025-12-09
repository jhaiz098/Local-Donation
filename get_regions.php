<?php
include 'db_connect.php';

$result = $conn->query("SELECT id, name FROM regions ORDER BY name");
$regions = [];
while($row = $result->fetch_assoc()) $regions[] = $row;

echo json_encode($regions);
?>
