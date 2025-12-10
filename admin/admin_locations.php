<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Management</title>

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

            <!-- Operations -->
            <li class="uppercase text-xs px-2 mt-4">Operations</li>
            <li><a href="admin_profiles.php" class="block px-4 py-2 rounded hover:bg-gray-200">Profiles</a></li>
            <li><a href="admin_donations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donations / Requests</a></li>
            <li><a href="admin_feedback.php" class="block px-4 py-2 rounded hover:bg-gray-200">Feedback</a></li>

            <!-- System -->
            <li class="uppercase text-xs px-2 mt-4">System</li>
            <li><a href="admin_locations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Location Management</a></li>
            <li><a href="admin_activities.php" class="block px-4 py-2 rounded hover:bg-gray-200">Activity</a></li>
            <li><a href="admin_audit_trails.php" class="block px-4 py-2 rounded hover:bg-gray-200">Audit Trails</a></li>
            <li><a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Settings</a></li>

            <!-- Support -->
            <li class="uppercase text-xs px-2 mt-4">Support</li>
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

    <h2 class="text-2xl font-bold mb-6">Location Management</h2>

    <!-- ================= ADD LOCATION FORMS ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <!-- Add Region -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Region</h3>
            <form class="flex gap-2">
                <input type="text" placeholder="Region Name" class="flex-1 p-1 text-sm border rounded">
                <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

        <!-- Add Province -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Province</h3>
            <form class="flex gap-2">
                <select class="p-1 text-sm border rounded flex-1">
                    <option>Select Region</option>
                    <option>Region 1</option>
                </select>
                <input type="text" placeholder="Province Name" class="flex-1 p-1 text-sm border rounded">
                <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

        <!-- Add City -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add City / Municipality</h3>
            <form class="flex gap-2">
                <select class="p-1 text-sm border rounded flex-1">
                    <option>Select Province</option>
                    <option>Province A</option>
                </select>
                <input type="text" placeholder="City Name" class="flex-1 p-1 text-sm border rounded">
                <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

        <!-- Add Barangay -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Barangay</h3>
            <form class="flex gap-2">
                <select class="p-1 text-sm border rounded flex-1">
                    <option>Select City / Municipality</option>
                    <option>City X</option>
                </select>
                <input type="text" placeholder="Barangay Name" class="flex-1 p-1 text-sm border rounded">
                <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

    </div>

    <!-- ================= EXISTING LOCATIONS TABLE ================= -->
    <div class="bg-white rounded-xl shadow-md p-4 overflow-x-auto">
        <h3 class="text-lg font-semibold mb-3">Existing Locations</h3>
        <table class="w-full text-xs border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-1 text-left">Region</th>
                    <th class="p-1 text-left">Province</th>
                    <th class="p-1 text-left">City / Municipality</th>
                    <th class="p-1 text-left">Barangay</th>
                    <th class="p-1 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <!-- Example Row -->
                <tr>
                    <td class="p-1 font-semibold">Region 1</td>
                    <td class="p-1 font-medium">Province A</td>
                    <td class="p-1">City X</td>
                    <td class="p-1">
                        Barangay 1, Barangay 2, Barangay 3
                    </td>
                    <td class="p-1 text-center space-x-1">
                        <button class="px-2 py-0.5 bg-yellow-500 text-white rounded text-[10px]">Edit</button>
                        <button class="px-2 py-0.5 bg-red-500 text-white rounded text-[10px]">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td class="p-1 font-semibold">Region 1</td>
                    <td class="p-1 font-medium">Province B</td>
                    <td class="p-1">City Y</td>
                    <td class="p-1">Barangay 4</td>
                    <td class="p-1 text-center space-x-1">
                        <button class="px-2 py-0.5 bg-yellow-500 text-white rounded text-[10px]">Edit</button>
                        <button class="px-2 py-0.5 bg-red-500 text-white rounded text-[10px]">Delete</button>
                    </td>
                </tr>
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

</body>
</html>
