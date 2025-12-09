<?php
include 'db_connect.php';

$recent_sql = "SELECT * FROM view_recent_feedback LIMIT 3";
$recent_result = $conn->query($recent_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bayanihan Hub</title>
<script src="src/tailwind.js"></script>
<style>
  html {
    scroll-behavior: smooth;
  }
</style>
</head>
<body class="bg-gray-100 font-sans relative">

<!-- Header -->
<header class="py-4 px-5 bg-white shadow-md flex justify-between items-center fixed w-full z-40">
    <h1 class="text-3xl md:text-4xl font-bold">
        <a href="#hero">Bayanihan Hub</a>
    </h1>

    <button id="hamburger" class="block md:hidden p-2 rounded bg-gray-200 hover:bg-gray-300 z-50">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <nav class="hidden md:flex gap-2 md:gap-4 text-center">
        <a class="block py-2 px-4 hover:bg-gray-200 rounded" href="#hero">Home</a>
        <a class="block py-2 px-4 hover:bg-gray-200 rounded" href="#workflow">How It Works</a>
        <a class="block py-2 px-4 hover:bg-gray-200 rounded" href="#reviews">Feedback</a>
        <a class="block py-2 px-4 hover:bg-gray-200 rounded" href="#mission">About Us</a>
        <a class="block py-2 px-4 hover:bg-blue-100 text-blue-800 rounded" href="login.php">Login</a>
        <a class="block py-2 px-4 hover:bg-green-100 text-green-800 rounded" href="register.php">Register</a>
    </nav>
</header>

<!-- Side Menu -->
<div id="side-menu" class="fixed inset-0 w-full h-full bg-white z-50 transform -translate-x-full transition-transform duration-300 flex flex-col pt-4">
    <button id="close-btn" class="self-end m-4 p-2 rounded bg-gray-200 hover:bg-gray-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
    <ul class="flex flex-col gap-4 px-6 mt-4">
        <li><a class="block py-2 px-4 hover:bg-gray-200 rounded" href="#hero">Home</a></li>
        <li><a class="block py-2 px-4 hover:bg-gray-200 rounded" href="#workflow">How It Works</a></li>
        <li><a class="block py-2 px-4 hover:bg-gray-200 rounded" href="#reviews">Feedback</a></li>
        <li><a class="block py-2 px-4 hover:bg-gray-200 rounded" href="#mission">About Us</a></li>
        <li><a class="block py-2 px-4 hover:bg-blue-100 text-blue-800 rounded" href="login.php">Login</a></li>
        <li><a class="block py-2 px-4 hover:bg-green-100 text-green-800 rounded" href="register.php">Register</a></li>
    </ul>
</div>

<!-- Hero Section -->
<section id="hero" class="bg-gradient-to-r from-blue-800 to-green-700 py-32 px-5 md:px-16 text-center text-white rounded-b-3xl shadow-lg">
    <h2 class="text-4xl md:text-6xl font-bold mb-4 md:mb-6">Bayanihan Made Simple</h2>
    <p class="text-lg md:text-2xl mb-6 md:mb-10 max-w-3xl mx-auto">
        Helping your community is easier than ever. Donate or request support quickly, safely, and transparently.
    </p>
    <div class="flex flex-col md:flex-row justify-center gap-4 md:gap-10 mb-6">
        <a class="bg-white text-blue-800 hover:bg-gray-100 transition-colors py-3 px-10 rounded text-lg md:text-xl font-semibold" href="#workflow">Learn How</a>
        <a class="bg-white text-green-800 hover:bg-gray-100 transition-colors py-3 px-10 rounded text-lg md:text-xl font-semibold" href="#reviews">See Feedback</a>
    </div>
    <p class="text-sm md:text-base text-gray-200">
        Quick, transparent, and community-focused donation matching.
    </p>
</section>

<!-- Donation Workflow Section -->
<section id="workflow" class="py-16 px-5 md:px-16 text-center bg-gray-50">
    <h3 class="text-3xl md:text-4xl font-bold mb-10">How Donations Work</h3>
    <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <div class="text-4xl mb-4">📝</div>
            <h4 class="font-bold mb-2">Sign Up</h4>
            <p>Create an account to donate or request help in your barangay.</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <div class="text-4xl mb-4">👤</div>
            <h4 class="font-bold mb-2">Create Profile</h4>
            <p>Fill in your profile information for proper matching.</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <div class="text-4xl mb-4">📦</div>
            <h4 class="font-bold mb-2">Post a Request/Donation</h4>
            <p>List the items you need or can donate so the system can match them.</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <div class="text-4xl mb-4">🔗</div>
            <h4 class="font-bold mb-2">Match</h4>
            <p>The system pairs donors and recipients within the community efficiently.</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <div class="text-4xl mb-4">🤝</div>
            <h4 class="font-bold mb-2">Complete Donation</h4>
            <p>Donors provide the items directly to recipients, fostering trust and transparency.</p>
        </div>

    </div>
</section>

<!-- User Feedback Section -->
<section id="reviews" class="py-16 px-6 bg-gray-50">
    <h2 class="text-3xl md:text-4xl font-semibold text-center text-gray-800 mb-12">
        Recent Feedback
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
        <?php while ($fb = $recent_result->fetch_assoc()): ?>
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col justify-center text-center">
            
            <p class="text-gray-700 italic text-base md:text-lg leading-relaxed mb-6">
                “<?= htmlspecialchars($fb['feedback']) ?>”
            </p>

            <div class="flex flex-col items-center space-y-1">
                <?php if ($fb['user_id']): ?>
                    <span class="font-medium text-gray-800">
                        — <?= htmlspecialchars($fb['first_name']) ?>
                    </span>
                    <span class="text-gray-500 text-sm">(User)</span>
                <?php else: ?>
                    <span class="font-medium text-gray-800">
                        — <?= htmlspecialchars($fb['profile_name']) ?>
                    </span>
                    <span class="text-gray-500 text-sm">
                        (<?= htmlspecialchars($fb['profile_type']) ?>)
                    </span>
                <?php endif; ?>
                
                <span class="text-gray-400 text-xs">
                    <?= date('M d, Y', strtotime($fb['created_at'])) ?>
                </span>
            </div>

        </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Extra Info Section -->
<section id="mission" class="py-16 px-5 md:px-16 bg-gray-50 text-center">
    <h3 class="text-3xl md:text-4xl font-bold mb-10">Why Choose Bayanihan Hub?</h3>
    <p class="max-w-3xl mx-auto text-lg md:text-xl mb-6">
        Bayanihan Hub connects donors and recipients within local communities, making sure help reaches those who need it most.
    </p>
    <p class="max-w-3xl mx-auto text-lg md:text-xl mb-6">
        Our platform is safe, fast, and transparent. We track each donation from start to finish.
    </p>
    <p class="max-w-3xl mx-auto text-lg md:text-xl">
        Join a community that values trust, cooperation, and bayanihan spirit.
    </p>
</section>

<!-- Mission -->
<section class="py-16 px-5 md:px-16 text-center bg-gray-50">
    <h3 class="text-3xl md:text-4xl font-bold mb-6">Our Mission</h3>
    <p class="max-w-3xl mx-auto text-lg md:text-xl mb-6">
        Bayanihan Hub exists to make local donation and resource matching simple, fast, and transparent. 
        We aim to strengthen communities by connecting donors and recipients within the same barangay.
    </p>
    <a class="bg-blue-800 hover:bg-blue-700 text-white py-3 px-8 rounded-lg font-semibold transition-colors" href="register.php">Get Started</a>
</section>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-10 px-5 md:px-16 text-center">
    <p>Bayanihan Hub &copy; 2025. All rights reserved.</p>
    <p class="mt-2 text-gray-400">Contact: info@bayanihanhub.com | FB: BayanihanHub</p>
</footer>

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

sideMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        sideMenu.classList.add('-translate-x-full');
    });
});




let currentIndex = 0;
const items = document.querySelectorAll(".carousel-item");

function showSlide(index) {
    items.forEach((item, i) => {
        item.style.opacity = (i === index) ? "100" : "0";
    });
}

document.getElementById("prevBtn").addEventListener("click", () => {
    currentIndex = (currentIndex - 1 + items.length) % items.length;
    showSlide(currentIndex);
});

document.getElementById("nextBtn").addEventListener("click", () => {
    currentIndex = (currentIndex + 1) % items.length;
    showSlide(currentIndex);
});

// Auto-slide every 5 sec
setInterval(() => {
    currentIndex = (currentIndex + 1) % items.length;
    showSlide(currentIndex);
}, 5000);
</script>

</body>
</html>
