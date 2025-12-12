<!-- Modal structure -->
<div id="modal" class="fixed inset-0 flex justify-center items-center bg-opacity-50 bg-gray-900 z-50 hidden">
    <div class="bg-white p-8 rounded-lg w-1/3">
        <h2 class="text-2xl font-bold mb-4">Donation Entry Details</h2>

        <!-- Display donation entry details -->
        <p><strong>Entry ID:</strong> <span id="modal-entry-id"></span></p>
        <p><strong>Profile ID:</strong> <span id="modal-profile-id"></span></p>
        <p><strong>Entry Type:</strong> <span id="modal-entry-type"></span></p>
        <p><strong>Details:</strong> <span id="modal-details"></span></p>
        <p><strong>Target Location:</strong> <span id="modal-target-location"></span></p>
        <p><strong>Created At:</strong> <span id="modal-created-at"></span></p>
        <p><strong>Updated At:</strong> <span id="modal-updated-at"></span></p>

        <!-- Items List -->
        <h3 class="mt-4 font-semibold">Items:</h3>
        <ul id="modal-items-list" class="list-disc pl-5 space-y-1"></ul>

        <div class="mt-4 flex justify-end">
            <!-- Close button -->
            <button class="px-4 py-2 bg-gray-500 text-white rounded" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>
