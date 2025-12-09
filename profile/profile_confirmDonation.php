<?php
header('Content-Type: application/json');
require '../db_connect.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if donation data is sent
if (!isset($_POST['donation_items'])) {
    echo json_encode(['status' => 'error', 'message' => 'No donation data received.']);
    exit;
}

// Decode the JSON donation data
$donationItems = json_decode($_POST['donation_items'], true);

// Ensure the donation data is an array
if (!is_array($donationItems)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid donation data. Must be an array.']);
    exit;
}

// Helper function to update and delete donation entry items
function updateItemQuantity($conn, $entryId, $itemEntryId, $quantity) {
    if ($quantity <= 0 || $entryId <= 0 || $itemEntryId <= 0) {
        return false;
    }

    // Update quantity of donation entry items
    $stmt = $conn->prepare("UPDATE donation_entry_items SET quantity = quantity - ? WHERE entry_id = ? AND item_entry_id = ?");
    $stmt->bind_param("iii", $quantity, $entryId, $itemEntryId);
    $stmt->execute();
    $stmt->close();

    // Remove items if quantity is zero or negative
    $stmt = $conn->prepare("DELETE FROM donation_entry_items WHERE entry_id = ? AND item_entry_id = ? AND quantity <= 0");
    $stmt->bind_param("ii", $entryId, $itemEntryId);
    $stmt->execute();
    $stmt->close();

    return true;
}

// Function to insert activity log
function logActivity($conn, $profileId, $description, $displayText) {
    $stmt = $conn->prepare("INSERT INTO activities (profile_id, description, display_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $profileId, $description, $displayText);
    $stmt->execute();
    $stmt->close();
}

// Begin MySQL transaction
$conn->begin_transaction();

try {
    // Step 1: Process donations and log donation activities
    foreach ($donationItems as $item) {
        $quantity = intval($item['quantity'] ?? 0);
        $requestEntryId = intval($item['recipient_entry_id'] ?? 0);
        $offerEntryId = intval($item['donator_entry_id'] ?? 0);
        $donor_profile_id = intval($item['donator_profile_id'] ?? 0);
        $recipient_profile_id = intval($item['recipient_profile_id'] ?? 0);
        $request_item_entry_id = intval($item['recipient_item_entry_id'] ?? 0);
        $offer_item_entry_id = intval($item['donator_item_entry_id'] ?? 0);
        $item_id = intval($item['recipient_item_id'] ?? 0);
        $unit = $conn->real_escape_string($item['unit']);

        // Skip invalid donation items
        if ($quantity <= 0 || $request_item_entry_id <= 0 || $offer_item_entry_id <= 0) {
            error_log("Skipping invalid donation item: " . json_encode($item));
            continue;
        }

        // Step 2: Insert donation log
        if ($item_id > 0) {
            $stmt = $conn->prepare("INSERT INTO donation_logs (donor_profile_id, recipient_profile_id, item_id, quantity, unit_name) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiis", $donor_profile_id, $recipient_profile_id, $item_id, $quantity, $unit);
            $stmt->execute();
            $stmt->close();

            // Query recipient profile details
            $recipient = getProfileDetails($conn, $recipient_profile_id);
            $recipient_profile_name = $recipient['profile_name'];
            $recipient_profile_type = $recipient['profile_type'];

            // Query item name
            $item_name = getItemName($conn, $item_id);

            // Query donor profile details
            $donor = getProfileDetails($conn, $donor_profile_id);
            $donor_profile_name = $donor['profile_name'];
            $donor_profile_type = $donor['profile_type'];

            // Log donation activity for the donor
            logActivity($conn, $donor_profile_id, 
                "Donated item ($item_name) of $quantity $unit to profile $recipient_profile_name. ($recipient_profile_type)", 
                "You donated $quantity $unit of item: $item_name.");
            
            // Log donation activity for the recipient
            logActivity($conn, $recipient_profile_id, 
                "Received item ($item_name) of $quantity $unit from profile $donor_profile_name. ($donor_profile_type)", 
                "You received $quantity $unit of item: $item_name.");
        }

        // Step 3: Update offer and request items in the donation entries
        updateItemQuantity($conn, $offerEntryId, $offer_item_entry_id, $quantity);
        updateItemQuantity($conn, $requestEntryId, $request_item_entry_id, $quantity);
    }

    // Step 4: Check for empty donation entries (entries with no items left)
    $stmt = $conn->prepare("
        SELECT de.entry_id, de.profile_id AS donor_profile_id, de.entry_type
        FROM donation_entries de
        LEFT JOIN donation_entry_items dei ON de.entry_id = dei.entry_id
        WHERE dei.entry_id IS NULL
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    // Step 5: Log activities for donors and recipients if items have run out
    while ($row = $result->fetch_assoc()) {
        $donorProfileId = $row['donor_profile_id'];
        $entryId = $row['entry_id'];
        $entry_type = $row['entry_type'];
        
        if ($entry_type == 'offer') {
            // Log the donor's "run out of items" activity
            logActivity($conn, $donorProfileId, 
                "Donation entry #$entryId has run out of items.",
                "Your donation offer has run out of items.");
        }
        
        if ($entry_type == 'request') {
            // Log the fulfillment for the recipient's request
            logActivity($conn, $recipient_profile_id, 
                "Your donation request has been fulfilled.",
                "Your donation request has been fulfilled. All items have been donated.");
        }
    }
    $stmt->close();

    // Step 6: Now delete the empty donation entries (entries with no items left)
    $conn->query("DELETE de FROM donation_entries de
        LEFT JOIN donation_entry_items dei ON de.entry_id = dei.entry_id
        WHERE dei.entry_id IS NULL
    ");

    // Step 7: Commit transaction
    $conn->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    // Rollback if any error occurs
    $conn->rollback();
    error_log("Donation processing failed: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Helper function to get profile details (name and type) for a given profile ID
function getProfileDetails($conn, $profileId) {
    $sql = "SELECT profile_name, profile_type FROM profiles WHERE profile_id = $profileId";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        error_log("Profile not found for profile_id: $profileId");
        return ['profile_name' => 'Unknown', 'profile_type' => 'Unknown']; // Default values
    }
}

// Helper function to get item name for a given item ID
function getItemName($conn, $itemId) {
    $sql = "SELECT item_name FROM items WHERE item_id = $itemId";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['item_name'];
    } else {
        error_log("Item not found for item_id: $itemId");
        return 'Unknown Item'; // Default value
    }
}
?>
