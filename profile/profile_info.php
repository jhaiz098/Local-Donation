    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Profile Information</title>
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
                <li><a href="profile_dashboard.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Overview</a></li>
                <li><a href="profile_info.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Profile Information</a></li>
                <li><a href="profile_activity.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Activity</a></li>
                <li><a href="profile_myRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">My Requests & Offers</a></li>
                <li><a href="profile_allRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">All Requests & Offers</a></li>
                <li><a href="profile_members.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Members</a></li>
                <li><a href="profile_settings.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Settings</a></li>
            </ul>
        </nav>

        <main class="flex-1 p-6 overflow-y-auto">
            <!-- PROFILE HEADER -->
            <div class="flex items-center bg-white shadow p-4 rounded mb-6">
                <img src="../images/profile_pic_placeholder1.png"
                    class="w-24 h-24 rounded-full border mr-4"
                    alt="Profile Picture">
                <div>
                    <h2 class="text-3xl font-bold">Juan Dela Cruz</h2>
                    <p class="text-gray-700">Profile Type: Individual</p>
                    <p class="text-gray-500 text-sm">Profile ID: #001</p>
                </div>
            </div>

            <section id="info" class="content-section">
                <h3 class="text-2xl font-semibold mb-3">Profile Information</h3>
                <div class="bg-white shadow p-5 rounded">
                    <h4 class="text-xl font-semibold mb-4">Personal Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p><span class="font-semibold">First Name:</span> Juan</p>
                        <p><span class="font-semibold">Middle Name:</span> Santos</p>
                        <p><span class="font-semibold">Last Name:</span> Dela Cruz</p>
                        <p><span class="font-semibold">Date of Birth:</span> 1993-01-15</p>
                        <p><span class="font-semibold">Age:</span> 30</p>
                        <p><span class="font-semibold">Gender:</span> Male</p>
                    </div>

                    <hr class="my-4">
                    <h4 class="text-xl font-semibold mb-4">Contact Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p><span class="font-semibold">Phone:</span> 09123456789</p>
                        <p><span class="font-semibold">Email:</span> juan@email.com</p>
                    </div>

                    <hr class="my-4">
                    <h4 class="text-xl font-semibold mb-4">Address</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p><span class="font-semibold">Region:</span> Region IV-A</p>
                        <p><span class="font-semibold">Province:</span> Laguna</p>
                        <p><span class="font-semibold">City:</span> Calamba</p>
                        <p><span class="font-semibold">Barangay:</span> Barangay 1</p>
                        <p><span class="font-semibold">ZIP Code:</span> 4027</p>
                    </div>
                </div>
            </section>
        </main>
    </body>
    </html>
