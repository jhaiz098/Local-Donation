<?php
include "../db_connect.php"; // Includes the connection

// Step 1: Get the filter from the URL (default to 'all' if not set)
$filter = $_GET['filter'] ?? 'all';

// Step 2: Set pagination variables
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // Profiles per page
$offset = ($page - 1) * $perPage; // Calculate the offset for the query

// Step 3: Get the total number of profiles for pagination
$totalQuery = "SELECT COUNT(*) FROM profiles";
if ($filter !== 'all') {
    $totalQuery .= " WHERE profile_type = ?";
}
$stmtTotal = $conn->prepare($totalQuery);

// If we have a specific filter, bind the parameter
if ($filter !== 'all') {
    $stmtTotal->bind_param("s", $filter);
}

$stmtTotal->execute();
$totalResult = $stmtTotal->get_result();
$totalRows = $totalResult->fetch_row()[0];
$totalPages = ceil($totalRows / $perPage); // Calculate total pages

// Step 4: Modify the query to include LIMIT and OFFSET for pagination
$query = "SELECT * FROM profiles";
if ($filter !== 'all') {
    $query .= " WHERE profile_type = ?";
}
$query .= " LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);

// Bind parameters for filter, limit, and offset
if ($filter !== 'all') {
    $stmt->bind_param("sii", $filter, $perPage, $offset);
} else {
    $stmt->bind_param("ii", $perPage, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $profiles = [];
    while ($profile = $result->fetch_assoc()) {
        
        // Step 5: Check profile type
        $profileId = $profile['profile_id'];
        $profileType = $profile['profile_type'];

        // Initialize location variables
        $regionName = $provinceName = $cityName = $barangayName = '';
        $ownerId = ''; // Initialize owner user ID

        // Step 6: Get the user_id with owner role from profile_members table
        $queryOwner = "SELECT user_id FROM profile_members WHERE profile_id = ? AND role = 'owner'";
        $stmtOwner = $conn->prepare($queryOwner);
        $stmtOwner->bind_param("i", $profileId);
        $stmtOwner->execute();
        $ownerResult = $stmtOwner->get_result();
        
        if ($ownerResult->num_rows > 0) {
            $ownerId = $ownerResult->fetch_assoc()['user_id']; // Get the user_id of the owner
        }

        // Step 7: Get data from corresponding table based on profile type
        if ($profileType == 'individual') {
            $table = 'profiles_individual';
        } elseif ($profileType == 'family') {
            $table = 'profiles_family';
        } elseif ($profileType == 'institution') {
            $table = 'profiles_institution';
        } elseif ($profileType == 'organization') {
            $table = 'profiles_organization';
        }

        // Fetch the corresponding profile information
        $queryDetails = "SELECT * FROM $table WHERE profile_id = ?";
        $stmt = $conn->prepare($queryDetails);
        $stmt->bind_param("i", $profileId);
        $stmt->execute();
        $profileDetails = $stmt->get_result()->fetch_assoc();

        // Get the location details (region, province, city, barangay)
        if ($profileDetails) {
            $regionId = $profileDetails['region_id'];
            $provinceId = $profileDetails['province_id'];
            $cityId = $profileDetails['city_id'];
            $barangayId = $profileDetails['barangay_id'];

            // Fetch location names from respective tables
            $queryLocation = "SELECT r.name AS region_name, p.name AS province_name, c.name AS city_name, b.name AS barangay_name
                              FROM regions r
                              JOIN provinces p ON r.id = p.region_id
                              JOIN cities c ON p.id = c.province_id
                              JOIN barangays b ON c.id = b.city_id
                              WHERE r.id = ? AND p.id = ? AND c.id = ? AND b.id = ?";

            $stmtLocation = $conn->prepare($queryLocation);
            $stmtLocation->bind_param("iiii", $regionId, $provinceId, $cityId, $barangayId);
            $stmtLocation->execute();
            $location = $stmtLocation->get_result()->fetch_assoc();

            // If location found, assign the names
            if ($location) {
                $regionName = $location['region_name'];
                $provinceName = $location['province_name'];
                $cityName = $location['city_name'];
                $barangayName = $location['barangay_name'];
            }
        }

        // Add each profile with the location to the profiles array
        $profiles[] = [
            'profile_id' => $profileId,
            'profile_pic' => $profile['profile_pic'],
            'profile_name' => $profile['profile_name'],
            'profile_type' => $profileType,
            'owner' => $ownerId, // Display user_id of the owner (or the owner's name if needed)
            
            // Conditional logic for phone number based on profile type
            'phone_number' => (
                $profileType == 'individual' ? $profileDetails['phone_number'] :
                ($profileType == 'family' ? $profileDetails['contact_number'] :
                ($profileType == 'institution' ? $profileDetails['official_contact_number'] :
                ($profileType == 'organization' ? $profileDetails['contact_number'] : '-')))
            ),
            
            'location' => "$barangayName / $cityName / $provinceName / $regionName",
            'created_at' => $profile['created_at']
        ];

    }
} else {
    $profiles = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profiles</title>

    <script src="../src/tailwind.js"></script>
    <link rel="stylesheet" href="../src/style.css">
</head>
<body class="bg-gray-100">

<!-- ================= HEADER ================= -->
<header class="py-4 px-5 bg-white shadow-md flex justify-between items-center fixed w-full top-0 z-20">
    <h1 class="text-2xl md:text-3xl font-bold">
        <a href="dashboard.php">Bayanihan Hub</a>
    </h1>

    <!-- Mobile Hamburger -->
    <button id="hamburger" class="block md:hidden p-2 rounded bg-gray-100 hover:bg-gray-200">
        <svg class="w-6 h-6 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</header>

<!-- ================= SIDEBAR (DESKTOP) ================= -->
<aside class="hidden md:block w-64 fixed top-16 left-0 h-[calc(100vh-4rem)] overflow-y-auto">
    <nav class="p-4">
        <ul class="space-y-1">
            <!-- Core -->
            <li class="uppercase text-xs px-2 mt-2">Core</li>
            <li>
                <a href="admin_dashboard.php" class="block px-4 py-2 rounded hover:bg-gray-200">
                    Dashboard
                </a>
            </li>
            <!-- Accounts -->
            <li class="uppercase text-xs px-2 mt-4">Accounts</li>
            <li><a href="admin_myAccount.php" class="block px-4 py-2 rounded hover:bg-gray-200">My Account</a></li>
            <li><a href="admin_users.php" class="block px-4 py-2 rounded hover:bg-gray-200">Users</a></li>
            <!-- Operations -->
            <li class="uppercase text-xs px-2 mt-4">Operations</li>
            <li><a href="admin_profiles.php" class="block px-4 py-2 rounded bg-gray-300 font-semibold">Profiles</a></li>
            <li><a href="admin_donations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donations / Requests</a></li>
            <li><a href="admin_feedback.php" class="block px-4 py-2 rounded hover:bg-gray-200">Feedback</a></li>
            <!-- System -->
            <li class="uppercase text-xs px-2 mt-4">System</li>
            <li><a href="admin_locations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Location Management</a></li>
            <li><a href="admin_donation_logs.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donation Logs</a></li>
            <li><a href="admin_activities.php" class="block px-4 py-2 rounded hover:bg-gray-200">Activity</a></li>
            <li><a href="admin_audit_trails.php" class="block px-4 py-2 rounded hover:bg-gray-200">Audit Trails</a></li>
            <li><a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Settings</a></li>
            <!-- Support -->
            <li class="uppercase text-xs px-2 mt-4">Support</li>
            <li><a href="admin_help.php" class="block px-4 py-2 rounded hover:bg-gray-200">Help / FAQ</a></li>
            <!-- Logout -->
            <li class="mt-6">
                <a href="admin_logout.php" class="block px-4 py-2 rounded bg-red-600 hover:bg-red-500 text-center">
                    Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- ================= MOBILE SIDE MENU ================= -->
<div id="side-menu" class="fixed inset-0 bg-white z-30 transform -translate-x-full transition-transform duration-300 md:hidden pt-20 overflow-y-auto">
    <button id="close-btn" class="absolute top-4 right-4 p-2 rounded bg-gray-200 hover:bg-gray-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>
    <ul class="flex flex-col gap-1 px-6">
        <li><a href="admin_dashboard.php" class="block px-4 py-2 rounded hover:bg-gray-200">Dashboard</a></li>
        <li><a href="admin_myAccount.php" class="block px-4 py-2 rounded hover:bg-gray-200">My Account</a></li>
        <li><a href="admin_users.php" class="block px-4 py-2 rounded hover:bg-gray-200">Users</a></li>
        <li><a href="admin_profiles.php" class="block px-4 py-2 rounded hover:bg-gray-200">Profiles</a></li>
        <li><a href="admin_donations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donations / Requests</a></li>
        <li><a href="admin_feedback.php" class="block px-4 py-2 rounded hover:bg-gray-200">Feedback</a></li>
        <li><a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Settings</a></li>
        <li><a href="admin_help.php" class="block px-4 py-2 rounded hover:bg-gray-200">Help / FAQ</a></li>
        <li><a href="admin_logout.php" class="block px-4 py-2 rounded bg-red-600 hover:bg-red-500">Logout</a></li>
    </ul>
</div>

<!-- ================= MAIN CONTENT ================= -->
<main class="pt-24 p-6 md:ml-64">
    <h2 class="text-2xl font-bold mb-6">Profile Management</h2>

    <!-- ================= FILTER TABS ================= -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="?filter=all&page=<?= $page ?>" class="px-4 py-2 rounded <?= $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">All Profiles</a>
        <a href="?filter=individual&page=<?= $page ?>" class="px-4 py-2 rounded <?= $filter === 'individual' ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">Individual</a>
        <a href="?filter=family&page=<?= $page ?>" class="px-4 py-2 rounded <?= $filter === 'family' ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">Family</a>
        <a href="?filter=institution&page=<?= $page ?>" class="px-4 py-2 rounded <?= $filter === 'institution' ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">Community Institution</a>
        <a href="?filter=organization&page=<?= $page ?>" class="px-4 py-2 rounded <?= $filter === 'organization' ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">Organization</a>
    </div>

    <!-- ================= PROFILE TABLE ================= -->
    <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <table class="w-full min-w-[900px] border-collapse text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Profile ID</th>
                    <th class="p-3">Profile Name</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Owner (User ID)</th>
                    <th class="p-3">Contact</th>
                    <th class="p-3">Location</th>
                    <th class="p-3">Created At</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($profiles as $profile): ?>
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3"><?= $profile['profile_id'] ?></td>
                        <td class="p-3 max-w-[180px]">
                            <div class="flex items-center gap-2">
                                <img src="<?= isset($profile['profile_pic']) ? '../'.$profile['profile_pic'] : '../uploads/profile_pic_placeholder1.png' ?>" class="w-8 h-8 rounded-full object-cover">
                                <span class="max-w-[150px] break-words whitespace-normal">
                                    <?= $profile['profile_name'] ?>
                                </span>
                            </div>
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-1 rounded bg-gray-200 text-xs"><?= ucfirst($profile['profile_type']) ?></span>
                        </td>
                        <td class="p-3"><?= $profile['owner'] ?></td>
                        <td class="p-3"><?= !empty($profile['phone_number']) ? $profile['phone_number'] : '-' ?></td>
                        <td class="p-3 max-w-[180px] break-words whitespace-normal"><?= !empty($profile['location']) ? $profile['location'] : '-' ?></td>
                        <td class="p-3"><?= $profile['created_at'] ?></td>
                        <!-- In your profile table -->
                        <td class="p-3 text-center">
                            <div class="flex gap-1 justify-center whitespace-nowrap">
                                <!-- View Button -->
                                <button onclick="openModal(<?= $profile['profile_id'] ?>, 'view')" class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">View</button>

                                <!-- Edit Button -->
                                <button onclick="openModal(<?= $profile['profile_id'] ?>, 'edit')" class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</button>
                                
                                <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">Disable</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>


        </table>
    </div>

    <!-- ================= PAGINATION ================= -->
    <div class="mt-4 flex justify-center gap-2">
        <?php if($page > 1): ?>
            <a href="?filter=<?= $filter ?>&page=<?= $page-1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">&laquo; Prev</a>
        <?php endif; ?>

        <?php for($i=1; $i<=$totalPages; $i++): ?>
            <a href="?filter=<?= $filter ?>&page=<?= $i ?>" 
            class="px-3 py-1 rounded <?= $i==$page ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
            <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if($page < $totalPages): ?>
            <a href="?filter=<?= $filter ?>&page=<?= $page+1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Next &raquo;</a>
        <?php endif; ?>
    </div>
</main>

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

    function openModal(profileId, action = 'view') {
        // Decide the URL based on action (view or edit)
        const modalUrl = action === 'edit' 
            ? `profile_edit_modal.php?profile_id=${profileId}`  // Edit modal
            : `profile_view_modal.php?profile_id=${profileId}`; // View modal

        // Fetch and display the modal content via Ajax
        fetch(modalUrl)
            .then(response => response.text())
            .then(data => {
                // Inject modal content into the page
                document.body.insertAdjacentHTML('beforeend', data);
                
                // Show modal
                const modal = document.getElementById('profileModal');
                if (modal) {
                    modal.style.display = 'flex';
                    // Add event listeners for close buttons
                    attachModalCloseEvent();
                }
            })
            .catch(error => {
                console.error('Error fetching modal:', error);
            });
    }



    // Function to attach close event listeners to the close buttons
    function attachModalCloseEvent() {
        const modal = document.getElementById('profileModal');
        if (modal) {
            // Close modal when clicking on the 'X' button
            const closeButton = modal.querySelector('#closeBtn');
            const closeModalButton = modal.querySelector('#closeModalBtn');

            if (closeButton) {
                closeButton.addEventListener('click', closeModal);
            }

            if (closeModalButton) {
                closeModalButton.addEventListener('click', closeModal);
            }

            // Close modal if the user clicks outside of the modal content (overlay)
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            // Close modal if the user presses the "Escape" key
            window.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });
        }
    }

    // Function to close the modal
    function closeModal() {
        const modal = document.getElementById('profileModal');
        if (modal) {
            modal.style.display = 'none';  // Hide the modal
            modal.remove();  // Remove the modal content from the DOM
        }
    }



</script>

</body>
</html>
