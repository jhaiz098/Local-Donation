<?php
// profile_dashboard.php
require '../db_connect.php'; // adjust path

if (isset($_GET['profile_id'])) {
    $_SESSION['profile_id'] = intval($_GET['profile_id']);
} elseif (!isset($_SESSION['profile_id'])) {
    echo "No profile selected.";
    exit;
}

$userId = $_SESSION['user_id'];
$profileId = $_SESSION['profile_id'];

// Fetch the role for the user and set it in the session
$stmt = $conn->prepare("SELECT pm.role FROM profile_members pm
                        JOIN users u ON pm.user_id = u.user_id
                        WHERE pm.profile_id = ? AND u.user_id = ?");
$stmt->bind_param("ii", $profileId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['role'] = $row['role'];
} else {
    echo "No role found for the given user and profile.";
    exit;
}
$stmt->close();

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

// Fetch profile data based on type
switch ($profileType) {
    case 'individual':
        $stmt = $conn->prepare("SELECT * FROM profiles_individual WHERE profile_id = ?");
        break;
    case 'family':
        $stmt = $conn->prepare("SELECT * FROM profiles_family WHERE profile_id = ?");
        break;
    case 'institution':
        $stmt = $conn->prepare("SELECT * FROM profiles_institution WHERE profile_id = ?");
        break;
    case 'organization':
        $stmt = $conn->prepare("SELECT * FROM profiles_organization WHERE profile_id = ?");
        break;
    default:
        echo "Unknown profile type.";
        exit;
}

$stmt->bind_param("i", $profileId);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();
$stmt->close();

if (!$profile) {
    echo "Profile data not found.";
    exit;
}

// Common variables
$profilePic = !empty($profileMain['profile_pic']) ? "../" . $profileMain['profile_pic'] : "../uploads/profile_pic_placeholder1.png";
$fullName = isset($profile['first_name']) ? trim("{$profile['first_name']} {$profile['middle_name']} {$profile['last_name']}") : $profileName;

// Helper function to get location name by table
function getLocationName($conn, $table, $id) {
    if (empty($id)) return 'N/A';
    $stmt = $conn->prepare("SELECT name FROM $table WHERE id = ?");
    if (!$stmt) return 'N/A';
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $name = $result->fetch_assoc()['name'] ?? 'N/A';
    $stmt->close();
    return $name;
}

// Fetch location names if IDs exist
$regionName   = isset($profile['region_id'])   ? getLocationName($conn, 'regions', $profile['region_id'])   : 'N/A';
$provinceName = isset($profile['province_id']) ? getLocationName($conn, 'provinces', $profile['province_id']) : 'N/A';
$cityName     = isset($profile['city_id'])     ? getLocationName($conn, 'cities', $profile['city_id'])     : 'N/A';
$barangayName = isset($profile['barangay_id']) ? getLocationName($conn, 'barangays', $profile['barangay_id']) : 'N/A';
$zipCode      = $profile['zip_code'] ?? 'N/A';

$personalInfo = [];
$locationInfo = [
    'Region' => $regionName,
    'Province' => $provinceName,
    'City/Municipality' => $cityName,
    'Barangay' => $barangayName,
    'ZIP Code' => $zipCode
];

if ($profileType === 'individual') {
    $dob = $profile['date_of_birth'] ?? null;
    $age = $dob ? date_diff(date_create($dob), date_create('today'))->y : 'N/A';
    $personalInfo = [
        'Full Name' => $fullName,
        'Age' => $age,
        'Gender' => $profile['gender'] ?? 'N/A',
        'Phone' => $profile['phone_number'] ?? 'N/A',
        'Email' => $profile['email'] ?? 'N/A'
    ];
} else {
    foreach ($profile as $key => $value) {
        if (in_array($key, ['profile_id', 'profile_pic', 'region_id', 'province_id', 'city_id', 'barangay_id', 'zip_code'])) continue;
        $label = ucwords(str_replace('_',' ',$key));
        $personalInfo[$label] = $value ?? 'N/A';
    }
}

// ----------------- Enhanced Donations Queries -----------------

// Donations Received
$stmt = $conn->prepare("
    SELECT dl.log_id, dl.item_id, dl.quantity, dl.created_at, 
           i.item_name, dl.unit_name, p.profile_name AS donor_name, p.profile_type AS donor_type
    FROM donation_logs dl
    JOIN profiles p ON dl.donor_profile_id = p.profile_id
    LEFT JOIN items i ON dl.item_id = i.item_id
    WHERE dl.recipient_profile_id = ?
    ORDER BY dl.created_at DESC
    LIMIT 5
");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$result = $stmt->get_result();
$donationsReceived = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Donations Given
$stmt = $conn->prepare("
    SELECT dl.log_id, dl.item_id, dl.quantity, dl.created_at, 
           i.item_name, dl.unit_name, p.profile_name AS recipient_name, p.profile_type AS recipient_type
    FROM donation_logs dl
    JOIN profiles p ON dl.recipient_profile_id = p.profile_id
    LEFT JOIN items i ON dl.item_id = i.item_id
    WHERE dl.donor_profile_id = ?
    ORDER BY dl.created_at DESC
    LIMIT 5
");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$result = $stmt->get_result();
$donationsGiven = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Dashboard</title>
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

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-6 overflow-y-auto">
        <!-- PROFILE HEADER -->
        <div class="flex items-center bg-white shadow p-6 rounded-lg mb-6">
            <img src="<?= htmlspecialchars($profilePic) ?>" class="w-28 h-28 rounded-full border mr-6" alt="Profile Picture">
            <div>
                <h2 class="text-3xl font-bold"><?= htmlspecialchars($profileName) ?></h2>
                <p class="text-gray-700">Profile Type: <?= htmlspecialchars(ucfirst($profileType)) ?></p>
                <p class="text-gray-500 text-sm">Profile ID: #<?= htmlspecialchars($profileId) ?></p>
            </div>
        </div>

        <!-- PROFILE INFORMATION SECTION -->
        <section id="profile-info" class="content-section">
            <h3 class="text-2xl font-semibold mb-4">Profile Information</h3>
            <div class="bg-white shadow p-6 rounded-lg grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Personal Info Column -->
                <div class="space-y-3">
                    <?php foreach ($personalInfo as $label => $value): ?>
                        <p><span class="font-semibold"><?= htmlspecialchars($label) ?>:</span> <?= htmlspecialchars($value) ?></p>
                    <?php endforeach; ?>
                </div>

                <!-- Location Info Column -->
                <div class="space-y-3">
                    <?php foreach ($locationInfo as $label => $value): ?>
                        <p><span class="font-semibold"><?= htmlspecialchars($label) ?>:</span> <?= htmlspecialchars($value) ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- DONATIONS SECTION -->
        <section id="donations" class="content-section mt-8">
            <h3 class="text-2xl font-semibold mb-4">Donations</h3>

            <!-- Donations Received -->
            <div class="bg-white shadow p-6 rounded-lg mb-6">
                <h4 class="text-xl font-semibold mb-3">Recent Donations Received</h4>
                <?php if (!empty($donationsReceived)): ?>
                    <table class="w-full table-auto text-left border-collapse">
                        <thead>
                            <tr class="border-b">
                                <th class="p-2">From</th>
                                <th class="p-2">Profile Type</th>
                                <th class="p-2">Item</th>
                                <th class="p-2">Unit</th>
                                <th class="p-2">Quantity</th>
                                <th class="p-2">Date Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donationsReceived as $donation): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2"><?= htmlspecialchars($donation['donor_name']) ?></td>
                                    <td class="p-2"><?= htmlspecialchars($donation['donor_type']) ?></td>
                                    <td class="p-2"><?= htmlspecialchars($donation['item_name'] ?? $donation['item_id']) ?></td>
                                    <td class="p-2"><?= htmlspecialchars($donation['unit_name'] ?? 'N/A') ?></td>
                                    <td class="p-2"><?= htmlspecialchars($donation['quantity']) ?></td>
                                    <td class="p-2"><?= htmlspecialchars($donation['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-gray-500">No donations received yet.</p>
                <?php endif; ?>
            </div>

            <!-- Donations Given -->
            <div class="bg-white shadow p-6 rounded-lg">
                <h4 class="text-xl font-semibold mb-3">Recent Donations Given</h4>
                <?php if (!empty($donationsGiven)): ?>
                    <table class="w-full table-auto text-left border-collapse">
                        <!-- Donations Given -->
                        <thead>
                            <tr class="border-b">
                                <th class="p-2">To</th>
                                <th class="p-2">Profile Type</th>
                                <th class="p-2">Item</th>
                                <th class="p-2">Unit</th>
                                <th class="p-2">Quantity</th>
                                <th class="p-2">Date Given</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donationsGiven as $donation): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2"><?= htmlspecialchars($donation['recipient_name']) ?></td>
                                    <td class="p-2"><?= htmlspecialchars($donation['recipient_type']) ?></td>
                                    <td class="p-2"><?= htmlspecialchars($donation['item_name'] ?? $donation['item_id']) ?></td>
                                    <td class="p-2"><?= htmlspecialchars($donation['unit_name'] ?? 'N/A') ?></td>
                                    <td class="p-2"><?= htmlspecialchars($donation['quantity']) ?></td>
                                    <td class="p-2"><?= htmlspecialchars($donation['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-gray-500">No donations given yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
