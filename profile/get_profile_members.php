<?php
require '../db_connect.php';

$profileId = intval($_GET['profile_id'] ?? 0);

$stmt = $conn->prepare("
    SELECT pm.*, u.first_name, u.middle_name, u.last_name, u.email 
    FROM profile_members pm
    JOIN users u ON pm.user_id = u.user_id
    WHERE pm.profile_id = ?
");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$result = $stmt->get_result();

$members = [];
while($row = $result->fetch_assoc()) {
    $members[] = [
        'id' => $row['id'],
        'user_id' => $row['user_id'],
        'email' => $row['email'],
        'name' => trim($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']),
        'role' => $row['role'],
        'created_at' => $row['created_at']
    ];
}

echo json_encode($members);
