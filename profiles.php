<?php
require 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// --- Fetch user account info (with location names and age) from the view ---
$stmt = $conn->prepare("
    SELECT *, YEAR(CURDATE()) - YEAR(date_of_birth) AS age
    FROM vw_users_with_location
    WHERE user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userAccount = $result->fetch_assoc() ?? [];
$stmt->close();

// Set default values for optional fields
$userAccount['phone_number'] = $userAccount['phone_number'] ?? "N/A";
$userAccount['middle_name'] = $userAccount['middle_name'] ?? "N/A";
$userAccount['age'] = $userAccount['age'] ?? '';

// --- Fetch all profiles for this user in one query ---
$stmt = $conn->prepare("
    SELECT DISTINCT p.profile_id, p.profile_name, p.profile_pic, p.profile_type, p.created_at
    FROM profile_members pm
    JOIN profiles p ON pm.profile_id = p.profile_id
    WHERE pm.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$userProfiles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Now $userAccount has all user info (with location names & age)
// and $userProfiles contains all profiles in one simple array
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profiles</title>
<script src="src/tailwind.js"></script>
<link rel="stylesheet" href="src/style.css">
</head>
<body class="bg-gray-100">

<!-- Header (unchanged) -->
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
      <h1 class="text-3xl md:text-4xl font-bold mb-2">Your Profiles</h1>
      <p class="text-lg md:text-xl text-blue-100 mb-4">
        Manage all profiles linked to your account.
      </p>
    </div>
    <div class="flex-1 bg-blue-600 rounded-lg p-6 text-center hidden md:flex items-center justify-center">
      <div class="text-white font-bold text-xl">
        Profile Overview
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
            <li><a href="profiles.php" class="block py-2 px-4 bg-gray-300 rounded">Profiles</a></li>
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
        <li><a href="my_account.php" class="block py-2 px-4 hover:bg-gray-200 rounded">My Account</a></li>
        <li><a href="profiles.php" class="block py-2 px-4 bg-gray-300 rounded">Profiles</a></li>
        <li><a href="notifications.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Activity</a></li>
        <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Feedback</a></li>
        <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
        <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
        <li><a href="logout.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a></li>
    </ul>
</div>

<div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-50 hidden overflow-auto pt-20">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl font-bold">&times;</button>
        <h2 class="text-2xl font-semibold mb-4">Add New Profile</h2>
        
        <!-- Profile Type -->
        <div class="mb-4">
            <label class="font-medium">Profile Type:</label>
            <div class="flex gap-4 mt-2">
                <label><input type="radio" name="profileType" value="individual" checked> Individual</label>
                <label><input type="radio" name="profileType" value="family"> Family</label>
                <label><input type="radio" name="profileType" value="institution"> Community Institution</label>
                <label><input type="radio" name="profileType" value="organization"> Organization</label>
            </div>
        </div>

        <!-- Dynamic Form -->
        <form id="profileForm" enctype="multipart/form-data" class="space-y-4"></form>

        <div class="flex justify-end gap-2 mt-4">
            <button id="cancelProfile" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
            <button id="saveProfile" class="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-600">Save Profile</button>
        </div>
    </div>
</div>

<!-- Profiles List Section -->
<section id="profilesList" class="px-5 py-8 bg-white shadow-md m-4 max-w-6xl mx-auto rounded">

    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold">Your Profiles</h2>
        <button id="addProfileBtn" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-600 transition">
            + Add Profile
        </button>
    </div>

    <!-- Profiles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($userProfiles as $profile): 
            // Determine badge color based on profile type
            switch($profile['profile_type']){
                case 'individual':
                    $badgeClass = 'bg-blue-500 text-white';
                    break;
                case 'family':
                    $badgeClass = 'bg-green-500 text-white';
                    break;
                case 'institution':
                    $badgeClass = 'bg-purple-500 text-white';
                    break;
                case 'organization':
                    $badgeClass = 'bg-yellow-500 text-black';
                    break;
                default:
                    $badgeClass = 'bg-gray-300 text-black';
            }
            // echo $profile['profile_id'];
            // echo $profile['profile_pic'];
        ?>
            <div class="profile-card bg-gray-50 p-4 rounded shadow flex flex-col items-center text-center cursor-pointer"
         data-id="<?= $profile['profile_id'] ?>">
                <img src="<?= !empty($profile['profile_pic']) ? $profile['profile_pic'] : 'uploads/profile_pic_placeholder1.png' ?>" alt="Profile Picture" class="w-24 h-24 rounded-full mb-3 object-cover">

                <h3 class="font-semibold text-lg mb-1"><?= htmlspecialchars($profile['profile_name']) ?></h3>
        
                Created At <?= date('Y-m-d', strtotime($profile['created_at'])) ?>

                <!-- Profile Type Badge -->
                <span class="text-xs px-2 py-0.5 rounded-full font-semibold <?= $badgeClass ?>">
                    <?= htmlspecialchars(ucfirst($profile['profile_type'])) ?>
                </span>

                <div class="flex gap-2 mt-3">
                    <button data-id="<?= $profile['profile_id'] ?>" class="delete-btn bg-red-600 text-white px-3 py-1 rounded hover:bg-red-500 text-sm">Delete</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>



</section>

<!-- Profile Dashboard Section (Hidden Initially) -->
<section id="profileDashboardContainer" class="px-5 py-8 bg-white shadow-md m-4 max-w-6xl mx-auto rounded hidden">
    <button id="backToProfiles" class="mb-4 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
        &larr; Back to Profiles
    </button>
    <iframe id="profileDashboardFrame" src="" class="w-full h-[600px] border rounded hidden"></iframe>
</section>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm text-center">
        <h2 class="text-xl font-semibold mb-4">Are you sure you want to delete this profile?</h2>
        <div class="flex justify-center gap-4">
            <button id="cancelDelete" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
            <button id="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-500">Delete</button>
        </div>
    </div>
</div>

<script>
const hamburger = document.getElementById('hamburger');
const sideMenu = document.getElementById('side-menu');
const closeBtn = document.getElementById('close-btn');

hamburger.addEventListener('click', () => sideMenu.classList.remove('-translate-x-full'));
closeBtn.addEventListener('click', () => sideMenu.classList.add('-translate-x-full'));
sideMenu.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => sideMenu.classList.add('-translate-x-full'));
});

// ===== ADD PROFILE MODAL =====
const profileModal = document.getElementById('profileModal');
const addProfileBtn = document.getElementById('addProfileBtn');
const closeModalBtn = document.getElementById('closeModal');
const cancelProfileBtn = document.getElementById('cancelProfile');

// ===== SHOW / CLOSE MODAL =====
addProfileBtn.addEventListener('click', () => {
    console.log("asdasd");
    profileModal.classList.remove('hidden');
    const selectedType = document.querySelector('input[name="profileType"]:checked');
    generateForm(selectedType ? selectedType.value : 'individual');
    console.log("asdas")
});
closeModalBtn.addEventListener('click', () => profileModal.classList.add('hidden'));
cancelProfileBtn.addEventListener('click', () => profileModal.classList.add('hidden'));

// ===== CHANGE FORM DYNAMICALLY =====
document.querySelectorAll('input[name="profileType"]').forEach(radio => {
    radio.addEventListener('change', () => generateForm(radio.value));
});

const defaultProfilePic = "uploads/profile_pic_placeholder1.png";
const userAccount = <?php echo json_encode($userAccount); ?>;

const institutionTypes = ["Barangay", "School", "Public Hospital", "LGU Office"];
const organizationTypes = ["NGO", "Charity", "Corporation", "Cooperative"];

const profiles = <?php echo json_encode($userProfiles); ?>;

// ===== DYNAMIC PROFILE FORM GENERATION =====
function generateForm(type){
    profileForm.innerHTML = ""; // reset form
    profileForm.innerHTML = `
        <div class="flex flex-col items-center mb-4">
            <label class="block font-medium mb-2">Profile Picture:</label>
            <img id="profilePicPreview" src="${type === 'individual' ? userAccount.profile_pic : defaultProfilePic}" class="w-24 h-24 rounded-full border mb-2">
        </div>
    `;

    if(type !== "individual"){
        const picButtons = document.createElement('div');
        picButtons.className = "flex gap-2 justify-center mb-4";

        const addPicBtn = document.createElement('button');
        addPicBtn.type = "button";
        addPicBtn.textContent = "Add Profile Picture";
        addPicBtn.className = "px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-600";
        picButtons.appendChild(addPicBtn);

        const resetBtn = document.createElement('button');
        resetBtn.type = "button";
        resetBtn.textContent = "Reset Picture";
        resetBtn.className = "px-4 py-2 bg-gray-300 rounded hover:bg-gray-400";
        picButtons.appendChild(resetBtn);

        profileForm.appendChild(picButtons);

        const fileInput = document.createElement('input');
        fileInput.type = "file";
        fileInput.accept = "uploads/*";
        fileInput.name = "profilePic"; // ✅ important so PHP sees it
        fileInput.className = "hidden";
        profileForm.appendChild(fileInput);


        addPicBtn.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', (e) => {
            if(e.target.files && e.target.files[0]){
                const reader = new FileReader();
                reader.onload = ev => document.getElementById('profilePicPreview').src = ev.target.result;
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        resetBtn.addEventListener('click', () => {
            document.getElementById('profilePicPreview').src = userAccount.profilePic;
            fileInput.value = "";
        });
    }

    // INDIVIDUAL
    if(type === "individual"){
        profileForm.insertAdjacentHTML('beforeend', `
            <div><label class="block font-medium">First Name:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.first_name}</span></div>
            <div><label class="block font-medium">Middle Name:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.middle_name}</span></div>
            <div><label class="block font-medium">Last Name:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.last_name}</span></div>
            <div><label class="block font-medium">Date of Birth:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.date_of_birth}</span></div>
            <div><label class="block font-medium">Age:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.age}</span></div>
            <div><label class="block font-medium">Gender:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.gender}</span></div>
            <div><label class="block font-medium">Phone:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.phone_number}</span></div>
            <div><label class="block font-medium">Email:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.email}</span></div>
        `);

        profileForm.insertAdjacentHTML('beforeend', `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div><label class="block font-medium">Region:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.region_name}</span></div>
                <div><label class="block font-medium">Street / Barangay:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.barangay_name}</span></div>
                <div><label class="block font-medium">City / Municipality:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.city_name}</span></div>
                <div><label class="block font-medium">Province:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.province_name}</span></div>
                <div><label class="block font-medium">ZIP Code:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.zip_code}</span></div>
            </div>
        `);
    }

    // FAMILY
    else if(type === "family"){
        profileForm.insertAdjacentHTML('beforeend', `
            <div><label class="block font-medium">Household Name:</label><input type="text" name="profileName" placeholder="Household Name" class="w-full border p-2 rounded" required></div>
            <div><label class="block font-medium">Contact Person:</label><input type="text" placeholder="Contact Person" name="contactPerson" class="w-full border p-2 rounded" required></div>
            <div><label class="block font-medium">Phone Number:</label><input type="text" pattern="[0-9]{11}" name="phoneNumber" placeholder="Phone Number" class="w-full border p-2 rounded" required></div>
            <div><label class="block font-medium">Email Address:</label><input type="email" placeholder="Email Address" name="emailAddress" class="w-full border p-2 rounded" required></div>
        `);
    }

    // INSTITUTION
    else if(type === "institution"){
        profileForm.insertAdjacentHTML('beforeend', `
            <div><label class="block font-medium">Institution Type:</label>
                <select name="institutionType" class="w-full border p-2 rounded" required>
                    <option disabled selected>Select Institution Type...</option>
                    ${institutionTypes.map(i=>`<option>${i}</option>`).join('')}
                </select>
            </div>
            <div><label class="block font-medium">Institution Name:</label><input type="text" placeholder="Institution Name" name="profileName" class="w-full border p-2 rounded" required></div>
            <div><label class="block font-medium">Contact Person:</label><input type="text" placeholder="Contact Person" name="contactPerson" class="w-full border p-2 rounded" required></div>
            <div><label class="block font-medium">Phone Number:</label><input type="text" pattern="[0-9]{11}" placeholder="Phone Number" name="phoneNumber" class="w-full border p-2 rounded" required></div>
            <div><label class="block font-medium">Email Address:</label><input type="email" placeholder="Email Address" name="emailAddress" class="w-full border p-2 rounded" required></div>
        `);
    }

    // ORGANIZATION
    else if(type === "organization"){
        profileForm.insertAdjacentHTML('beforeend', `
            <div><label class="block font-medium">Organization Type:</label>
                <select name="organizationType" class="w-full border p-2 rounded" required>
                    <option disabled selected>Select Organization Type...</option>
                    ${organizationTypes.map(o=>`<option>${o}</option>`).join('')}
                </select>
            </div>
            <div><label class="block font-medium">Organization Name:</label><input type="text" placeholder="Organization Name" name="profileName" class="w-full border p-2 rounded" required></div>
            <div><label class="block font-medium">Contact Person:</label><input type="text" placeholder="Contact Person" name="contactPerson" class="w-full border p-2 rounded" required></div>
            <div><label class="block font-medium">Phone Number:</label><input type="text" pattern="[0-9]{11}" placeholder="Phone Number" name="phoneNumber" class="w-full border p-2 rounded" required></div>
            <div><label class="block font-medium">Email Address:</label><input type="email" placeholder="Email Address" name="emailAddress" class="w-full border p-2 rounded" required></div>
            <div><label class="block font-medium">SEC/DTI Registration:</label><input type="text" placeholder="SEC/DTI Registration" name="registration" class="w-full border p-2 rounded" required></div>
        `);
    }

    // Address fields for all non-individual profiles
    if(type !== "individual"){
        // Insert the address HTML first
        profileForm.insertAdjacentHTML('beforeend', `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block font-medium">Region:</label>
                    <select name="region" id="region" class="w-full border p-2 rounded">
                        <option value="" disabled selected>Select Region...</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Province:</label>
                    <select name="province" id="province" class="w-full border p-2 rounded" disabled>
                        <option value="" disabled selected>Select Province...</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">City / Municipality:</label>
                    <select name="city" id="city" class="w-full border p-2 rounded" disabled>
                        <option value="" disabled selected>Select City...</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Barangay:</label>
                    <select name="barangay" id="barangay" class="w-full border p-2 rounded" disabled>
                        <option value="" disabled selected>Select Barangay...</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">ZIP Code:</label>
                    <input type="text" id="zip" placeholder="Zip Code" name="zip" class="w-full border p-2 rounded">
                </div>
            </div>
        `);

        // Query the selects
        const regionSelect = document.getElementById("region");
        const provinceSelect = document.getElementById("province");
        const citySelect = document.getElementById("city");
        const barangaySelect = document.getElementById("barangay");

        // Disable them initially (except Region)
        disableSelect(provinceSelect);
        disableSelect(citySelect);
        disableSelect(barangaySelect);

        // Load regions
        loadOptions('get_regions.php', regionSelect);

        // Event listeners
        regionSelect.addEventListener('change', () => {
            disableSelect(provinceSelect);
            disableSelect(citySelect);
            disableSelect(barangaySelect);
            loadOptions(`get_provinces.php?region_id=${regionSelect.value}`, provinceSelect);
        });

        provinceSelect.addEventListener('change', () => {
            disableSelect(citySelect);
            disableSelect(barangaySelect);
            loadOptions(`get_cities.php?province_id=${provinceSelect.value}`, citySelect);
        });

        citySelect.addEventListener('change', () => {
            disableSelect(barangaySelect);
            loadOptions(`get_barangays.php?city_id=${citySelect.value}`, barangaySelect);
        });

    }

}

function disableSelect(selectElement) {
    selectElement.disabled = true;
    selectElement.classList.add("bg-gray-200");
    selectElement.innerHTML = `<option value="">Select ${selectElement.name.charAt(0).toUpperCase() + selectElement.name.slice(1)}</option>`;
}

function enableSelect(selectElement) {
    selectElement.disabled = false;
    selectElement.classList.remove("bg-gray-200");
}

function loadOptions(url, selectElement, selectedID = null) {
    fetch(url)
        .then(res => res.json())
        .then(data => {
            selectElement.innerHTML = `<option value="">Select ${selectElement.name.charAt(0).toUpperCase() + selectElement.name.slice(1)}</option>`;
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

// ===== SAVE PROFILE =====
const saveProfileBtn = document.getElementById("saveProfile");
saveProfileBtn.addEventListener("click", () => {
    console.log("Save button clicked");
    // Trigger browser validation
    if (!profileForm.checkValidity()) {
        profileForm.reportValidity(); // shows native alerts for required fields
        return; // stop execution if form is invalid
    }

    const type = document.querySelector('input[name="profileType"]:checked').value;

    // Prevent creating a second individual profile
    if (type === "individual" && profiles.some(p => p.profile_type === "individual")) {
        alert("You already have an individual profile. Only one is allowed per account.");
        return; // stop the save
    }

    const formData = new FormData(profileForm);

    formData.append('profileType', type);

    // Individual profile
    if(type === "individual"){
        formData.append('userProfilePic', userAccount.profile_pic);
        formData.append('firstName', userAccount.first_name);
        formData.append('middleName', userAccount.middle_name);
        formData.append('lastName', userAccount.last_name);
        formData.append('dob', userAccount.date_of_birth);
        formData.append('gender', userAccount.gender);
        formData.append('phone', userAccount.phone_number);
        formData.append('email', userAccount.email);
        formData.append('region', userAccount.region_id);
        formData.append('province', userAccount.province_id);
        formData.append('city', userAccount.city_id);
        formData.append('barangay', userAccount.barangay_id);
        formData.append('zip', userAccount.zip_code);
    }
    // Family
    else if (type === "family") {
        formData.append('profileName', formData.get('profileName')); 
        formData.append('contactPerson', formData.get('contactPerson'));
        formData.append('phoneNumber', formData.get('phoneNumber'));
        formData.append('emailAddress', formData.get('emailAddress'));

        formData.append('region', document.getElementById("region").value);
        formData.append('province', document.getElementById("province").value);
        formData.append('city', document.getElementById("city").value);
        formData.append('barangay', document.getElementById("barangay").value);
        formData.append('zip', document.getElementById("zip").value);
    }

    // Institution
    else if (type === "institution") {
        formData.append('institutionType', formData.get('institutionType'));
        formData.append('institutionName', formData.get('profileName'));
        formData.append('contactPerson', formData.get('contactPerson'));
        formData.append('phoneNumber', formData.get('phoneNumber'));
        formData.append('emailAddress', formData.get('emailAddress'));

        formData.append('region', document.getElementById("region").value);
        formData.append('province', document.getElementById("province").value);
        formData.append('city', document.getElementById("city").value);
        formData.append('barangay', document.getElementById("barangay").value);
        formData.append('zip', document.getElementById("zip").value);
    }

    // Organization
    else if (type === "organization") {
        formData.append('organizationType', formData.get('organizationType'));
        formData.append('organizationName', formData.get('profileName'));
        formData.append('contactPerson', formData.get('contactPerson'));
        formData.append('phoneNumber', formData.get('phoneNumber'));
        formData.append('emailAddress', formData.get('emailAddress'));
        formData.append('registration', formData.get('registration'));

        formData.append('region', document.getElementById("region").value);
        formData.append('province', document.getElementById("province").value);
        formData.append('city', document.getElementById("city").value);
        formData.append('barangay', document.getElementById("barangay").value);
        formData.append('zip', document.getElementById("zip").value);
    }

    // Send to PHP
    // --- DEBUG: Log FormData ---
    for (let [key, value] of formData.entries()) {
        console.log(key, value);
    }

    // Send to PHP
    fetch('save_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text()) // read as text first
    .then(text => {
        if (!text) {
            console.error("Empty response from server");
            alert("Server returned empty response. Profile not saved.");
            return;
        }

        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error("Invalid JSON response:", text);
            alert("Server returned invalid data. Check console for details.");
            return;
        }

        console.log("Server response:", data);

        if (data.success) {
            alert("Profile saved successfully!");
            profileModal.classList.add('hidden');
            window.location.reload();
            // Update local profiles for UI immediately
            let profileName = type === "individual" ? `${userAccount.firstName} ${userAccount.lastName}` : formData.get('profileName') || "Unnamed Profile";
            profiles.push({
                profile_id: data.profile_id || Date.now(), // fallback if PHP doesn't return ID
                profile_type: type,
                profile_name: profileName,
                profile_pic: data.profile_pic || "images/profile_pic_placeholder1.png",
                notificationCount: 1
            });
        } else {
            alert("Error: " + (data.message || "Unknown error"));
        }
    })
    .catch(err => {
        console.error("Fetch error:", err);
        alert("Failed to save profile. Check console for errors.");
    });
});

const profileCards = document.querySelectorAll('.profile-card');
const profilesSection = document.getElementById('profilesList');
const profileDashboardContainer = document.getElementById('profileDashboardContainer');
const profileDashboardFrame = document.getElementById('profileDashboardFrame');
const backToProfiles = document.getElementById('backToProfiles');

document.querySelectorAll('.profile-card').forEach(card => {
    card.addEventListener('click', (e) => {
        // Prevent clicks on buttons inside the card
        if (e.target.tagName === 'BUTTON' || e.target.tagName === 'A') return;

        const profileId = card.getAttribute('data-id');
        openProfileDashboard(profileId);

        console.log('Profile clicked:', profileId);
    });
});

function openProfileDashboard(profileId){
    // Hide main profiles section
    profilesSection.style.display = 'none';

    // Show profile dashboard
    profileDashboardContainer.style.display = 'block';
    profileDashboardFrame.src = `profile/profile_dashboard.php?profile_id=${profileId}`;
    profileDashboardFrame.style.display = 'block';
}

// Back button
backToProfiles.addEventListener('click', () => {
    profileDashboardContainer.style.display = 'none';
    profileDashboardFrame.src = '';
    profilesSection.style.display = 'block';
});

let deleteProfileId = null; // store the profile to delete
const deleteModal = document.getElementById('deleteModal');
const cancelDelete = document.getElementById('cancelDelete');
const confirmDelete = document.getElementById('confirmDelete');

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation(); // prevent triggering card click
        deleteProfileId = btn.getAttribute('data-id'); // save ID
        deleteModal.classList.remove('hidden'); // show modal
    });
});

// Cancel button
cancelDelete.addEventListener('click', () => {
    deleteProfileId = null;
    deleteModal.classList.add('hidden');
});

// Confirm delete button
confirmDelete.addEventListener('click', () => {
    if (!deleteProfileId) return;

    fetch(`delete_profile.php?id=${deleteProfileId}`)
        .then(res => res.json())
        .then(data => {
            alert(data.message); // simple alert after delete
            if (data.success) {
                document.querySelector(`.delete-btn[data-id="${deleteProfileId}"]`).closest('.profile-card').remove();
                window.location.reload();
            }
            deleteProfileId = null;
            deleteModal.classList.add('hidden');
        })
        .catch(err => {
            console.error(err);
            alert("Failed to delete profile. Check console.");
            deleteProfileId = null;
            deleteModal.classList.add('hidden');
        });
});

</script>

</body>
</html>
