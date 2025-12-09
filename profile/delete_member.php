<?php
require '../db_connect.php';
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['user_id'], $_POST['profile_id'])) {
        throw new Exception('Missing parameters');
    }

    $userId = intval($_POST['user_id']);
    $profileId = intval($_POST['profile_id']);

    if (!$userId || !$profileId) {
        throw new Exception('Invalid user or profile ID');
    }

    $stmt = $conn->prepare("DELETE FROM profile_members WHERE user_id = ? AND profile_id = ?");
    if (!$stmt) throw new Exception($conn->error);

    $stmt->bind_param("ii", $userId, $profileId);

    if (!$stmt->execute()) {
        throw new Exception('Failed to delete member: ' . $stmt->error);
    }

    echo json_encode(['success' => true]);

    $stmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
