<?php
// add_member.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require '../db_connect.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $userId = intval($_POST['user_id'] ?? 0);
    $profileId = intval($_POST['profile_id'] ?? 0);
    $role = trim($_POST['role'] ?? 'member');
    $email = trim($_POST['email'] ?? '');

    if (!$profileId) throw new Exception('Invalid or missing profile_id.');

    // If user_id is missing or 0, try to get it from email
    if (!$userId && $email) {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        if (!$user) throw new Exception("User not found with email: $email");
        $userId = $user['user_id'];
    }

    if (!$userId) throw new Exception('Invalid or missing user_id.');

    // Check if user exists (optional now, since we got user_id from email)
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE user_id = ?");
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows === 0) {
        throw new Exception("User not found with user_id: $userId");
    }

    // Check if already a member
    $stmt = $conn->prepare("SELECT 1 FROM profile_members WHERE user_id = ? AND profile_id = ?");
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

    $stmt->bind_param("ii", $userId, $profileId);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if ($res->num_rows > 0) throw new Exception('User is already a member');

    // Insert new member
    $stmt = $conn->prepare("INSERT INTO profile_members (user_id, profile_id, role) VALUES (?, ?, ?)");
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);

    $stmt->bind_param("iis", $userId, $profileId, $role);
    if (!$stmt->execute()) throw new Exception("Insert failed: " . $stmt->error);
    $stmt->close();

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
