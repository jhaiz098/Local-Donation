<?php
require '../db_connect.php';

/* ---------------- PROFILE CONTEXT ---------------- */
$profileId = $_GET['profile_id'] ?? $_SESSION['profile_id'] ?? die("No profile selected.");
$_SESSION['profile_id'] = (int)$profileId;
$userId = $_SESSION['user_id'];

/* ---------------- FETCH USER ROLE ---------------- */
$role = $conn->query("
    SELECT role
    FROM profile_members
    WHERE profile_id=$profileId AND user_id=$userId
")->fetch_assoc()['role'] ?? die("No role found");
$_SESSION['role'] = $role;

/* ---------------- PERMISSIONS ---------------- */
function isDisabled($permission, $role){
    $map = [
        'Manage Members'=>['owner','admin','manager'],
        'Manage Offers & Requests'=>['owner','admin','manager'],
        'View Activities'=>['owner','admin','manager','member'],
        'Manage Settings'=>['owner','admin','manager','member']
    ];
    return !in_array($role,$map[$permission]??[]);
}

/* ---------------- FETCH PROFILE DASHBOARD ---------------- */
$profile = $conn->query("SELECT * FROM v_profile_dashboard WHERE profile_id=$profileId")->fetch_assoc() ?? die("Profile not found");

$profileName = $profile['profile_name'];
$profileType = $profile['profile_type'];
$profilePic  = $profile['profile_pic'] ? "../{$profile['profile_pic']}" : "../uploads/profile_pic_placeholder1.png";

$fullName = $profile['first_name'] ?? $profile['household_name'] ?? $profile['institution_name'] ?? $profile['organization_name'] ?? $profileName;

/* ---------------- LOCATION ---------------- */
$regionId   = $profile['individual_region_id'] ?? $profile['family_region_id'] ?? $profile['institution_region_id'] ?? $profile['org_region_id'];
$provinceId = $profile['individual_province_id'] ?? $profile['family_province_id'] ?? $profile['institution_province_id'] ?? $profile['org_province_id'];
$cityId     = $profile['individual_city_id'] ?? $profile['family_city_id'] ?? $profile['institution_city_id'] ?? $profile['org_city_id'];
$barangayId = $profile['individual_barangay_id'] ?? $profile['family_barangay_id'] ?? $profile['institution_barangay_id'] ?? $profile['org_barangay_id'];
$zipCode    = $profile['individual_zip_code'] ?? $profile['family_zip_code'] ?? $profile['institution_zip_code'] ?? $profile['org_zip_code'] ?? 'N/A';

function getLocation($conn,$table,$id){
    if(!$id) return 'N/A';
    $name = $conn->query("SELECT name FROM $table WHERE id=$id")->fetch_assoc()['name'] ?? 'N/A';
    return $name;
}

$locationInfo = [
    'Region'=>getLocation($conn,'regions',$regionId),
    'Province'=>getLocation($conn,'provinces',$provinceId),
    'City/Municipality'=>getLocation($conn,'cities',$cityId),
    'Barangay'=>getLocation($conn,'barangays',$barangayId),
    'ZIP Code'=>$zipCode
];

/* ---------------- PERSONAL INFO ---------------- */
$personalInfo = [];
if($profileType==='individual'){
    $dob = $profile['date_of_birth'] ?? null;
    $personalInfo = [
        'Full Name'=>$fullName,
        'Age'=>$dob?date_diff(date_create($dob),date_create('today'))->y:'N/A',
        'Gender'=>$profile['gender'] ?? 'N/A',
        'Phone'=>$profile['individual_phone'] ?? 'N/A',
        'Email'=>$profile['individual_email'] ?? 'N/A'
    ];
}elseif($profileType==='family'){
    $personalInfo = [
        'Household Name'=>$profile['household_name'] ?? 'N/A',
        'Primary Contact'=>$profile['primary_contact_person'] ?? 'N/A',
        'Phone'=>$profile['family_contact_number'] ?? 'N/A',
        'Email'=>$profile['family_email'] ?? 'N/A'
    ];
}elseif($profileType==='institution'){
    $personalInfo = [
        'Institution Name'=>$profile['institution_name'] ?? 'N/A',
        'Contact Person'=>$profile['official_contact_person'] ?? 'N/A',
        'Phone'=>$profile['official_contact_number'] ?? 'N/A',
        'Email'=>$profile['official_email'] ?? 'N/A'
    ];
}else{
    $personalInfo = [
        'Organization Name'=>$profile['organization_name'] ?? 'N/A',
        'Contact Person'=>$profile['org_contact_person'] ?? 'N/A',
        'Phone'=>$profile['org_contact_number'] ?? 'N/A',
        'Email'=>$profile['org_email'] ?? 'N/A',
        'Registration Number'=>$profile['registration_number'] ?? 'N/A'
    ];
}

/* ---------------- DONATIONS ---------------- */
function getDonations($conn,$profileId,$type='received',$limit=5){
    $res=[];
    $stmt = $conn->prepare("CALL sp_profile_dashboard_get_donations(?,?,?)");
    $stmt->bind_param("isi",$profileId,$type,$limit);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result) $res=$result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $res;
}

$donationsReceived = getDonations($conn,$profileId,'received');
$donationsGiven    = getDonations($conn,$profileId,'given');
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
            <li>
                <a href="profile_pending_donations.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">My Pending Donations</a>
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
