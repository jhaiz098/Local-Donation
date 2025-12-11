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
                <button type="button" id="changeProfileBtn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Change Profile
                </button>
                <input type="file" id="editProfilePicInput" class="hidden" accept="image/*">
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

            <!-- Location Dropdowns -->
            <div>
                <label class="block font-semibold">Region</label>
                <select id="editRegion" name="region_id" class="border rounded w-full px-2 py-1"></select>
            </div>
            <div>
                <label class="block font-semibold">Province</label>
                <select id="editProvince" name="province_id" class="border rounded w-full px-2 py-1"></select>
            </div>
            <div>
                <label class="block font-semibold">City</label>
                <select id="editCity" name="city_id" class="border rounded w-full px-2 py-1"></select>
            </div>
            <div>
                <label class="block font-semibold">Barangay</label>
                <select id="editBarangay" name="barangay_id" class="border rounded w-full px-2 py-1"></select>
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
            // Remove previous click listeners if re-initializing
            btn.replaceWith(btn.cloneNode(true));
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const userId = btn.dataset.user_id;
                console.log('Editing user_id:', userId); // check in console

                document.getElementById('editUserId').value = userId;
                document.getElementById('editFirstName').value = btn.dataset.first_name;
                document.getElementById('editMiddleName').value = btn.dataset.middle_name;
                document.getElementById('editLastName').value = btn.dataset.last_name;
                document.getElementById('editDOB').value = btn.dataset.dob;
                document.getElementById('editGender').value = btn.dataset.gender;
                document.getElementById('editZip').value = btn.dataset.zip;
                document.getElementById('editPhone').value = btn.dataset.phone;
                document.getElementById('editEmail').value = btn.dataset.email;
                document.getElementById('editModalProfilePic').src = btn.dataset.profile_pic;

                // Load locations
                loadRegions(btn.dataset.region);
                loadProvinces(btn.dataset.region, btn.dataset.province);
                loadCities(btn.dataset.province, btn.dataset.city);
                loadBarangays(btn.dataset.city, btn.dataset.barangay);

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



    // Load Regions
    function loadRegions(selectedRegion = null) {
        fetch('../get_regions.php')
        .then(res => res.json())
        .then(data => {
            const regionSelect = document.getElementById('editRegion');
            regionSelect.innerHTML = '<option value="">Select Region</option>';
            data.forEach(r => {
                const selected = selectedRegion == r.id ? 'selected' : '';
                regionSelect.innerHTML += `<option value="${r.id}" ${selected}>${r.name}</option>`;
            });
            toggleProvince(regionSelect.value);
        });
    }

    // Load Provinces
    function loadProvinces(regionId, selectedProvince = null) {
        const provinceSelect = document.getElementById('editProvince');
        provinceSelect.innerHTML = '<option value="">Select Province</option>';
        if (!regionId) {
            toggleProvince(null);
            return;
        }
        fetch(`../get_provinces.php?region_id=${regionId}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(p => {
                const selected = selectedProvince == p.id ? 'selected' : '';
                provinceSelect.innerHTML += `<option value="${p.id}" ${selected}>${p.name}</option>`;
            });
            toggleProvince(regionId);
        });
    }

    // Load Cities
    function loadCities(provinceId, selectedCity = null) {
        const citySelect = document.getElementById('editCity');
        citySelect.innerHTML = '<option value="">Select City</option>';
        if (!provinceId) {
            toggleCity(null);
            return;
        }
        fetch(`../get_cities.php?province_id=${provinceId}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(c => {
                const selected = selectedCity == c.id ? 'selected' : '';
                citySelect.innerHTML += `<option value="${c.id}" ${selected}>${c.name}</option>`;
            });
            toggleCity(provinceId);
        });
    }

    // Load Barangays
    function loadBarangays(cityId, selectedBarangay = null) {
        const barangaySelect = document.getElementById('editBarangay');
        barangaySelect.innerHTML = '<option value="">Select Barangay</option>';
        if (!cityId) {
            toggleBarangay(null);
            return;
        }
        fetch(`../get_barangays.php?city_id=${cityId}`)
        .then(res => res.json())
        .then(data => {
            data.forEach(b => {
                const selected = selectedBarangay == b.id ? 'selected' : '';
                barangaySelect.innerHTML += `<option value="${b.id}" ${selected}>${b.name}</option>`;
            });
            toggleBarangay(cityId);
        });
    }

    // Enable/disable functions
    function toggleProvince(regionId) {
        const province = document.getElementById('editProvince');
        province.disabled = !regionId;
        province.classList.toggle('bg-gray-200', !regionId);
    }

    function toggleCity(provinceId) {
        const city = document.getElementById('editCity');
        city.disabled = !provinceId;
        city.classList.toggle('bg-gray-200', !provinceId);
    }

    function toggleBarangay(cityId) {
        const barangay = document.getElementById('editBarangay');
        barangay.disabled = !cityId;
        barangay.classList.toggle('bg-gray-200', !cityId);
    }

    // Event listeners
    document.getElementById('editRegion').addEventListener('change', function() {
        loadProvinces(this.value);
        document.getElementById('editCity').innerHTML = '<option value="">Select City</option>';
        document.getElementById('editBarangay').innerHTML = '<option value="">Select Barangay</option>';
        toggleCity(null);
        toggleBarangay(null);
    });

    document.getElementById('editProvince').addEventListener('change', function() {
        loadCities(this.value);
        document.getElementById('editBarangay').innerHTML = '<option value="">Select Barangay</option>';
        toggleBarangay(null);
    });

    document.getElementById('editCity').addEventListener('change', function() {
        loadBarangays(this.value);
    });


    const changeProfileBtn = document.getElementById('changeProfileBtn');
    const editProfilePicInput = document.getElementById('editProfilePicInput');
    const editModalProfilePic = document.getElementById('editModalProfilePic');

    // When "Change Profile" button is clicked, trigger file input
    changeProfileBtn.addEventListener('click', () => {
        editProfilePicInput.click();
    });

    // Preview selected image
    editProfilePicInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                editModalProfilePic.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });



    // Make sure to append user_id in form submit
    document.getElementById('editUserForm').addEventListener('submit', function(e){
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('user_id', document.getElementById('editUserId').value);

        const profileFile = document.getElementById('editProfilePicInput').files[0];
        if(profileFile){
            formData.append('profile_pic', profileFile);
        }

        fetch('../my_account_update.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success'){
                    alert('User updated successfully!');
                    editUserModal.classList.add('hidden');
                    editUserModal.classList.remove('flex');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => { console.error(err); alert('Unexpected error'); });
    });
</script>
