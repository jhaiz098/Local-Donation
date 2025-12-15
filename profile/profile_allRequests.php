<?php
require '../db_connect.php';

if (isset($_GET['profile_id'])) {
    $_SESSION['profile_id'] = intval($_GET['profile_id']);
} elseif (!isset($_SESSION['profile_id'])) {
    echo "No profile selected.";
    exit;
}

$profileId = $_SESSION['profile_id'];
$role = $_SESSION['role'];

// Helper function to disable links if user doesn't have permission
function isDisabled($permission, $role) {
    $permissionsMap = [
        'Manage Members' => ['owner', 'admin', 'manager'],
        'Manage Offers & Requests' => ['owner', 'admin', 'manager'],
        'View Activities' => ['owner', 'admin', 'manager', 'member'],
        'Manage Settings' => ['owner', 'admin', 'manager', 'member']
    ];
    return !in_array($role, $permissionsMap[$permission]);
}

// Get main profile info
$stmt = $conn->prepare("SELECT profile_type, profile_name, profile_pic FROM profiles WHERE profile_id = ?");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$result = $stmt->get_result();
$profileMain = $result->fetch_assoc();
$stmt->close();

if (!$profileMain) {
    echo "Profile not found.";
    exit;
}

$profileType = $profileMain['profile_type'];
$profileName = $profileMain['profile_name'];
$profilePic = !empty($profileMain['profile_pic']) ? "../" . $profileMain['profile_pic'] : "../uploads/profile_pic_placeholder1.png";

// Fetch all requests and offers using the view
$allEntries = [];
$tempEntries = [];

$sql = "SELECT * FROM vw_donation_entries ORDER BY created_at DESC";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $entry_id = $row['entry_id'];

    // Initialize entry only once
    if (!isset($tempEntries[$entry_id])) {
        $tempEntries[$entry_id] = [
            'entry_id' => $row['entry_id'],
            'type' => $row['entry_type'],
            'details' => $row['details'],
            'target_area' => $row['target_area'],
            'date' => date('Y-m-d', strtotime($row['created_at'])),
            'profile_id' => $row['profile_id'],
            'profile_name' => $row['profile_name'],
            'profile_type' => $row['profile_type'],
            'region_id' => $row['region_id'],
            'province_id' => $row['province_id'],
            'city_id' => $row['city_id'],
            'barangay_id' => $row['barangay_id'],
            'region_name' => $row['region_name'] ?? 'N/A',
            'province_name' => $row['province_name'] ?? 'N/A',
            'city_name' => $row['city_name'] ?? 'N/A',
            'barangay_name' => $row['barangay_name'] ?? 'N/A',
            'items' => []
        ];
    }

    // Add item if exists
    if ($row['item_id']) {
        $tempEntries[$entry_id]['items'][] = [
            'item_id' => $row['item_id'],
            'item_name' => $row['item_name'],
            'quantity' => $row['quantity'],
            'unit_name' => $row['unit_name'] ?: 'pcs'
        ];
    }
}

// Convert to indexed array
$allEntries = array_values($tempEntries);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Requests & Offers</title>
    <script src="../src/tailwind.js"></script>
    <link rel="stylesheet" href="../src/style2.css">
</head>
<body class="bg-gray-100 h-screen flex">
<nav class="w-64 bg-white shadow-md flex flex-col p-6">
    <div class="mb-10">
        <h1 class="text-2xl font-bold">Profile</h1>
        <p class="text-gray-500 text-sm">Dashboard Menu</p>
    </div>
    <ul class="flex flex-col gap-2 text-gray-700 font-medium">
        <li>
            <a href="profile_dashboard.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('View Activities', $role) ? 'disabled-link' : '' ?>">Profile Information</a>
        </li>
        <li>
            <a href="profile_activity.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('View Activities', $role) ? 'disabled-link' : '' ?>">Activity</a>
        </li>
        <li>
            <a href="profile_myRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">My Requests & Offers</a>
        </li>
        <li>
            <a href="profile_allRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">All Requests & Offers</a>
        </li>

        <?php if ($profileType !== 'individual'): ?>
            <li>
                <a href="profile_members.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Members', $role) ? 'disabled-link' : '' ?>">Members</a>
            </li>
        <?php endif; ?>

        <li>
            <a href="profile_settings.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Settings', $role) ? 'disabled-link' : '' ?>">Access Levels</a>
        </li>
    </ul>
</nav>

<main class="flex-1 p-6 overflow-y-auto">
<div class="flex items-center bg-white shadow p-6 rounded-lg mb-6">
    <img src="<?= htmlspecialchars($profilePic) ?>" class="w-28 h-28 rounded-full border mr-6" alt="Profile Picture">
    <div>
        <h2 class="text-3xl font-bold"><?= htmlspecialchars($profileName) ?></h2>
        <p class="text-gray-700">Profile Type: <?= htmlspecialchars(ucfirst($profileType)) ?></p>
        <p class="text-gray-500 text-sm">Profile ID: #<?= htmlspecialchars($profileId) ?></p>
    </div>
</div>

<section id="allRequests">
    <h3 class="text-2xl font-semibold mb-3">All Requests & Offers</h3>

    <!-- Requests Table -->
    <div class="bg-white shadow p-4 rounded mb-6">
        <h4 class="text-xl font-semibold mb-2">Donation Requests</h4>
        <div class="grid grid-cols-[60px_2fr_2fr_2fr_2fr_2fr] font-bold border-b-2 border-gray-300 p-2">
            <div>No.</div>
            <div>Profile</div>
            <div>Details</div>
            <div>Items</div>
            <div>Target Area</div>
            <div>Date Added</div>
        </div>
        <div id="allRequestTable" class="space-y-1"></div>
        <div class="flex justify-between items-center mt-2">
            <button id="prevReq" class="px-2 py-1 bg-gray-200 rounded">Previous</button>
            <span id="reqPage" class="text-gray-600"></span>
            <button id="nextReq" class="px-2 py-1 bg-gray-200 rounded">Next</button>
        </div>
    </div>

    <!-- Offers Table -->
    <div class="bg-white shadow p-4 rounded">
        <h4 class="text-xl font-semibold mb-2">Donation Offers</h4>
        <div class="grid grid-cols-[60px_2fr_2fr_2fr_2fr_2fr] font-bold border-b-2 border-gray-300 p-2">
            <div>No.</div>
            <div>Profile</div>
            <div>Details</div>
            <div>Items</div>
            <div>Target Area</div>
            <div>Date Added</div>
        </div>
        <div id="allOfferTable" class="space-y-1"></div>
        <div class="flex justify-between items-center mt-2">
            <button id="prevOff" class="px-2 py-1 bg-gray-200 rounded">Previous</button>
            <span id="offPage" class="text-gray-600"></span>
            <button id="nextOff" class="px-2 py-1 bg-gray-200 rounded">Next</button>
        </div>
    </div>
</section>
</main>

<script>
const requests = <?= json_encode($allEntries); ?>;
const itemsPerPage = 4;
let reqPageNum = 1;
let offPageNum = 1;

const requestTable = document.getElementById("allRequestTable");
const offerTable = document.getElementById("allOfferTable");
const reqPage = document.getElementById("reqPage");
const offPage = document.getElementById("offPage");

function getProfileColor(type){
    switch(type.toLowerCase()){ // convert to lowercase for matching
        case "individual": return "bg-blue-500 text-white";
        case "family": return "bg-green-500 text-white";
        case "organization": return "bg-orange-500 text-white";
        case "community": return "bg-violet-500 text-white";
        default: return "bg-gray-200 text-gray-800";
    }
}

function renderRequests(){
    const start = (reqPageNum-1)*itemsPerPage;
    const end = start + itemsPerPage;
    const paginated = requests.filter(r=>r.type==="request").slice(start,end);
    requestTable.innerHTML = "";

    paginated.forEach((r,i)=>{
        const row = document.createElement("div");
        row.className="grid grid-cols-[60px_2fr_2fr_2fr_2fr_2fr] gap-2 p-2 rounded border-b border-gray-100 items-start";

        const itemsText = r.items.map(it=>`<span class='inline-block bg-gray-200 px-2 py-0.5 rounded text-sm'>${it.item_name} x${it.quantity} ${it.unit_name}</span>`).join(" ");

        row.innerHTML=`
            <div class="font-medium text-gray-700 break-words whitespace-normal">${start+i+1}</div>
            <div class="break-words whitespace-normal">${r.profile_name} <span class="px-2 py-0.5 rounded-full text-sm ${getProfileColor(r.profile_type)}">${r.profile_type}</span></div>
            <div class="text-gray-800 break-words whitespace-normal">${r.details}</div>
            <div class="flex flex-wrap gap-1">${itemsText}</div>
            <div class="break-words whitespace-normal">${r.target_area}</div>
            <div class="text-gray-500 text-sm whitespace-normal">${r.date}</div>
        `;
        requestTable.appendChild(row);
    });

    const totalPages = Math.ceil(requests.filter(r=>r.type==="request").length/itemsPerPage);
    reqPage.textContent = `Page ${reqPageNum} of ${totalPages}`;
}

function renderOffers(){
    const start = (offPageNum-1)*itemsPerPage;
    const end = start + itemsPerPage;
    const paginated = requests.filter(r=>r.type==="offer").slice(start,end);
    offerTable.innerHTML = "";

    paginated.forEach((r,i)=>{
        const row = document.createElement("div");
        row.className="grid grid-cols-[60px_2fr_2fr_2fr_2fr_2fr] gap-2 p-2 rounded border-b border-gray-100 items-start";

        const itemsText = r.items.map(it=>`<span class='inline-block bg-gray-200 px-2 py-0.5 rounded text-sm'>${it.item_name} x${it.quantity} ${it.unit_name}</span>`).join(" ");

        row.innerHTML=`
            <div class="font-medium text-gray-700 break-words whitespace-normal">${start+i+1}</div>
            <div class="break-words whitespace-normal">${r.profile_name} <span class="px-2 py-0.5 rounded-full text-sm ${getProfileColor(r.profile_type)}">${r.profile_type}</span></div>
            <div class="text-gray-800 break-words whitespace-normal">${r.details}</div>
            <div class="flex flex-wrap gap-1">${itemsText}</div>
            <div class="break-words whitespace-normal">${r.target_area}</div>
            <div class="text-gray-500 text-sm whitespace-normal">${r.date}</div>
        `;
        offerTable.appendChild(row);
    });

    const totalPages = Math.ceil(requests.filter(r=>r.type==="offer").length/itemsPerPage);
    offPage.textContent = `Page ${offPageNum} of ${totalPages}`;
}

// Pagination buttons
document.getElementById("prevReq").addEventListener("click",()=>{ if(reqPageNum>1){ reqPageNum--; renderRequests(); }});
document.getElementById("nextReq").addEventListener("click",()=>{ const totalPages = Math.ceil(requests.filter(r=>r.type==="request").length/itemsPerPage); if(reqPageNum<totalPages){ reqPageNum++; renderRequests(); }});
document.getElementById("prevOff").addEventListener("click",()=>{ if(offPageNum>1){ offPageNum--; renderOffers(); }});
document.getElementById("nextOff").addEventListener("click",()=>{ const totalPages = Math.ceil(requests.filter(r=>r.type==="offer").length/itemsPerPage); if(offPageNum<totalPages){ offPageNum++; renderOffers(); }});

// Initial render
renderRequests();
renderOffers();
</script>
</body>
</html>
