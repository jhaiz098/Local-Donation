<?php
include "../db_connect.php";

// Get the profile_id from the URL
$profile_id = $_GET['profile_id'] ?? 0;

if ($profile_id > 0) {
    // Fetch profile details
    $query = "SELECT * FROM profiles WHERE profile_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $profile_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $profile = $result->fetch_assoc();
        $profileName = $profile['profile_name'];
        $profileType = $profile['profile_type'];
        $profilePic = $profile['profile_pic'];
        $createdAt = $profile['created_at'];
        
        // Get additional profile details based on profile type
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
    } else {
        echo "Profile not found.";
        exit();
    }
} else {
    echo "Invalid profile ID.";
    exit();
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $profile_name = $_POST['profile_name'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';

    // Step 1: Update the main profile table
    $queryProfile = "UPDATE profiles SET profile_name = ? WHERE profile_id = ?";
    $stmtProfile = $conn->prepare($queryProfile);
    $stmtProfile->bind_param("si", $profile_name, $profile_id);
    $stmtProfile->execute();

    // Step 2: Update the specific profile table based on the profile type
    $queryDetails = "";
    if ($profileType == 'individual') {
        $queryDetails = "UPDATE profiles_individual SET first_name = ?, last_name = ?, phone_number = ? WHERE profile_id = ?";
        $stmtDetails = $conn->prepare($queryDetails);
        $stmtDetails->bind_param("sssi", $first_name, $last_name, $phone_number, $profile_id);
    } elseif ($profileType == 'family') {
        $queryDetails = "UPDATE profiles_family SET contact_number = ? WHERE profile_id = ?";
        $stmtDetails = $conn->prepare($queryDetails);
        $stmtDetails->bind_param("si", $contact_number, $profile_id);
    }

    // Execute the query
    if ($queryDetails) {
        $stmtDetails->execute();
    }

    // Redirect back to the profile page or display a success message
    echo '<script>alert("Profile updated successfully!"); window.location.href="admin_profiles.php";</script>';
    exit();
}

?>

<!-- Profile Edit Modal -->
<div id="profileEditModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-lg w-full">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Edit Profile</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <!-- Profile Picture -->
        <div class="flex justify-center mb-4">
            <img src="<?= !empty($profilePic) ? '../' . $profilePic : '../uploads/profile_pic_placeholder1.png' ?>" 
                 alt="Profile Picture" class="w-24 h-24 rounded-full object-cover">
        </div>

        <!-- Edit Profile Form -->
        <form id="editProfileForm" action="profile_edit_modal.php?profile_id=<?= $profile_id ?>" method="POST">
            <input type="hidden" name="profile_id" value="<?= $profile_id ?>">

            <div class="mb-4">
                <label for="profile_name" class="block text-sm font-medium text-gray-700">Profile Name</label>
                <input type="text" id="profile_name" name="profile_name" value="<?= htmlspecialchars($profileName) ?>" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <?php if ($profileType == 'individual'): ?>
                <div class="mb-4">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($profileDetails['first_name']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($profileDetails['last_name']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($profileDetails['phone_number']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            <?php elseif ($profileType == 'family'): ?>
                <div class="mb-4">
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($profileDetails['contact_number']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function closeModal() {
        document.getElementById('profileEditModal').style.display = 'none';
    }
</script>
