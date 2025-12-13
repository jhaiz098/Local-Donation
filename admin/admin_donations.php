<?php
include('../db_connect.php');

// Get the filter from the query string (default is 'all')
$filter = $_GET['filter'] ?? 'all'; 

// Fetch donations/requests based on the selected filter
switch ($filter) {
    case 'offer':
        $sql = "SELECT * FROM donation_entries WHERE entry_type = 'offer'";
        break;
    case 'request':
        $sql = "SELECT * FROM donation_entries WHERE entry_type = 'request'";
        break;
    default:
        $sql = "SELECT * FROM donation_entries"; // 'all' filter
        break;
}

$result = $conn->query($sql);

// Store the data in an array
$donation_entries = [];
if ($result->num_rows > 0) {
    while ($entry = $result->fetch_assoc()) {
        // Get the items associated with the current donation entry
        $entry_id = $entry['entry_id'];
        
        // Modify the query to join the donation_entry_items with the items table to get item names
        $item_sql = "
            SELECT dei.item_id, i.item_name, dei.unit_name, dei.quantity
            FROM donation_entry_items dei
            JOIN items i ON dei.item_id = i.item_id 
            WHERE dei.entry_id = ?
        ";
        
        $stmt = $conn->prepare($item_sql);
        $stmt->bind_param("i", $entry_id);
        $stmt->execute();
        $item_result = $stmt->get_result();

        // Determine target location based on entry type
        $target_location = $entry['entry_type'] == 'request' ? '-' : $entry['target_area'];

        // Store data into the array
        $donation_entries[] = [
            'entry_id' => $entry['entry_id'],
            'profile_id' => htmlspecialchars($entry['profile_id']),
            'entry_type' => $entry['entry_type'],
            'details' => $entry['details'],
            'target_location' => $target_location,
            'created_at' => $entry['created_at'],
            'updated_at' => $entry['updated_at'],
            'items' => [] // to store the items
        ];

        // Loop through the items and add them to the donation entry
        while ($item = $item_result->fetch_assoc()) {
            $donation_entries[count($donation_entries) - 1]['items'][] = [
                'item_name' => htmlspecialchars($item['item_name']), // Store the item name
                'unit_name' => htmlspecialchars($item['unit_name']),
                'quantity' => htmlspecialchars($item['quantity']),
            ];
        }
    }
}

// $conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Donations / Requests</title>

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
            <li><a href="admin_donations.php" class="block px-4 py-2 rounded bg-gray-300 font-semibold">Donations / Requests</a></li>

            <!-- System -->
            <li class="uppercase text-xs px-2 mt-4">System</li>
            <li><a href="admin_items.php" class="block px-4 py-2 rounded hover:bg-gray-200">Item Management</a></li>
            <li><a href="admin_locations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Location Management</a></li>
            <li><a href="admin_donation_logs.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donation Logs</a></li>
            <li><a href="admin_activities.php" class="block px-4 py-2 rounded hover:bg-gray-200">Activity</a></li>
            <li><a href="admin_audit_trails.php" class="block px-4 py-2 rounded hover:bg-gray-200">Audit Trails</a></li>
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

<!-- ================= MAIN CONTENT ================= -->
<main class="pt-24 p-6 md:ml-64">
    <h2 class="text-2xl font-bold mb-6">Donations / Requests Management</h2>

    <!-- ================= FILTER TABS ================= -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="?filter=all" class="px-4 py-2 rounded <?= $filter == 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">All</a>
        <a href="?filter=offer" class="px-4 py-2 rounded <?= $filter == 'offer' ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">Offers</a>
        <a href="?filter=request" class="px-4 py-2 rounded <?= $filter == 'request' ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">Requests</a>
    </div>


    <!-- ================= DONATION / REQUEST TABLE ================= -->
    <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <table class="w-full min-w-[900px] border-collapse text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Entry ID</th>
                    <th class="p-3">Profile ID</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Details</th>
                    <th class="p-3">Items</th> <!-- New column for Items -->
                    <th class="p-3">Target Area</th>
                    <th class="p-3">Created At</th>
                    <th class="p-3">Updated At</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if we have stored data
                if (count($donation_entries) > 0) {
                    // Loop through the stored data and output rows in the table
                    foreach ($donation_entries as $entry) {
                        echo '<tr class="border-t hover:bg-gray-50">';
                        echo '<td class="p-3">' . $entry['entry_id'] . '</td>';
                        echo '<td class="p-3">' . $entry['profile_id'] . '</td>';
                        echo '<td class="p-3"><span class="px-2 py-1 rounded ' . ($entry['entry_type'] == 'offer' ? 'bg-green-200' : 'bg-yellow-200') . ' text-xs">' . ucfirst($entry['entry_type']) . '</span></td>';
                        echo '<td class="p-3"><span class="px-2 py-1 rounded">' . ucfirst($entry['details']) . '</span></td>';
                        
                        // Items column with vertical list
                        echo '<td class="p-3">';
                        if (count($entry['items']) > 0) {
                            echo '<ul class="list-disc pl-5 space-y-1">'; // Improved style for list
                            foreach ($entry['items'] as $item) {
                                echo '<li>' . $item['item_name'] .' '. $item['quantity'] .' (' . $item['unit_name'] . ')</li>'; // Display item name
                            }
                            echo '</ul>';
                        } else {
                            echo 'No items';
                        }
                        echo '</td>';
                        
                        echo '<td class="p-3">' . $entry['target_location'] . '</td>';
                        echo '<td class="p-3">' . $entry['created_at'] . '</td>';
                        echo '<td class="p-3">' . $entry['updated_at'] . '</td>';
                        echo '<td class="p-3 text-center">';
                        echo '<div class="flex gap-1 justify-center whitespace-nowrap">';
                        echo '<button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 view-btn" onclick="openModal(this)" 
                                data-entry-id="' . $entry['entry_id'] . '" 
                                data-profile-id="' . $entry['profile_id'] . '" 
                                data-entry-type="' . $entry['entry_type'] . '" 
                                data-details="' . htmlspecialchars($entry['details'], ENT_QUOTES, 'UTF-8') . '" 
                                data-target-location="' . $entry['target_location'] . '" 
                                data-created-at="' . $entry['created_at'] . '" 
                                data-updated-at="' . $entry['updated_at'] . '" 
                                data-items=\'' . json_encode($entry['items']) . '\'>View</button>';
                        echo '<button class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600" onclick="openEditModal(this)" 
                                data-entry-id="' . $entry['entry_id'] . '" 
                                data-profile-id="' . $entry['profile_id'] . '" 
                                data-entry-type="' . $entry['entry_type'] . '" 
                                data-details="' . htmlspecialchars($entry['details'], ENT_QUOTES, 'UTF-8') . '" 
                                data-target-location="' . $entry['target_location'] . '" 
                                data-created-at="' . $entry['created_at'] . '" 
                                data-updated-at="' . $entry['updated_at'] . '" 
                                data-items=\'' . json_encode($entry['items']) . '\'>Edit</button>';


                        echo '<button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600" onclick="handleButtonClick(this, \'Delete\')" data-entry-id="' . $entry['entry_id'] . '">Delete</button>';
                        echo '</div>';
                        echo '</td>';
                        echo '</tr>';
                    }
                } else {
                    // If no entries found
                    echo '<tr><td colspan="8" class="text-center p-3">No entries found.</td></tr>';
                }
                ?>
            </tbody>
        </table>
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
    // Function to handle button click (View, Edit, Delete)
function handleButtonClick(button, action) {
    const entryId = button.getAttribute('data-entry-id');

    if (action === 'View') {
        window.location.href = "donation_view.php?entry_id=" + entryId;
    } else if (action === 'Edit') {
        openModal(button); // Open the edit modal
    } else if (action === 'Delete') {
        // Perform delete action (e.g., show a confirmation)
        alert('Delete clicked for Entry ID: ' + entryId);
    }
}
</script>

<?php include 'donation_edit.php'; ?>
<?php include 'donation_view.php'; ?>
<script>

// Function to open the modal and pass the data
function openModal(button) {
    // Get the data from the button's data attributes
    const entryId = button.getAttribute('data-entry-id');
    const profileId = button.getAttribute('data-profile-id');
    const entryType = button.getAttribute('data-entry-type');
    const details = button.getAttribute('data-details');
    const targetLocation = button.getAttribute('data-target-location');
    const createdAt = decodeURIComponent(button.getAttribute('data-created-at'));  // Decode URL-encoded date
    const updatedAt = decodeURIComponent(button.getAttribute('data-updated-at'));  // Decode URL-encoded date
    const itemsData = JSON.parse(button.getAttribute('data-items'));  // Parse the items data from JSON

    // Populate the modal with the data
    document.getElementById('modal-entry-id').innerText = entryId;
    document.getElementById('modal-profile-id').innerText = profileId;
    document.getElementById('modal-entry-type').innerText = entryType;
    document.getElementById('modal-details').innerText = details;
    document.getElementById('modal-target-location').innerText = targetLocation;
    document.getElementById('modal-created-at').innerText = createdAt;
    document.getElementById('modal-updated-at').innerText = updatedAt;

    // Display items in the modal
    const itemsList = document.getElementById('modal-items-list');
    itemsList.innerHTML = '';  // Clear existing items
    if (itemsData.length > 0) {
        itemsData.forEach(item => {
            const listItem = document.createElement('li');
            listItem.innerHTML = `${item.item_name} ${item.quantity} (${item.unit_name})`;
            itemsList.appendChild(listItem);
        });
    } else {
        itemsList.innerHTML = 'No items available.';
    }

    // Show the modal
    document.getElementById('modal').classList.remove('hidden');
}

// Function to close the modal
function closeModal() {
    document.getElementById('modal').classList.add('hidden');  // Add 'hidden' to hide the modal
}

</script>

</body>
</html>
