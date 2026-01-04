<?php
require '../db_connect.php';

/* ---------------- PROFILE CONTEXT ---------------- */

if (isset($_GET['profile_id'])) {
    $_SESSION['profile_id'] = (int)$_GET['profile_id'];
}
if (!isset($_SESSION['profile_id'])) {
    die("No profile selected.");
}

$profileId = $_SESSION['profile_id'];
$role = $_SESSION['role'] ?? 'member';

/* ---------------- PERMISSIONS ---------------- */

function isDisabled($permission, $role) {
    $map = [
        'Manage Members'            => ['owner','admin','manager'],
        'Manage Offers & Requests'  => ['owner','admin','manager'],
        'View Activities'           => ['owner','admin','manager','member'],
        'Manage Settings'           => ['owner','admin','manager','member'],
    ];
    return !in_array($role, $map[$permission] ?? []);
}

/* ---------------- PAGINATION ---------------- */

$itemsPerPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $itemsPerPage;

/* ---------------- FETCH ACTIVITIES + PROFILE ---------------- */

$stmt = $conn->prepare("
    SELECT activity_id, description, display_text, created_at,
           profile_type, profile_name, profile_pic
    FROM v_profile_activities
    WHERE profile_id = ?
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $profileId, $itemsPerPage, $offset);
$stmt->execute();
$logs = $stmt->get_result();

/* ---------------- PROFILE INFO (from first row if exists) ---------------- */

if ($row = $logs->fetch_assoc()) {
    $profileType = $row['profile_type'];
    $profileName = $row['profile_name'];
    $profilePic  = $row['profile_pic'] 
        ? "../{$row['profile_pic']}" 
        : "../uploads/profile_pic_placeholder1.png";
    // Reset pointer for later loop
    $logs->data_seek(0);
} else {
    die("Profile not found.");
}

/* ---------------- COUNT TOTAL LOGS ---------------- */

$countStmt = $conn->prepare("
    SELECT COUNT(*) FROM activities WHERE profile_id = ?
");
$countStmt->bind_param("i", $profileId);
$countStmt->execute();
$totalLogs = $countStmt->get_result()->fetch_row()[0];
$totalPages = ceil($totalLogs / $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity</title>
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
        <li><a href="profile_dashboard.php" class="nav-item block p-2 rounded hover:bg-gray-200 <?= isDisabled('View Activities', $role) ? 'disabled-link' : '' ?>">Profile Information</a></li>
        <li><a href="profile_activity.php" class="nav-item block p-2 rounded hover:bg-gray-200 <?= isDisabled('View Activities', $role) ? 'disabled-link' : '' ?>">Activity</a></li>
        <li><a href="profile_myRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">My Requests & Offers</a></li>
        <li><a href="profile_allRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">All Requests & Offers</a></li>
        <li><a href="profile_pending_donations.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">My Pending Donations</a></li>
        <?php if ($profileType !== 'individual'): ?>
        <li><a href="profile_members.php" class="nav-item block p-2 rounded hover:bg-gray-200 <?= isDisabled('Manage Members', $role) ? 'disabled-link' : '' ?>">Members</a></li>
        <?php endif; ?>

        <li><a href="profile_settings.php" class="nav-item block p-2 rounded hover:bg-gray-200 <?= isDisabled('Manage Settings', $role) ? 'disabled-link' : '' ?>">Access Levels</a></li>
    </ul>
</nav>

<main class="flex-1 p-6 overflow-y-auto">

    <!-- PROFILE HEADER -->
    <div class="flex items-center bg-white shadow p-6 rounded-lg mb-6">
        <img src="<?= htmlspecialchars($profilePic) ?>" class="w-28 h-28 rounded-full border mr-6">
        <div>
            <h2 class="text-3xl font-bold"><?= htmlspecialchars($profileName) ?></h2>
            <p class="text-gray-700">Profile Type: <?= htmlspecialchars(ucfirst($profileType)) ?></p>
            <p class="text-gray-500 text-sm">Profile ID: #<?= htmlspecialchars($profileId) ?></p>
        </div>
    </div>

    <!-- ACTIVITY LIST -->
    <section id="activity" class="content-section">
        <h3 class="text-2xl font-semibold mb-3">Activity</h3>

        <div class="bg-white shadow p-4 rounded">
            <div class="grid grid-cols-12 gap-4 font-semibold text-gray-700 border-b border-gray-300 pb-2 mb-2">
                <div class="col-span-1">No.</div>
                <div class="col-span-7">Activity</div>
                <div class="col-span-4">Time</div>
            </div>

            <?php
            $rowNumber = $offset + 1;
            while ($log = $logs->fetch_assoc()): ?>
                <div class="grid grid-cols-12 gap-4 p-2 rounded hover:bg-gray-50 border-b border-gray-100">
                    <div class="col-span-1 font-medium text-gray-700"><?= $rowNumber++; ?></div>
                    <div class="col-span-7 text-gray-800"><?= htmlspecialchars($log['display_text']); ?></div>
                    <div class="col-span-4 text-gray-500 text-sm">
                        <?= date("F j, Y g:i A", strtotime($log['created_at'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>

            <?php if ($totalLogs == 0): ?>
                <p class="text-center text-gray-500 py-4">No activity found.</p>
            <?php endif; ?>

            <!-- PAGINATION -->
            <div class="flex justify-between items-center mt-4">
                <a href="?page=<?= max(1, $page - 1) ?>" class="px-3 py-1 bg-gray-200 rounded <?= $page == 1 ? 'opacity-50 pointer-events-none' : '' ?>">Previous</a>
                <span class="text-gray-600">Page <?= $page ?> of <?= $totalPages ?></span>
                <a href="?page=<?= min($totalPages, $page + 1) ?>" class="px-3 py-1 bg-gray-200 rounded <?= $page >= $totalPages ? 'opacity-50 pointer-events-none' : '' ?>">Next</a>
            </div>

        </div>
    </section>

</main>

</body>
</html>
