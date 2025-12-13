<?php
include('../db_connect.php');

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch items and their corresponding units
$items_query = "
    SELECT items.item_id AS item_id, items.item_name AS item_name, GROUP_CONCAT(item_units.unit_name) AS unit_names
    FROM items
    LEFT JOIN item_units ON items.item_id = item_units.item_id
    GROUP BY items.item_id
    ORDER BY items.item_name
";
// Execute the query
$items_result = $conn->query($items_query);

// Check for query error
if (!$items_result) {
    die("Error executing query: " . $conn->error);
}

$user_id = $_SESSION['user_id'];

$roleSql = "SELECT role FROM users WHERE user_id = ?";
$roleStmt = $conn->prepare($roleSql);
$roleStmt->bind_param("i", $user_id);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
$roleRow = $roleResult->fetch_assoc();

$disabledClass = 'opacity-50 cursor-not-allowed pointer-events-none bg-gray-200';
$currentRole = $roleRow['role'] ?? 'User';

$isStaff = ($currentRole === 'Staff');
$isAdmin = ($currentRole === 'Admin');
$isSuperuser = ($currentRole === 'Superuser');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Management</title>

    <script src="../src/tailwind.js"></script>
    <link rel="stylesheet" href="../src/style.css">
</head>
<body class="bg-gray-100">

<!-- ================= HEADER ================= -->
<header class="py-4 px-5 bg-white shadow-md flex justify-between items-center fixed w-full top-0 z-20">
    <h1 class="text-2xl md:text-3xl font-bold">
        <a href="dashboard.php">Bayanihan Hub</a>
    </h1>

    <!-- Mobile Hamburger -->
    <button id="hamburger" class="block md:hidden p-2 rounded bg-gray-100 hover:bg-gray-200">
        <svg class="w-6 h-6 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</header>

<!-- ================= SIDEBAR (DESKTOP) ================= -->
<aside class="hidden md:block w-64 fixed top-16 left-0 h-[calc(100vh-4rem)] overflow-y-auto">
    <nav class="p-4">
        <ul class="space-y-1">

            <!-- Core -->
            <li class="uppercase text-xs px-2 mt-2">Core</li>
            <li>
                <a href="admin_dashboard.php" class="block px-4 py-2 rounded hover:bg-gray-200">
                    Dashboard
                </a>
            </li>

            <!-- Accounts -->
            <li class="uppercase text-xs px-2 mt-4">Accounts</li>
            <li><a href="admin_myAccount.php" class="block px-4 py-2 rounded hover:bg-gray-200">My Account</a></li>
            <li><a href="admin_users.php" class="block px-4 py-2 rounded hover:bg-gray-200">Users</a></li>
            <li><a href="admin_profiles.php" class="block px-4 py-2 rounded hover:bg-gray-200">Profiles</a></li>

            <!-- Operations -->
            <li class="uppercase text-xs px-2 mt-4">Operations</li>
            <li><a href="admin_donations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donations / Requests</a></li>

            <!-- System -->
            <li class="uppercase text-xs px-2 mt-4">System</li>
            <li><a href="admin_items.php" class="block px-4 py-2 rounded bg-gray-300 font-semibold">Item Management</a></li>
            <li><a href="admin_locations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Location Management</a></li>
            <li><a href="admin_donation_logs.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donation Logs</a></li>
            <li><a href="admin_activities.php" class="block px-4 py-2 rounded hover:bg-gray-200">Activity</a></li>
            <li class="<?= ($isStaff || $isAdmin || $incomplete) ? $disabledClass : '' ?>">
                <a href="admin_audit_trails.php" class="block px-4 py-2 rounded">Audit Trails</a>
            </li>
            <li><a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Access Level Management</a></li>

            <!-- Support -->
            <li class="uppercase text-xs px-2 mt-4">Support</li>
            <li><a href="admin_feedback.php" class="block px-4 py-2 rounded hover:bg-gray-200">Feedback</a></li>
            <li><a href="admin_help.php" class="block px-4 py-2 rounded hover:bg-gray-200">Help / FAQ</a></li>

            <!-- Logout -->
            <li class="mt-6">
                <a href="admin_logout.php" class="block px-4 py-2 rounded bg-red-600 hover:bg-red-500 text-center">
                    Logout
                </a>
            </li>

        </ul>
    </nav>
</aside>

<!-- ================= MOBILE SIDE MENU ================= -->
<div id="side-menu"
    class="fixed inset-0 bg-white z-30 transform -translate-x-full transition-transform duration-300 md:hidden pt-20 overflow-y-auto">

    <button id="close-btn" class="absolute top-4 right-4 p-2 rounded bg-gray-200 hover:bg-gray-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <ul class="flex flex-col gap-1 px-6">
        <li><a href="admin_dashboard.php" class="block px-4 py-2 rounded hover:bg-gray-200">Dashboard</a></li>
        <li><a href="admin_myAccount.php" class="block px-4 py-2 rounded hover:bg-gray-200">My Account</a></li>
        <li><a href="admin_users.php" class="block px-4 py-2 rounded hover:bg-gray-200">Users</a></li>
        <li><a href="admin_profiles.php" class="block px-4 py-2 rounded hover:bg-gray-200">Profiles</a></li>
        <li><a href="admin_donations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donations / Requests</a></li>
        <li><a href="admin_feedback.php" class="block px-4 py-2 rounded hover:bg-gray-200">Feedback</a></li>
        <li><a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Settings</a></li>
        <li><a href="admin_help.php" class="block px-4 py-2 rounded hover:bg-gray-200">Help / FAQ</a></li>
        <li><a href="admin_logout.php" class="block px-4 py-2 rounded bg-red-600 hover:bg-red-500">Logout</a></li>
    </ul>
</div>


<main class="pt-24 p-6 md:ml-64">
    <div class="flex justify-between mb-4">
        <h2 class="text-xl font-semibold">Manage Items</h2>
    </div>

    <!-- ================= ADD ITEM AND UNIT FORMS ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <!-- Add Item -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Item</h3>
            <form id="add-item-form" class="flex items-center gap-4">
                <input type="text" id="item-name" placeholder="Item Name" class="p-2 text-sm border rounded flex-1" required>
                <button type="button" id="add-item-btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add Item</button>
            </form>
        </div>

        <!-- Add Unit Section -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Unit</h3>
            <form id="add-unit-form" class="flex items-center gap-4">
                <!-- Select Item -->
                <div class="relative flex-1">
                    <select id="select-item" class="p-2 text-sm border rounded w-full overflow-auto" required>
                        <option value="">Select Item</option>
                        <!-- Items will be populated here -->
                        <?php
                        // Rewind the result set (to allow the items to be fetched again for the select dropdown)
                        $items_result->data_seek(0); // This rewinds the result set so that we can loop through it again

                        // Fetch items and populate the select options
                        if ($items_result->num_rows > 0) {
                            while ($item = $items_result->fetch_assoc()) {
                                echo '<option value="' . $item['item_id'] . '">' . htmlspecialchars($item['item_name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Unit Name -->
                <input type="text" id="unit-name" placeholder="Unit Name" class="p-2 text-sm border rounded flex-1" required>

                <!-- Add Unit Button -->
                <button type="button" id="add-unit-btn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">Add Unit</button>
            </form>
        </div>

    </div>

    <!-- ================= EXISTING ITEMS TABLE ================= -->
    <h2 class="text-2xl font-bold mb-6">Existing Items</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Items Section -->
        <div class="bg-white rounded-lg shadow p-4 h-[500px] overflow-y-auto">
            <h3 class="font-bold mb-2 text-lg">Items</h3>
            <ul id="items" class="space-y-2">
                <?php
                // Rewind the result set before displaying the items again
                $items_result->data_seek(0); // Rewind to the start

                while ($item = $items_result->fetch_assoc()):
                    // Split the comma-separated unit names and remove any empty or extra spaces
                    $unit_names = explode(',', $item['unit_names']); 
                    $unit_names = array_map('trim', $unit_names); // Trim each unit name
                    $unit_names = array_filter($unit_names, fn($unit) => !empty($unit)); // Filter out empty units

                    // Prepare a simple string of unit names to pass as a data attribute (just unit names, no HTML)
                    $units_list = implode(',', $unit_names); 
                ?>
                    <li class="flex justify-between items-center p-2 rounded cursor-pointer hover:bg-blue-100 transition" data-item-id="<?= $item['item_id']; ?>" data-units="<?= htmlspecialchars($units_list); ?>">
                        <span><?= $item['item_name']; ?></span>
                        <div class="flex gap-1">
                            <button class="px-2 py-0.5 bg-yellow-500 text-white rounded text-xs" onclick="editItem('<?= $item['item_id']; ?>', 'item', '<?= htmlspecialchars($item['item_name']); ?>')">Edit</button>
                            <button class="px-2 py-0.5 bg-red-500 text-white rounded text-xs" onclick="deleteItem('<?= $item['item_id']; ?>', 'item')">Delete</button>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>

        <!-- Units Section -->
        <div class="bg-white rounded-lg shadow p-4 h-[500px] overflow-y-auto">
            <h3 class="font-bold mb-2 text-lg">Units</h3>
            <ul id="units" class="space-y-2">
                <li class="text-xs cursor-pointer text-gray-500">Select an Item</li> <!-- Placeholder for Units -->
            </ul>
        </div>

    </div>


</main>


<!-- ================= JS ================= -->
<script>
    const hamburger = document.getElementById('hamburger');
    const sideMenu = document.getElementById('side-menu');
    const closeBtn = document.getElementById('close-btn');

    hamburger.addEventListener('click', () => {
        sideMenu.classList.remove('-translate-x-full');
    });

    closeBtn.addEventListener('click', () => {
        sideMenu.classList.add('-translate-x-full');
    });
</script>

<script>
    // Function to handle item click and display associated units
    function showUnits(itemId) {
        const item = document.querySelector(`#items li[data-item-id='${itemId}']`);
        const units = item.getAttribute('data-units'); // Get the unit names from the data attribute

        const unitsList = document.getElementById('units');
        
        // Clear the current list
        unitsList.innerHTML = '';

        if (units && units.trim() !== '') {
            // Split the unit names and trim each one
            const unitArray = units.split(',').map(unit => unit.trim()).filter(unit => unit !== '');

            // Display each unit with its respective buttons
            unitArray.forEach(unit => {
                const listItem = document.createElement('li');
                listItem.classList.add('flex', 'justify-between', 'items-center'); // Add flex classes for layout

                // Create the unit name span
                const unitSpan = document.createElement('span');
                unitSpan.textContent = unit;

                // Create the Edit and Delete buttons
                const buttonDiv = document.createElement('div');
                buttonDiv.classList.add('flex', 'gap-1');

                const editButton = document.createElement('button');
                editButton.classList.add('px-2', 'py-0.5', 'bg-yellow-500', 'text-white', 'rounded', 'text-xs');
                editButton.textContent = 'Edit';
                editButton.onclick = function() {
                    editUnit(unit, itemId); // Call the editUnit function
                };

                const deleteButton = document.createElement('button');
                deleteButton.classList.add('px-2', 'py-0.5', 'bg-red-500', 'text-white', 'rounded', 'text-xs');
                deleteButton.textContent = 'Delete';
                deleteButton.onclick = function() {
                    deleteUnit(unit, itemId); // Call the deleteUnit function
                };

                // Append buttons to the button div
                buttonDiv.appendChild(editButton);
                buttonDiv.appendChild(deleteButton);

                // Append the unit name and button div to the list item
                listItem.appendChild(unitSpan);
                listItem.appendChild(buttonDiv);

                // Add the list item to the units list
                unitsList.appendChild(listItem);
            });
        } else {
            // If no units are available, show a placeholder
            const noUnitsItem = document.createElement('li');
            noUnitsItem.textContent = 'No units available for this item.';
            noUnitsItem.classList.add('text-xs', 'text-gray-500');
            unitsList.appendChild(noUnitsItem);
        }
    }




    // Function to edit an item
    function editItem(itemId, type, currentItemName) {
        // Validate item ID and type
        if (itemId && type) {
            // Prompt the user for the new item name with the default value set to the current item name
            const newItemName = prompt("Enter the new name for item " + currentItemName +":", currentItemName);

            // Validate the input (ensure it's not empty)
            if (newItemName && newItemName.trim() !== "") {
                // Prepare data for sending
                const data = {
                    action: 'edit',
                    item_id: itemId,
                    type: type,
                    new_item_name: newItemName
                };

                // Send data to edit_item.php using AJAX
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "edit_item.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert(`Item ${currentItemName} has been edited.`);
                        window.location.reload();
                        console.log(xhr.responseText); // For debugging response
                    }
                };
                xhr.send("action=" + data.action + "&item_id=" + data.item_id + "&type=" + data.type + "&new_item_name=" + encodeURIComponent(data.new_item_name));
            } else if(newItemName!=null) {
                alert('Please enter a valid name for the item.');
            }
        } else {
            alert('Invalid item or type.');
        }
    }



    // Function to delete an item
    function deleteItem(itemId, type) {
        // Validate item ID (simple validation)
        if (itemId && type) {
            // Confirm before deletion
            const confirmDelete = confirm(`Are you sure you want to delete this ${type}?`);
            if (confirmDelete) {
                // Prepare data for sending
                const data = {
                    action: 'delete',
                    item_id: itemId,
                    type: type
                };

                // Send data to delete_item.php using AJAX
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_item.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert(`${type} has been deleted.`);
                        window.location.reload();
                        console.log(xhr.responseText); // For debugging response
                    }
                };
                xhr.send("action=" + data.action + "&item_id=" + data.item_id + "&type=" + data.type);
            }
        } else {
            alert('Invalid item or type.');
        }
    }

    // Function to edit a unit
    function editUnit(unit, itemId) {
        // Prompt the user for the new unit name
        const newUnitName = prompt("Enter the new name for unit: " + unit, unit);

        // If the user entered a valid new name (not empty or null)
        if (newUnitName && newUnitName.trim() !== "") {
            // Prepare data for sending
            const data = {
                action: 'edit',
                item_id: itemId,
                old_unit_name: unit,
                new_unit_name: newUnitName.trim()  // Remove leading/trailing spaces
            };

            // Send the data to edit_unit.php using AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "edit_unit.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText); // Parse the JSON response

                    if (response.status === "success") {
                        alert(`Unit "${unit}" has been updated to "${newUnitName}".`);
                        window.location.reload(); // Reload to see changes
                    } else {
                        alert("Error: " + response.message);
                    }
                }
            };
            xhr.send("action=" + data.action + "&item_id=" + data.item_id + "&old_unit_name=" + encodeURIComponent(data.old_unit_name) + "&new_unit_name=" + encodeURIComponent(data.new_unit_name));
        } else if (newUnitName !== null) {
            alert('Please enter a valid unit name.');
        }
    }


    // Function to delete a unit
    function deleteUnit(unit, itemId) {
        // Confirm before deleting
        const confirmDelete = confirm(`Are you sure you want to delete the unit: "${unit}"?`);

        if (confirmDelete) {
            // Prepare data for sending
            const data = {
                action: 'delete',
                item_id: itemId,
                unit_name: unit
            };

            // Send the data to delete_unit.php using AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "delete_unit.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText); // Parse the JSON response

                    if (response.status === "success") {
                        alert(`Unit "${unit}" has been deleted.`);
                        window.location.reload(); // Reload to reflect changes
                    } else {
                        alert("Error: " + response.message);
                    }
                }
            };
            xhr.send("action=" + data.action + "&item_id=" + data.item_id + "&unit_name=" + encodeURIComponent(data.unit_name));
        }
    }


    // Adding click event listener to each item in the list
    document.querySelectorAll('#items li').forEach(item => {
        item.addEventListener('click', function() {
            const itemId = this.getAttribute('data-item-id');
            showUnits(itemId);
        });
    });

    // Add event listener to the "Add Item" button
    document.getElementById('add-item-btn').addEventListener('click', function() {
        // Get the item name from the input field
        const itemName = document.getElementById('item-name').value.trim();

        // Validate if the item name is not empty
        if (itemName === "") {
            alert("Item name cannot be empty.");
            return;
        }

        // Prepare data for sending to the server
        const data = {
            action: 'add',
            item_name: itemName
        };

        // Send data to add_item.php using AJAX
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "add_item.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText); // Parse the JSON response
                
                if (response.status === "success") {
                    alert("Item has been added successfully.");
                    // Optionally reload the page or update the item list
                    window.location.reload(); // Reloading the page to show the new item
                } else {
                    alert("Error: " + response.message);
                }
            }
        };
        xhr.send("action=" + data.action + "&item_name=" + encodeURIComponent(data.item_name));
    });

    // Function to handle adding a unit
    document.getElementById('add-unit-btn').addEventListener('click', function() {
        // Get the selected item ID from the dropdown
        const itemId = document.getElementById('select-item').value;
        
        // Get the unit name from the input field
        const unitName = document.getElementById('unit-name').value.trim();
        
        // Validate that an item is selected and unit name is not empty
        if (itemId === "" || unitName === "") {
            alert("Please select an item and provide a unit name.");
            return;
        }
        
        // Get the item name corresponding to the selected item
        const itemName = document.querySelector(`#select-item option[value='${itemId}']`).textContent;
        
        // Prepare data for sending to add_unit.php
        const data = {
            action: 'add',
            item_id: itemId,
            item_name: itemName,
            unit_name: unitName
        };

        // Create a new XMLHttpRequest object for AJAX
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "add_unit.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Handle the response after the request is sent
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText); // Parse the JSON response
                
                if (response.status === "success") {
                    alert("Unit has been added successfully.");
                    window.location.reload(); // Reload the page to show the new unit
                } else {
                    alert("Error: " + response.message);
                }
            }
        };

        // Send the data via POST request
        xhr.send(
            "action=" + data.action + 
            "&item_id=" + data.item_id + 
            "&item_name=" + encodeURIComponent(data.item_name) + 
            "&unit_name=" + encodeURIComponent(data.unit_name)
        );
    });
</script>

</body>
</html>
