<?php
include('../db_connect.php');

// Fetch regions
$regions_query = "SELECT * FROM regions ORDER BY name";
$regions_result = $conn->query($regions_query);

// Fetch provinces
$provinces_query = "SELECT * FROM provinces ORDER BY name";
$provinces_result = $conn->query($provinces_query);

// Fetch cities
$cities_query = "SELECT * FROM cities ORDER BY name";
$cities_result = $conn->query($cities_query);

// Fetch barangays
$barangays_query = "SELECT * FROM barangays ORDER BY name";
$barangays_result = $conn->query($barangays_query);

// Prepare data for JavaScript
$regions_data = [];
$provinces_data = [];
$cities_data = [];
$barangays_data = [];

while ($region = $regions_result->fetch_assoc()) {
    $regions_data[] = $region;
}

while ($province = $provinces_result->fetch_assoc()) {
    $provinces_data[] = $province;
}

while ($city = $cities_result->fetch_assoc()) {
    $cities_data[] = $city;
}

while ($barangay = $barangays_result->fetch_assoc()) {
    $barangays_data[] = $barangay;
}
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
                <a href="admin_dashboard.php" class="block px-4 py-2 rounded bg-gray-300 font-semibold">
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
            <li><a href="admin_items.php" class="block px-4 py-2 rounded hover:bg-gray-200">Item Management</a></li>
            <li><a href="admin_locations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Location Management</a></li>
            <li><a href="admin_donation_logs.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donation Logs</a></li>
            <li><a href="admin_activities.php" class="block px-4 py-2 rounded hover:bg-gray-200">Activity</a></li>
            <li><a href="admin_audit_trails.php" class="block px-4 py-2 rounded hover:bg-gray-200">Audit Trails</a></li>
            <li><a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Settings</a></li>

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
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" id="add-item-btn">Add New Item</button>
        </div>

        <!-- ================= ADD ITEM AND UNIT FORMS ================= -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            <!-- Add Item -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Item</h3>
            <form id="add-item-form" class="flex items-center gap-4">
                <input type="text" id="item-name" placeholder="Item Name" class="p-2 text-sm border rounded flex-1" required>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add Item</button>
            </form>
        </div>

        <!-- Add Unit -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Unit</h3>
            <form id="add-unit-form" class="flex items-center gap-4">
                <!-- Select Item -->
                <div class="relative flex-1">
                    <select id="select-item" class="p-2 text-sm border rounded w-full overflow-auto" required>
                        <option value="">Select Item</option>
                        <!-- Items will be populated here -->
                    </select>
                </div>

                <!-- Unit Name -->
                <input type="text" id="unit-name" placeholder="Unit Name" class="p-2 text-sm border rounded flex-1" required>

                <!-- Add Unit Button -->
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">Add Unit</button>
            </form>
        </div>
    </div>

    <!-- ================= EXISTING ITEMS TABLE ================= -->
    <h2 class="text-2xl font-bold mb-6">Existing Items</h2>

    <div class="grid grid-cols-2 gap-4">

        <!-- Items Section -->
        <div class="bg-white rounded-lg shadow p-4 h-[500px] overflow-y-auto">
            <h3 class="font-bold mb-2 text-lg">Items</h3>
            <ul id="items" class="space-y-2">
                <?php 
                // while($item = $items_result->fetch_assoc()): 
                ?>
                    <li class="flex justify-between items-center p-2 rounded cursor-pointer hover:bg-blue-100 transition">
                        <!-- <span onclick="selectItem('<?= $item['id']; ?>')"><?= $item['name']; ?></span> -->
                        <div class="flex gap-1">
                            <button class="px-2 py-0.5 bg-yellow-500 text-white rounded text-xs" onclick="editItem('<?= $item['id']; ?>', 'item')">Edit</button>
                            <button class="px-2 py-0.5 bg-red-500 text-white rounded text-xs" onclick="deleteItem('<?= $item['id']; ?>', 'item')">Delete</button>
                        </div>
                    </li>
                <?php
                // endwhile; 
                ?>
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

</body>
</html>
