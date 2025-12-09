<?php
require '../db_connect.php';

header("Content-Type: application/json");
ini_set('display_errors', 0); // show PHP errors for debugging
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$profile_id = $_SESSION['profile_id'] ?? null;

if (!$profile_id) {
    echo json_encode(["status" => "error", "message" => "Session expired or no profile ID."]);
    exit;
}

$type = $_POST['type'] ?? null;
$details = $_POST['details'] ?? null;
$items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];
$target_area = $_POST['target_area'] ?? null;
$entry_id = isset($_POST['entry_id']) ? intval($_POST['entry_id']) : null; // for editing

if (!$type || !$details || empty($items)) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

$conn->begin_transaction();

try {
    if ($entry_id) {
        // === UPDATE EXISTING ENTRY ===
        // Update donation entry details
        $stmt = $conn->prepare("
            UPDATE donation_entries 
            SET entry_type = ?, details = ?, target_area = ? 
            WHERE entry_id = ? AND profile_id = ?
        ");
        $stmt->bind_param("sssii", $type, $details, $target_area, $entry_id, $profile_id);
        $stmt->execute();
        $stmt->close();

        // Log the activity for updating donation entry details
        logActivity($conn, $profile_id, 
            "Updated donation entry #$entry_id: $type", 
            "You updated a donation $type details.");

        // Delete old items for the entry (to avoid duplication)
        $delStmt = $conn->prepare("DELETE FROM donation_entry_items WHERE entry_id = ?");
        $delStmt->bind_param("i", $entry_id);
        $delStmt->execute();
        $delStmt->close();

        // // Log the activity for deleting all items from the entry
        // logActivity($conn, $profile_id, 
        //     "Deleted all items from donation entry #$entry_id", 
        //     "You deleted all items from the donation entry.");
    } else {
        // === INSERT NEW ENTRY ===
        $stmt = $conn->prepare("
            INSERT INTO donation_entries (profile_id, entry_type, details, target_area)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("isss", $profile_id, $type, $details, $target_area);
        $stmt->execute();
        $entry_id = $stmt->insert_id;
        $stmt->close();

        // Log the activity for adding a new donation entry
        logActivity($conn, $profile_id, 
            "Added new donation entry #$entry_id: $type", 
            "You added a new donation entry $type.");
    }

    // Insert items (only for new/updated entries)
    if (!empty($items)) {
        $stmtItem = $conn->prepare("
            INSERT INTO donation_entry_items (entry_id, item_id, quantity, unit_name)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($items as $it) {
            // Lookup item_id based on item_name
            $getId = $conn->prepare("SELECT item_id FROM items WHERE item_name = ?");
            $getId->bind_param("s", $it['name']);
            $getId->execute();
            $result = $getId->get_result()->fetch_assoc();
            $getId->close();

            if (!$result) {
                throw new Exception("Item not found: " . $it['name']);
            }

            $item_id = intval($result['item_id']);
            $quantity = intval($it['quantity']);
            $unit_name = $it['unit'];

            // Check if the item already exists for this entry
            $checkExistingItem = $conn->prepare("SELECT quantity FROM donation_entry_items WHERE entry_id = ? AND item_id = ?");
            $checkExistingItem->bind_param("ii", $entry_id, $item_id);
            $checkExistingItem->execute();
            $existingItem = $checkExistingItem->get_result()->fetch_assoc();
            $checkExistingItem->close();

            if ($existingItem) {
                // If item exists, update the quantity
                $newQuantity = $existingItem['quantity'] + $quantity;
                $updateItem = $conn->prepare("UPDATE donation_entry_items SET quantity = ? WHERE entry_id = ? AND item_id = ?");
                $updateItem->bind_param("iii", $newQuantity, $entry_id, $item_id);
                $updateItem->execute();
                $updateItem->close();

                // // Log activity for updating quantity of an existing item
                // logActivity($conn, $profile_id, 
                //     "Updated item quantity (ID: $item_id) in donation entry #$entry_id", 
                //     "You updated the quantity of $unit_name to $newQuantity for item ID: $item_id in donation entry #$entry_id.");
            } else {
                // Insert a new item if it doesn't exist
                $stmtItem->bind_param("iiis", $entry_id, $item_id, $quantity, $unit_name);
                $stmtItem->execute();

                // // Log activity for adding the item
                // logActivity($conn, $profile_id, 
                //     "Added item (ID: $item_id) to donation entry #$entry_id", 
                //     "You added $quantity $unit_name of item ID: $item_id to donation entry #$entry_id.");
            }
        }

        $stmtItem->close();
    }

    $conn->commit();

    echo json_encode(["status" => "success", "entry_id" => $entry_id]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
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
