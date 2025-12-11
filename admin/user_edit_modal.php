<!-- user_edit_modal.php -->
<div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-start justify-center z-50 overflow-auto p-4">
    <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-6 relative mt-10">
        <button id="closeEditModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">✕</button>
        <h3 class="text-xl font-bold mb-4">Edit User</h3>
        <form id="editUserForm" class="grid grid-cols-2 gap-4 text-sm">
            <!-- Profile Picture -->
            <div class="col-span-2 mt-4 flex flex-col items-center">
                <strong>Profile Picture:</strong>
                <img id="editModalProfilePic" class="w-24 h-24 rounded-full mt-2 mb-3 object-cover">
                <input type="file" id="editProfilePicInput" class="mt-2 w-full text-sm" accept="image/*">
            </div>

            <hr class="col-span-2 w-full">

            <input type="hidden" id="editUserId" name="user_id">

            <div>
                <label class="block font-semibold">First Name</label>
                <input type="text" id="editFirstName" name="first_name" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">Middle Name</label>
                <input type="text" id="editMiddleName" name="middle_name" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">Last Name</label>
                <input type="text" id="editLastName" name="last_name" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">Date of Birth</label>
                <input type="date" id="editDOB" name="date_of_birth" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">Gender</label>
                <select id="editGender" name="gender" class="border rounded w-full px-2 py-1">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold">ZIP Code</label>
                <input type="text" id="editZip" name="zip_code" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">Phone Number</label>
                <input type="text" id="editPhone" name="phone_number" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">Email</label>
                <input type="email" id="editEmail" name="email" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">Role</label>
                <select id="editRole" name="role" class="border rounded w-full px-2 py-1">
                    <option value="User">User</option>
                    <option value="Superuser">Superuser</option>
                    <option value="Admin">Admin</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold">Region ID</label>
                <input type="number" id="editRegion" name="region_id" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">Province ID</label>
                <input type="number" id="editProvince" name="province_id" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">City ID</label>
                <input type="number" id="editCity" name="city_id" class="border rounded w-full px-2 py-1">
            </div>
            <div>
                <label class="block font-semibold">Barangay ID</label>
                <input type="number" id="editBarangay" name="barangay_id" class="border rounded w-full px-2 py-1">
            </div>

            <!-- Buttons -->
            <div class="col-span-2 flex justify-end mt-4 gap-2">
                <button type="button" id="cancelEditBtn" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    const editUserModal = document.getElementById('editUserModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEditBtn');

    function initEditButtons() {
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('editUserId').value = btn.getAttribute('data-user_id');
                document.getElementById('editFirstName').value = btn.getAttribute('data-first_name');
                document.getElementById('editMiddleName').value = btn.getAttribute('data-middle_name');
                document.getElementById('editLastName').value = btn.getAttribute('data-last_name');
                document.getElementById('editDOB').value = btn.getAttribute('data-dob');
                document.getElementById('editGender').value = btn.getAttribute('data-gender');
                document.getElementById('editZip').value = btn.getAttribute('data-zip');
                document.getElementById('editPhone').value = btn.getAttribute('data-phone');
                document.getElementById('editEmail').value = btn.getAttribute('data-email');
                document.getElementById('editRole').value = btn.getAttribute('data-role');
                document.getElementById('editRegion').value = btn.getAttribute('data-region');
                document.getElementById('editProvince').value = btn.getAttribute('data-province');
                document.getElementById('editCity').value = btn.getAttribute('data-city');
                document.getElementById('editBarangay').value = btn.getAttribute('data-barangay');
                document.getElementById('editModalProfilePic').src = btn.getAttribute('data-profile_pic');
                
                editUserModal.classList.remove('hidden');
                editUserModal.classList.add('flex');
            });
        });
    }

    closeEditModal.addEventListener('click', () => {
        editUserModal.classList.add('hidden');
        editUserModal.classList.remove('flex');
    });

    cancelEditBtn.addEventListener('click', () => {
        editUserModal.classList.add('hidden');
        editUserModal.classList.remove('flex');
    });

    editUserModal.addEventListener('click', (e) => {
        if(e.target === editUserModal){
            editUserModal.classList.add('hidden');
            editUserModal.classList.remove('flex');
        }
    });

    // Optional: handle form submission here via AJAX
</script>
