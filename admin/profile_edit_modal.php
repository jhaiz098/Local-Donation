<?php
// Include database connection
include "../db_connect.php";

// Get the profile_id from the URL (if present)
$profile_id = $_GET['profile_id'] ?? 0;

if ($profile_id > 0) {
    // Fetch profile details from the database
    $query = "SELECT * FROM profiles WHERE profile_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $profile_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $profile = $result->fetch_assoc();
        $profileName = $profile['profile_name'];
        $profilePic = $profile['profile_pic'];
        $profileType = $profile['profile_type'];

        // Additional profile details for specific types
        $profileDetails = [];
        if ($profileType == 'individual') {
            $queryDetails = "SELECT * FROM profiles_individual WHERE profile_id = ?";
            $stmtDetails = $conn->prepare($queryDetails);
            $stmtDetails->bind_param("i", $profile_id);
            $stmtDetails->execute();
            $profileDetails = $stmtDetails->get_result()->fetch_assoc();
        } elseif ($profileType == 'family') {
            $queryDetails = "SELECT * FROM profiles_family WHERE profile_id = ?";
            $stmtDetails = $conn->prepare($queryDetails);
            $stmtDetails->bind_param("i", $profile_id);
            $stmtDetails->execute();
            $profileDetails = $stmtDetails->get_result()->fetch_assoc();
        } elseif ($profileType == 'institution') {
            $queryDetails = "SELECT * FROM profiles_institution WHERE profile_id = ?";
            $stmtDetails = $conn->prepare($queryDetails);
            $stmtDetails->bind_param("i", $profile_id);
            $stmtDetails->execute();
            $profileDetails = $stmtDetails->get_result()->fetch_assoc();
        } elseif ($profileType == 'organization') {
            $queryDetails = "SELECT * FROM profiles_organization WHERE profile_id = ?";
            $stmtDetails = $conn->prepare($queryDetails);
            $stmtDetails->bind_param("i", $profile_id);
            $stmtDetails->execute();
            $profileDetails = $stmtDetails->get_result()->fetch_assoc();
        }
    }
}
?>

<!-- profile_edit_modal.php -->
<div id="profileModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 overflow-auto max-h-[80vh]">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Edit Profile</h3>
            <!-- Close button -->
            <button id="closeBtn" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>

        <!-- Profile Picture -->
        <div class="flex justify-center mb-4">
            <img src="<?= !empty($profilePic) ? '../' . $profilePic : '../uploads/profile_pic_placeholder1.png' ?>" 
                 alt="Profile Picture" class="w-24 h-24 rounded-full object-cover">
        </div>

        <!-- Edit Profile Form -->
        <form id="editProfileForm" action="profile_edit_modal.php?profile_id=<?= $profile_id ?>" method="POST">
            <input type="hidden" name="profile_id" value="<?= $profile_id ?>">

            <!-- Profile Name -->
            <div class="mb-4">
                <label for="profile_name" class="block text-sm font-medium text-gray-700">Profile Name</label>
                <input type="text" id="profile_name" name="profile_name" value="<?= htmlspecialchars($profileName) ?>" 
                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <?php if ($profileType == 'individual'): ?>
                <!-- Individual Fields -->
                <div class="mb-4">
                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($profileDetails['first_name']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="<?= htmlspecialchars($profileDetails['middle_name']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($profileDetails['last_name']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($profileDetails['date_of_birth']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                    <select id="gender" name="gender" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Male" <?= $profileDetails['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $profileDetails['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= $profileDetails['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            <?php elseif ($profileType == 'family'): ?>
                <!-- Family Fields -->
                <div class="mb-4">
                    <label for="household_name" class="block text-sm font-medium text-gray-700">Household Name</label>
                    <input type="text" id="household_name" name="household_name" value="<?= htmlspecialchars($profileDetails['household_name']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="primary_contact_person" class="block text-sm font-medium text-gray-700">Primary Contact Person</label>
                    <input type="text" id="primary_contact_person" name="primary_contact_person" value="<?= htmlspecialchars($profileDetails['primary_contact_person']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($profileDetails['contact_number']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            <?php elseif ($profileType == 'institution'): ?>
                <!-- Institution Fields -->
                <div class="mb-4">
                    <label for="institution_name" class="block text-sm font-medium text-gray-700">Institution Name</label>
                    <input type="text" id="institution_name" name="institution_name" value="<?= htmlspecialchars($profileDetails['institution_name']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="official_contact_person" class="block text-sm font-medium text-gray-700">Official Contact Person</label>
                    <input type="text" id="official_contact_person" name="official_contact_person" value="<?= htmlspecialchars($profileDetails['official_contact_person']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="official_contact_number" class="block text-sm font-medium text-gray-700">Official Contact Number</label>
                    <input type="text" id="official_contact_number" name="official_contact_number" value="<?= htmlspecialchars($profileDetails['official_contact_number']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            <?php elseif ($profileType == 'organization'): ?>
                <!-- Organization Fields -->
                <div class="mb-4">
                    <label for="organization_name" class="block text-sm font-medium text-gray-700">Organization Name</label>
                    <input type="text" id="organization_name" name="organization_name" value="<?= htmlspecialchars($profileDetails['organization_name']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                    <input type="text" id="contact_person" name="contact_person" value="<?= htmlspecialchars($profileDetails['contact_person']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="mb-4">
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($profileDetails['contact_number']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            <?php endif; ?>

            <!-- Save Changes Button -->
            <div class="flex justify-between items-center">
                <button id="closeModalBtn" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500">Close</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Function to close the modal
    function closeModal() {
        const modal = document.getElementById('profileModal');
        if (modal) {
            modal.style.display = 'none';  // Hide the modal
        }
    }

    // Close the modal when the "X" button is clicked
    document.querySelector('#closeBtn').addEventListener('click', function() {
        closeModal();
    });

    // Close the modal when the user presses "Esc"
    window.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    // Close modal when the user clicks outside the modal content (clicks the overlay)
    document.getElementById('profileModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeModal();
        }
    });
</script>
