<!-- donation_edit.php -->
<div id="edit-modal"
     class="fixed inset-0 bg-black/50 flex items-center justify-center hidden z-50">

    <div class="bg-white p-6 rounded shadow-lg w-96 max-h-[90vh] flex flex-col">
        <h3 class="text-xl font-semibold mb-4">
            Edit Request / Offer
        </h3>

        <form id="editForm"
              action="update_donation.php"
              method="POST"
              class="space-y-3 overflow-y-auto flex-1 text-sm">

            <input type="hidden" name="entry_id" id="edit-entry-id">

            <!-- Type -->
            <div>
                <label class="block text-gray-700">Type</label>
                <select id="edit-entry-type"
                        name="entry_type"
                        class="w-full border rounded p-1 text-sm"
                        required>
                    <option value="request">Request</option>
                    <option value="offer">Offer</option>
                </select>
            </div>

            <!-- Details -->
            <div>
                <label class="block text-gray-700">Details</label>
                <textarea id="edit-details"
                          name="details"
                          class="w-full border rounded p-1 h-20 text-sm"
                          required></textarea>
            </div>

            <!-- Target Area -->
            <div id="edit-target-location">
                <label class="block text-gray-700">Target Area</label>
                <input type="text"
                       name="target_location"
                       class="w-full border rounded p-1 text-sm"
                       required>
            </div>

            <!-- Items -->
            <div>
                <label class="block text-gray-700">Items</label>

                <div id="edit-items-list"
                     class="space-y-2 max-h-64 overflow-y-auto pr-2">
                     
                    <!-- items injected here -->
                </div>
                <div id="itemsContainer" class="space-y-2 max-h-64 overflow-y-auto pr-2"></div>
                <button type="button" id="addItemBtn" class="mt-2 px-2 py-1 bg-green-500 text-white rounded text-sm">Add Item</button>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2 mt-3">
                <button type="button"
                        onclick="closeEditModal()"
                        class="px-3 py-1 bg-gray-200 rounded text-sm">
                    Cancel
                </button>

                <button type="submit"
                        class="px-3 py-1 bg-blue-500 text-white rounded text-sm">
                    Save
                </button>
            </div>

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

    if(entryType == 'request'){
        document.getElementById('edit-target-location').classList.add('hidden')
    }else{
        document.getElementById('edit-target-location').value = targetLocation;
    }

    document.getElementById('edit-entry-type').value = entryType;
    document.getElementById('edit-details').value = details;

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

                <select class="border rounded p-1 flex-1 item-select text-sm">${item.item_name}</select>
                <input type="number" class="border rounded p-1 w-20 text-sm" min="1" value="${item.quantity}">
                <select class="border rounded p-1 w-20 unit-select text-sm">${item.unit_name}</select>
                <button type="button" class="bg-red-500 text-white px-2 rounded text-sm">X</button>
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
