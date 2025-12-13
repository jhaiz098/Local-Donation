<?php
include('../db_connect.php');

// Handle role change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    // Prevent changing the only superuser
    $superusers = $conn->query("SELECT user_id FROM users WHERE role='Superuser'")->fetch_all(MYSQLI_ASSOC);
    if (count($superusers) == 1 && $user_id == $superusers[0]['user_id'] && $new_role != 'Superuser') {
        echo "<script>alert('The only Superuser cannot have their role changed.');</script>";
    } else {
        $conn->query("UPDATE users SET role='{$new_role}' WHERE user_id={$user_id}");
        // Redirect to avoid resubmitting POST
        header("Location: admin_settings.php?role_changed=1");
        exit;
    }
}

// Fetch users
$users = $conn->query("SELECT user_id, CONCAT(first_name,' ',middle_name,' ',last_name) AS name, email, role FROM users WHERE role!='User'")->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>



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
            <li><a href="admin_items.php" class="block px-4 py-2 rounded hover:bg-gray-200">Item Management</a></li>
            <li><a href="admin_locations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Location Management</a></li>
            <li><a href="admin_donation_logs.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donation Logs</a></li>
            <li><a href="admin_activities.php" class="block px-4 py-2 rounded hover:bg-gray-200">Activity</a></li>
            <li><a href="admin_audit_trails.php" class="block px-4 py-2 rounded hover:bg-gray-200">Audit Trails</a></li>
            <li><a href="admin_settings.php" class="block px-4 py-2 rounded bg-gray-300 font-semibold">Access Level Management</a></li>

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

    <h2 class="text-2xl font-bold mb-6">Access Level Management</h2>

    <!-- ================= PERMISSIONS OVERVIEW ================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        <!-- Staff -->
<div class="bg-white rounded-xl shadow p-5 border">
    <h3 class="text-lg font-semibold text-blue-700 mb-2">Staff</h3>
    <ul class="text-sm text-gray-700 space-y-1 list-disc ml-4">
        <li>View users and profiles</li>
        <li>View donations, requests, and feedback</li>
        <li class="text-gray-400">No access to system settings</li>
    </ul>
</div>

<!-- Admin -->
<div class="bg-white rounded-xl shadow p-5 border">
    <h3 class="text-lg font-semibold text-yellow-700 mb-2">Admin</h3>
    <ul class="text-sm text-gray-700 space-y-1 list-disc ml-4">
        <li>Manage users and profiles</li>
        <li>Manage donations and feedback</li>
        <li>Access system modules</li>
        <li>Assign roles to Staff and Admin</li>
        <li class="text-gray-400">Cannot modify Superusers</li>
    </ul>
</div>

<!-- Superuser -->
<div class="bg-white rounded-xl shadow p-5 border">
    <h3 class="text-lg font-semibold text-red-700 mb-2">Superuser</h3>
    <ul class="text-sm text-gray-700 space-y-1 list-disc ml-4">
        <li>Full system control</li>
        <li>Manage all users and roles</li>
        <li>Access audit logs</li>
    </ul>
</div>


    </div>


    <!-- ================= USERS TABLE ================= -->
    <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">User ID</th>
                    <th class="p-3 text-left">Name</th>
                    <th class="p-3 text-left">Email</th>
                    <th class="p-3 text-left">Current Role</th>
                    <th class="p-3 text-center">Change Role</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through users array and display each user
                foreach ($users as $user) {
                    $user_id = htmlspecialchars($user['user_id']);
                    $name = htmlspecialchars($user['name']);
                    $email = htmlspecialchars($user['email']);
                    $role = htmlspecialchars($user['role']);
                    $role_class = ($role == 'Admin') ? 'text-yellow-700' : (($role == 'Superuser') ? 'text-red-700' : 'text-blue-700');

                    echo "<tr class='border-t hover:bg-gray-50'>";
                    echo "<td class='p-3 font-medium'>{$user_id}</td>";
                    echo "<td class='p-3'>{$name}</td>";
                    echo "<td class='p-3'>{$email}</td>";
                    echo "<td class='p-3 {$role_class} font-semibold'>{$role}</td>";
                    
                    // Form for changing role
                    echo "<td class='p-3 text-center'>
                            <form method='POST' action='admin_settings.php'>
                                <select name='role' class='border rounded p-1 text-sm' onchange='this.form.submit()'>
                                    <option " . ($role == 'Staff' ? 'selected' : '') . ">Staff</option>
                                    <option " . ($role == 'Admin' ? 'selected' : '') . ">Admin</option>
                                    <option " . ($role == 'Superuser' ? 'selected' : '') . ">Superuser</option>
                                </select>
                                <input type='hidden' name='user_id' value='{$user_id}' />
                            </form>
                        </td>";
                    echo "</tr>";
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
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('role_changed') === '1') {
        alert("Role changed successfully!");
        // Remove the query parameter to prevent alert on reload
        window.history.replaceState({}, document.title, "admin_settings.php");
    }
</script>



</body>
</html>
