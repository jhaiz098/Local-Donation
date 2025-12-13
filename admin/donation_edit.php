<!-- donation_edit.php -->
<!-- donation_edit.php -->
<div id="edit-modal" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <h2 class="text-2xl font-bold mb-4">Edit Donation / Request</h2>

        <form action="update_donation.php" method="POST">
            <input type="hidden" name="entry_id" id="edit-entry-id">

            <!-- Donation Details -->
            <div class="mb-4">
                <label for="edit-entry-type" class="block text-sm font-medium text-gray-700">Type</label>
                <select id="edit-entry-type" name="entry_type" class="mt-1 block w-full rounded border-gray-300" required>
                    <option value="offer">Offer</option>
                    <option value="request">Request</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="edit-details" class="block text-sm font-medium text-gray-700">Details</label>
                <textarea id="edit-details" name="details" rows="4" class="mt-1 block w-full rounded border-gray-300" required></textarea>
            </div>

            <div class="mb-4">
                <label for="edit-target-location" class="block text-sm font-medium text-gray-700">Target Location</label>
                <input type="text" id="edit-target-location" name="target_location" class="mt-1 block w-full rounded border-gray-300" required>
            </div>

            <!-- Items -->
            <div id="edit-items-list" class="mb-4">
                <!-- Items will be populated here dynamically -->
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save Changes</button>
            <button type="button" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 ml-4" onclick="closeEditModal()">Cancel</button>
        </form>
    </div>
</div>


<script>
// Function to open the edit modal and populate the form fields
function openEditModal(button) {
    const entryId = button.getAttribute('data-entry-id');
    const profileId = button.getAttribute('data-profile-id');
    const entryType = button.getAttribute('data-entry-type');
    const details = button.getAttribute('data-details');
    const targetLocation = button.getAttribute('data-target-location');
    const createdAt = decodeURIComponent(button.getAttribute('data-created-at'));  // Decode URL-encoded date
    const updatedAt = decodeURIComponent(button.getAttribute('data-updated-at'));  // Decode URL-encoded date
    const itemsData = JSON.parse(button.getAttribute('data-items'));  // Parse the items data from JSON

    // Populate the form with data
    document.getElementById('edit-entry-id').value = entryId;
    document.getElementById('edit-entry-type').value = entryType;
    document.getElementById('edit-details').value = details;
    document.getElementById('edit-target-location').value = targetLocation;

    // Populate the items
    const itemsList = document.getElementById('edit-items-list');
    itemsList.innerHTML = '';  // Clear existing items
    itemsData.forEach((item, index) => {
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('mb-2');
        itemDiv.innerHTML = `
            <div>
                <label class="block text-sm font-medium text-gray-700">Item Name</label>
                <input type="text" name="item_names[]" value="${item.item_name}" class="mt-1 block w-full rounded border-gray-300" required>

                <label class="block text-sm font-medium text-gray-700 mt-2">Quantity</label>
                <input type="number" name="quantities[]" value="${item.quantity}" class="mt-1 block w-full rounded border-gray-300" required>

                <label class="block text-sm font-medium text-gray-700 mt-2">Unit</label>
                <input type="text" name="unit_names[]" value="${item.unit_name}" class="mt-1 block w-full rounded border-gray-300" required>
            </div>
        `;
        itemsList.appendChild(itemDiv);
    });

    // Show the modal
    document.getElementById('edit-modal').classList.remove('hidden');
}

// Function to close the edit modal
function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}
</script>
