<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>

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

    <h2 class="text-2xl font-bold mb-6">User Management</h2>

    <!-- ================= FILTER TABS ================= -->
    <div class="flex flex-wrap gap-2 mb-6">
        <button class="px-4 py-2 rounded bg-blue-600 text-white font-semibold">
            All Users
        </button>
        <button class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">
            Regular Users
        </button>
        <button class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">
            Administrative Users
        </button>
        <button class="px-4 py-2 rounded bg-yellow-300 hover:bg-yellow-400 text-black font-semibold">
            Pending Admins
        </button>
    </div>


    <!-- ================= USER TABLE ================= -->
    <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <table class="w-full min-w-[900px] border-collapse text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">User ID</th>
                    <th class="p-3">User</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Role</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Location</th>
                    <th class="p-3">Joined</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                <!-- Sample User -->
                <tr class="border-t hover:bg-gray-50">
                    <td class="p-3">1</td>
                    <td class="p-3 flex items-center gap-2">
                        <img src="../assets/default-avatar.png" class="w-8 h-8 rounded-full object-cover">
                        <span class="truncate max-w-[150px]">Juan M. Dela Cruz</span>
                    </td>
                    <td class="p-3 truncate max-w-[180px]">juan@email.com</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded bg-gray-200 text-xs">User</span>
                    </td>
                    <td class="p-3">09123456789</td>
                    <td class="p-3 truncate max-w-[180px]">Tacloban City, Leyte</td>
                    <td class="p-3">2025-01-12</td>
                    <td class="p-3 text-center">
                        <div class="flex gap-1 justify-center whitespace-nowrap">
                            <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">View</button>
                            <button class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</button>
                            <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Disable</button>
                        </div>
                    </td>
                </tr>

                <!-- Pending Admin Example -->
                <tr class="border-t bg-yellow-50 hover:bg-yellow-100">
                    <td class="p-3">2</td>
                    <td class="p-3 flex items-center gap-2">
                        <img src="../assets/default-avatar.png" class="w-8 h-8 rounded-full object-cover">
                        <span class="truncate max-w-[150px]">Maria Santos</span>
                    </td>
                    <td class="p-3 truncate max-w-[180px]">maria@email.com</td>
                    <td class="p-3">
                        <span class="px-2 py-1 rounded bg-yellow-200 text-xs font-semibold">Pending Admin</span>
                    </td>
                    <td class="p-3">09987654321</td>
                    <td class="p-3 truncate max-w-[180px]">Cebu City, Cebu</td>
                    <td class="p-3">2025-02-02</td>
                    <td class="p-3 text-center">
                        <div class="flex gap-1 justify-center whitespace-nowrap">
                            <button class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">Approve</button>
                            <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Reject</button>
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
