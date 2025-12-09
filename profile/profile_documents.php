<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Documents</title>
    <script src="../src/tailwind.js"></script>
</head>
<body class="bg-gray-100 h-screen flex">

    <!-- LEFT NAVIGATION -->
    <nav class="w-64 bg-white shadow-md flex flex-col p-6">
        <div class="mb-10">
            <h1 class="text-2xl font-bold">Profile</h1>
            <p class="text-gray-500 text-sm">Dashboard Menu</p>
        </div>
        <ul class="flex flex-col gap-2 text-gray-700 font-medium">
            <li><a href="profile_dashboard.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Profile Information</a></li>
            <li><a href="profile_activity.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Activity</a></li>
            <li><a href="profile_myRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">My Requests & Offers</a></li>
            <li><a href="profile_allRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">All Requests & Offers</a></li>
            <li><a href="profile_members.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Members</a></li>
            <li><a href="profile_settings.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer">Settings</a></li>
        </ul>
    </nav>

    <main class="flex-1 p-6 overflow-y-auto">
        <!-- PROFILE HEADER -->
        <div class="flex items-center bg-white shadow p-4 rounded mb-6">
            <img src="../images/profile_pic_placeholder1.png"
                 class="w-24 h-24 rounded-full border mr-4"
                 alt="Profile Picture">
            <div>
                <h2 class="text-3xl font-bold">Juan Dela Cruz</h2>
                <p class="text-gray-700">Profile Type: Individual</p>
                <p class="text-gray-500 text-sm">Profile ID: #001</p>
            </div>
        </div>
        
        <section id="documents">
            <h3 class="text-2xl font-semibold mb-3">My Documents</h3>

            <div class="bg-white shadow p-4 rounded mb-4">
                <!-- Documents Table -->
                <div class="grid grid-cols-3 font-bold border-b-2 border-gray-300 p-2">
                    <div class="w-16">No.</div>
                    <div>Document Name</div>
                    <div>Date Uploaded</div>
                </div>
                <div id="documentTable" class="space-y-1"></div>
                
                <!-- Pagination -->
                <div class="flex justify-between items-center mt-2">
                    <button id="prevDoc" class="px-2 py-1 bg-gray-200 rounded">Previous</button>
                    <span id="docPage" class="text-gray-600"></span>
                    <button id="nextDoc" class="px-2 py-1 bg-gray-200 rounded">Next</button>
                </div>
            </div>
        </section>
    </main>

<script>
const userDocuments = [
    { no: 1, docName: "ID Card.pdf", date: "2025-11-30" },
    { no: 2, docName: "Proof of Residence.pdf", date: "2025-11-29" },
    { no: 3, docName: "Medical Report.pdf", date: "2025-11-28" },
    { no: 4, docName: "Donation Form.pdf", date: "2025-11-27" },
    { no: 5, docName: "Certificate.pdf", date: "2025-11-26" }
];

const itemsPerPage = 4;
let docPageNum = 1;

const documentTable = document.getElementById("documentTable");
const docPage = document.getElementById("docPage");

function renderDocuments(){
    const start = (docPageNum-1)*itemsPerPage;
    const end = start + itemsPerPage;
    const paginated = userDocuments.slice(start,end);
    documentTable.innerHTML = "";

    paginated.forEach((d,i)=>{
        const row = document.createElement("div");
        row.className = "grid grid-cols-3 gap-2 p-2 rounded border-b border-gray-100 items-start";

        row.innerHTML = `
            <div class="font-medium text-gray-700 break-words whitespace-normal w-16">${start+i+1}</div>
            <div class="text-gray-800 break-words whitespace-normal">${d.docName}</div>
            <div class="text-gray-500 text-sm whitespace-normal">${d.date}</div>
        `;

        documentTable.appendChild(row);
    });

    const totalPages = Math.ceil(userDocuments.length/itemsPerPage);
    docPage.textContent = `Page ${docPageNum} of ${totalPages}`;
}

// Pagination buttons
document.getElementById("prevDoc").addEventListener("click", ()=>{
    if(docPageNum>1){ docPageNum--; renderDocuments(); }
});
document.getElementById("nextDoc").addEventListener("click", ()=>{
    const totalPages = Math.ceil(userDocuments.length/itemsPerPage);
    if(docPageNum<totalPages){ docPageNum++; renderDocuments(); }
});

// Initial render
renderDocuments();
</script>

</body>
</html>
