<!-- profile_edit_modal.php -->

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
        }
    }
}
?>

<!-- profile_edit_modal.php -->
<div id="profileModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50" style="display: none;">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-lg w-full">
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
            <?php elseif ($profileType == 'family'): ?>
                <div class="mb-4">
                    <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($profileDetails['contact_number']) ?>" 
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            <?php endif; ?>

            <div class="mb-4">
            <button id="closeModalBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Close</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save Changes</button>
        </div>
    </div>
</div>

<script>
    // Function to close the modal
    function closeModal() {
        const modal = document.getElementById('profileEditModal');
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
    document.getElementById('profileEditModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeModal();
        }
    });
</script>
