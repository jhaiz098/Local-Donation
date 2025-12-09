<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<script src="src/tailwind.js"></script>
<link rel="stylesheet" href="src/style.css">
</head>
<body class="bg-gray-100 font-sans relative">

<!-- Header -->
<header class="py-4 px-5 bg-white shadow-md flex justify-between items-center">
    <h1 class="text-3xl md:text-4xl font-bold">
        <a href="index.php">Bayanihan Hub</a>
    </h1>

    <!-- Hamburger button: sm only -->
    <button id="hamburger" class="block md:hidden p-2 rounded bg-gray-200 hover:bg-gray-300 z-20">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    <!-- Desktop nav -->
    <nav class="hidden md:flex gap-2 md:gap-4 text-center">
        <a class="block py-2 px-4 hover:bg-gray-200 rounded" href="index.php#hero">Home</a>
        <a class="block py-2 px-4 hover:bg-gray-200 rounded" href="index.php#workflow">How It Works</a>
        <a class="block py-2 px-4 hover:bg-gray-200 rounded" href="index.php#reviews">Feedback</a>
        <a class="block py-2 px-4 hover:bg-gray-200 rounded" href="index.php#mission">About Us</a>
        <a class="block py-2 px-4 hover:bg-blue-100 text-blue-800 rounded" href="login.php">Login</a>
        <a class="block py-2 px-4 hover:bg-green-100 text-green-800 rounded" href="register.php">Register</a>
    </nav>
</header>

<!-- Side Menu -->
<div id="side-menu" class="fixed inset-0 w-full h-full bg-white z-30 transform -translate-x-full transition-transform duration-300 md:hidden flex flex-col pt-4">
    <!-- Close Button -->
    <button id="close-btn" class="self-end m-4 p-2 rounded bg-gray-200 hover:bg-gray-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <ul class="flex flex-col gap-4 px-6 mt-4">
        <li><a class="block py-2 px-4 hover:bg-gray-200 rounded" href="index.php#hero">Home</a></li>
        <li><a class="block py-2 px-4 hover:bg-gray-200 rounded" href="index.php#workflow">How It Works</a></li>
        <li><a class="block py-2 px-4 hover:bg-gray-200 rounded" href="index.php#reviews">Feedback</a></li>
        <li><a class="block py-2 px-4 hover:bg-gray-200 rounded" href="index.php#mission">About Us</a></li>
        <li><a class="block py-2 px-4 hover:bg-blue-100 text-blue-800 rounded" href="login.php">Login</a></li>
        <li><a class="block py-2 px-4 hover:bg-green-100 text-green-800 rounded" href="register.php">Register</a></li>
    </ul>
</div>

<!-- Login Form Section -->
<div class="flex justify-center items-center min-h-[calc(100vh-80px)] px-4">
    <div class="w-full max-w-md py-8 px-6 md:px-10 rounded-3xl bg-gray-800 text-white shadow-lg">
        <h2 class="text-2xl md:text-3xl text-center font-bold mb-6">Administrative Login</h2>
        <form action="admin_login_logic.php" method="post">
            <!-- Email -->
            <div class="mb-4">
                <label class="block mb-2">Email</label>
                <input 
                    name="email"
                    class="w-full py-2 px-3 rounded-xl border-2 border-gray-300 bg-white text-gray-900 focus:outline-none focus:border-blue-500"
                    type="email" 
                    placeholder="Enter your email"
                >
            </div>
            <!-- Password -->
            <div class="mb-6">
                <label class="block mb-2">Password</label>
                <input 
                    name="password"
                    class="w-full py-2 px-3 rounded-xl border-2 border-gray-300 bg-white text-gray-900 focus:outline-none focus:border-blue-500"
                    type="password" 
                    placeholder="Enter your password"
                >
            </div>
            <button class="w-full bg-blue-700 hover:bg-blue-600 py-2 px-3 rounded-3xl text-lg font-semibold transition-colors mb-4">
                Login
            </button>
            <p class="text-center text-gray-300">
                Not an admin?
                <a class="text-blue-500 underline hover:text-blue-400" href="login.php">Login as a user</a>
            </p>


        </form>
    </div>
</div>

<script>
const hamburger = document.getElementById('hamburger');
const sideMenu = document.getElementById('side-menu');
const closeBtn = document.getElementById('close-btn');

// Open menu
hamburger.addEventListener('click', () => {
    sideMenu.classList.remove('-translate-x-full');
});

// Close menu
closeBtn.addEventListener('click', () => {
    sideMenu.classList.add('-translate-x-full');
});

// Close menu when clicking a link
sideMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        sideMenu.classList.add('-translate-x-full');
    });
});

// Check for messages from login.php
const params = new URLSearchParams(window.location.search);
if (params.has("status")) {
    const status = params.get("status");
    const message = params.get("message") || "";
    alert(message); // or display in a div
}
</script>

</body>
</html>
