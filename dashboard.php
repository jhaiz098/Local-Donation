<?php 
include 'db_connect.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$userAccount = [];

// Fetch user info from the view
if ($user_id) {
    $stmt = $conn->prepare("
        SELECT *
        FROM vw_users_with_location
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userAccount = $result->fetch_assoc() ?? [];
    $stmt->close();
}

// Get the 3 most recent activities for this user
$stmt = $conn->prepare("
    SELECT *
    FROM activities
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 3
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$activities = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Check if account is complete
$requiredFields = ['first_name', 'last_name', 'date_of_birth', 'gender', 'email', 'region_id', 'province_id', 'city_id', 'barangay_id', 'zip_code'];
$accountComplete = true;
foreach ($requiredFields as $field) {
    if (empty($userAccount[$field])) {
        $accountComplete = false;
        break;
    }
}

// Fetch user profiles
$userProfiles = [];
if ($user_id) {
    $stmt = $conn->prepare("
        SELECT p.profile_id, p.profile_name, p.profile_type
        FROM profile_members pm
        JOIN profiles p ON pm.profile_id = p.profile_id
        WHERE pm.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $userProfiles = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Set defaults for optional fields
$userAccount['phone_number'] = $userAccount['phone_number'] ?? "N/A";
$userAccount['middle_name'] = $userAccount['middle_name'] ?? "N/A";
$userAccount['age'] = $userAccount['age'] ?? "";


$stmt = $conn->prepare("SELECT reason_id, reason_name FROM reasons ORDER BY reason_name");
$stmt->execute();
$reasons = $stmt->get_result();

// ===== PAGINATION =====
$limit = 5; // rows per page
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$countStmt = $conn->prepare("
    SELECT COUNT(DISTINCT entry_id) AS total
    FROM vw_donation_entries
");
$countStmt->execute();
$totalRows = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();

$totalPages = ceil($totalRows / $limit);
$donationEntries = [];

$stmt = $conn->prepare("
    SELECT
        entry_id,
        entry_type,
        details,
        target_area,
        created_at,
        profile_name,

        reason_id,
        reason_name,

        region_name,
        province_name,
        city_name,
        barangay_name
    FROM vw_donation_entries
    GROUP BY entry_id
    ORDER BY created_at DESC
    LIMIT ? OFFSET ?
");

$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $donationEntries[] = $row;
}
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
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
      <h1 class="text-3xl md:text-4xl font-bold mb-2">Welcome to Your Dashboard!</h1>
      <p class="text-lg md:text-xl text-blue-100 mb-4">
        View your activities, recent updates, and navigate through your account easily.
      </p>
    </div>
    <div class="flex-1 bg-blue-600 rounded-lg p-6 text-center hidden md:flex items-center justify-center">
      <div class="text-white font-bold text-xl">
        Dashboard Overview
      </div>
    </div>
  </div>
</section>

<!-- Navigation (desktop) -->
<nav class="hidden md:flex bg-white shadow-md flex py-2 px-2 justify-between">
    <div>
        <ul class="flex">
            <li><a href="dashboard.php" class="block py-2 px-4 bg-gray-300 rounded">Dashboard</a></li>
            <li><a href="my_account.php" class="block py-2 px-4 hover:bg-gray-200 rounded">My Account</a></li>
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
        <li><a href="dashboard.php" class="block py-2 px-4 bg-gray-300 rounded">Dashboard</a></li>
        <li><a href="my_account.php" class="block py-2 px-4 hover:bg-gray-200 rounded">My Account</a></li>
        <li><a href="profiles.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Profiles</a></li>
        <li><a href="notifications.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Activity</a></li>
        <li><a href="feedback.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Feedback</a></li>
        <li><a href="settings.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Settings</a></li>
        <li><a href="help.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Help/FAQ</a></li>
        <li><a href="logout.php" class="block py-2 px-4 hover:bg-gray-200 rounded">Logout</a></li>
    </ul>
</div>

<!-- Dashboard Section -->
 <div id="main-content">
    <section class="px-5 py-8 bg-white shadow-md m-4">
    <div class="mb-6">
        <h2 class="text-2xl font-bold">Welcome, <?php echo htmlspecialchars($userAccount['first_name']); ?></h2>
        <p class="text-gray-600">Here's an overview of your account.</p>
    </div>

    <?php if (!$accountComplete): ?>
    <div class="mb-6 bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded">
        <p class="text-yellow-700 font-medium">⚠ Your account information is incomplete.</p>
        <p class="text-yellow-700 text-sm">Please update your account details before creating a profile.</p>
        <a href="my_account.php" class="text-yellow-800 font-semibold underline mt-2 inline-block">Update Now</a>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
        
        <!-- Profile Selector -->
        <div class="bg-white p-3 rounded shadow md:col-span-2 lg:col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-purple-600 text-2xl">👥</span>
                <h3 class="text-xl font-semibold">Your Profiles</h3>
            </div>

            <div id="profilesList" class="space-y-2 mb-4 text-sm text-gray-700 max-h-64 overflow-y-auto">
                <p class="text-gray-500">You currently have no profiles created.</p>
            </div>

            <button id="addProfileBtn" class="inline-block bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-600">
                + Add Profile
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-1 gap-6">
            <!-- Recent Activity -->
            <div id="activity-box" class="bg-white shadow-md rounded p-2 flex flex-col gap-2">
                <h2 class="font-bold text-lg mb-2">Recent Activities</h2>
                <?php if(count($activities) > 0): ?>
                    <?php foreach($activities as $act): ?>
                        <div class="bg-green-50 p-2 rounded shadow flex flex-col">
                            <p class="font-semibold text-sm"><?= htmlspecialchars($act['display_text']) ?></p>
                            <p class="text-gray-400 text-xs"><?= date('M d, Y H:i', strtotime($act['created_at'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500 text-sm">No recent activities.</p>
                <?php endif; ?>
            </div>

            <!-- Tips -->
            <div class="bg-white p-3 rounded shadow md:col-span-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-red-600 text-2xl">💡</span>
                    <h3 class="text-xl font-semibold">Tips & How to Use</h3>
                </div>
                    <p class="text-gray-600 text-sm">
                        Start by completing your account and creating a profile. 
                        Once you have a profile, you can request help or offer donations.
                    </p>
                </div>
            </div>
    </div>

    <div class="grid my-5 grid-cols-1 md:grid-cols-1 lg:grid-cols-1 gap-6">
        <!-- Suggested Offers & Requests -->
        <div class="bg-white p-3 rounded shadow md:col-span-1">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-blue-600 text-2xl">🤝</span>
                <h3 class="text-xl font-semibold">Offers & Requests You May Be Interested In</h3>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <!-- Reason Filter -->
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Filter by Request / Offer
                    </label>

                    <select class="w-full border rounded p-2 text-sm mb-5" name="reason_id">
                        <option value="">All Donation Entries</option>
                        <option value="offers">Offers</option>
                        <option value="requests">Requests</option>
                    </select>
                </div>

                <div>
                    <!-- Reason Filter -->
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Filter by Reason
                    </label>

                    <select class="w-full border rounded p-2 text-sm mb-5" name="reason_id">
                        <option value="">All Reasons</option>

                        <?php while ($row = $reasons->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['reason_id']) ?>">
                                <?= htmlspecialchars($row['reason_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            


            <p class="text-gray-600 text-sm">
                Select a reason to view donation offers or requests related to a specific community need.
            </p>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full border border-gray-200 rounded text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-3 py-2 border">Type</th>
                            <th class="px-3 py-2 border">Reason</th>
                            <th class="px-3 py-2 border">Details</th>
                            <th class="px-3 py-2 border target-area-col hidden">Target Area</th>
                            <th class="px-3 py-2 border">Location</th>
                            <th class="px-3 py-2 border">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($donationEntries as $entry): ?>
                            <tr class="hover:bg-gray-50">

                                <!-- Type -->
                                <td class="px-3 py-2 border font-semibold
                                    <?= strtolower($entry['entry_type']) === 'offer'
                                        ? 'text-green-600'
                                        : 'text-blue-600' ?>">
                                    <?= htmlspecialchars(ucfirst($entry['entry_type'])) ?>
                                </td>

                                <!-- Reason -->
                                <td class="px-3 py-2 border">
                                    <?= htmlspecialchars($entry['reason_name'] ?? '—') ?>
                                </td>

                                <!-- Details -->
                                <td class="px-3 py-2 border max-w-xs break-words text-gray-700">
                                    <?= htmlspecialchars($entry['details'] ?? '—') ?>
                                </td>

                                <!-- Target Area (ONLY FOR OFFERS) -->
                                <td class="px-3 py-2 border target-area-col hidden">
                                    <?php if (strtolower($entry['entry_type']) === 'offer'): ?>
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-xs">
                                            <?= htmlspecialchars(ucfirst($entry['target_area'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Location -->
                                <td class="px-3 py-2 border text-xs text-gray-600">
                                    <?= htmlspecialchars($entry['barangay_name'] ?? '') ?><br>
                                    <?= htmlspecialchars($entry['city_name'] ?? '') ?><br>
                                    <?= htmlspecialchars($entry['province_name'] ?? '') ?>
                                </td>

                                <!-- Date -->
                                <td class="px-3 py-2 border text-gray-500">
                                    <?= date("Y-m-d", strtotime($entry['created_at'])) ?>
                                </td>

                            </tr>
                            <?php endforeach; ?>

                    </tbody>
                </table>

                <?php if ($totalPages > 1): ?>
                    <div class="flex justify-center items-center gap-2 mt-6 text-sm">

                        <!-- Prev -->
                        <a href="?page=<?= max(1, $page - 1) ?>"
                        class="px-3 py-1 border rounded
                        <?= $page <= 1 ? 'text-gray-400 pointer-events-none' : 'hover:bg-gray-100' ?>">
                            Prev
                        </a>

                        <!-- Page numbers -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>"
                            class="px-3 py-1 border rounded
                            <?= $i == $page
                                    ? 'bg-blue-500 text-white'
                                    : 'hover:bg-gray-100' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <!-- Next -->
                        <a href="?page=<?= min($totalPages, $page + 1) ?>"
                        class="px-3 py-1 border rounded
                        <?= $page >= $totalPages ? 'text-gray-400 pointer-events-none' : 'hover:bg-gray-100' ?>">
                            Next
                        </a>

                    </div>
                    <?php endif; ?>

            </div>

        </div>


    </div>
    
    </section>
    
    <!-- Profile Dashboard Container -->
    <div id="profileDashboardContainer" class="bg-white shadow-md rounded m-4 p-4" style="display:none;">
        <button id="backToDashboardBtn" class="bg-gray-700 text-white px-4 py-2 rounded mb-4 hover:bg-gray-600">
            ← Back to Dashboard
        </button>
        <iframe id="profileDashboardFrame" class="w-full h-[600px] border rounded" src=""></iframe>
    </div>
</div>

<!-- Add Profile Modal -->
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

<script>
// ===== MOBILE MENU =====
const hamburger = document.getElementById('hamburger');
const sideMenu = document.getElementById('side-menu');
const closeBtn = document.getElementById('close-btn');

hamburger.addEventListener('click', () => sideMenu.classList.remove('-translate-x-full'));
closeBtn.addEventListener('click', () => sideMenu.classList.add('-translate-x-full'));
sideMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', () => sideMenu.classList.add('-translate-x-full')));

// ===== PROFILE DASHBOARD =====
const mainContent = document.getElementById("main-content");
const profileDashboardContainer = document.getElementById("profileDashboardContainer");
const profileDashboardFrame = document.getElementById("profileDashboardFrame");
const backToDashboardBtn = document.getElementById("backToDashboardBtn");
profileDashboardContainer.style.display = "none";
profileDashboardFrame.style.display = "none";

const addProfileBtn = document.getElementById('addProfileBtn');
const accountComplete = <?php echo $accountComplete ? 'true' : 'false'; ?>;

if (!accountComplete) {
    addProfileBtn.disabled = true; // disables click
    addProfileBtn.classList.remove('bg-blue-700', 'hover:bg-blue-600'); // remove original styles
    addProfileBtn.classList.add('bg-gray-400', 'cursor-not-allowed'); // add disabled styles
    addProfileBtn.title = "Complete your account information first";
}



function openProfileDashboard(profileId) {
    const dashboardSection = mainContent.querySelector('section');
    if (dashboardSection) dashboardSection.style.display = 'none';
    profileDashboardContainer.style.display = 'block';
    profileDashboardFrame.src = `profile/profile_dashboard.php?profile_id=${profileId}`;
    profileDashboardFrame.style.display = 'block';
}

backToDashboardBtn.addEventListener("click", () => {
    profileDashboardFrame.style.display = 'none';
    profileDashboardFrame.src = "";
    const dashboardSection = mainContent.querySelector('section');
    if (dashboardSection) dashboardSection.style.display = 'block';
    profileDashboardContainer.style.display = 'none';
});

// ===== ADD PROFILE MODAL =====
const profileModal = document.getElementById('profileModal');
const closeModalBtn = document.getElementById('closeModal');
const cancelProfileBtn = document.getElementById('cancelProfile');
const profileForm = document.getElementById('profileForm');
const profilesList = document.getElementById("profilesList");

const profiles = <?php echo json_encode($userProfiles); ?>;
renderProfiles();


// ===== RENDER PROFILES WITH NOTIFICATIONS & TYPE BADGE =====
function renderProfiles() {
    const profilesList = document.getElementById("profilesList");
    profilesList.innerHTML = "";

    if (profiles.length === 0) {
        profilesList.innerHTML = `<p class="text-gray-500">You currently have no profiles created.</p>`;
    } else {
        profiles.forEach(profile => {
            const div = document.createElement("div");
            div.className = "flex justify-between items-center p-2 border rounded hover:bg-gray-100 cursor-pointer";

            const nameDiv = document.createElement("div");
            nameDiv.className = "flex items-center gap-2";

            const profileName = document.createElement("span");
            profileName.textContent = profile.profile_name;
            profileName.className = "font-medium";

            const typeBadge = document.createElement("span");
            const type = profile.profile_type ? profile.profile_type.toLowerCase() : "unknown";
            typeBadge.textContent = type.charAt(0).toUpperCase() + type.slice(1);
            typeBadge.className = `text-xs px-2 py-0.5 rounded-full font-semibold ${
                profile.profile_type === "individual" ? "bg-blue-500 text-white" :
                profile.profile_type === "family" ? "bg-green-500 text-white" :
                profile.profile_type === "institution" ? "bg-purple-500 text-white" :
                profile.profile_type === "organization" ? "bg-yellow-500 text-black" : "bg-gray-300 text-black"
            }`;

            nameDiv.appendChild(profileName);
            nameDiv.appendChild(typeBadge);
            div.appendChild(nameDiv);

            div.addEventListener("click", () => openProfileDashboard(profile.profile_id));
            profilesList.appendChild(div);
        });
    }
}


// ===== USER ACCOUNT INFO =====
const userAccount = <?php echo json_encode($userAccount); ?>;

const individualProfilePic = userAccount.profilePic; // the user's actual photo
const defaultProfilePic = 'uploads/profile_pic_placeholder1.png'; // generic placeholder for non-individual profiles

// Map PHP keys to JS-friendly keys
userAccount.firstName = userAccount.first_name || '';
userAccount.middleName = userAccount.middle_name || '';
userAccount.lastName = userAccount.last_name || '';
userAccount.dob = userAccount.date_of_birth || '';
userAccount.sex = userAccount.gender || '';
userAccount.phone = userAccount.phone_number || '';
userAccount.email = userAccount.email || '';
userAccount.region = userAccount.region_id || '';
userAccount.province = userAccount.province_id || '';
userAccount.city = userAccount.city_id || '';
userAccount.barangay = userAccount.barangay_id || '';
userAccount.zip = userAccount.zip_code || '';
userAccount.profilePic = userAccount.profile_pic || 'uploads/profile_pic_placeholder1.png';

const institutionTypes = ["Barangay", "School", "Public Hospital", "LGU Office"];
const organizationTypes = ["NGO", "Charity", "Corporation", "Cooperative"];



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

// ===== SHOW / CLOSE MODAL =====
addProfileBtn.addEventListener('click', () => {
    profileModal.classList.remove('hidden');
    const selectedType = document.querySelector('input[name="profileType"]:checked');
    generateForm(selectedType ? selectedType.value : 'individual');
});
closeModalBtn.addEventListener('click', () => profileModal.classList.add('hidden'));
cancelProfileBtn.addEventListener('click', () => profileModal.classList.add('hidden'));

// ===== CHANGE FORM DYNAMICALLY =====
document.querySelectorAll('input[name="profileType"]').forEach(radio => {
    radio.addEventListener('change', () => generateForm(radio.value));
});

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
        formData.append('userProfilePic', userAccount.profilePic);
        formData.append('firstName', userAccount.firstName);
        formData.append('middleName', userAccount.middleName);
        formData.append('lastName', userAccount.lastName);
        formData.append('dob', userAccount.dob);
        formData.append('gender', userAccount.sex);
        formData.append('phone', userAccount.phone);
        formData.append('email', userAccount.email);
        formData.append('region', userAccount.region);
        formData.append('province', userAccount.province);
        formData.append('city', userAccount.city);
        formData.append('barangay', userAccount.barangay);
        formData.append('zip', userAccount.zip);
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
            
            // Update local profiles for UI immediately
            let profileName = type === "individual" ? `${userAccount.firstName} ${userAccount.lastName}` : formData.get('profileName') || "Unnamed Profile";
            profiles.push({
                profile_id: data.profile_id || Date.now(), // fallback if PHP doesn't return ID
                profile_type: type,
                profile_name: profileName,
                profile_pic: data.profile_pic || "uploads/profile_pic_placeholder1.png",
                notificationCount: 1
            });
            
            renderProfiles();
        } else {
            alert("Error: " + (data.message || "Unknown error"));
        }
    })
    .catch(err => {
        console.error("Fetch error:", err);
        alert("Failed to save profile. Check console for errors.");
    });
});


// ===== DYNAMIC PROFILE FORM GENERATION =====
function generateForm(type){
    profileForm.innerHTML = ""; // reset form

    profileForm.innerHTML = `
        <div class="flex flex-col items-center mb-4">
            <label class="block font-medium mb-2">Profile Picture:</label>
            <img id="profilePicPreview" src="${type === 'individual' ? userAccount.profilePic : defaultProfilePic}" class="w-24 h-24 rounded-full border mb-2">
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
        fileInput.accept = "image/*";
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
            <div><label class="block font-medium">First Name:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.firstName}</span></div>
            <div><label class="block font-medium">Middle Name:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.middleName}</span></div>
            <div><label class="block font-medium">Last Name:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.lastName}</span></div>
            <div><label class="block font-medium">Date of Birth:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.dob}</span></div>
            <div><label class="block font-medium">Age:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.age}</span></div>
            <div><label class="block font-medium">Gender:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.sex}</span></div>
            <div><label class="block font-medium">Phone:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.phone}</span></div>
            <div><label class="block font-medium">Email:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.email}</span></div>
        `);

        profileForm.insertAdjacentHTML('beforeend', `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div><label class="block font-medium">Region:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.region_name}</span></div>
                <div><label class="block font-medium">Street / Barangay:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.barangay_name}</span></div>
                <div><label class="block font-medium">City / Municipality:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.city_name}</span></div>
                <div><label class="block font-medium">Province:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.province_name}</span></div>
                <div><label class="block font-medium">ZIP Code:</label><span class="block p-2 bg-gray-100 rounded">${userAccount.zip}</span></div>
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
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const hasOffer = [...document.querySelectorAll("td")]
        .some(td => td.textContent.trim().toLowerCase() === "offer");

    if (hasOffer) {
        document.querySelectorAll(".target-area-col")
            .forEach(el => el.classList.remove("hidden"));
    }
});
</script>


</body>
</html>
