<?php
include "../admin_connect.php";

$user_id = $_SESSION['user_id'] ?? null;
$php_role = $_SESSION['role'] ?? 'Staff'; // Default to Staff

// ----------------- ACTIVATE MYSQL ROLE -----------------
if (in_array($php_role, ['Staff', 'Admin', 'Superuser'])) {
    $conn->query("SET ROLE " . strtolower($php_role));
}

// Fetch feedback entries
$feedbackSql = "SELECT * FROM feedback ORDER BY created_at DESC";
$feedbackResult = $conn->query($feedbackSql);

$roleSql = "SELECT role FROM users WHERE user_id = ?";
$roleStmt = $conn->prepare($roleSql);
$roleStmt->bind_param("i", $user_id);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
$roleRow = $roleResult->fetch_assoc();

$disabledClass = 'opacity-50 cursor-not-allowed pointer-events-none bg-gray-200';
$currentRole = $roleRow['role'] ?? 'User';

$isStaff = ($currentRole === 'Staff');
$isAdmin = ($currentRole === 'Admin');
$isSuperuser = ($currentRole === 'Superuser');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Feedback</title>

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
            <li>
                <a href="admin_items.php"
                class="block px-4 py-2 rounded
                <?= ($isStaff || $incomplete) ? $disabledClass : 'hover:bg-gray-200' ?>">
                    Item Management
                </a>
            </li>
            <li class="<?= ($isStaff || $incomplete) ? $disabledClass : '' ?>">
                <a href="admin_locations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Location Management</a>
            </li>
            <li class="<?= ($isStaff || $incomplete) ? $disabledClass : '' ?>">
                <a href="admin_donation_logs.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donation Logs</a>
            </li>
            <li class="<?= ($isStaff || $incomplete) ? $disabledClass : '' ?>">
                <a href="admin_activities.php" class="block px-4 py-2 rounded hover:bg-gray-200">Activity</a>
            </li>
            <li class="<?= ($isStaff || $isAdmin || $incomplete) ? $disabledClass : '' ?>">
                <a href="admin_audit_trails.php" class="block px-4 py-2 rounded hover:bg-gray-200">Audit Trails</a>
            </li>
            <li class="<?= ($isStaff || $incomplete) ? $disabledClass : '' ?>">
                <a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Access Level Management</a>
            </li>

            <!-- Support -->
            <li class="uppercase text-xs px-2 mt-4">Support</li>
            <li><a href="admin_feedback.php" class="block px-4 py-2 rounded bg-gray-300 font-semibold">Feedback</a></li>
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

    <h2 class="text-2xl font-bold mb-6">Feedback Management</h2>

    <!-- ================= FEEDBACK TABLE ================= -->
    <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <table class="w-full min-w-[800px] border-collapse text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">ID</th>
                    <th class="p-3">User / Profile</th>
                    <th class="p-3">Feedback</th>
                    <th class="p-3">Submitted At</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($feedbackResult && $feedbackResult->num_rows > 0): ?>
                <?php while ($row = $feedbackResult->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3">
                            <?= htmlspecialchars($row['feedback_id']) ?>
                        </td>

                        <td class="p-3 text-xs">
                            User ID: <?= htmlspecialchars($row['user_id']) ?><br>
                            <?php if (!empty($row['profile_id']) || !null): ?>
                                Profile ID: <?= htmlspecialchars($row['profile_id']) ?>
                            <?php else: ?>
                                <span class="text-gray-400">No Profile</span>
                            <?php endif; ?>
                        </td>

                        <td class="p-3 truncate max-w-[300px]">
                            <?= htmlspecialchars($row['feedback']) ?>
                        </td>

                        <td class="p-3">
                            <?= htmlspecialchars($row['created_at']) ?>
                        </td>

                        <td class="p-3 text-center">
                            <div class="flex gap-1 justify-center whitespace-nowrap">
                                <button
                                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                                    onclick="openFeedbackModal(
                                        '<?= htmlspecialchars($row['feedback_id']) ?>',
                                        '<?= htmlspecialchars($row['user_id']) ?>',
                                        '<?= htmlspecialchars($row['profile_id']) ?>',
                                        `<?= htmlspecialchars($row['feedback']) ?>`,
                                        '<?= htmlspecialchars($row['created_at']) ?>'
                                    )">
                                    View
                                </button>


                                <?php if ($isAdmin || $isSuperuser): ?>
                                    <a href="admin_feedback_delete.php?id=<?= $row['feedback_id'] ?>"
                                    onclick="return confirm('Delete this feedback?')"
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                        Delete
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">
                        No feedback found.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>

        </table>
    </div>

</main>

<!-- ================= FEEDBACK VIEW MODAL ================= -->
<div id="feedbackModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">

        <!-- Close button -->
        <button onclick="closeFeedbackModal()"
                class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
            ✕
        </button>

        <h3 class="text-xl font-bold mb-4">Feedback Details</h3>

        <div class="space-y-3 text-sm text-gray-700">
            <p><strong>Feedback ID:</strong> <span id="m_feedback_id"></span></p>
            <p><strong>User ID:</strong> <span id="m_user_id"></span></p>
            <p><strong>Profile ID:</strong> <span id="m_profile_id"></span></p>

            <div>
                <strong>Message:</strong>
                <div class="mt-1 p-3 bg-gray-100 rounded text-gray-800 max-h-40 overflow-y-auto">
                    <span id="m_feedback"></span>
                </div>
            </div>

            <p class="text-xs text-gray-500">
                Submitted at: <span id="m_created_at"></span>
            </p>
        </div>

        <div class="mt-5 text-right">
            <button onclick="closeFeedbackModal()"
                    class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                Close
            </button>
        </div>
    </div>
</div>


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
function openFeedbackModal(id, userId, profileId, feedback, createdAt) {
    document.getElementById('m_feedback_id').textContent = id;
    document.getElementById('m_user_id').textContent = userId;
    document.getElementById('m_profile_id').textContent = profileId || 'No Profile';
    document.getElementById('m_feedback').textContent = feedback;
    document.getElementById('m_created_at').textContent = createdAt;

    document.getElementById('feedbackModal').classList.remove('hidden');
    document.getElementById('feedbackModal').classList.add('flex');
}

function closeFeedbackModal() {
    document.getElementById('feedbackModal').classList.add('hidden');
    document.getElementById('feedbackModal').classList.remove('flex');
}
</script>


</body>
</html>
