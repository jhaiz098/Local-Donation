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
$profilePic  = $profile['profile_pic'] ? "../{$profile['profile_pic']}" : "../uploads/profile_pic_placeholder1.png";

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

// Fetch pending donations MADE BY this profile (donor side)
$sql = "
    SELECT 
        pdi.pending_item_id,
        pdi.quantity,
        pdi.unit_name,
        pdi.created_at,

        i.item_name,

        r.profile_name AS requester_name,
        r.profile_type AS requester_type,
        
        de.entry_id
    FROM pending_donation_items pdi
    JOIN donation_entries de ON de.entry_id = pdi.entry_id
    JOIN profiles r ON r.profile_id = de.profile_id
    JOIN items i ON i.item_id = pdi.item_id
    WHERE pdi.donor_profile_id = ?
    ORDER BY pdi.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $profileId);
$stmt->execute();
$result = $stmt->get_result();
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
                <a href="profile_pending_donations.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">My Pending Donations</a>
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

<!-- MAIN CONTENT -->
<div class="flex-1 p-8 overflow-y-auto">

    <h2 class="text-2xl font-bold mb-1">My Pending Donations</h2>
    <p class="text-gray-600 text-sm mb-6">
        These donations are waiting for the requester’s confirmation.
    </p>

    <?php if ($result->num_rows === 0): ?>
        <div class="bg-white p-6 rounded shadow text-center text-gray-500">
            You have no pending donations.
        </div>
    <?php else: ?>
        <div class="bg-white rounded shadow overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-100">
                    <tr class="text-left border-b">
                        <th class="p-3 w-16">#</th>
                        <th class="p-3 w-40">Item</th>
                        <th class="p-3 w-32">Quantity</th>
                        <th class="p-3 w-48">Requester</th>
                        <th class="p-3 w-32">Request ID</th>
                        <th class="p-3 w-40">Date</th>
                        <th class="p-3 w-32">Status</th>
                        <th class="p-3 text-center w-28">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-3"><?= $no++ ?></td>

                            <td class="p-3 font-medium">
                                <?= htmlspecialchars($row['item_name']) ?>
                            </td>

                            <td class="p-3">
                                <?= $row['quantity'] . ' ' . htmlspecialchars($row['unit_name']) ?>
                            </td>

                            <td class="p-3 text-blue-700">
                                <?= htmlspecialchars($row['requester_name']) ?>
                                <span class="text-gray-500 text-xs">
                                    (<?= htmlspecialchars($row['requester_type']) ?>)
                                </span>
                            </td>


                            <td class="p-3">
                                #<?= $row['entry_id'] ?>
                            </td>

                            <td class="p-3 text-gray-600">
                                <?= date('Y-m-d', strtotime($row['created_at'])) ?>
                            </td>

                            <td class="p-3">
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">
                                    Pending
                                </span>
                            </td>

                            <td class="p-3 text-center">
                                <button 
                                    class="px-3 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 cancel-donation-btn"
                                    data-pending-id="<?= $row['pending_item_id'] ?>">
                                    Cancel
                                </button>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- ACTION SCRIPT -->
<script>
document.addEventListener('click', function (e) {
    if (!e.target.classList.contains('cancel-donation-btn')) return;

    const pendingId = e.target.dataset.pendingId;

    if (!confirm('Cancel this pending donation?')) return;

    fetch('cancel_pending_donation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'pending_item_id=' + pendingId
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            e.target.closest('tr').remove();
        } else {
            alert(data.message || 'Failed to cancel donation.');
        }
    });
});
</script>

</body>
</html>