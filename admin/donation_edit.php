<?php
// Fetch all items
$item_sql = "SELECT * FROM items";
$item_result = $conn->query($item_sql);
$all_items = [];
while ($row = $item_result->fetch_assoc()) {
    $all_items[] = $row;
}

// Fetch all item units, grouped by item_id
$unit_sql = "SELECT * FROM item_units"; 
$unit_result = $conn->query($unit_sql);
$all_units = [];

// Create an associative array of item_id => units
while ($row = $unit_result->fetch_assoc()) {
    $all_units[$row['item_id']][] = $row['unit_name'];
}

// Convert to JSON for JavaScript
$all_items_json = json_encode($all_items);
$all_units_json = json_encode($all_units);
?>

<script>
// Fetching PHP data and passing it to JavaScript
const allItems = <?php echo $all_items_json; ?>;
const allUnits = <?php echo $all_units_json; ?>;

// console.log(allItems, allUnits);  // Debugging: check if data is passed correctly

// Function to open the edit modal and populate the form fields
function openEditModal(button) {
    const entryId = button.getAttribute('data-entry-id');
    const profileId = button.getAttribute('data-profile-id');
    const entryType = button.getAttribute('data-entry-type');
    const details = button.getAttribute('data-details');
    const targetLocation = button.getAttribute('data-target-location');
    const createdAt = decodeURIComponent(button.getAttribute('data-created-at'));  // Decode URL-encoded date
    const updatedAt = decodeURIComponent(button.getAttribute('data-updated-at'));  // Decode URL-encoded date

    // Parse the items data from JSON
    const itemsData = JSON.parse(button.getAttribute('data-items'));  

    // Debugging: Check the content of itemsData
    // console.log('itemsData:', itemsData);
    // alert('itemsData: ' + JSON.stringify(itemsData));  // Show in alert for visual confirmation

    // Populate the form with data
    document.getElementById('edit-entry-id').value = entryId;

    if (entryType == 'request') {
    document.getElementById('edit-target-location').classList.add('hidden');
    } else {
        document.getElementById('edit-target-location').classList.remove('hidden');
        document.getElementById('modalTargetArea').value = targetLocation;  // Update this line to target the select element
    }


    document.getElementById('edit-entry-type').value = entryType;
    document.getElementById('edit-details').value = details;

    // Populate the items
    const itemsList = document.getElementById('edit-items-list');
    itemsList.innerHTML = '';  // Clear existing items

    itemsData.forEach((item) => {
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('mb-2', 'flex', 'gap-2', 'items-center');

        // Item select
        const itemSelect = document.createElement('select');
        itemSelect.className = 'border rounded p-1 flex-1 item-select text-sm';

        // Unit select (to be populated dynamically)
        const unitSelect = document.createElement('select');
        unitSelect.className = 'border rounded p-1 w-20 unit-select text-sm';

        // Function to populate units based on item_id
        function populateUnits(selectedItemId) {
            const units = allUnits[selectedItemId] || [];
            unitSelect.innerHTML = ''; // Clear previous options

            // Populate the units for the selected item
            units.forEach((unit) => {
                const option = document.createElement('option');
                option.value = unit;
                option.textContent = unit;
                unitSelect.appendChild(option);
            });

            // Pre-select the unit based on the item's unit_name from the data
            if (item.unit_name) {
                unitSelect.value = item.unit_name; // Pre-select the unit if it's available in the data
            }
        }

        // Populate item select options
        allItems.forEach((it) => {
            const option = document.createElement('option');
            option.value = it.item_id; // Use item_id, not item_name
            option.textContent = it.item_name;

            // Pre-select the correct item based on the entry data
            if (it.item_name === item.item_name){
                option.selected = true; // Compare item names
                populateUnits(it.item_id);  // Now calling populateUnits after item is selected
            } 

            itemSelect.appendChild(option);
        });

        // Quantity input
        const qtyInput = document.createElement('input');
        qtyInput.type = 'number';
        qtyInput.min = 1;
        qtyInput.value = item.quantity;
        qtyInput.className = 'border rounded p-1 w-20 text-sm';

        // Update unit options when a different item is selected
        itemSelect.addEventListener('change', function() {
            populateUnits(itemSelect.value); // This ensures the unit dropdown is populated with correct options
        });

        // Remove button
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'bg-red-500 text-white px-2 rounded text-sm';
        removeBtn.textContent = 'X';
        removeBtn.onclick = () => itemDiv.remove();

        // Append elements to itemDiv
        itemDiv.appendChild(itemSelect);
        itemDiv.appendChild(qtyInput);
        itemDiv.appendChild(unitSelect);
        itemDiv.appendChild(removeBtn);

        // Append itemDiv to the items list
        itemsList.appendChild(itemDiv);
    });

    // Show the modal
    document.getElementById('edit-modal').classList.remove('hidden');
}


// Function to close the edit modal
function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function () {
    // Fetch the 'Add Item' button
    const addItemBtn = document.getElementById('addItemBtn');

    addItemBtn.addEventListener('click', function () {
        // Fetch the items list (this is where the new item input will be appended)
        const itemsList = document.getElementById('edit-items-list');

        // Create a new div for the item
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('mb-2', 'flex', 'gap-2', 'items-center');

        // Create the item select dropdown
        const itemSelect = document.createElement('select');
        itemSelect.className = 'border rounded p-1 flex-1 item-select text-sm';

        // Create the unit select dropdown
        const unitSelect = document.createElement('select');
        unitSelect.className = 'border rounded p-1 w-20 unit-select text-sm';

        // Function to populate the unit select based on the selected item
        function populateUnits(selectedItemId) {
            const units = allUnits[selectedItemId] || [];
            unitSelect.innerHTML = ''; // Clear previous options

            // Populate the units for the selected item
            units.forEach((unit) => {
                const option = document.createElement('option');
                option.value = unit;
                option.textContent = unit;
                unitSelect.appendChild(option);
            });
        }

        // Populate the item select options dynamically
        allItems.forEach((it) => {
            const option = document.createElement('option');
            option.value = it.item_id;
            option.textContent = it.item_name;
            itemSelect.appendChild(option);
        });

        // Quantity input field
        const qtyInput = document.createElement('input');
        qtyInput.type = 'number';
        qtyInput.min = 1;
        qtyInput.value = 1; // Default quantity
        qtyInput.className = 'border rounded p-1 w-20 text-sm';

        // Update the unit options when an item is selected
        itemSelect.addEventListener('change', function () {
            populateUnits(itemSelect.value); // Ensure the unit dropdown is populated with the correct options
        });

        // Add a remove button to the item input
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'bg-red-500 text-white px-2 rounded text-sm';
        removeBtn.textContent = 'X';
        removeBtn.onclick = () => itemDiv.remove(); // Remove the item when clicked

        // Append the item select, quantity input, unit select, and remove button to the itemDiv
        itemDiv.appendChild(itemSelect);
        itemDiv.appendChild(qtyInput);
        itemDiv.appendChild(unitSelect);
        itemDiv.appendChild(removeBtn);

        // Append the itemDiv to the items list
        itemsList.appendChild(itemDiv);

        // Populate the units for the first item on creation (optional)
        populateUnits(allItems[0].item_id);
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editForm');

    // Ensure the form exists before adding the event listener
    if (editForm) {
        editForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Collect form data
            const entryId = document.getElementById('edit-entry-id').value;
            const entryType = document.getElementById('edit-entry-type').value;
            const details = document.getElementById('edit-details').value;
            const targetLocation = document.getElementById('modalTargetArea').value;

            // Collect the selected items and their quantities
            const items = [];
            const itemDivs = document.querySelectorAll('#edit-items-list > div');
            itemDivs.forEach(itemDiv => {
                const itemSelect = itemDiv.querySelector('.item-select');
                const unitSelect = itemDiv.querySelector('.unit-select');
                const qtyInput = itemDiv.querySelector('input[type="number"]');

                items.push({
                    item_id: itemSelect.value,
                    unit_name: unitSelect.value,
                    quantity: qtyInput.value
                });
            });

            // Prepare data to send via AJAX
            const formData = new FormData();
            formData.append('entry_id', entryId);
            formData.append('entry_type', entryType);
            formData.append('details', details);
            formData.append('target_location', targetLocation);
            formData.append('items', JSON.stringify(items)); // Send items as JSON string

            // Send the form data via AJAX to update_donation.php
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_donation.php', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Log the raw response to the console for debugging
                    console.log(xhr.responseText);

                    // Try parsing the response as JSON
                    try {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.success) {
                            alert('Donation updated successfully!');
                            window.location.reload();
                            closeEditModal(); // Close the modal on success
                        } else {
                            alert('Error updating donation: ' + response.message);
                        }
                    } catch (e) {
                        // Handle invalid JSON response
                        console.error("Error parsing JSON:", e);
                        alert('Unexpected response from server.');
                    }
                } else {
                    alert('An error occurred. Please try again.');
                }
            };

            xhr.send(formData); // Send the form data to the server
        });
    }
});




</script>

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
                <select id="modalTargetArea" name="target_location" class="w-full border rounded p-1 text-sm">
                    <option value="philippines">Entire Philippines</option>
                    <option value="region">Same Region</option>
                    <option value="province">Same Province</option>
                    <option value="city">Same City/Municipality</option>
                    <option value="barangay">Same Barangay</option>
                </select>
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