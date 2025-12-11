<!-- user_view_modal.php -->
<!-- User Info Modal -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 overflow-auto p-4">
    <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full p-6 relative mt-10">
        <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">✕</button>
        <h3 class="text-xl font-bold mb-4">User Details</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <!-- Profile Picture -->
            <div class="col-span-2 mt-4 flex flex-col items-center">
                <strong>Profile Picture:</strong>
                <img id="modalProfilePic" class="w-24 h-24 rounded-full mt-2 mb-3 object-cover">
            </div>

            <hr class="col-span-2 w-full">

            <div><strong>User ID:</strong> <span id="modalUserId"></span></div>
            <div><strong>First Name:</strong> <span id="modalFirstName"></span></div>
            <div><strong>Middle Name:</strong> <span id="modalMiddleName"></span></div>
            <div><strong>Last Name:</strong> <span id="modalLastName"></span></div>
            <div><strong>Date of Birth:</strong> <span id="modalDOB"></span></div>
            <div><strong>Gender:</strong> <span id="modalGender"></span></div>
            <div><strong>ZIP Code:</strong> <span id="modalZip"></span></div>
            <div><strong>Phone Number:</strong> <span id="modalPhone"></span></div>
            <div><strong>Email:</strong> <span id="modalEmail"></span></div>
            <div><strong>Role:</strong> <span id="modalRole"></span></div>
            <div><strong>Joined:</strong> <span id="modalJoined"></span></div>
            <div><strong>Region:</strong> <span id="modalRegion"></span></div>
            <div><strong>Province:</strong> <span id="modalProvince"></span></div>
            <div><strong>City:</strong> <span id="modalCity"></span></div>
            <div><strong>Barangay:</strong> <span id="modalBarangay"></span></div>
        </div>
    </div>
</div>

<script>
    const userModal = document.getElementById('userModal');
    const closeModal = document.getElementById('closeModal');

    const modalUserId = document.getElementById('modalUserId');
    const modalFirstName = document.getElementById('modalFirstName');
    const modalMiddleName = document.getElementById('modalMiddleName');
    const modalLastName = document.getElementById('modalLastName');
    const modalDOB = document.getElementById('modalDOB');
    const modalGender = document.getElementById('modalGender');
    const modalZip = document.getElementById('modalZip');
    const modalPhone = document.getElementById('modalPhone');
    const modalEmail = document.getElementById('modalEmail');
    const modalRole = document.getElementById('modalRole');
    const modalJoined = document.getElementById('modalJoined');
    const modalRegion = document.getElementById('modalRegion');
    const modalProvince = document.getElementById('modalProvince');
    const modalCity = document.getElementById('modalCity');
    const modalBarangay = document.getElementById('modalBarangay');
    const modalProfilePic = document.getElementById('modalProfilePic');

    function initViewButtons() {
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                modalUserId.textContent = btn.getAttribute('data-user_id');
                modalFirstName.textContent = btn.getAttribute('data-first_name');
                modalMiddleName.textContent = btn.getAttribute('data-middle_name');
                modalLastName.textContent = btn.getAttribute('data-last_name');
                modalDOB.textContent = btn.getAttribute('data-dob');
                modalGender.textContent = btn.getAttribute('data-gender');
                modalZip.textContent = btn.getAttribute('data-zip');
                modalPhone.textContent = btn.getAttribute('data-phone');
                modalEmail.textContent = btn.getAttribute('data-email');
                modalRole.textContent = btn.getAttribute('data-role');
                modalJoined.textContent = btn.getAttribute('data-created');
                modalRegion.textContent = btn.getAttribute('data-region');
                modalProvince.textContent = btn.getAttribute('data-province');
                modalCity.textContent = btn.getAttribute('data-city');
                modalBarangay.textContent = btn.getAttribute('data-barangay');
                modalProfilePic.src = btn.getAttribute('data-profile_pic');

                userModal.classList.remove('hidden');
                userModal.classList.add('flex');
            });
        });
    }

    closeModal.addEventListener('click', () => {
        userModal.classList.add('hidden');
        userModal.classList.remove('flex');
    });

    userModal.addEventListener('click', (e) => {
        if (e.target === userModal) {
            userModal.classList.add('hidden');
            userModal.classList.remove('flex');
        }
    });
</script>
