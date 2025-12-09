<?php
include 'db_connect.php';

$user_id = $_SESSION['user_id']; // logged-in user

// Pagination setup
$items_per_page = 5; // Number of items per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page
$offset = ($page - 1) * $items_per_page; // Calculate offset

// Get total number of activities
$sqlTotal = "SELECT COUNT(*) AS total FROM activities WHERE user_id = ?";
$stmtTotal = $conn->prepare($sqlTotal);
$stmtTotal->bind_param("i", $user_id);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalActivities = $resultTotal->fetch_assoc()['total'];
$stmtTotal->close();

// Get activities for this user with pagination
$sql = "SELECT * FROM activities 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $offset, $items_per_page);
$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while($row = $result->fetch_assoc()) {
    $activities[] = $row;
}
$stmt->close();

// Calculate total pages
$total_pages = ceil($totalActivities / $items_per_page);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Activity</title>
<script src="src/tailwind.js"></script>
<link rel="stylesheet" href="src/style.css">
</head>
<body class="bg-gray-100">

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
      <h1 class="text-3xl md:text-4xl font-bold mb-2">Activity</h1>
      <p class="text-lg md:text-xl text-blue-100 mb-4">
        Keep track of your actions and activity history.
      </p>
    </div>

    <div class="flex-1 bg-blue-600 rounded-lg p-6 text-center hidden md:flex items-center justify-center">
      <div class="text-white font-bold text-xl">
        Activity Overview
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
            <li><a href="notifications.php" class="block py-2 px-4 bg-gray-300 rounded">Activity</a></li>
            <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Feedback</a></li>
            <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
            <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
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
        <li><a href="notifications.php" class="block py-2 px-4 bg-gray-300 rounded">Activity</a></li>
        <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Feedback</a></li>
        <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
        <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
        <li><a href="#" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a></li>
    </ul>
</div>

<!-- Main Activity Section -->
<section class="max-w-6xl mx-auto px-5 py-8 flex flex-col md:flex-row gap-6">
    <!-- Right Column: Activity Feed -->
    <div class="w-full">
        <div id="activity-box" class="bg-white shadow-md rounded p-4 flex flex-col gap-4">
            <h2 class="font-bold text-xl mb-3">Your Activities</h2>
            <?php if(count($activities) > 0): ?>
                <?php foreach($activities as $act): ?>
                    <div class="bg-green-50 p-3 rounded shadow flex flex-col">
                        <p class="font-semibold"><?= htmlspecialchars($act['display_text']) ?></p>
                        <p class="text-gray-400 text-xs mt-1"><?= date('M d, Y H:i', strtotime($act['created_at'])) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500">No activities yet.</p>
            <?php endif; ?>

            <!-- Pagination Controls -->
            <div class="flex justify-between items-center mt-4">
                <div>
                    <?php if ($page > 1): ?>
                        <a href="notifications.php?page=<?= $page - 1 ?>" class="text-blue-500 hover:text-blue-700">Previous</a>
                    <?php else: ?>
                        <span class="text-gray-500">Previous</span>
                    <?php endif; ?>
                </div>
                <div class="text-center">
                    <span>Page <?= $page ?> of <?= $total_pages ?></span>
                </div>
                <div>
                    <?php if ($page < $total_pages): ?>
                        <a href="notifications.php?page=<?= $page + 1 ?>" class="text-blue-500 hover:text-blue-700">Next</a>
                    <?php else: ?>
                        <span class="text-gray-500">Next</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const hamburger = document.getElementById('hamburger');
const sideMenu = document.getElementById('side-menu');
const closeBtn = document.getElementById('close-btn');

hamburger.addEventListener('click', () => sideMenu.classList.remove('-translate-x-full'));
closeBtn.addEventListener('click', () => sideMenu.classList.add('-translate-x-full'));
sideMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => sideMenu.classList.add('-translate-x-full'));
});
</script>

</body>
</html>
