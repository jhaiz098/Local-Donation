<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overview</title>
    <script src="../src/tailwind.js"></script>
</head>
<body class="bg-gray-100 h-screen flex">
    <!-- LEFT NAVIGATION -->
    <nav class="w-64 bg-white shadow-md flex flex-col p-6">
        <div class="mb-10">
            <h1 class="text-2xl font-bold">Profile</h1>
            <p class="text-gray-500 text-sm">Dashboard Menu</p>
        </div>
        <ul class="flex flex-col gap-2 text-gray-700 font-medium">
            <li><a href="profile_dashboard.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Profile Information</a></li>
            <li><a href="profile_activity.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Activity</a></li>
            <li><a href="profile_myRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">My Requests & Offers</a></li>
            <li><a href="profile_allRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">All Requests & Offers</a></li>
            <li><a href="profile_members.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Members</a></li>
            <li><a href="profile_settings.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Settings</a></li>
        </ul>
    </nav>

    <main class="flex-1 p-6 overflow-y-auto">
        <section id="overview" class="content-section">
            <h3 class="text-2xl font-semibold mb-3">Overview</h3>
            <div class="bg-white p-5 shadow rounded mb-6">
                <h4 class="text-xl font-semibold mb-2">Profile Summary</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><span class="font-semibold">Full Name:</span> Juan Santos Dela Cruz</p>
                        <p><span class="font-semibold">Age:</span> 30</p>
                        <p><span class="font-semibold">Gender:</span> Male</p>
                    </div>
                    <div>
                        <p><span class="font-semibold">Region:</span> Region IV-A</p>
                        <p><span class="font-semibold">City/Barangay:</span> Calamba / Barangay 1</p>
                        <p><span class="font-semibold">Email:</span> juan@email.com</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
