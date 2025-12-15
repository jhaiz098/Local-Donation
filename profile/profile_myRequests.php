<?php
require '../db_connect.php';
error_reporting(0);
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

// Fetch items and units from database
$itemsArray = [];
$itemResult = $conn->query("SELECT i.item_id, i.item_name, u.unit_name 
                            FROM items i 
                            LEFT JOIN item_units u ON i.item_id = u.item_id 
                            ORDER BY i.item_name");

while($row = $itemResult->fetch_assoc()){
    $itemName = $row['item_name'];
    $unitName = $row['unit_name'];
    if(!isset($itemsArray[$itemName])){
        $itemsArray[$itemName] = [];
    }
    if($unitName && !in_array($unitName, $itemsArray[$itemName])){
        $itemsArray[$itemName][] = $unitName;
    }
}

$itemsJson = json_encode($itemsArray);

// ===== Fetch donation entries =====
$requests = [];

// Fetch donation entries for this profile
$entryStmt = $conn->prepare("
    SELECT 
        de.entry_id, 
        de.entry_type, 
        de.details, 
        de.created_at, 
        de.target_area, 
        p.profile_name, 
        p.profile_id, 
        p.profile_type
    FROM 
        donation_entries de
    JOIN 
        profiles p ON de.profile_id = p.profile_id
    WHERE 
        de.profile_id = ? 
    ORDER BY 
        de.created_at DESC
");

$entryStmt->bind_param("i", $profileId);
$entryStmt->execute();
$entryResult = $entryStmt->get_result();

while ($entry = $entryResult->fetch_assoc()) {
    $profile_id = $entry['profile_id'];
    $profile_type = $entry['profile_type']; // The type of profile (individual, family, etc.)

    // Initialize query for location data based on profile type
    $locationStmt = null;

    // Choose the table based on profile type
    switch ($profile_type) {
        case 'individual':
            $locationStmt = $conn->prepare("SELECT region_id, province_id, city_id, barangay_id FROM profiles_individual WHERE profile_id = ?");
            break;
        case 'family':
            $locationStmt = $conn->prepare("SELECT region_id, province_id, city_id, barangay_id FROM profiles_family WHERE profile_id = ?");
            break;
        case 'institution':
            $locationStmt = $conn->prepare("SELECT region_id, province_id, city_id, barangay_id FROM profiles_institution WHERE profile_id = ?");
            break;
        case 'organization':
            $locationStmt = $conn->prepare("SELECT region_id, province_id, city_id, barangay_id FROM profiles_organization WHERE profile_id = ?");
            break;
    }

    if ($locationStmt) {
        $locationStmt->bind_param("i", $profile_id);
        $locationStmt->execute();
        $locationResult = $locationStmt->get_result();
        $locationData = $locationResult->fetch_assoc();

                // Fetch region, province, city, and barangay names
        $regionStmt = $conn->prepare("SELECT name FROM regions WHERE id = ?");
        $regionStmt->bind_param("i", $locationData['region_id']);
        $regionStmt->execute();
        $region_name = $regionStmt->get_result()->fetch_assoc()['name'];

        $provinceStmt = $conn->prepare("SELECT name FROM provinces WHERE id = ?");
        $provinceStmt->bind_param("i", $locationData['province_id']);
        $provinceStmt->execute();
        $province_name = $provinceStmt->get_result()->fetch_assoc()['name'];

        $cityStmt = $conn->prepare("SELECT name FROM cities WHERE id = ?");
        $cityStmt->bind_param("i", $locationData['city_id']);
        $cityStmt->execute();
        $city_name = $cityStmt->get_result()->fetch_assoc()['name'];

        $barangayStmt = $conn->prepare("SELECT name FROM barangays WHERE id = ?");
        $barangayStmt->bind_param("i", $locationData['barangay_id']);
        $barangayStmt->execute();
        $barangay_name = $barangayStmt->get_result()->fetch_assoc()['name'];

        // Fetch items for this entry
        $itemsStmt = $conn->prepare("
            SELECT dei.item_entry_id, i.item_id, i.item_name, dei.quantity, dei.unit_name
            FROM donation_entry_items dei
            JOIN items i ON dei.item_id = i.item_id
            WHERE dei.entry_id = ?
        ");
        $itemsStmt->bind_param("i", $entry['entry_id']);
        $itemsStmt->execute();
        $itemsResult = $itemsStmt->get_result();

        $entryItems = [];
        while ($it = $itemsResult->fetch_assoc()) {
            $entryItems[] = [
                "item_entry_id" => $it['item_entry_id'],
                "item_id" => $it['item_id'],
                "name" => $it['item_name'],
                "quantity" => $it['quantity'],
                "unit" => $it['unit_name']
            ];
        }
        $itemsStmt->close();


        // Add location details and items to the entry data
        $requests[] = [
            "entry_id" => $entry['entry_id'],
            "type" => ucfirst($entry['entry_type']),
            "details" => $entry['details'],
            "items" => $entryItems, // Add items related to this entry
            "target_area" => $entry['target_area'] ?? "philippines",
            "date" => date("Y-m-d", strtotime($entry['created_at'])),
            "profile_name" => $entry['profile_name'],
            "profile_id" => isset($entry['profile_id']) ? $entry['profile_id'] : null,
            "profile_type" => isset($entry['profile_type']) ? $entry['profile_type'] : null,

            // Add location names
            "region_name" => $region_name ?? 'N/A',
            "province_name" => $province_name ?? 'N/A',
            "city_name" => $city_name ?? 'N/A',
            "barangay_name" => $barangay_name ?? 'N/A'
        ];
    }
}



// Fetch all requests from other profiles for matching
$otherRequests = [];

// Fetch donation entries
$otherStmt = $conn->prepare("
    SELECT de.entry_id, de.entry_type, de.details, de.target_area, de.created_at, 
           p.profile_name, p.profile_id, p.profile_type
    FROM donation_entries de
    JOIN profiles p ON de.profile_id = p.profile_id
    WHERE de.profile_id != ? 
    ORDER BY de.created_at DESC
");
$otherStmt->bind_param("i", $profileId);
$otherStmt->execute();
$otherResult = $otherStmt->get_result();

while ($entry = $otherResult->fetch_assoc()) {
    $profile_id = $entry['profile_id'];
    $profile_type = $entry['profile_type'];

    // Fetch location details based on profile type
    $locationStmt = null;
    switch ($profile_type) {
        case 'individual':
            $locationStmt = $conn->prepare("SELECT region_id, province_id, city_id, barangay_id FROM profiles_individual WHERE profile_id = ?");
            break;
        case 'family':
            $locationStmt = $conn->prepare("SELECT region_id, province_id, city_id, barangay_id FROM profiles_family WHERE profile_id = ?");
            break;
        case 'institution':
            $locationStmt = $conn->prepare("SELECT region_id, province_id, city_id, barangay_id FROM profiles_institution WHERE profile_id = ?");
            break;
        case 'organization':
            $locationStmt = $conn->prepare("SELECT region_id, province_id, city_id, barangay_id FROM profiles_organization WHERE profile_id = ?");
            break;
    }

    if ($locationStmt) {
        $locationStmt->bind_param("i", $profile_id);
        $locationStmt->execute();
        $locationResult = $locationStmt->get_result();

        // Fetch location data
        $locationData = $locationResult->fetch_assoc();
        $region_id = $locationData['region_id'];
        $province_id = $locationData['province_id'];
        $city_id = $locationData['city_id'];
        $barangay_id = $locationData['barangay_id'];

        // Fetch region, province, city, and barangay names
        $regionStmt = $conn->prepare("SELECT name FROM regions WHERE id = ?");
        $regionStmt->bind_param("i", $region_id);
        $regionStmt->execute();
        $region_name = $regionStmt->get_result()->fetch_assoc()['name'];

        $provinceStmt = $conn->prepare("SELECT name FROM provinces WHERE id = ?");
        $provinceStmt->bind_param("i", $province_id);
        $provinceStmt->execute();
        $province_name = $provinceStmt->get_result()->fetch_assoc()['name'];

        $cityStmt = $conn->prepare("SELECT name FROM cities WHERE id = ?");
        $cityStmt->bind_param("i", $city_id);
        $cityStmt->execute();
        $city_name = $cityStmt->get_result()->fetch_assoc()['name'];

        $barangayStmt = $conn->prepare("SELECT name FROM barangays WHERE id = ?");
        $barangayStmt->bind_param("i", $barangay_id);
        $barangayStmt->execute();
        $barangay_name = $barangayStmt->get_result()->fetch_assoc()['name'];


        // Fetch items for this entry
        $itemsStmt = $conn->prepare("
            SELECT dei.item_entry_id, i.item_id, i.item_name, dei.quantity, dei.unit_name
            FROM donation_entry_items dei
            JOIN items i ON dei.item_id = i.item_id
            WHERE dei.entry_id = ?
        ");
        $itemsStmt->bind_param("i", $entry['entry_id']);
        $itemsStmt->execute();
        $itemsResult = $itemsStmt->get_result();

        $entryItems = [];
        while ($it = $itemsResult->fetch_assoc()) {
            $entryItems[] = [
                "item_entry_id" => $it['item_entry_id'],
                "item_id" => $it['item_id'],
                "name" => $it['item_name'],
                "quantity" => $it['quantity'],
                "unit" => $it['unit_name']
            ];
        }
        $itemsStmt->close();

        // Combine all the data
        $otherRequests[] = [
            "entry_id" => $entry['entry_id'],
            "details" => $entry['details'],
            "items" => $entryItems, // Add items related to this entry
            "target_area" => $entry['target_area'] ?? "philippines",
            "date" => date("Y-m-d", strtotime($entry['created_at'])),
            "profile_name" => $entry['profile_name'],
            "profile_id" => $entry['profile_id'],
            "profile_type" => $entry['profile_type'],
            "region_name" => $region_name,
            "province_name" => $province_name,
            "city_name" => $city_name,
            "barangay_name" => $barangay_name
        ];
    }
}

$otherStmt->close();

// echo "<pre>";
// var_dump($otherRequests);
// echo "</pre>";

$entryStmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Requests & Offers</title>
    <script src="../src/tailwind.js"></script>
    <link rel="stylesheet" href="../src/style2.css">
</head>
<body class="bg-gray-100 h-screen flex">

<!-- LEFT NAVIGATION -->
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
    <!-- PROFILE HEADER -->
    <div class="flex items-center bg-white shadow p-6 rounded-lg mb-6">
        <img src="<?= htmlspecialchars($profilePic) ?>"
             class="w-28 h-28 rounded-full border mr-6"
             alt="Profile Picture">
        <div>
            <h2 class="text-3xl font-bold"><?= htmlspecialchars($profileName) ?></h2>
            <p class="text-gray-700">Profile Type: <?= htmlspecialchars(ucfirst($profileType))?></p>
            <p class="text-gray-500 text-sm">Profile ID: #<?= htmlspecialchars($profileId) ?></p>
        </div>
    </div>

    <!-- MY REQUESTS & OFFERS -->
    <section id="myRequests">
        <button id="addRequestBtn" class="mb-3 px-3 py-1 bg-green-500 text-white rounded">Add New Request/Offer</button>

        <!-- REQUESTS TABLE -->
        <div class="bg-white shadow p-4 rounded mb-6">
            <h4 class="text-xl font-semibold mb-2">My Donation Requests</h4>
            <div class="grid grid-cols-[60px_2fr_1fr_150px_100px] font-bold border-b-2 border-gray-300 p-2 text-sm">
                <div>No.</div>
                <div>Details</div>
                <div>Items</div>
                <!-- <div>Target Area</div> -->
                <div>Date Added</div>
            </div>
            <div id="requestTable" class="space-y-1 text-sm"></div>
        </div>

        <!-- OFFERS TABLE -->
        <div class="bg-white shadow p-4 rounded">
            <h4 class="text-xl font-semibold mb-2">My Donation Offers</h4>
            <div class="grid grid-cols-[60px_2fr_1fr_150px_100px] font-bold border-b-2 border-gray-300 p-2 text-sm">
                <div>No.</div>
                <div>Details</div>
                <div>Items</div>
                <div>Target Area</div>
                <div>Date Added</div>
            </div>
            <div id="offerTable" class="space-y-1 text-sm"></div>
        </div>
    </section>

    <!-- Donation Modal -->
    <div id="donationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded shadow-lg w-96 max-h-[90vh] flex flex-col">
            <h3 class="text-xl font-semibold mb-4">Confirm Donation</h3>
            
            <!-- Profile Name and Details -->
            <div class="mb-4">
                <p><strong>Profile Name:</strong> <span id="donorProfileName"></span></p>
                <p><strong>Details:</strong> <span id="donorDetails"></span></p>
            </div>
            
            <!-- Table for Items -->
            <div class="overflow-x-auto mb-4">
                <table class="table-auto w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2">No.</th>
                            <th class="p-2">Item</th>
                            <th class="p-2">Quantity Needed</th>
                            <th class="p-2">Quantity Available</th>
                            <th class="p-2">Donation Quantity</th>
                            <th class="p-2">Unit</th>
                        </tr>
                    </thead>
                    <tbody id="donationItemsTable">
                        <!-- Rows will be populated dynamically -->
                    </tbody>
                </table>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" id="donationModalCancel" class="px-3 py-1 bg-gray-200 rounded text-sm">Cancel</button>
                <button type="button" id="donationModalConfirm" class="px-3 py-1 bg-green-500 text-white rounded text-sm">Confirm Donation</button>
            </div>
        </div>
    </div>

    <div id="profileModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-[42rem] max-w-full relative">
            <button id="closeProfileModal" class="absolute top-2 right-2 text-gray-600 hover:text-black">&times;</button>

            <!-- Header with type badge -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold">Profile Details</h2>
                <span id="profileTypeBadge" class="px-3 py-1 rounded text-white font-semibold text-sm"></span>
            </div>

            <!-- Profile content -->
            <div id="profileModalContent" class="text-gray-700 gap-4">
                Loading...
            </div>
        </div>
    </div>





</main>

<script>

const otherRequestsJS = <?= json_encode($otherRequests) ?>;
// console.log(otherRequestsJS); 
// Existing JavaScript code to render requests and offers
const requests = <?= json_encode($requests) ?>;
const predefinedItems = <?= $itemsJson ?>;
const requestTable = document.getElementById("requestTable");
const offerTable = document.getElementById("offerTable");

console.log("Requests:", requests);
console.log("Other Requests:", otherRequestsJS);

function renderRequests() {
    requestTable.innerHTML = "";
    offerTable.innerHTML = "";
    let reqNo = 1;

    const myRequests = (requests && Array.isArray(requests)) ? requests.filter(r => r.type === "Request") : [];
    const myOffers = (requests && Array.isArray(requests)) ? requests.filter(r => r.type === "Offer") : [];

    // --- Requests ---
    myRequests.forEach(r => {
        const row = document.createElement("div");
        row.className = "grid grid-cols-[60px_2fr_1fr_150px_100px] gap-2 p-2 rounded hover:bg-gray-50 border-b border-gray-100 items-start cursor-pointer text-sm";

        const itemsText = (r.items && Array.isArray(r.items)) 
            ? r.items.map(it => `<span class='inline-block bg-gray-200 px-1 py-0.5 rounded text-xs'>${it.name} x${it.quantity} ${it.unit || 'pcs'}</span>`).join(" ") 
            : '';

        row.innerHTML = `
            <div class="font-medium text-gray-700">${reqNo}</div>
            <div class="text-gray-800 break-words">${r.details || "---"}</div>
            <div class="flex flex-wrap gap-1">${itemsText}</div>
            <div class="text-gray-500">${r.date}</div>
        `;
        requestTable.appendChild(row);
        reqNo++;

        row.addEventListener("click", () => openModal(r));
    });

    // --- Offers ---
    myOffers.forEach((o, offIndex) => {
        const row = document.createElement("div");
        row.className = "grid grid-cols-[60px_2fr_1fr_150px_100px] gap-2 p-2 rounded hover:bg-gray-50 border-b border-gray-100 items-start cursor-pointer text-sm";
        row.dataset.profileId = o.profile_id;

        const itemsText = (o.items && Array.isArray(o.items)) 
            ? o.items.map(it => `<span class='inline-block bg-gray-200 px-1 py-0.5 rounded text-xs'>${it.name} x${it.quantity} ${it.unit || 'pcs'}</span>`).join(" ") 
            : '';

        row.addEventListener("click", (e) => {
            if (e.target.closest(".donate-btn") || e.target.closest(".ml-16")) return;
            openModal(o);
        });

        row.innerHTML = `
            <div class="font-medium text-gray-700">${offIndex + 1}</div>
            <div class="text-gray-800 break-words">${o.details || "---"}</div>
            <div class="flex flex-wrap gap-1">${itemsText}</div>
            <div class="text-gray-700">${o.target_area || 'philippines'}</div>
            <div class="text-gray-500">${o.date}</div>
        `;
        offerTable.appendChild(row);

        const matchingRequests = findMatchesForOffer(o);
        if (matchingRequests.length > 0) {
            const matchDiv = document.createElement("div");
            matchDiv.className = "ml-16 mt-1 text-blue-600 text-sm font-semibold cursor-pointer";
            matchDiv.innerText = `View ${matchingRequests.length} Match${matchingRequests.length > 1 ? 'es' : ''}`;

            const expandedDiv = document.createElement("div");
            expandedDiv.className = "ml-16 mt-1 border border-gray-200 rounded text-xs hidden";

            matchingRequests.forEach((m, i) => {
                const matchRow = document.createElement("div");
                matchRow.className = "grid grid-cols-[60px_2fr_2fr_150px_100px_100px] gap-1 p-1 border-b border-gray-100 items-center text-xs";
                matchRow.innerHTML = `
                    <div class="text-gray-700">${i + 1}</div>
                    <div class="text-blue-700"><a href="#" class="profile-link" data-profile-id="${m.profile_id}">${m.profile_name} (${m.profile_type})</a></div>
                    <div class="text-gray-800 break-words">${m.details || "---"}</div>
                    <div class="flex flex-wrap gap-1">
                    ${(m.items && Array.isArray(m.items)) 
                        ? m.items.map(it => `<span class='inline-block bg-gray-100 px-1 py-0.5 rounded text-xs'>${it.name} x${it.quantity} ${it.unit || 'pcs'}</span>`).join(' ') 
                        : ''}
                    </div>
                    <div class="text-gray-500">${m.date}</div>
                    <div><button class="px-2 py-0.5 bg-green-500 text-white rounded donate-btn" data-entry-id="${m.entry_id}">Donate</button></div>
                `;
                expandedDiv.appendChild(matchRow);
            });

            matchDiv.addEventListener("click", () => expandedDiv.classList.toggle("hidden"));
            offerTable.appendChild(matchDiv);
            offerTable.appendChild(expandedDiv);
        }
    });

    // Add event listeners to donation buttons after offers are rendered
    document.querySelectorAll('.donate-btn').forEach(button => {
        button.addEventListener("click", function() {
            const entryId = this.getAttribute("data-entry-id");
            openDonationModal(entryId);
        });
    });
}

renderRequests();


// ===== MODAL =====
const modal = document.createElement("div");
modal.className = "fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50";
modal.innerHTML = `
    <div class="bg-white p-6 rounded shadow-lg w-96 max-h-[90vh] flex flex-col">
        <h3 class="text-xl font-semibold mb-4" id="modalTitle">Add Request/Offer</h3>
        <form id="modalForm" class="space-y-3 overflow-y-auto flex-1 text-sm">
            <div>
                <label class="block text-gray-700">Type</label>
                <select id="modalType" class="w-full border rounded p-1">
                    <option value="Request">Request</option>
                    <option value="Offer">Offer</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700">Details</label>
                <textarea id="modalDetails" class="w-full border rounded p-1 h-20 text-sm"></textarea>
            </div>
            <div id="modalTargetArea2">
                <label class="block text-gray-700">Target Area</label>
                <select id="modalTargetArea" class="w-full border rounded p-1 text-sm">
                    <option value="philippines">Entire Philippines</option>
                    <option value="region">Same Region</option>
                    <option value="province">Same Province</option>
                    <option value="city">Same City/Municipality</option>
                    <option value="barangay">Same Barangay</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-700">Items</label>
                <div id="itemsContainer" class="space-y-2 max-h-64 overflow-y-auto pr-2"></div>
                <button type="button" id="addItemBtn" class="mt-2 px-2 py-1 bg-green-500 text-white rounded text-sm">Add Item</button>
            </div>
            <div class="flex justify-end gap-2 mt-3">
                <button type="button" id="modalCancel" class="px-3 py-1 bg-gray-200 rounded text-sm">Cancel</button>
                <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded text-sm">Save</button>
                <button type="button" id="modalDelete" class="px-3 py-1 bg-red-500 text-white rounded text-sm hidden">Delete</button>
            </div>
        </form>
    </div>
`;
document.body.appendChild(modal);

// Get modal elements
const modalType = document.getElementById("modalType");
const modalTargetArea = document.getElementById("modalTargetArea2");

// Function to update the visibility of the Target Area based on modalType
function updateTargetAreaVisibility() {
    if (modalType.value === "Request") {
        modalTargetArea.classList.add("hidden");  // Hide Target Area for Request
    } else {
        modalTargetArea.classList.remove("hidden");  // Show Target Area for Offer
    }
}

// Event listener to toggle the visibility of Target Area when modalType changes
modalType.addEventListener("change", () => {
    updateTargetAreaVisibility();  // Hide or show the Target Area based on the selected type
});

// Event listener to close the modal when the cancel button is clicked
document.getElementById("modalCancel").addEventListener("click", () => {
    modal.classList.add("hidden");
});

// You can trigger the modal opening as needed
// Example usage: openModal();  // This will open the modal with the correct visibility for the target area


let editingRequest = null;
const itemsContainer = document.getElementById("itemsContainer");

// ===== Item Row =====
function createItemRow(it = { name: '', quantity: 1, unit: '' }) {
    const row = document.createElement("div");
    row.className = "flex gap-2 items-center text-sm";

    const itemOptions = Object.keys(predefinedItems).map(item => `<option value="${item}" ${it.name === item ? 'selected' : ''}>${item}</option>`).join('');

    const initialItem = it.name || Object.keys(predefinedItems)[0] || '';
    const initialUnit = it.unit || (predefinedItems[initialItem]?.[0] || '');
    const unitOptions = predefinedItems[initialItem]?.map(u => `<option value="${u}" ${initialUnit === u ? 'selected' : ''}>${u}</option>`).join('') || '<option value="">Unit</option>';

    row.innerHTML = `
        <select class="border rounded p-1 flex-1 item-select text-sm">${itemOptions}</select>
        <input type="number" class="border rounded p-1 w-20 text-sm" min="1" value="${it.quantity}">
        <select class="border rounded p-1 w-20 unit-select text-sm">${unitOptions}</select>
        <button type="button" class="bg-red-500 text-white px-2 rounded text-sm">X</button>
    `;

    const itemSelect = row.querySelector(".item-select");
    const unitSelect = row.querySelector(".unit-select");
    const deleteBtn = row.querySelector("button");

    itemSelect.addEventListener("change", () => {
        const units = predefinedItems[itemSelect.value] || [''];
        unitSelect.innerHTML = units.map(u => `<option value="${u}">${u}</option>`).join('');
    });

    deleteBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        row.remove();
    });

    itemsContainer.appendChild(row);
}

// ===== Open Modal =====
// ===== Open Add/Edit Request/Offer Modal =====
function openModal(req = null) {
    
    editingRequest = req;
    modal.classList.remove("hidden");
    itemsContainer.innerHTML = "";
    const details = document.getElementById("modalDetails");
    const targetArea = document.getElementById("modalTargetArea");

    if (req) {
        document.getElementById("modalTitle").innerText = "Edit Request/Offer";
        document.getElementById("modalType").value = req.type;
        details.value = req.details || "";

        

        req.items.forEach(it => createItemRow(it));
        targetArea.value = req.target_area || "philippines";

        document.getElementById("modalDelete").classList.remove("hidden");
    } else {
        // --- NEW ENTRY ---
        document.getElementById("modalTitle").innerText = "Add Request/Offer";
        document.getElementById("modalType").value = "Request";
        details.value = "";
        targetArea.value = "philippines";

        createItemRow(); // Always add one item row by default
        document.getElementById("modalDelete").classList.add("hidden");
    }

    modal.classList.remove("hidden");
    updateTargetAreaVisibility();
}

document.getElementById("addItemBtn").addEventListener("click", () => createItemRow());
document.getElementById("addRequestBtn").addEventListener("click", () => openModal());
document.getElementById("modalCancel").addEventListener("click", () => modal.classList.add("hidden"));

// ===== Save =====
document.getElementById("modalForm").addEventListener("submit", e => {
    e.preventDefault();

    const type = document.getElementById("modalType").value.toLowerCase();
    const details = document.getElementById("modalDetails").value;

    const items = Array.from(itemsContainer.children).map(row => ({
        name: row.querySelector(".item-select").value,
        quantity: Number(row.querySelector("input").value),
        unit: row.querySelector(".unit-select").value
    }));

    if (!items.length) { 
        alert("Add at least one item."); 
        return; 
    }

    const formData = new FormData();
    formData.append("type", type);
    formData.append("details", details);
    formData.append("items", JSON.stringify(items));
    formData.append("target_area", document.getElementById("modalTargetArea").value);

    // If editing, append entry_id to the form data
    if (editingRequest && editingRequest.entry_id) {
        formData.append("entry_id", editingRequest.entry_id); // <-- important
    }

    fetch("profile_saveEntry.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                alert("Saved successfully!");
                modal.classList.add("hidden");
                editingRequest = null;
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        });
});

// Delete entry
document.getElementById("modalDelete").addEventListener("click", () => {
    if (!editingRequest || !editingRequest.entry_id) return;

    const confirmDelete = confirm("Are you sure you want to delete this entry?");
    if (!confirmDelete) return;

    const formData = new FormData();
    formData.append("entry_id", editingRequest.entry_id);
    formData.append("type", editingRequest.type); // <-- Send the type here (request/offer)

    fetch("profile_deleteEntry.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("Entry deleted successfully!");
            modal.classList.add("hidden");
            editingRequest = null;
            location.reload(); // reload table to reflect deletion
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(err => {
        console.error(err);
        alert("An error occurred while deleting the entry.");
    });
});

function openDonationModal(entryId) {
    entryId = parseInt(entryId);
    
    // FIX: use JS variable instead of PHP inside function
    const clickedEntry = [...requests, ...otherRequestsJS].find(r => r.entry_id === entryId);
    
    if (!clickedEntry) {
        alert("Request/Offer not found.");
        return;
    }

    editingRequest = clickedEntry;

    // Set donor profile name and details
    document.getElementById("donorProfileName").textContent = editingRequest.profile_name;
    document.getElementById("donorDetails").textContent = editingRequest.details || "---";

    const donationItemsTable = document.getElementById("donationItemsTable");
    donationItemsTable.innerHTML = "";

    if (!editingRequest.items || editingRequest.items.length === 0) {
        const row = document.createElement("tr");
        row.className = "border-b";
        row.innerHTML = `<td class="p-2 text-center" colspan="6">No items available.</td>`;
        donationItemsTable.appendChild(row);
    } else {
        const donorOffers = requests.filter(r => r.type === 'Offer'); // your offers

        editingRequest.items.forEach((item, index) => {
        // Find the matching offer for this request item
        const matchingOffer = requests.find(o => 
            o.type === 'Offer' && 
            o.items.some(oi => oi.name === item.name && oi.quantity > 0)
        );

        const matchingOfferItem = matchingOffer?.items.find(oi => oi.name === item.name && oi.quantity > 0) ?? null;

        if (!matchingOfferItem) {
            console.warn("No matching offer item for:", item.name);
        }

        const availableQuantity = matchingOfferItem ? matchingOfferItem.quantity : 0;
        const offerEntryId = matchingOffer ? matchingOffer.entry_id : null;
        const offerItemEntryId = matchingOfferItem ? matchingOfferItem.item_entry_id : null;

        // Skip adding rows if there is no valid offerItemEntryId
        // if (!offerItemEntryId) return;

        console.log("Offer Item Entry ID:", offerItemEntryId);
        
        const row = document.createElement("tr");
        row.dataset.donorItemEntryId = offerItemEntryId || '';
        row.dataset.recipientItemEntryId = item.item_entry_id || '';
        row.dataset.itemEntryId = offerItemEntryId || '';
        row.dataset.requestEntryId = editingRequest.entry_id;
        row.dataset.offerEntryId = offerEntryId || '';
        row.dataset.profileId = editingRequest.profile_id;
        row.dataset.donorProfileId = <?= json_encode($profileId) ?>;
        row.dataset.donorItemId = matchingOfferItem ? matchingOfferItem.item_id : '';  // <-- donor's item_id
        row.dataset.recipientItemId = item.item_id || '';  // <-- recipient's item_id


        row.className = "border-b";
        row.innerHTML = `
            <td class="p-2">${index + 1}</td>
            <td class="p-2">${item.name}</td>
            <td class="p-2">${item.quantity}</td>
            <td class="p-2">${availableQuantity}</td>
            <td class="p-2">
                <input type="number" min="0" max="${availableQuantity}" 
                    class="border rounded p-1 w-20 text-sm donation-quantity" 
                    value="0" ${availableQuantity === 0 ? 'disabled' : ''}>
            </td>
            <td class="p-2">${item.unit}</td>
        `;
        donationItemsTable.appendChild(row);
    });
        }

        document.getElementById("donationModal").classList.remove("hidden");
    }

// Cancel donation modal
document.getElementById("donationModalCancel").addEventListener("click", function() {
    document.getElementById("donationModal").classList.add("hidden");
});

// Confirm donation functionality (save the donation data)

document.getElementById("donationModalConfirm").addEventListener("click", () => {
    const donationItems = Array.from(document.querySelectorAll("#donationItemsTable tr"))
    .map(row => {
        const qty = Number(row.querySelector(".donation-quantity").value);
        const offerItemEntryId = row.dataset.itemEntryId ? parseInt(row.dataset.itemEntryId) : 0;

        // Skip if no matching offer item or quantity is 0
        if (qty <= 0 || !offerItemEntryId) return null;

        return {
            donator_profile_id: parseInt(row.dataset.donorProfileId),
            donator_entry_id: parseInt(row.dataset.offerEntryId),
            donator_item_entry_id: offerItemEntryId,
            donator_item_id: parseInt(row.dataset.donorItemId),
            recipient_profile_id: parseInt(row.dataset.profileId),
            recipient_entry_id: parseInt(row.dataset.requestEntryId) || null,
            recipient_item_entry_id: parseInt(row.dataset.recipientItemEntryId) || null,
            recipient_item_id: parseInt(row.dataset.recipientItemId) || null,
            quantity: qty,
            unit: row.querySelector("td:last-child").textContent // <-- get the unit from the table cell
        };


    })
    .filter(x => x !== null);

    console.log(JSON.stringify(donationItems));

    if (!donationItems.length) {
        alert("Please enter a donation quantity.");
        return;
    }

    fetch("profile_confirmDonation.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "donation_items=" + encodeURIComponent(JSON.stringify(donationItems))
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("Donation confirmed!");
            document.getElementById("donationModal").classList.add("hidden");
            location.reload();
        } else {
            alert("Error: " + data.message);
        }
    });
}); // <-- Add this closing parenthesis & semicolon


// --- Matching logic ---

// Matching function that checks if two profiles are in the same geographical area
function isMatchingGeography(requestProfile, offerProfile, targetArea) {
    if (targetArea === "philippines") {
        return true;  // All profiles in the Philippines match
    }
    // alert(targetArea + requestProfile.region_name + offerProfile.region_name);
    // For "region", check if the regions match
    if (targetArea === "region" && requestProfile.region_name === offerProfile.region_name) {
        return true;
    }

    // For "province", check if the provinces match
    if (targetArea === "province" && requestProfile.province_name === offerProfile.province_name) {
        return true;
    }

    // For "city", check if the cities match
    if (targetArea === "city" && requestProfile.city_name === offerProfile.city_name) {
        return true;
    }

    // For "barangay", check if the barangays match
    if (targetArea === "barangay" && requestProfile.barangay_name === offerProfile.barangay_name) {
        return true;
    }

    return false;  // If no match, return false
}


// Matching function to check if two target areas match
function isMatchingTargetArea(requestTarget, offerTarget) {
    // If both are Philippines, they match
    if (requestTarget === "philippines" && offerTarget === "philippines") {
        return true;
    }

    // Request is region, offer is Philippines - match
    if (requestTarget === "region" && offerTarget === "philippines") {
        return true;
    }

    // Request is region, offer is the same region - match
    if (requestTarget === "region" && offerTarget === "region") {
        return true;
    }

    // Request is barangay, offer is barangay - they match only if they are the same barangay
    if (requestTarget === "barangay" && offerTarget === "barangay") {
        return true;
    }

    // If the request area is more specific (barangay), but offer is broader (region, Philippines), they do not match
    if (requestTarget === "barangay" && (offerTarget === "region" || offerTarget === "philippines")) {
        return false;
    }

    // Otherwise, they do not match
    return false;
}

function findMatchesForOffer(offer) {
    const matchingRequests = [];

    if (Array.isArray(otherRequestsJS)) {
        otherRequestsJS.forEach(request => {
            // Default location names to "N/A" if undefined
            request.region_name = request.region_name || "N/A";  
            request.province_name = request.province_name || "N/A";
            request.city_name = request.city_name || "N/A";
            request.barangay_name = request.barangay_name || "N/A";
            
            // First, check if geography (location) matches
            if (isMatchingGeography(request, offer, offer.target_area)) {
                
                // // Log the current request and offer being checked
                // console.log("Checking Request:", request);
                // console.log("Against Offer:", offer);

                // Once geography matches, check if items match
                const itemsMatch = isItemsMatch(request.items, offer.items);
                
                if (itemsMatch) {
                    // If both geography and items match, add to the matching requests list
                    matchingRequests.push(request);
                }
            }
        });
    } else {
        console.error("otherRequestsJS is not an array");
    }

    return matchingRequests;
}

function isItemsMatch(requestItems, offerItems) {
    if (!Array.isArray(requestItems) || !Array.isArray(offerItems)) return false;

    return requestItems.some(requestItem => {
        const matchingOfferItem = offerItems.find(offerItem => {
            console.log(
                Number(offerItem.item_id) === Number(requestItem.item_id)
            );
            return Number(offerItem.item_id) === Number(requestItem.item_id);
        });

        if (!matchingOfferItem) return false;

        return Number(matchingOfferItem.quantity) >= Number(requestItem.quantity);
    });
}




function isMatchingTargetAreaAndLocation(request, offer) {
    const levels = { 'philippines': 0, 'region': 1, 'province': 2, 'city': 3, 'barangay': 4 };

    const reqLevel = levels[request.target_area || 'philippines'];
    const offerLevel = levels[offer.target_area || 'philippines'];

    // If offer is broader or same level as request
    if (offerLevel <= reqLevel) {
        // Same level → check IDs
        if (reqLevel === offerLevel) {
            switch (reqLevel) {
                case 0: return true; // Philippines
                case 1: return request.region_id === offer.region_id;
                case 2: return request.province_id === offer.province_id;
                case 3: return request.city_id === offer.city_id;
                case 4: return request.barangay_id === offer.barangay_id;
            }
        }
        // Offer is broader → always match
        return true;
    }

    // Request is broader than offer → no match
    return false;
}





// --- Rendering logic ---

document.addEventListener("click", (e) => {
    const link = e.target.closest(".profile-link");
    if (link) {
        e.preventDefault();
        const profile_id = link.dataset.profileId;
        openProfileModal(profile_id);
    }
});


function openProfileModal(profile_id) {
    const modal = document.getElementById("profileModal");
    const content = document.getElementById("profileModalContent");
    // alert(profile_id);
    modal.classList.remove("hidden");
    content.innerHTML = "Loading...";
    fetch("get_profile.php?id=" + profile_id)
    
    
        .then(res => res.json())
        .then(data => {
            // Set badge color and text based on profile type
            const badge = document.getElementById("profileTypeBadge");
            let profileHTML = "";

            // Profile Header (common for all types)
            let profileHeader = `
            <div class="flex items-center gap-4 border-b pb-3 mb-3">
                <img src="${data.profile.profile_pic ? '../' + data.profile.profile_pic : '../uploads/profile_pic_placeholder1.png'}" 
                    alt="Profile Picture" 
                    class="w-20 h-20 rounded-full object-cover border">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">${data.profile.profile_name}</h3>
                    <p class="text-gray-600 capitalize">${data.profile.profile_type}</p>
                    <p class="text-gray-500 text-sm">Profile ID: #${data.profile.profile_id}</p>
                </div>
            </div>
            `;

            // Profile Details (type-specific)
            let profileDetails = "";
            switch (data.profile.profile_type) {
                case 'individual':
                    profileDetails = `
                        <div class="grid grid-cols-2 gap-x-4">
                            <div><strong>Region:</strong> ${data.location.region_name || 'N/A'}</div>
                            <div><strong>Province:</strong> ${data.location.province_name || 'N/A'}</div>
                            <div><strong>City:</strong> ${data.location.city_name || 'N/A'}</div>
                            <div><strong>Barangay:</strong> ${data.location.barangay_name || 'N/A'}</div>
                            <div><strong>Contact:</strong> ${data.profile.phone_number || 'N/A'}</div>
                            <div><strong>Birthday:</strong> ${data.profile.birthday || 'N/A'}</div>
                        </div>
                    `;
                    break;
                case 'family':
                    profileDetails = `
                        <div class="grid grid-cols-2 gap-x-4">
                            <div><strong>Household Name:</strong> ${data.profile.profile_name}</div>
                            <div><strong>Primary Contact Person:</strong> ${data.profile.primary_contact_person || 'N/A'}</div>
                            <div><strong>Contact Number:</strong> ${data.profile.phone_number || 'N/A'}</div>
                            <div><strong>Email:</strong> ${data.location.email || 'N/A'}</div>
                            <div><strong>Region:</strong> ${data.location.region_name || 'N/A'}</div>
                            <div><strong>Province:</strong> ${data.location.province_name || 'N/A'}</div>
                            <div><strong>City:</strong> ${data.location.city_name || 'N/A'}</div>
                            <div><strong>Barangay:</strong> ${data.location.barangay_name || 'N/A'}</div>
                            <div><strong>Zip Code:</strong> ${data.location.zip_code || 'N/A'}</div>
                        </div>
                    `;
                    break;
                case 'institution':
                    profileDetails = `
                        <div class="grid grid-cols-2 gap-x-4">
                            <div><strong>Institution Type:</strong> ${data.profile.institution_type}</div>
                            <div><strong>Institution Name:</strong> ${data.profile.profile_name}</div>
                            <div><strong>Official Contact Person:</strong> ${data.location.official_contact_person || 'N/A'}</div>
                            <div><strong>Official Contact Number:</strong> ${data.location.official_contact_number || 'N/A'}</div>
                            <div><strong>Official Email:</strong> ${data.profile.official_email || 'N/A'}</div>
                            <div><strong>Region:</strong> ${data.location.region_name || 'N/A'}</div>
                            <div><strong>Province:</strong> ${data.location.province_name || 'N/A'}</div>
                            <div><strong>City:</strong> ${data.location.city_name || 'N/A'}</div>
                            <div><strong>Barangay:</strong> ${data.location.barangay_name || 'N/A'}</div>
                            <div><strong>Contact:</strong> ${data.profile.phone_number || 'N/A'}</div>
                        </div>
                    `;
                    break;
                case 'organization':
                    profileDetails = `
                        <div class="grid grid-cols-2 gap-x-4">
                            <div><strong>Organization Name:</strong> ${data.profile.profile_name}</div>
                            <div><strong>Head:</strong> ${data.profile.head_name || 'N/A'}</div>
                            <div><strong>Region:</strong> ${data.location.region_name || 'N/A'}</div>
                            <div><strong>Province:</strong> ${data.location.province_name || 'N/A'}</div>
                            <div><strong>City:</strong> ${data.location.city_name || 'N/A'}</div>
                            <div><strong>Barangay:</strong> ${data.location.barangay_name || 'N/A'}</div>
                            <div><strong>Contact:</strong> ${data.profile.phone_number || 'N/A'}</div>
                        </div>
                    `;
                    break;
            }

            // Combine header + details
            document.getElementById("profileModalContent").innerHTML = profileHeader + profileDetails;

        })
        .catch(() => {
            document.getElementById("profileModalContent").innerHTML = "Error loading profile.";
        });
}

document.getElementById("closeProfileModal").addEventListener("click", () => {
    document.getElementById("profileModal").classList.add("hidden");
});


document.getElementById("closeProfileModal").addEventListener("click", () => {
    document.getElementById("profileModal").classList.add("hidden");
});


</script>

</body>
</html>
