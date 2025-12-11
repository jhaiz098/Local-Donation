<?php
include '../db_connect.php';

// Get the pending admin ID from the request
$pendingAdminId = $_POST['pending_admin_id'];  // Assuming this is passed via POST

// Step 1: Retrieve the pending admin data
$sql = "SELECT * FROM pending_admins WHERE pending_admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pendingAdminId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $adminData = $result->fetch_assoc();
    
    // Step 2: Insert into the `users` table using the stored procedure
    $stmt = $conn->prepare("CALL sp_insert_user(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssssssssss", 
        $adminData['first_name'], 
        $adminData['middle_name'], 
        $adminData['last_name'], 
        $adminData['date_of_birth'], 
        $adminData['gender'], 
        $adminData['zip_code'], 
        $adminData['phone_number'], 
        $adminData['email'], 
        $adminData['password'],
        $adminData['role'],
        $adminData['region_id'], 
        $adminData['province_id'], 
        $adminData['city_id'], 
        $adminData['barangay_id']
    );

    if ($stmt->execute()) {
        // Step 3: Delete from `pending_admins` table after successful insertion
        $deleteSql = "DELETE FROM pending_admins WHERE pending_admin_id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $pendingAdminId);
        $deleteStmt->execute();
        
        // Respond with success
        echo json_encode(["status" => "success", "message" => "Admin approved successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to approve admin."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Pending admin not found."]);
}
?>
