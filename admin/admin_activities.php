<?php
include "../db_connect.php"; // Include your database connection

$user_id = $_SESSION['user_id'];

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

// Number of records to display per page
$records_per_page = 10;

// Get the current page from the query string, default to 1 if not set
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting record based on the current page
$offset = ($page - 1) * $records_per_page;

// Fetch activities from the database with LIMIT and OFFSET for pagination
$query = "SELECT * FROM activities ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offset, $records_per_page); // Bind offset and records per page as integers
$stmt->execute();
$result = $stmt->get_result();

// Check if any records were found
$activities = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
}

// Get the total number of activities for pagination calculation
$query_total = "SELECT COUNT(*) as total FROM activities";
$result_total = $conn->query($query_total);
$total_activities = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_activities / $records_per_page); // Calculate total number of pages
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Activities</title>

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
            <li><a href="admin_items.php" class="block px-4 py-2 rounded hover:bg-gray-200">Item Management</a></li>
            <li><a href="admin_locations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Location Management</a></li>
            <li><a href="admin_donation_logs.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donation Logs</a></li>
            <li><a href="admin_activities.php" class="block px-4 py-2 rounded bg-gray-300 font-semibold">Activity</a></li>
            <li class="<?= ($isStaff || $isAdmin || $incomplete) ? $disabledClass : '' ?>">
                <a href="admin_audit_trails.php" class="block px-4 py-2 rounded">Audit Trails</a>
            </li>
            <li class="<?= ($isStaff || $incomplete) ? $disabledClass : '' ?>">
              <a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Access Level Management</a>
            </li>

            <!-- Support -->
            <li class="uppercase text-xs px-2 mt-4">Support</li>
            <li><a href="admin_feedback.php" class="block px-4 py-2 rounded hover:bg-gray-200">Feedback</a></li>
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
  <h2 class="text-2xl font-bold mb-6">Activity Logs</h2>

  <div class="bg-white rounded-xl shadow-md overflow-x-auto">
    <table class="w-full border-collapse text-sm">
      <thead class="bg-gray-100 text-left">
        <tr>
          <th class="p-3">No.</th> <!-- Added No. Column -->
          <th class="p-3">User / Profile</th>
          <th class="p-3">Description</th>
          <th class="p-3">Display Text</th>
          <th class="p-3">Created At</th>
        </tr>
      </thead>
      <tbody class="text-gray-700">
        <?php 
          $serial_no = $offset + 1; // Start from the correct serial number for the page
          foreach ($activities as $activity): 
        ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="p-3"><?= $serial_no++ ?></td> <!-- Display Serial Number -->
            <td class="p-3">
              <span class="font-medium"><?= $activity['user_id'] ?: 'System' ?></span>
              <br>
              <span class="text-xs text-gray-500"><?= $activity['profile_id'] ? 'Profile' : 'User' ?></span>
            </td>
            <td class="p-3 truncate max-w-[250px]"><?= htmlspecialchars($activity['description']) ?></td>
            <td class="p-3 truncate max-w-[200px]"><?= htmlspecialchars($activity['display_text'] ?: 'No display text') ?></td>
            <td class="p-3"><?= $activity['created_at'] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination Controls -->
  <div class="mt-4 flex justify-between">
    <!-- Previous Page Button -->
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Previous</a>
    <?php else: ?>
      <span class="px-4 py-2 bg-gray-100 rounded cursor-not-allowed">Previous</span>
    <?php endif; ?>

    <!-- Next Page Button -->
    <?php if ($page < $total_pages): ?>
      <a href="?page=<?= $page + 1 ?>" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">Next</a>
    <?php else: ?>
      <span class="px-4 py-2 bg-gray-100 rounded cursor-not-allowed">Next</span>
    <?php endif; ?>
  </div>

</main>

<!-- ================= JS ================= -->
<script>
  const hamburger = document.getElementById('hamburger');
  const sideMenu = document.getElementById('side-menu');
  const closeBtn = document.getElementById('close-btn');

  if (hamburger) {
    hamburger.addEventListener('click', () => {
      sideMenu.classList.remove('-translate-x-full');
    });
  }

  if (closeBtn) {
    closeBtn.addEventListener('click', () => {
      sideMenu.classList.add('-translate-x-full');
    });
  }
</script>

</body>
</html>