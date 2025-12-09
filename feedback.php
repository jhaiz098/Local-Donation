<?php
include 'db_connect.php';

$user_id = $_SESSION['user_id']; // Logged-in user

// Fetch user profiles with allowed roles
$profiles = [];
$profile_sql = "SELECT pm.profile_id, p.profile_name, p.profile_type, pm.role 
                FROM profile_members pm
                JOIN profiles p ON pm.profile_id = p.profile_id
                WHERE pm.user_id = ?";
$stmt = $conn->prepare($profile_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$profile_result = $stmt->get_result();
while($row = $profile_result->fetch_assoc()) {
    if(in_array($row['role'], ['owner', 'admin', 'manager'])) {
        $profiles[] = $row;
    }
}

// Handle feedback submission
if($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['feedback'])) {
    $feedback = trim($_POST['feedback']);
    $as_type = $_POST['as_type'];

    if(strlen($feedback) > 1000) { 
        $error_msg = "Feedback too long.";
    }

    if($as_type === 'user') {
        $insert_sql = "INSERT INTO feedback (user_id, feedback) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("is", $user_id, $feedback);
    } else {
        $profile_id = $_POST['profile_id'];
        $role_check_sql = "SELECT role FROM profile_members WHERE profile_id = ? AND user_id = ?";
        $role_stmt = $conn->prepare($role_check_sql);
        $role_stmt->bind_param("ii", $profile_id, $user_id);
        $role_stmt->execute();
        $role_result = $role_stmt->get_result()->fetch_assoc();
        if(!$role_result || !in_array($role_result['role'], ['owner', 'admin', 'manager'])) {
            $error_msg = "Your profile role cannot submit feedback.";
        } else {
            $insert_sql = "INSERT INTO feedback (profile_id, feedback) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("is", $profile_id, $feedback);
        }
    }

    if(isset($stmt) && $stmt->execute()) {
        $success_msg = "Feedback submitted successfully!";
    } elseif(!isset($error_msg)) {
        $error_msg = "Failed to submit feedback.";
    }
}

// Fetch all feedback by user and eligible profiles
$feedbacks = [];
$feedback_sql = "SELECT f.*, u.user_id AS user_id, u.first_name, 
                        p.profile_id AS profile_id, p.profile_name, p.profile_type, pm.role
                 FROM feedback f
                 LEFT JOIN users u ON f.user_id = u.user_id
                 LEFT JOIN profiles p ON f.profile_id = p.profile_id
                 LEFT JOIN profile_members pm ON f.profile_id = pm.profile_id AND pm.user_id = ?
                 WHERE f.user_id = ? OR f.profile_id IN (SELECT profile_id FROM profile_members WHERE user_id = ?)
                 ORDER BY f.created_at DESC";
$stmt = $conn->prepare($feedback_sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Feedback</title>
<link rel="stylesheet" href="src/style.css">
<script src="src/tailwind.js"></script>
</head>
<body class="bg-gray-100 font-sans">

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
      <h1 class="text-3xl md:text-4xl font-bold mb-2">Feedback</h1>
      <p class="text-lg md:text-xl text-blue-100 mb-4">
        Share your thoughts and help us improve Bayanihan Hub for everyone.
      </p>
    </div>

    <div class="flex-1 bg-blue-600 rounded-lg p-6 text-center hidden md:flex items-center justify-center">
      <div class="text-white font-bold text-xl">
        Feedback Overview
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
            <li><a href="feedback.php" class="block py-2 px-4 bg-gray-300 rounded">Feedback</a></li>
            <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
            <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
        </ul>
    </div>
    <div>
        <a href="logout.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a>
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
        <li><a href="feedback.php" class="block py-2 px-4 bg-gray-300 rounded">Feedback</a></li>
        <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
        <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
        <li><a href="logout.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a></li>
    </ul>
</div>

<!-- Feedback Submission -->
<section class="max-w-4xl my-8 mx-auto bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-2xl font-bold mb-4">Submit Feedback</h2>

    <?php if(isset($success_msg)) echo "<p class='text-green-600 mb-4'>{$success_msg}</p>"; ?>
    <?php if(isset($error_msg)) echo "<p class='text-red-600 mb-4'>{$error_msg}</p>"; ?>

    <form method="POST" class="space-y-4">
        <textarea name="feedback" rows="4" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Write your feedback..."></textarea>

        <div class="flex flex-wrap items-center gap-4">
            <label class="flex items-center gap-2"><input type="radio" name="as_type" value="user" checked> Submit as User</label>
            <?php foreach($profiles as $prof): ?>
                <label class="flex items-center gap-2 bg-gray-100 px-3 py-1 rounded hover:bg-gray-200 cursor-pointer">
                    <input type="radio" name="as_type" value="profile" onclick="document.getElementById('profile_id').value='<?= $prof['profile_id'] ?>'">
                    <?= htmlspecialchars($prof['profile_name']) ?> (<?= htmlspecialchars($prof['profile_type']) ?>) (<?= htmlspecialchars($prof['role']) ?>)
                </label>
            <?php endforeach; ?>
        </div>

        <input type="hidden" name="profile_id" id="profile_id" value="">

        <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition-colors duration-200">Submit</button>
    </form>
</section>

<!-- Feedback List -->
<section class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-2xl font-bold mb-4">Your Feedbacks</h2>

    <?php if(count($feedbacks) > 0): ?>
        <div class="space-y-3">
        <?php foreach($feedbacks as $fb): ?>
            <div class="bg-gray-50 p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                <p class="text-gray-800"><?= htmlspecialchars($fb['feedback']) ?></p>
                <p class="text-gray-500 text-sm mt-1">
                    By: 
                    <?php 
                    if($fb['user_id']) {
                        echo htmlspecialchars($fb['first_name']);
                    } else {
                        echo htmlspecialchars($fb['profile_name']) . " (" . htmlspecialchars($fb['profile_type']) . ") (" . htmlspecialchars($fb['role']) . ")";
                    }
                    ?> | <?= date('M d, Y H:i', strtotime($fb['created_at'])) ?>
                </p>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-gray-500">No feedback submitted yet.</p>
    <?php endif; ?>
</section>

</body>
</html>
