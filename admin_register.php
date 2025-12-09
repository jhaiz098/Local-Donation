<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>
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

<!-- Register Form Section -->
<div class="flex justify-center items-center min-h-[calc(100vh-80px)] px-4 py-10">
    <div class="w-full max-w-lg py-8 px-6 md:px-10 rounded-3xl bg-gray-800 text-white shadow-lg">

        <h2 class="text-2xl md:text-3xl text-center font-bold mb-6">Register as Staff</h2>
        <h3 class="text-1xl text-center mb-6 text-red-500">Subject to Approval</h3>

        <form action="admin_register_logic.php" method="post">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- FIRST NAME -->
                <div>
                    <label class="block mb-2">First Name</label>
                    <input type="text" name="first_name" placeholder="First Name" class="w-full py-2 px-3 rounded-xl bg-white text-gray-900 border-2 border-gray-300 focus:outline-none focus:border-blue-500">
                </div>

                <!-- MIDDLE NAME -->
                <div>
                    <label class="block mb-2">Middle Name</label>
                    <input type="text" name="middle_name" placeholder="Middle Name" class="w-full py-2 px-3 rounded-xl bg-white text-gray-900 border-2 border-gray-300 focus:outline-none focus:border-blue-500">
                </div>

                <!-- LAST NAME -->
                <div>
                    <label class="block mb-2">Last Name</label>
                    <input type="text" name="last_name" placeholder="Last Name" class="w-full py-2 px-3 rounded-xl bg-white text-gray-900 border-2 border-gray-300 focus:outline-none focus:border-blue-500">
                </div>

                <!-- DOB -->
                <div>
                    <label class="block mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" class="w-full py-2 px-3 rounded-xl bg-white text-gray-900 border-2 border-gray-300 focus:outline-none focus:border-blue-500">
                </div>

                <!-- GENDER -->
                <div>
                    <label class="block mb-2">Gender</label>
                    <select name="gender" class="w-full py-2 px-3 rounded-xl bg-white text-gray-900 border-2 border-gray-300 focus:outline-none focus:border-blue-500">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- EMAIL -->
                <div class="md:col-span-2">
                    <label class="block mb-2">Email</label>
                    <input type="email" name="email" placeholder="Email" class="w-full py-2 px-3 rounded-xl bg-white text-gray-900 border-2 border-gray-300 focus:outline-none focus:border-blue-500">
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="block mb-2">Password</label>
                    <input type="password" name="password" placeholder="Password" class="w-full py-2 px-3 rounded-xl bg-white text-gray-900 border-2 border-gray-300 focus:outline-none focus:border-blue-500">
                </div>

                <!-- CONFIRM PASSWORD -->
                <div>
                    <label class="block mb-2">Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" class="w-full py-2 px-3 rounded-xl bg-white text-gray-900 border-2 border-gray-300 focus:outline-none focus:border-blue-500">
                </div>

            </div>

            <button type="submit" class="w-full bg-blue-700 hover:bg-blue-600 py-2 px-3 rounded-3xl text-lg font-semibold transition-colors mt-6 mb-4">
                Register
            </button>

            <p class="text-center text-gray-300">
                Not part of the administration?
                <a class="text-blue-500 underline hover:text-blue-400" href="register.php">Register as a user</a>
            </p>



        </form>
    </div>
</div>

<script>
// HAMBURGER MENU (same as your code)
const hamburger = document.getElementById('hamburger');
const sideMenu = document.getElementById('side-menu');
const closeBtn = document.getElementById('close-btn');

hamburger.addEventListener('click', () => sideMenu.classList.remove('-translate-x-full'));
closeBtn.addEventListener('click', () => sideMenu.classList.add('-translate-x-full'));
sideMenu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => sideMenu.classList.add('-translate-x-full')));

// Check URL parameters for messages
const params = new URLSearchParams(window.location.search);
if (params.has("status")) {
    const status = params.get("status");
    const message = params.get("message") || "Registration successful!";
    alert(message); // or display in a div instead of alert
}


// ==========================
//  FORM VALIDATION START
// ==========================

document.querySelector("form").addEventListener("submit", function (e) {
    e.preventDefault(); // stop default submission for validation

    const firstName = document.querySelector("input[placeholder='First Name']").value.trim();
    const middleName = document.querySelector("input[placeholder='Middle Name']").value.trim();
    const lastName = document.querySelector("input[placeholder='Last Name']").value.trim();
    const dob = document.querySelector("input[type='date']").value;
    const gender = document.querySelector("select").value;
    const email = document.querySelector("input[type='email']").value.trim();
    const password = document.querySelector("input[placeholder='Password']").value;
    const confirmPass = document.querySelector("input[placeholder='Confirm Password']").value;

    // DOB validation
    let age = null;
    if (dob) {
        const birthDate = new Date(dob);
        const today = new Date();
        age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
    }

    const errors = [];

    if (!firstName) errors.push("First name is required.");
    if (!lastName) errors.push("Last name is required.");
    if (!dob) errors.push("Date of Birth is required.");
    if (dob && age < 0) errors.push("Invalid Date of Birth.");
    if (dob && age < 18) errors.push("You must be at least 18 years old.");
    if (!gender) errors.push("Gender is required.");
    if (!email) errors.push("Email is required.");
    if (email && !email.includes("@")) errors.push("Email format is invalid.");
    if (!password) errors.push("Password is required.");
    if (password.length < 6) errors.push("Password must be at least 6 characters.");
    if (password !== confirmPass) errors.push("Passwords do not match.");

    if (errors.length > 0) {
        alert(errors.join("\n"));
        return; // stop submission if there are errors
    }

    // No errors → submit the form normally to PHP
    this.submit(); // now data goes to register.php
});

</script>


</body>
</html>
