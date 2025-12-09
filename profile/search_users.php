<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../db_connect.php';

$q = $_GET['q'] ?? '';
$profileId = intval($_GET['profile_id'] ?? 0);

if(!$q || !$profileId) exit(json_encode([]));

$q = "%$q%";

// Only users not already in the profile and not the owner
$sql = "SELECT u.user_id, u.email
        FROM users u
        WHERE u.email LIKE ?
        AND u.user_id NOT IN (SELECT user_id FROM profile_members WHERE profile_id = ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $q, $profileId);
$stmt->execute();
$res = $stmt->get_result();

$users = [];
while($row = $res->fetch_assoc()){
    $users[] = $row;
}

echo json_encode($users);
