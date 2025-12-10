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
    <h2 class="text-2xl font-bold mb-6">Donations / Requests Management</h2>

    <!-- ================= FILTER TABS ================= -->
    <div class="flex flex-wrap gap-2 mb-6">
        <button class="px-4 py-2 rounded bg-blue-600 text-white font-semibold">All</button>
        <button class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Offers</button>
        <button class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Requests</button>
    </div>

    <!-- ================= DONATION / REQUEST TABLE ================= -->
    <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <table class="w-full min-w-[900px] border-collapse text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Entry ID</th>
                    <th class="p-3">Profile</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Details</th>
                    <th class="p-3">Target Area</th>
                    <th class="p-3">Created At</th>
                    <th class="p-3">Updated At</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sample Offer -->
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3">1</td>
                    <td class="p-3">Juan Dela Cruz</td>
                    <td class="p-3"><span class="px-2 py-1 rounded bg-green-200 text-xs">Offer</span></td>
                    <td class="p-3 truncate max-w-[200px]">500kg of rice for donation</td>
                    <td class="p-3">Tacloban City, Leyte</td>
                    <td class="p-3">2025-03-01</td>
                    <td class="p-3">2025-03-02</td>
                    <td class="p-3 text-center">
                        <div class="flex gap-1 justify-center whitespace-nowrap">
                            <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">View</button>
                            <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                        </div>
                    </td>
                </tr>

                <!-- Sample Request -->
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3">2</td>
                    <td class="p-3">Santos Family</td>
                    <td class="p-3"><span class="px-2 py-1 rounded bg-yellow-200 text-xs">Request</span></td>
                    <td class="p-3 truncate max-w-[200px]">Looking for medical supplies</td>
                    <td class="p-3">—</td>
                    <td class="p-3">2025-04-10</td>
                    <td class="p-3">2025-04-10</td>
                    <td class="p-3 text-center">
                        <div class="flex gap-1 justify-center whitespace-nowrap">
                            <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">View</button>
                            <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                        </div>
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
