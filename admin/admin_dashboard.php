<?php
require '../db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

// Count all users by role
$sql = "SELECT 
            COUNT(*) AS total_users,
            SUM(role='User') AS user_count,
            SUM(role='Staff') AS staff_count,
            SUM(role='Admin') AS admin_count,
            SUM(role='Superuser') AS superuser_count
        FROM users";
$result = $conn->query($sql);
$counts = $result->fetch_assoc();

// Count pending admin requests
$pending_requests_sql = "SELECT COUNT(*) AS total_pending FROM pending_admins";
$pending_result = $conn->query($pending_requests_sql);
$pending = $pending_result->fetch_assoc();
$pending_requests = $pending['total_pending'];

// Count feedback received
$feedback_sql = "SELECT COUNT(*) AS total_feedback FROM feedback";
$feedback_result = $conn->query($feedback_sql);
$feedback = $feedback_result->fetch_assoc();
$feedback_received = $feedback['total_feedback'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Tailwind -->
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

<!-- ================= MAIN CONTENT ================= -->
<main class="pt-24 p-6 md:ml-64">
    
    <?php 
    // if ($isAdmin && !$adminAccountComplete): 
    ?>
        <div class="mb-6 bg-red-100 border-l-4 border-red-600 p-4 rounded">
            <p class="text-red-700 font-medium">⚠ Admin account setup incomplete.</p>
            <p class="text-red-700 text-sm">
                Please complete your admin profile and required details to access all admin features.
            </p>
            <a href="admin_account.php" class="text-red-800 font-semibold underline mt-2 inline-block">
                Complete Setup
            </a>
        </div>
    <?php 
    // endif; 
    ?>

    <?php 
    // if ($isAdmin && !$hasAllPermissions): 
    ?>
        <div class="mb-6 bg-blue-100 border-l-4 border-blue-600 p-4 rounded">
            <p class="text-blue-700 font-medium">ℹ Limited admin access detected.</p>
            <p class="text-blue-700 text-sm">
                Some admin permissions are missing. Contact the system administrator if this is unexpected.
            </p>
        </div>
    <?php 
    // endif;
    ?>



    <h2 class="text-2xl font-bold mb-6">Admin Dashboard</h2>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-semibold mb-2">Total Users (All Roles)</h3>
            <p class="text-3xl font-bold text-blue-600"><?= $counts['total_users'] ?></p><br>
            <p class="text-sm text-gray-500">
                Users: <?= $counts['user_count'] ?> | Staff: <?= $counts['staff_count'] ?> | Admin: <?= $counts['admin_count'] ?> | Superusers: <?= $counts['superuser_count'] ?>
            </p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-semibold mb-2">Pending Requests</h3>
            <p class="text-3xl font-bold text-yellow-600"><?= $pending_requests ?></p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-semibold mb-2">Feedback Received</h3>
            <p class="text-3xl font-bold text-green-600"><?= $feedback_received ?></p>
        </div>
    </div>


    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="admin_users.php" class="block bg-blue-100 p-6 rounded-xl shadow-md hover:bg-blue-200 transition">
            <h4 class="font-semibold text-lg mb-1">Manage Users</h4>
            <p>View and control user accounts.</p>
        </a>

        <a href="admin_donations.php" class="block bg-green-100 p-6 rounded-xl shadow-md hover:bg-green-200 transition">
            <h4 class="font-semibold text-lg mb-1">Donations</h4>
            <p>Monitor donation activity.</p>
        </a>

        <a href="admin_feedback.php" class="block bg-yellow-100 p-6 rounded-xl shadow-md hover:bg-yellow-200 transition">
            <h4 class="font-semibold text-lg mb-1">Feedback</h4>
            <p>Check user feedback.</p>
        </a>
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