<?php
// Include the database connection
include '../db_connect.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Check if the data exists
if (isset($data['id']) && isset($data['newName']) && isset($data['type'])) {
    $id = $data['id'];
    $newName = $data['newName'];
    $type = $data['type'];

    // Map types to actual table names
    $typeToTable = [
        'region' => 'regions',
        'province' => 'provinces',
        'city' => 'cities',
        'barangay' => 'barangays'
    ];

    // Ensure that the type is valid and maps to a table
    if (isset($typeToTable[$type])) {
        $tableName = $typeToTable[$type];  // Get the correct table name

        // Prepare the query to update the name based on the type
        $updateQuery = "UPDATE $tableName SET name = ? WHERE id = ?";

        // Prepare the statement
        if ($stmt = $conn->prepare($updateQuery)) {
            $stmt->bind_param('si', $newName, $id); // 's' for string, 'i' for integer

            // Debugging output before executing
            // Log the query and parameters (do not expose sensitive info in production)
            error_log("Executing query: $updateQuery with ID: $id and Name: $newName");

            if ($stmt->execute()) {
                // Return a success response
                echo json_encode(['success' => true]);
            } else {
                // Log any error message from the query execution
                error_log("SQL Error: " . $stmt->error);  // Log SQL error for debugging
                echo json_encode(['success' => false, 'message' => 'Failed to update location', 'error' => $stmt->error]);
            }

            $stmt->close();
        } else {
            // Handle query preparation error
            error_log("Query preparation failed: " . $conn->error);  // Log query preparation error
            echo json_encode(['success' => false, 'message' => 'Failed to prepare query', 'error' => $conn->error]);
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
