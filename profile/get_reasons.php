<?php
include '../db_connect.php';

$stmt = $conn->prepare("SELECT reason_id, reason_name FROM reasons ORDER BY reason_name ASC");
$stmt->execute();
$result = $stmt->get_result();

$reasons = [];
while($row = $result->fetch_assoc()) {
    $reasons[] = $row;
}

header('Content-Type: application/json');
echo json_encode($reasons);
?>
