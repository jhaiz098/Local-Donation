<?php
require '../db_connect.php';

if (isset($_GET['profile_id'])) {
    $_SESSION['profile_id'] = intval($_GET['profile_id']);
} elseif (!isset($_SESSION['profile_id'])) {
    echo "No profile selected.";
    exit;
}

$profileId = $_SESSION['profile_id'];
$role = $_SESSION['role'];

// Helper function to disable links if user doesn't have permission
function isDisabled($permission, $role) {
    $permissionsMap = [
        'Manage Members' => ['owner', 'admin', 'manager'],
        'Manage Offers & Requests' => ['owner', 'admin', 'manager'],
        'View Activities' => ['owner', 'admin', 'manager', 'member'],
        'Manage Settings' => ['owner', 'admin', 'manager', 'member']
    ];
    return !in_array($role, $permissionsMap[$permission]);
}


// Get main profile info
$stmt = $conn->prepare("SELECT profile_type, profile_name, profile_pic FROM profiles WHERE profile_id = ?");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$result = $stmt->get_result();
$profileMain = $result->fetch_assoc();
$stmt->close();

if (!$profileMain) {
    echo "Profile not found.";
    exit;
}

$profileType = $profileMain['profile_type'];
$profileName = $profileMain['profile_name'];
$profilePic = !empty($profileMain['profile_pic']) ? "../" . $profileMain['profile_pic'] : "../uploads/profile_pic_placeholder1.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Access Levels</title>
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
            <li><a href="profile_pending_donations.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">Pending Donations</a></li>

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

    <main class="flex-1 p-6 overflow-y-auto">

        <!-- PROFILE HEADER -->
        <div class="flex items-center bg-white shadow p-6 rounded-lg mb-6">
            <img src="<?= htmlspecialchars($profilePic) ?>"
                 class="w-28 h-28 rounded-full border mr-6"
                 alt="Profile Picture">
            <div>
                <h2 class="text-3xl font-bold"><?= htmlspecialchars($profileName) ?></h2>
                <p class="text-gray-700">Profile Type: <?= htmlspecialchars(ucfirst($profileType))?></p>
                <p class="text-gray-500 text-sm">Profile ID: #<?= htmlspecialchars($profileId) ?></p>
            </div>
        </div>

        <section id="settings">
            <h3 class="text-2xl font-semibold mb-3">Access Levels</h3>

            <!-- ACCESS LEVELS -->
            <div class="bg-white shadow p-4 rounded">
                <!-- <h4 class="text-xl font-semibold mb-4">Role Access Levels</h4> -->

                <!-- <p class="text-gray-600 mb-3 text-sm">Assign permissions for each role/position in your profile.</p> -->

                <table class="w-full border">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2 border">Role / Position</th>
                            <th class="p-2 border">Manage Members</th>
                            <th class="p-2 border">Manage Donation Offers & Requests</th>
                            <th class="p-2 border">View Activities</th>
                            <th class="p-2 border">Submit Feedback</th>
                            <th class="p-2 border">View Access Levels</th>
                        </tr>
                    </thead>

                    <tbody class="text-center">
                        <tr>
                            <td class="p-2 border font-medium">Owner</td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                        </tr>

                        <tr>
                            <td class="p-2 border font-medium">Admin</td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                        </tr>

                        <tr>
                            <td class="p-2 border font-medium">Manager</td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                        </tr>

                        <tr>
                            <td class="p-2 border font-medium">Member</td>
                            <td class="p-2 border"><input type="checkbox" disabled></td>
                            <td class="p-2 border"><input type="checkbox" disabled></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                            <td class="p-2 border"><input type="checkbox" disabled></td>
                            <td class="p-2 border"><input type="checkbox" disabled checked></td>
                        </tr>

                        <tr>
                            <td class="p-2 border font-medium">Guest</td>
                            <td class="p-2 border"><input type="checkbox" disabled></td>
                            <td class="p-2 border"><input type="checkbox" disabled></td>
                            <td class="p-2 border"><input type="checkbox" disabled></td>
                            <td class="p-2 border"><input type="checkbox" disabled></td>
                            <td class="p-2 border"><input type="checkbox" disabled></td>
                        </tr>
                    </tbody>
                </table>


                <!-- <div class="mt-4">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save Access Levels</button>
                </div> -->
            </div>
        </section>

    </main>

</body>
</html>
