<?php
include "../db_connect.php";

// Get the profile ID from the query string
$profile_id = $_GET['profile_id'] ?? 0;

if ($profile_id > 0) {
    // Fetch the profile details
    $query = "SELECT * FROM profiles WHERE profile_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $profile_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $profile = $result->fetch_assoc();
        $profileName = $profile['profile_name'];
        $profileType = $profile['profile_type'];
        $createdAt = $profile['created_at'];
        $profilePic = $profile['profile_pic'];  // Add profile picture

        // Fetch additional profile details based on profile type
        $table = '';
        $profileDetails = [];
        if ($profileType == 'individual') {
            $table = 'profiles_individual';
        } elseif ($profileType == 'family') {
            $table = 'profiles_family';
        } elseif ($profileType == 'institution') {
            $table = 'profiles_institution';
        } elseif ($profileType == 'organization') {
            $table = 'profiles_organization';
        }

        if ($table) {
            $queryDetails = "SELECT * FROM $table WHERE profile_id = ?";
            $stmtDetails = $conn->prepare($queryDetails);
            $stmtDetails->bind_param("i", $profile_id);
            $stmtDetails->execute();
            $profileDetails = $stmtDetails->get_result()->fetch_assoc();
        }

        // Fetch location details
        $regionName = $provinceName = $cityName = $barangayName = '';
        if ($profileDetails) {
            $regionId = $profileDetails['region_id'];
            $provinceId = $profileDetails['province_id'];
            $cityId = $profileDetails['city_id'];
            $barangayId = $profileDetails['barangay_id'];

            // Query to get location names
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

            if ($location) {
                $regionName = $location['region_name'];
                $provinceName = $location['province_name'];
                $cityName = $location['city_name'];
                $barangayName = $location['barangay_name'];
            }
        }

        // Fetch members if profile type is not 'individual'
        $members = [];
        if ($profileType !== 'individual') {
            $queryMembers = "SELECT pm.user_id, u.first_name, u.last_name, pm.role
                             FROM profile_members pm
                             JOIN users u ON pm.user_id = u.user_id
                             WHERE pm.profile_id = ?";
            $stmtMembers = $conn->prepare($queryMembers);
            $stmtMembers->bind_param("i", $profile_id);
            $stmtMembers->execute();
            $membersResult = $stmtMembers->get_result();

            // Add members to the array
            while ($member = $membersResult->fetch_assoc()) {
                $members[] = $member;
            }
        }

    } else {
        echo "Profile not found.";
        exit();
    }
} else {
    echo "Invalid profile ID.";
    exit();
}
?>

<!-- Modal for Profile Details -->
<div id="profileModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-lg w-full">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Profile Details</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        
        <!-- Profile Picture -->
        <div class="flex justify-center mb-4">
            <img src="<?= !empty($profilePic) ? '../' . $profilePic : '../uploads/profile_pic_placeholder1.png' ?>" 
                 alt="Profile Picture" class="w-24 h-24 rounded-full object-cover">
        </div>

        <!-- Profile Name, Type, and Creation Date -->
        <p><strong>Profile Name:</strong> <?= htmlspecialchars($profileName) ?></p>
        <p><strong>Profile Type:</strong> <?= ucfirst($profileType) ?></p>
        <p><strong>Created At:</strong> <?= $createdAt ?></p>
        
        <p><strong>Location:</strong> <?= htmlspecialchars("$barangayName, $cityName, $provinceName, $regionName") ?></p>

        <!-- Conditional Fields for Each Profile Type -->
        <?php if ($profileType == 'individual' && $profileDetails): ?>
            <p><strong>First Name:</strong> <?= htmlspecialchars($profileDetails['first_name']) ?></p>
            <p><strong>Middle Name:</strong> <?= htmlspecialchars($profileDetails['middle_name']) ?></p>
            <p><strong>Last Name:</strong> <?= htmlspecialchars($profileDetails['last_name']) ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($profileDetails['date_of_birth']) ?></p>
            <p><strong>Gender:</strong> <?= htmlspecialchars($profileDetails['gender']) ?></p>
            <p><strong>Phone Number:</strong> <?= htmlspecialchars($profileDetails['phone_number']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($profileDetails['email']) ?></p>

        <?php elseif ($profileType == 'family' && $profileDetails): ?>
            <p><strong>Household Name:</strong> <?= htmlspecialchars($profileDetails['household_name']) ?></p>
            <p><strong>Primary Contact Person:</strong> <?= htmlspecialchars($profileDetails['primary_contact_person']) ?></p>
            <p><strong>Contact Number:</strong> <?= htmlspecialchars($profileDetails['contact_number']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($profileDetails['email']) ?></p>
            <p><strong>Zip Code:</strong> <?= htmlspecialchars($profileDetails['zip_code']) ?></p>

        <?php elseif ($profileType == 'institution' && $profileDetails): ?>
            <p><strong>Institution Type:</strong> <?= htmlspecialchars($profileDetails['institution_type']) ?></p>
            <p><strong>Institution Name:</strong> <?= htmlspecialchars($profileDetails['institution_name']) ?></p>
            <p><strong>Official Contact Person:</strong> <?= htmlspecialchars($profileDetails['official_contact_person']) ?></p>
            <p><strong>Official Contact Number:</strong> <?= htmlspecialchars($profileDetails['official_contact_number']) ?></p>
            <p><strong>Official Email:</strong> <?= htmlspecialchars($profileDetails['official_email']) ?></p>

        <?php elseif ($profileType == 'organization' && $profileDetails): ?>
            <p><strong>Organization Type:</strong> <?= htmlspecialchars($profileDetails['organization_type']) ?></p>
            <p><strong>Organization Name:</strong> <?= htmlspecialchars($profileDetails['organization_name']) ?></p>
            <p><strong>Contact Person:</strong> <?= htmlspecialchars($profileDetails['contact_person']) ?></p>
            <p><strong>Contact Number:</strong> <?= htmlspecialchars($profileDetails['contact_number']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($profileDetails['email']) ?></p>
            <p><strong>Registration Number:</strong> <?= htmlspecialchars($profileDetails['registration_number']) ?></p>
        <?php endif; ?>

        <!-- Display Members if Profile Type is not 'individual' -->
        <?php if (!empty($members)): ?>
            <div class="mt-4">
                <h4 class="font-semibold">Profile Members:</h4>
                <div class="overflow-y-auto max-h-64 mt-2"> <!-- Added a wrapper with scroll functionality -->
                    <table class="min-w-full border-collapse text-sm">
                        <thead>
                            <tr>
                                <th class="p-2 border">User ID</th>
                                <th class="p-2 border">Name</th>
                                <th class="p-2 border">Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td class="p-2 border"><?= htmlspecialchars($member['user_id']) ?></td>
                                    <td class="p-2 border"><?= htmlspecialchars($member['first_name'] . ' ' . $member['last_name']) ?></td>
                                    <td class="p-2 border"><?= htmlspecialchars($member['role']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>


        <div class="mt-4 flex justify-end">
            <button onclick="closeModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Close</button>
        </div>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('profileModal').style.display = 'none';
    }
</script>
