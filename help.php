<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Help / FAQ</title>
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
      <h1 class="text-3xl md:text-4xl font-bold mb-2">Help & FAQ</h1>
      <p class="text-lg md:text-xl text-blue-100 mb-4">
        Find answers to common questions and learn how to use our website.
      </p>
    </div>

    <div class="flex-1 bg-blue-600 rounded-lg p-6 text-center hidden md:flex items-center justify-center">
      <div class="text-white font-bold text-xl">
        Support Center
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
            <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
            <li><a href="help.php" class="block py-2 px-4 bg-gray-300 rounded">Help/FAQ</a></li>
        </ul>
    </div>
    <div>
        <a href="#" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a>
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
        <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
        <li><a href="help.php" class="block py-2 px-4 bg-gray-300 rounded">Help/FAQ</a></li>
        <li><a href="#" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a></li>
    </ul>
</div>

<!-- FAQ Section -->
<section class="px-5 py-12 bg-white shadow-md m-4 rounded max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-800 mb-10 text-center">Frequently Asked Questions</h2>

    <div class="space-y-6">
        <!-- FAQ Item -->
        <div class="rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-100 px-6 py-4">
                <p class="font-semibold text-gray-800 text-lg">Q: How do I update my account to enable profile creation?</p>
            </div>
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <p class="text-gray-700">A: Go to <strong>My Account</strong>, complete all required fields including your personal and location information. Once your account is complete, you can create a profile.</p>
            </div>
        </div>

        <div class="rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-100 px-6 py-4">
                <p class="font-semibold text-gray-800 text-lg">Q: How do I create a profile?</p>
            </div>
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <p class="text-gray-700">A: Navigate to the <strong>Profiles</strong> page and click <strong>Add Profile</strong>. Fill in the profile name, type, and other required details, then submit.</p>
            </div>
        </div>

        <div class="rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-100 px-6 py-4">
                <p class="font-semibold text-gray-800 text-lg">Q: How do I request or offer a donation?</p>
            </div>
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <p class="text-gray-700">A: Select a profile and click the <strong>Add Request / Offer</strong> button. Fill in the details about the items or assistance you want to request or offer, then submit.</p>
            </div>
        </div>

        <div class="rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-100 px-6 py-4">
                <p class="font-semibold text-gray-800 text-lg">Q: How does matching work?</p>
            </div>
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <p class="text-gray-700">A: The system matches donation offers with requests based on the target area location. When a match is found, the request or offer is listed below the corresponding donation entry.</p>
            </div>
        </div>

        <div class="rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-100 px-6 py-4">
                <p class="font-semibold text-gray-800 text-lg">Q: Can I submit feedback?</p>
            </div>
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <p class="text-gray-700">A: Yes! Go to the <strong>Feedback</strong> page and submit feedback either as your user account or as a profile you manage. Feedback helps us improve the system.</p>
            </div>
        </div>

        <div class="rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gray-100 px-6 py-4">
                <p class="font-semibold text-gray-800 text-lg">Q: How do I delete my account?</p>
            </div>
            <div class="bg-white px-6 py-4 border-t border-gray-200">
                <p class="text-gray-700">A: Go to <strong>Settings &gt; Danger Zone</strong> and click "Delete My Account". This action is permanent and cannot be undone.</p>
            </div>
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
