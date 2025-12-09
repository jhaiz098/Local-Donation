<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<script src="../src/tailwind.js"></script>
<link rel="stylesheet" href="../src/style.css">
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <header class="py-4 px-5 bg-white shadow-md flex justify-between items-center">
        <h1 class="text-3xl md:text-4xl font-bold">
            <a href="dashboard.php">Bayanihan Hub</a>
        </h1>
        <button id="hamburger" class="block md:hidden p-2 rounded bg-gray-200 hover:bg-gray-300 z-20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </header>

    <!-- Navigation (desktop) -->
    <nav class="hidden md:flex bg-white shadow-md flex py-2 px-2 justify-between">
        <div>
            <ul class="flex">
                <li><a href="dashboard.php" class="block py-2 px-4 bg-gray-300 rounded">Dashboard</a></li>
                <li><a href="my_account.php" class="block py-2 px-4 hover:bg-gray-200 rounded">My Account</a></li>
                <li><a href="profiles.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Users</a></li>
                <li><a href="profiles.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Admins</a></li>
                <li><a href="profiles.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Profiles</a></li>
                <li><a href="notifications.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Activity</a></li>
                <li><a href="notifications.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Audit Trails</a></li>
                <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Feedbacks</a></li>
                <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Donations / Requests</a></li>
                <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
                <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
            </ul>
        </div>
        <div>
            <a href="admin_logout.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a>
        </div>

    </nav>

    <!-- Side Menu (mobile) -->
    <div id="side-menu" class="fixed inset-0 w-full h-full bg-white z-30 transform -translate-x-full transition-transform duration-300 md:hidden flex flex-col pt-4">
        <button id="close-btn" class="self-end m-4 p-2 rounded bg-gray-200 hover:bg-gray-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <ul class="flex flex-col gap-4 px-6 mt-4">
            <li><a href="dashboard.php" class="block py-2 px-4 bg-gray-300 rounded">Dashboard</a></li>
            <li><a href="my_account.php" class="block py-2 px-4 hover:bg-gray-200 rounded">My Account</a></li>
            <li><a href="my_account.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Users</a></li>
            <li><a href="my_account.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Admins</a></li>
            <li><a href="profiles.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Profiles</a></li>
            <li><a href="notifications.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Activity</a></li>
            <li><a href="notifications.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Audit Trails</a></li>
            <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Feedbacks</a></li>
            <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Donations / Requests</a></li>
            <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
            <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
            <li><a href="admin_logout.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <main class="p-6">
        <h2 class="text-2xl font-bold mb-6">Admin Dashboard</h2>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2">Total Users</h3>
                <p class="text-3xl font-bold text-blue-600">150</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2">Pending Approvals</h3>
                <p class="text-3xl font-bold text-yellow-600">12</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md">
                <h3 class="text-lg font-semibold mb-2">Recent Feedback</h3>
                <p class="text-3xl font-bold text-green-600">8</p>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="profiles.php" class="block bg-blue-100 p-6 rounded-xl shadow-md hover:bg-blue-200 transition">
                <h4 class="font-semibold text-lg mb-1">Manage Users</h4>
                <p>View, edit, or approve users.</p>
            </a>
            <a href="feedback.php" class="block bg-green-100 p-6 rounded-xl shadow-md hover:bg-green-200 transition">
                <h4 class="font-semibold text-lg mb-1">View Feedback</h4>
                <p>Check all submitted feedback.</p>
            </a>
            <a href="donations.php" class="block bg-yellow-100 p-6 rounded-xl shadow-md hover:bg-yellow-200 transition">
                <h4 class="font-semibold text-lg mb-1">Donations / Requests</h4>
                <p>Monitor donation activity.</p>
            </a>
        </div>
    </main>


</body>
</html>