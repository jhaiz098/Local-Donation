<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings</title>
<script src="src/tailwind.js"></script>
<link rel="stylesheet" href="src/style.css">
</head>

<body class="bg-gray-100 transition-colors duration-300">

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

<!-- Hero Section -->
<section class="bg-blue-700 text-white py-12 px-5 rounded-b-lg shadow-md mb-6">
  <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
    <div class="flex-1">
      <h1 class="text-3xl md:text-4xl font-bold mb-2">Settings</h1>
      <p class="text-lg md:text-xl text-blue-100 mb-4">
        Manage your preferences, privacy, notifications, and account options.
      </p>
    </div>

    <div class="flex-1 bg-blue-600 rounded-lg p-6 text-center hidden md:flex items-center justify-center">
      <div class="text-white font-bold text-xl">
        Settings Overview
      </div>
    </div>
  </div>
</section>

<!-- Navigation (desktop) -->
<nav class="hidden md:flex bg-white shadow-md flex py-2 px-2 justify-between">
    <div>
        <ul class="flex">
            <li><a href="dashboard.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Dashboard</a></li>
            <li><a href="my_account.php" class="block py-2 px-4 hover:bg-gray-200 rounded">My Account</a></li>
            <li><a href="profiles.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Profiles</a></li>
            <li><a href="notifications.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Activity</a></li>
            <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Feedback</a></li>
            <li><a href="settings.php" class="block py-2 px-4 bg-gray-300 rounded">Settings</a></li>
            <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
        </ul>
    </div>
    <div>
        <a href="logout.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a>
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
        <li><a href="dashboard.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Dashboard</a></li>
        <li><a href="my_account.php" class="block py-2 px-4 hover:bg-gray-200 rounded">My Account</a></li>
        <li><a href="profiles.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Profiles</a></li>
        <li><a href="notifications.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Activity</a></li>
        <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Feedback</a></li>
        <li><a href="settings.php" class="block py-2 px-4 bg-gray-300 rounded">Settings</a></li>
        <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
        <li><a href="logout.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a></li>
    </ul>
</div>

<!-- Settings Section -->
<section class="px-5 py-8 bg-white shadow-md m-4 rounded">

    <h2 class="text-2xl font-bold mb-6">Settings</h2>

    <div class="space-y-6">

        <!-- Account Settings -->
        <!-- <div class="bg-gray-50 p-4 rounded shadow">
            <h3 class="text-xl font-semibold mb-3">Account Settings</h3>

            <div class="space-y-3">
                <div>
                    <label class="block font-medium mb-1">Email</label>
                    <input type="email" class="w-full p-2 border rounded" placeholder="your@email.com">
                </div>

                <div>
                    <label class="block font-medium mb-1">Change Password</label>
                    <input type="password" class="w-full p-2 border rounded mb-2" placeholder="New password">
                    <input type="password" class="w-full p-2 border rounded" placeholder="Confirm new password">
                </div>

                <button class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Save Changes
                </button>
            </div>
        </div> -->

        <!-- Privacy -->
        <!-- <div class="bg-gray-50 p-4 rounded shadow">
            <h3 class="text-xl font-semibold mb-3">Privacy</h3>

            <div class="space-y-3">
                <label class="flex justify-between">
                    <span>Show profile publicly</span>
                    <input type="checkbox">
                </label>

                <label class="flex justify-between">
                    <span>Allow messages</span>
                    <input type="checkbox">
                </label>
            </div>
        </div> -->

        <!-- Notification Settings -->
        <!-- <div class="bg-gray-50 p-4 rounded shadow">
            <h3 class="text-xl font-semibold mb-3">Notifications</h3>

            <div class="space-y-3">
                <label class="flex justify-between">
                    <span>Email notifications</span>
                    <input type="checkbox" checked>
                </label>

                <label class="flex justify-between">
                    <span>In-app notifications</span>
                    <input type="checkbox" checked>
                </label>
            </div>
        </div> -->

        <!-- Appearance -->
        <!-- <div class="bg-gray-50 p-4 rounded shadow">
            <h3 class="text-xl font-semibold mb-3">Appearance</h3>

            <div class="space-y-3">
                <label class="block font-medium mb-1">Theme</label>
                <select id="themeSelect" class="w-full p-2 border rounded">
                    <option value="light">Light</option>
                    <option value="dark">Dark</option>
                    <option value="auto">Auto (system)</option>
                </select>
            </div>
        </div> -->

        <!-- Danger Zone -->
        <div class="bg-red-50 p-4 rounded shadow border border-red-300">
            <h3 class="text-xl font-semibold text-red-700 mb-2">Danger Zone</h3>
            <p class="text-sm text-red-600 mb-3">Deleting your account is permanent and cannot be undone.</p>

            <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-500">
                Delete My Account
            </button>
        </div>

    </div>
</section>

<script>
// MOBILE MENU
const hamburger = document.getElementById("hamburger");
const sideMenu = document.getElementById("side-menu");
const closeBtn = document.getElementById("close-btn");

hamburger.addEventListener("click", () => sideMenu.classList.remove("-translate-x-full"));
closeBtn.addEventListener("click", () => sideMenu.classList.add("-translate-x-full"));

sideMenu.querySelectorAll("a").forEach(link => {
    link.addEventListener("click", () => sideMenu.classList.add("-translate-x-full"));
});
</script>

</body>
</html>
