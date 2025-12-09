<?php
require '../db_connect.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['profile_id'])) {
    $_SESSION['profile_id'] = intval($_GET['profile_id']);
} elseif (!isset($_SESSION['profile_id'])) {
    echo "No profile selected.";
    exit;
}

$profileId = $_SESSION['profile_id'];
$role = $_SESSION['role'];

// Helper function to disable links if user doesn't have permission
function isDisabled($permission, $role) {
    $permissionsMap = [
        'Manage Members' => ['owner', 'admin', 'manager'],
        'Manage Offers & Requests' => ['owner', 'admin', 'manager'],
        'View Activities' => ['owner', 'admin', 'manager', 'member'],
        'Manage Settings' => ['owner', 'admin', 'manager', 'member']
    ];
    return !in_array($role, $permissionsMap[$permission]);
}

// Get main profile info
$stmt = $conn->prepare("SELECT profile_type, profile_name, profile_pic FROM profiles WHERE profile_id = ?");
$stmt->bind_param("i", $profileId);
$stmt->execute();
$result = $stmt->get_result();
$profileMain = $result->fetch_assoc();
$stmt->close();

if (!$profileMain) {
    echo "Profile not found.";
    exit;
}

$profileType = $profileMain['profile_type'];
$profileName = $profileMain['profile_name'];
$profilePic = !empty($profileMain['profile_pic']) ? "../" . $profileMain['profile_pic'] : "../uploads/profile_pic_placeholder1.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Members</title>
    <script src="../src/tailwind.js"></script>
    <link rel="stylesheet" href="../src/style2.css">
</head>
<body class="bg-gray-100 h-screen flex">

    <!-- LEFT NAVIGATION -->
    <nav class="w-64 bg-white shadow-md flex flex-col p-6">
        <div class="mb-10">
            <h1 class="text-2xl font-bold">Profile</h1>
            <p class="text-gray-500 text-sm">Dashboard Menu</p>
        </div>
        <ul class="flex flex-col gap-2 text-gray-700 font-medium">
            <li>
                <a href="profile_dashboard.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('View Activities', $role) ? 'disabled-link' : '' ?>">Profile Information</a>
            </li>
            <li>
                <a href="profile_activity.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('View Activities', $role) ? 'disabled-link' : '' ?>">Activity</a>
            </li>
            <li>
                <a href="profile_myRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">My Requests & Offers</a>
            </li>
            <li>
                <a href="profile_allRequests.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Offers & Requests', $role) ? 'disabled-link' : '' ?>">All Requests & Offers</a>
            </li>

            <?php if ($profileType !== 'individual'): ?>
                <li>
                    <a href="profile_members.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Members', $role) ? 'disabled-link' : '' ?>">Members</a>
                </li>
            <?php endif; ?>

            <li>
                <a href="profile_settings.php" class="nav-item block p-2 rounded hover:bg-gray-200 cursor-pointer <?= isDisabled('Manage Settings', $role) ? 'disabled-link' : '' ?>">Access Levels</a>
            </li>
        </ul>
    </nav>


    <main class="flex-1 p-6 overflow-y-auto">
        <!-- PROFILE HEADER -->
        <div class="flex items-center bg-white shadow p-6 rounded-lg mb-6">
            <img src="<?= htmlspecialchars($profilePic) ?>"
                 class="w-28 h-28 rounded-full border mr-6"
                 alt="Profile Picture">
            <div>
                <h2 class="text-3xl font-bold"><?= htmlspecialchars($profileName) ?></h2>
                <p class="text-gray-700">Profile Type: <?= htmlspecialchars(ucfirst($profileType))?></p>
                <p class="text-gray-500 text-sm">Profile ID: #<?= htmlspecialchars($profileId) ?></p>
            </div>
        </div>
        
        <section id="members">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-2xl font-semibold">Members</h3>
                <button id="addMemberBtn" class="px-3 py-1 bg-blue-500 text-white rounded">Add Member</button>
            </div>

            <div class="bg-white shadow p-4 rounded">
                <div id="memberHeader" class="grid border-b-2 border-gray-300 font-bold p-2" style="grid-template-columns: 60px 1fr 1fr 100px 100px 80px;">
                    <div>No.</div>
                    <div>Name</div>
                    <div>Email</div>
                    <div>Role</div>
                    <div>Date Joined</div>
                    <div>Action</div> <!-- new column -->
                </div>



                <div id="memberTable" class="space-y-1"></div>

                <!-- Pagination -->
                <div class="flex justify-between items-center mt-2">
                    <button id="prevMember" class="px-2 py-1 bg-gray-200 rounded">Previous</button>
                    <span id="memberPage" class="text-gray-600"></span>
                    <button id="nextMember" class="px-2 py-1 bg-gray-200 rounded">Next</button>
                </div>
            </div>
        </section>
    </main>

    <!-- Modal for adding member -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded shadow-lg w-96 max-h-[90vh] flex flex-col">
            <h3 class="text-xl font-semibold mb-4" id="modalTitle">Add Member</h3>
            <form id="modalForm" class="space-y-3 flex-1 overflow-y-auto">
                <input type="hidden" id="memberId">
                <div>
                    <label class="block text-gray-700">User Email</label>
                    <input id="memberEmail" type="email" class="w-full border rounded p-1" autocomplete="off">
                    <ul id="memberSuggestions" class="border bg-white mt-1 max-h-40 overflow-y-auto hidden"></ul>
                </div>
                <div>
                    <label class="block text-gray-700">Access Level</label>
                    <select id="memberAccess" class="w-full border rounded p-1">
                        <option value="guest">Guest</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="member">Member</option>
                        <option value="guest">Guest</option>
                    </select>
                </div>
                <div class="flex justify-end gap-2 mt-3">
                    <button type="button" id="modalCancel" class="px-3 py-1 bg-gray-200 rounded">Cancel</button>
                    <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

<script>
// Modal toggle
const modal = document.getElementById("modal");
document.getElementById("addMemberBtn").addEventListener("click", () => {
    modal.classList.remove("hidden");
    document.getElementById("modalForm").reset();
});
document.getElementById("modalCancel").addEventListener("click", () => modal.classList.add("hidden"));

// Fetch and display profile members
async function loadMembers() {
    try {
        const res = await fetch(`get_profile_members.php?profile_id=<?= $profileId ?>`);
        const members = await res.json();

        const memberTable = document.getElementById("memberTable");
        memberTable.innerHTML = "";

        members.forEach((member, index) => {
            const div = document.createElement("div");
            div.style.display = "grid";
            div.style.gridTemplateColumns = "60px 1fr 1fr 100px 100px 80px"; 
            div.style.gap = "0.5rem";
            div.className = "border-b border-gray-200 p-2 items-center";

            const no = document.createElement("div");
            no.textContent = index + 1;

            const name = document.createElement("div");
            name.textContent = member.name;
            name.style.wordWrap = "break-word";
            name.style.overflowWrap = "break-word";

            const email = document.createElement("div");
            email.textContent = member.email;
            email.style.wordBreak = "break-all";
            email.style.overflowWrap = "anywhere";

            const role = document.createElement("div");
            role.textContent = member.role;

            const date = document.createElement("div");
            date.textContent = new Date(member.created_at).toLocaleDateString();

            const action = document.createElement("div");
            const delBtn = document.createElement("button");
            delBtn.textContent = "Delete";
            delBtn.className = "px-2 py-1 bg-red-500 text-white rounded";

            // Disable the button if the member's role is 'owner'
            if (member.role.toLowerCase() === "owner") {
                delBtn.disabled = true;
                delBtn.className = "px-2 py-1 bg-gray-400 text-white rounded cursor-not-allowed";
                delBtn.title = "Owner cannot be deleted";
            } else {
                delBtn.className = "px-2 py-1 bg-red-500 text-white rounded";
                delBtn.addEventListener("click", () => deleteMember(member.user_id));
            }


            action.appendChild(delBtn);

            div.appendChild(no);
            div.appendChild(name);
            div.appendChild(email);
            div.appendChild(role);
            div.appendChild(date);
            div.appendChild(action);

            memberTable.appendChild(div);
        });






    } catch (err) {
        console.error("Failed to load members:", err);
    }
}

// Load members on page load
loadMembers();

// Autocomplete for email only
const memberEmailInput = document.getElementById("memberEmail");
const memberIdInput = document.getElementById("memberId");
const suggestions = document.getElementById("memberSuggestions");

memberEmailInput.addEventListener("input", async () => {
    const query = memberEmailInput.value.trim();
    if (query.length < 1) {
        suggestions.classList.add("hidden");
        return;
    }

    try {
        const res = await fetch(`search_users.php?q=${encodeURIComponent(query)}&profile_id=<?= $profileId ?>`);
        const data = await res.json(); // parse JSON here
        console.log(data);

        suggestions.innerHTML = "";
        if (data.length > 0) {
            data.forEach(user => {
                const li = document.createElement("li");
                li.className = "p-2 cursor-pointer hover:bg-gray-200";
                li.textContent = user.email;
                li.addEventListener("click", () => {
                    memberEmailInput.value = user.email;
                    memberIdInput.value = user.user_id; // optional, for record
                    suggestions.classList.add("hidden");
                });
                suggestions.appendChild(li);
            });
            suggestions.classList.remove("hidden");
        } else {
            suggestions.classList.add("hidden");
        }

    } catch (err) {
        console.error('Failed to fetch or parse JSON:', err);
        suggestions.classList.add("hidden");
    }
});


// Submit add member
document.getElementById("modalForm").addEventListener("submit", async e => {
    e.preventDefault();

    const email = memberEmailInput.value.trim();
    const userId = memberIdInput.value; // hidden field set from suggestions
    const role = document.getElementById("memberAccess").value;

    if (!email || !userId) {
        alert("Please select a valid user from the suggestions. Typing manually is not allowed.");
        return;
    }
    //alert(userId + ", " + email);
    const formData = new FormData();
    formData.append("user_id", userId);
    formData.append("profile_id", <?= $profileId ?>);
    formData.append("role", role);
    formData.append("email", email);

    const res = await fetch("add_member.php", { method: "POST", body: formData });
    const data = await res.json();

    if (data.success) {
        alert("Member added!");
        modal.classList.add("hidden");
        loadMembers();
    } else {
        alert("Error: " + data.message);
    }
});



async function deleteMember(userId) {
    if (!confirm("Are you sure you want to remove this member?")) return;

    try {
        const formData = new FormData();
        formData.append("user_id", userId);
        formData.append("profile_id", <?= $profileId ?>);

        const res = await fetch("delete_member.php", {
            method: "POST",
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            alert("Member deleted!");
            loadMembers(); // reload members table
        } else {
            alert("Error: " + data.message);
        }
    } catch (err) {
        console.error(err);
        alert("Failed to delete member.");
    }
}


</script>

</body>
</html>
