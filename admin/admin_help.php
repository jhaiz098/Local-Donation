<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>

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

    <h2 class="text-2xl font-bold mb-6">Help & Support</h2>

    <!-- ================= QUICK HELP CARDS ================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">

        <div class="bg-white rounded-xl shadow p-5 border">
            <h3 class="text-lg font-semibold mb-2">User Management</h3>
            <p class="text-sm text-gray-700">
                Manage user accounts, update roles, and monitor account activity. 
                Staff have limited access, while Admins and Superusers can assign roles.
            </p>
        </div>

        <div class="bg-white rounded-xl shadow p-5 border">
            <h3 class="text-lg font-semibold mb-2">Profiles & Donations</h3>
            <p class="text-sm text-gray-700">
                View and manage individual, family, community, and organization profiles.
                Monitor donation offers and requests submitted by profiles.
            </p>
        </div>

        <div class="bg-white rounded-xl shadow p-5 border">
            <h3 class="text-lg font-semibold mb-2">System Activity</h3>
            <p class="text-sm text-gray-700">
                Activities and audit trails record system events such as logins,
                profile creation, and donation updates for accountability.
            </p>
        </div>

    </div>

    <!-- ================= FREQUENTLY ASKED QUESTIONS ================= -->
    <div class="bg-white rounded-xl shadow p-6 mb-10">
        <h3 class="text-xl font-semibold mb-4">Frequently Asked Questions</h3>

        <div class="space-y-4 text-sm text-gray-700">

            <div>
                <h4 class="font-semibold">Who can change user access levels?</h4>
                <p>
                    Only Admins and Superusers can change access levels.
                    Superuser accounts are protected to prevent accidental lockout.
                </p>
            </div>

            <div>
                <h4 class="font-semibold">Why can’t audit trails be edited or deleted?</h4>
                <p>
                    Audit trails are permanent system records used for security,
                    accountability, and troubleshooting purposes.
                </p>
            </div>

            <div>
                <h4 class="font-semibold">What happens if a Superuser is downgraded?</h4>
                <p>
                    Downgrading a Superuser removes their ability to manage system-level
                    permissions. The system must always retain at least one Superuser.
                </p>
            </div>

            <div>
                <h4 class="font-semibold">Why can’t I see some system settings?</h4>
                <p>
                    Certain settings are restricted based on your assigned role.
                    Contact a Superuser if you believe you need additional access.
                </p>
            </div>

        </div>
    </div>

    <!-- ================= CONTACT / SUPPORT ================= -->
    <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-600">
        <h3 class="text-lg font-semibold mb-2">Need Further Assistance?</h3>
        <p class="text-sm text-gray-700 mb-3">
            If you encounter system issues or need clarification on permissions,
            please contact the system administrator or development team.
        </p>
        <p class="text-sm text-gray-600">
            Email: <span class="font-medium">support@bayanihanhub.local</span><br>
            Response time: 1–2 business days
        </p>
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
