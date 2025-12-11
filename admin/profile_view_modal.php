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

        // Fetch additional profile details based on profile type
        $table = '';
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
        
        <p><strong>Profile Name:</strong> <?= htmlspecialchars($profileName) ?></p>
        <p><strong>Profile Type:</strong> <?= ucfirst($profileType) ?></p>
        <p><strong>Created At:</strong> <?= $createdAt ?></p>
        
        <p><strong>Location:</strong> <?= htmlspecialchars("$barangayName, $cityName, $provinceName, $regionName") ?></p>

        <!-- Show additional profile details -->
        <?php if ($profileDetails): ?>
            <p><strong>Phone Number:</strong> <?= htmlspecialchars($profileDetails['phone_number']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($profileDetails['email']) ?></p>
            <!-- Add other relevant profile details here -->
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
