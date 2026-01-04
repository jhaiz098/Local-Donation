<?php
require '../db_connect.php';

/* ---------------- PROFILE CONTEXT ---------------- */
$profileId = $_GET['profile_id'] ?? $_SESSION['profile_id'] ?? die("No profile selected.");
$_SESSION['profile_id'] = (int)$profileId;
$userId = $_SESSION['user_id'];

/* ---------------- FETCH PROFILE DASHBOARD ---------------- */
$profile = $conn->query("SELECT * FROM v_profile_dashboard WHERE profile_id=$profileId")->fetch_assoc() ?? die("Profile not found");

$profileName = $profile['profile_name'];
$profileType = $profile['profile_type'];

/* ---------------- FETCH USER ROLE ---------------- */
$role = $conn->query("
    SELECT role
    FROM profile_members
    WHERE profile_id=$profileId AND user_id=$userId
")->fetch_assoc()['role'] ?? die("No role found");
$_SESSION['role'] = $role;

/* ---------------- PERMISSIONS ---------------- */
function isDisabled($permission, $role){
    $map = [
        'Manage Members'=>['owner','admin','manager'],
        'Manage Offers & Requests'=>['owner','admin','manager'],
        'View Activities'=>['owner','admin','manager','member'],
        'Manage Settings'=>['owner','admin','manager','member']
    ];
    return !in_array($role,$map[$permission]??[]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Dashboard</title>
    <script src="../src/tailwind.js"></script>
    <link rel="stylesheet" href="../src/style2.css">
</head>
<body class="bg-gray-100 h-screen flex">

    <!-- LEFT NAVIGATION -->
    <nav class="w-64 bg-white shadow-md flex flex-col p-6">
        <div class="mb-10">
            <h1 class="text-2xl font-bold">Profile</h1>
            <p class="text-gray-500 text-sm">Dashboard Menu</p>
        </div>
        <ul class="flex flex-col gap-2 text-gray-700 font-medium">
            <li>
                <a href="profile_dashboard.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('View Activities', $role) ? 'disabled-link' : '' ?>">Profile Information</a>
            </li>
            <li>
                <a href="profile_activity.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('View Activities', $role) ? 'disabled-link' : '' ?>">Activity</a>
            </li>
            <li>
                <a href="profile_myRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">My Requests & Offers</a>
            </li>
            <li>
                <a href="profile_allRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">All Requests & Offers</a>
            </li>
            <li>
                <a href="profile_pending_donations.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">Pending Donations</a>
            </li>

            <?php if ($profileType !== 'individual'): ?>
                <li>
                    <a href="profile_members.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Members', $role) ? 'disabled-link' : '' ?>">Members</a>
                </li>
            <?php endif; ?>

            <li>
                <a href="profile_settings.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Settings', $role) ? 'disabled-link' : '' ?>">Access Levels</a>
            </li>
        </ul>
    </nav>
</body>
</html>