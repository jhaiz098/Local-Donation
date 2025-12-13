<?php
// Include your database connection file
include('../db_connect.php');

// Check if the entry_id is set and valid
if (isset($_POST['entry_id'])) {
    $entry_id = $_POST['entry_id'];

    // Prepare and execute the delete query
    $sql = "DELETE FROM donation_entries WHERE entry_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $entry_id);  // 'i' means integer
    if ($stmt->execute()) {
        // Successfully deleted the entry
        echo json_encode(['success' => true, 'message' => 'Donation entry deleted successfully.']);
    } else {
        // Error while deleting the entry
        echo json_encode(['success' => false, 'message' => 'Failed to delete the donation entry.']);
    }
    
    $stmt->close();
} else {
    // If entry_id is not provided or invalid
    echo json_encode(['success' => false, 'message' => 'Invalid request. Entry ID not found.']);
}

$conn->close();
?>
