<?php
include 'db_connect.php'; // Make sure the path is correct

// Assuming you have a logged-in user ID in session
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    // Fetch user info
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    // No user logged in
    $user = null;
}

$regions = [];
$result = $conn->query("SELECT * FROM regions ORDER BY name ASC");
while($row = $result->fetch_assoc()) {
    $regions[] = $row;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Account</title>
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
      <h1 class="text-3xl md:text-4xl font-bold mb-2">Manage Your Account</h1>
      <p class="text-lg md:text-xl text-blue-100 mb-4">
        Update your personal information and ensure your profile is up-to-date.
      </p>
    </div>
    <div class="flex-1 bg-blue-600 rounded-lg p-6 text-center hidden md:flex items-center justify-center">
      <div class="text-white font-bold text-xl">
        Account Overview
      </div>
    </div>
  </div>
</section>

<!-- Navigation (desktop) -->
<nav class="hidden md:flex bg-white shadow-md flex py-2 px-2 justify-between">
    <div>
        <ul class="flex">
            <li><a href="dashboard.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Dashboard</a></li>
            <li><a href="my_account.php" class="block py-2 px-4 bg-gray-300 rounded">My Account</a></li>
            <li><a href="profiles.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Profiles</a></li>
            <li><a href="notifications.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Activity</a></li>
            <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Feedback</a></li>
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
        <li><a href="dashboard.php" class="block py-2 px-4 hover:bg-gray-300 rounded">Dashboard</a></li>
        <li><a href="my_account.php" class="block py-2 px-4 bg-gray-300 rounded">My Account</a></li>
        <li><a href="profiles.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Profiles</a></li>
        <li><a href="notifications.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Activity</a></li>
        <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Feedback</a></li>
        <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
        <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
        <li><a href="logout.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a></li>
    </ul>
</div>

<!-- Profile Form Section -->
<section class="px-5 py-8 bg-white shadow-md m-4 max-w-4xl mx-auto rounded">
    <div class="mb-6">
        <h2 class="text-2xl font-bold">My Account</h2>
        <p class="text-gray-600">Fill in the required fields to create your profile. Optional fields are marked below. Fields marked with <span class="text-red-500">*</span> are required.</p>
    </div>

    <form class="space-y-6" enctype="multipart/form-data">
        <!-- Profile Picture -->
        <div class="flex items-center gap-6">
            <img id="profile-preview" src="<?php echo $user['profile_pic'] ?: 'uploads/profile_pic_placeholder1.png'; ?>" class="w-28 h-28 rounded-full object-cover border shadow">
            <div>
                <label class="block text-gray-700 font-medium mb-1">Profile Picture (Optional)</label>
                <button type="button" id="change-btn" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
                    Change Profile
                </button>
                <input type="file" name="profile_pic" id="profile-pic" accept="image/*" class="hidden">
                <p class="text-gray-500 text-sm mt-1">Upload JPG or PNG. Max 2MB.</p>
            </div>
        </div>

        <!-- Full Name -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="first-name" class="block text-gray-700 font-medium mb-1">First Name <span class="text-red-500">*</span></label>
                <input type="text" name="first_name" id="first-name" placeholder="First Name" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($user['first_name']); ?>">
            </div>
            <div>
                <label for="middle-name" class="block text-gray-700 font-medium mb-1">Middle Name (Optional)</label>
                <input type="text" name="middle_name" id="middle-name" placeholder="Middle Name" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($user['middle_name']); ?>">
            </div>
            <div>
                <label for="last-name" class="block text-gray-700 font-medium mb-1">Last Name <span class="text-red-500">*</span></label>
                <input type="text" name="last_name" id="last-name" placeholder="Last Name" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($user['last_name']); ?>">
            </div>
        </div>

        <!-- DOB, Age, Gender -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="dob" class="block text-gray-700 font-medium mb-1">Date of Birth <span class="text-red-500">*</span></label>
                <input type="date" name="date_of_birth" id="dob" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" value="<?php echo $user['date_of_birth']; ?>">
            </div>
            <div>
                <label for="age" class="block text-gray-700 font-medium mb-1">Age</label>
                <input type="text" id="age" placeholder="Age" class="w-full border p-2 rounded bg-gray-200 text-gray-700" disabled value="<?php
                if(!empty($user['date_of_birth'])){
                    $birthDate = new DateTime($user['date_of_birth']);
                    $today = new DateTime();
                    echo $birthDate->diff($today)->y;
                }
                ?>">
            </div>
            <div>
                <label for="gender" class="block text-gray-700 font-medium mb-1">Gender <span class="text-red-500">*</span></label>
                <select id="gender" name="gender" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Gender</option>
                    <option value="Male" <?= ($user['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($user['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($user['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>

            </div>
        </div>

        <!-- Address Fields -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Region -->
            <div>
                <label for="region" class="block text-gray-700 font-medium mb-1">Region <span class="text-red-500">*</span></label>
                <select id="region" name="region_id" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
                    <option value="" disabled selected>Select Region</option>
                    <?php foreach($regions as $region): ?>
                        <option value="<?= $region['id'] ?>" <?= ($user['region_id'] ?? '') == $region['id'] ? 'selected' : '' ?>>
                            <?= $region['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Province -->
            <div>
                <label for="province" class="block text-gray-700 font-medium mb-1">Province <span class="text-red-500">*</span></label>
                <select id="province" name="province_id" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" disabled>
                    <option value="" disabled selected>Select Province</option>
                </select>
            </div>

            <!-- City/Municipality -->
            <div>
                <label for="city" class="block text-gray-700 font-medium mb-1">City / Municipality <span class="text-red-500">*</span></label>
                <select id="city" name="city_id" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" disabled>
                    <option value="" disabled selected>Select City / Municipality</option>
                </select>
            </div>

            <!-- Barangay -->
            <div>
                <label for="barangay" class="block text-gray-700 font-medium mb-1">Barangay <span class="text-red-500">*</span></label>
                <select id="barangay" name="barangay_id" required class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" disabled>
                    <option value="" disabled selected>Select Barangay</option>
                </select>
            </div>
        </div>


        <!-- ZIP Code -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="zip" class="block text-gray-700 font-medium mb-1">ZIP Code <span class="text-red-500">*</span></label>
                <input type="text" name="zip_code" id="zip" required placeholder="ZIP Code" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>">
            </div>
            <div>
                <label for="phone" class="block text-gray-700 font-medium mb-1">Phone Number <span class="text-red-500">*</span></label>
                <input type="text" name="phone_number" required pattern="[0-9]{11}" id="phone" placeholder="09123456789" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>">
            </div>
        </div>

        <!-- Email and Password -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" required placeholder="your@email.com" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
            </div>
            <div>
                <label for="password" class="block text-gray-700 font-medium mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="password" required placeholder="Password" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label for="confirm-password" class="block text-gray-700 font-medium mb-1">Confirm Password <span class="text-red-500">*</span></label>
            <input type="password" name="confirm_password" id="confirm-password" required placeholder="Confirm Password" class="w-full border p-2 rounded focus:ring-2 focus:ring-blue-500">
        </div>

        <!-- Save & Cancel buttons -->
        <div class="flex gap-2 mt-4">
            <button type="submit" class="bg-blue-700 text-white px-6 py-2 rounded hover:bg-blue-600 transition-colors">
                Save Changes
            </button>
            <button type="button" id="cancel-btn" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 transition-colors">
                Revert Changes
            </button>
        </div>
    </form>
</section>

<script>
// ------------------------------
// Hamburger / Side Menu
// ------------------------------
const hamburger = document.getElementById('hamburger');
const sideMenu = document.getElementById('side-menu');
const closeBtn = document.getElementById('close-btn');

hamburger.addEventListener('click', () => sideMenu.classList.remove('-translate-x-full'));
closeBtn.addEventListener('click', () => sideMenu.classList.add('-translate-x-full'));
sideMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => sideMenu.classList.add('-translate-x-full')));

// ------------------------------
// Profile picture preview
// ------------------------------
const changeBtn = document.getElementById('change-btn');
const fileInput = document.getElementById('profile-pic');
const profilePreview = document.getElementById('profile-preview');
let originalProfileSrc = profilePreview.src;

changeBtn.addEventListener('click', () => fileInput.click());
fileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        profilePreview.src = URL.createObjectURL(this.files[0]);
    }
});

// ------------------------------
// Select elements
// ------------------------------
const region = document.getElementById("region");
const province = document.getElementById("province");
const city = document.getElementById("city");
const barangay = document.getElementById("barangay");

// ------------------------------
// Disable / enable helpers
// ------------------------------
function disableSelect(selectElement) {
    selectElement.disabled = true;
    selectElement.classList.add("bg-gray-200");
    if(selectElement.name == 'province_id') selectElement.innerHTML = `<option value="">Select Province</option>`;
    if(selectElement.name == 'city_id') selectElement.innerHTML = `<option value="">Select City/Municipality</option>`;
    if(selectElement.name == 'barangay_id') selectElement.innerHTML = `<option value="">Select Barangay</option>`;
}

function enableSelect(selectElement) {
    selectElement.disabled = false;
    selectElement.classList.remove("bg-gray-200");
}

// ------------------------------
// Load options helper
// ------------------------------
function loadOptions(url, selectElement, selectedID = null) {
    fetch(url)
        .then(res => res.json())
        .then(data => {
            if(selectElement.name == 'province_id') selectElement.innerHTML = `<option value="">Select Province</option>`;
            if(selectElement.name == 'city_id') selectElement.innerHTML = `<option value="">Select City/Municipality</option>`;
            if(selectElement.name == 'barangay_id') selectElement.innerHTML = `<option value="">Select Barangay</option>`;
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.text = item.name;
                if (selectedID && item.id == selectedID) option.selected = true;
                selectElement.appendChild(option);
            });
            enableSelect(selectElement);
        });
}

// ------------------------------
// Saved values from PHP
// ------------------------------
const savedData = {
    firstName: <?= json_encode($user['first_name'] ?? '') ?>,
    middleName: <?= json_encode($user['middle_name'] ?? '') ?>,
    lastName: <?= json_encode($user['last_name'] ?? '') ?>,
    dob: <?= json_encode($user['date_of_birth'] ?? '') ?>,
    gender: <?= json_encode($user['gender'] ?? '') ?>,
    email: <?= json_encode($user['email'] ?? '') ?>,
    phone: <?= json_encode($user['phone_number'] ?? '') ?>,
    zip: <?= json_encode($user['zip_code'] ?? '') ?>,
    regionID: <?= json_encode($user['region_id'] ?? '') ?>,
    provinceID: <?= json_encode($user['province_id'] ?? '') ?>,
    cityID: <?= json_encode($user['city_id'] ?? '') ?>,
    barangayID: <?= json_encode($user['barangay_id'] ?? '') ?>
};

// ------------------------------
// Initialize selects
// ------------------------------
disableSelect(province);
disableSelect(city);
disableSelect(barangay);

if (savedData.regionID) {
    region.value = savedData.regionID;
    loadOptions(`load_provinces.php?region_id=${savedData.regionID}`, province, savedData.provinceID);
}
if (savedData.provinceID) {
    loadOptions(`load_cities.php?province_id=${savedData.provinceID}`, city, savedData.cityID);
}
if (savedData.cityID) {
    loadOptions(`load_barangays.php?city_id=${savedData.cityID}`, barangay, savedData.barangayID);
}

// ------------------------------
// Event listeners for dynamic selection
// ------------------------------
region.addEventListener("change", () => {
    const regionID = region.value;

    disableSelect(province);
    disableSelect(city);
    disableSelect(barangay);

    if (!regionID) return;

    loadOptions(`load_provinces.php?region_id=${regionID}`, province);
});

province.addEventListener("change", () => {
    const provinceID = province.value;

    disableSelect(city);
    disableSelect(barangay);

    if (!provinceID) return;

    loadOptions(`load_cities.php?province_id=${provinceID}`, city);
});

city.addEventListener("change", () => {
    const cityID = city.value;

    disableSelect(barangay);

    if (!cityID) return;

    loadOptions(`load_barangays.php?city_id=${cityID}`, barangay);
});

// ------------------------------
// Revert changes button
// ------------------------------
const cancelBtn = document.getElementById('cancel-btn');
cancelBtn.addEventListener('click', () => {
    // Reset text inputs
    document.getElementById('first-name').value = savedData.firstName;
    document.getElementById('middle-name').value = savedData.middleName;
    document.getElementById('last-name').value = savedData.lastName;
    document.getElementById('dob').value = savedData.dob;
    document.getElementById('gender').value = savedData.gender;
    document.getElementById('email').value = savedData.email;
    document.getElementById('phone').value = savedData.phone;
    document.getElementById('zip').value = savedData.zip;

    // Clear password fields
    document.getElementById('password').value = "";
    document.getElementById('confirm-password').value = "";

    // Reset profile picture
    profilePreview.src = originalProfileSrc;
    fileInput.value = ""; // clear file input

    // Reset selects (region → province → city → barangay)
    region.value = savedData.regionID;
    disableSelect(province);
    disableSelect(city);
    disableSelect(barangay);

    if (savedData.regionID) {
        loadOptions(`load_provinces.php?region_id=${savedData.regionID}`, province, savedData.provinceID);
    }
    if (savedData.provinceID) {
        loadOptions(`load_cities.php?province_id=${savedData.provinceID}`, city, savedData.cityID);
    }
    if (savedData.cityID) {
        loadOptions(`load_barangays.php?city_id=${savedData.cityID}`, barangay, savedData.barangayID);
    }
});

// ------------------------------
// Form submission
// ------------------------------
document.querySelector("form").addEventListener("submit", function(e) {
    e.preventDefault(); // stop form refresh

    const formData = new FormData(this);

    fetch("my_account_update.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("Profile updated successfully!");
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(err => alert("Something went wrong."));
});
</script>

</body>
</html>
