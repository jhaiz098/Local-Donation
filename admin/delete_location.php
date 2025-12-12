<?php
// Include the database connection
include '../db_connect.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if the data exists
if (isset($data['id']) && isset($data['type'])) {
    $id = $data['id'];
    $type = $data['type'];

    // Ensure that the type (region, province, etc.) is a valid table name
    $validTypes = ['region', 'province', 'city', 'barangay'];
    
    if (in_array($type, $validTypes)) {
        // Prepare the query to delete the record based on the type
        $deleteQuery = "DELETE FROM $type WHERE id = ?";
        
        if ($stmt = $conn->prepare($deleteQuery)) {
            $stmt->bind_param('i', $id); // 'i' for integer
            
            if ($stmt->execute()) {
                // Return a success response
                echo json_encode(['success' => true]);
            } else {
                // Return a failure response
                echo json_encode(['success' => false, 'message' => 'Failed to delete location']);
            }
            
            $stmt->close();
        } else {
            // Handle query preparation error
            echo json_encode(['success' => false, 'message' => 'Failed to prepare query']);
        }
    } else {
        // Invalid location type
        echo json_encode(['success' => false, 'message' => 'Invalid location type']);
    }
} else {
    // Missing required data
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
}

// Close the database connection
$conn->close();
?>
