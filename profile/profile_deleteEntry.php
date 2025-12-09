<?php
require '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entry_id = isset($_POST['entry_id']) ? intval($_POST['entry_id']) : 0;
    $type = isset($_POST['type']) ? $_POST['type'] : ''; // Get the type of donation (offer/request)

    if ($entry_id <= 0 || empty($type)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid entry ID or type.']);
        exit;
    }

    // First, delete items linked to this entry
    $stmt1 = $conn->prepare("DELETE FROM donation_entry_items WHERE entry_id = ?");
    $stmt1->bind_param("i", $entry_id);
    $stmt1->execute();
    $stmt1->close();

    // Then, delete the entry itself
    $stmt2 = $conn->prepare("DELETE FROM donation_entries WHERE entry_id = ?");
    $stmt2->bind_param("i", $entry_id);
    $stmt2->execute();

    if ($stmt2->affected_rows > 0) {
        // Log the activity for deleting the donation entry
        logActivity($conn, $_SESSION['profile_id'], 
            "Deleted donation entry #$entry_id: " . strtolower($type), 
            "You deleted a donation entry " . strtolower($type));

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Entry not found or already deleted.']);
    }

    $stmt2->close();
}

// Function to log activity
function logActivity($conn, $profileId, $description, $displayText) {
    try {
        $stmt = $conn->prepare("INSERT INTO activities (profile_id, description, display_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $profileId, $description, $displayText);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
}
?>
