<?php
include '../db_connect.php';

$filter = $_GET['filter'] ?? 'all';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10; // users per page
$offset = ($page - 1) * $perPage;

// Count total users for pagination
switch($filter) {
    case 'regular':
        $countSql = "SELECT COUNT(*) as total FROM users WHERE role = 'User'";
        break;
    case 'admin':
        $countSql = "SELECT COUNT(*) as total FROM users WHERE role != 'User'";
        break;
    case 'pending':
        $countSql = "SELECT COUNT(*) as total FROM pending_admins";
        break;
    default:
        $countSql = "SELECT COUNT(*) as total FROM users";
        break;
}

$countResult = $conn->query($countSql);
$totalUsers = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $perPage);

switch($filter) {
    case 'regular':
        $sql = "SELECT u.*, r.name AS region_name, p.name AS province_name, 
                       c.name AS city_name, b.name AS barangay_name
                FROM users u
                LEFT JOIN regions r ON u.region_id = r.id
                LEFT JOIN provinces p ON u.province_id = p.id
                LEFT JOIN cities c ON u.city_id = c.id
                LEFT JOIN barangays b ON u.barangay_id = b.id
                WHERE u.role = 'User'
                ORDER BY u.user_id ASC
                LIMIT $perPage OFFSET $offset";
        break;

    case 'admin':
        $sql = "SELECT u.*, r.name AS region_name, p.name AS province_name, 
                       c.name AS city_name, b.name AS barangay_name
                FROM users u
                LEFT JOIN regions r ON u.region_id = r.id
                LEFT JOIN provinces p ON u.province_id = p.id
                LEFT JOIN cities c ON u.city_id = c.id
                LEFT JOIN barangays b ON u.barangay_id = b.id
                WHERE u.role != 'User'
                ORDER BY u.user_id ASC
                LIMIT $perPage OFFSET $offset";
        break;

    case 'pending':
        $sql = "SELECT pa.*
                FROM pending_admins pa
                ORDER BY pa.pending_admin_id ASC
                LIMIT $perPage OFFSET $offset";
        break;

    default:
        $sql = "SELECT u.*, r.name AS region_name, p.name AS province_name, 
                       c.name AS city_name, b.name AS barangay_name
                FROM users u
                LEFT JOIN regions r ON u.region_id = r.id
                LEFT JOIN provinces p ON u.province_id = p.id
                LEFT JOIN cities c ON u.city_id = c.id
                LEFT JOIN barangays b ON u.barangay_id = b.id
                ORDER BY u.user_id ASC
                LIMIT $perPage OFFSET $offset";
        break;
}

$result = $conn->query($sql);
$users = [];
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $users[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>

    <script src="../src/tailwind.js"></script>
    <link rel="stylesheet" href="../src/style.css">
</head>
<body class="bg-gray-100">

<!-- ================= HEADER ================= -->
<header class="py-4 px-5 bg-white shadow-md flex justify-between items-center fixed w-full top-0 z-20">
    <h1 class="text-2xl md:text-3xl font-bold">
        <a href="dashboard.php">Bayanihan Hub</a>
    </h1>

    <!-- Mobile Hamburger -->
    <button id="hamburger" class="block md:hidden p-2 rounded bg-gray-100 hover:bg-gray-200">
        <svg class="w-6 h-6 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
</header>

<!-- ================= SIDEBAR (DESKTOP) ================= -->
<aside class="hidden md:block w-64 fixed top-16 left-0 h-[calc(100vh-4rem)] overflow-y-auto">
    <nav class="p-4">
        <ul class="space-y-1">

            <!-- Core -->
            <li class="uppercase text-xs px-2 mt-2">Core</li>
            <li>
                <a href="admin_dashboard.php" class="block px-4 py-2 rounded hover:bg-gray-200">
                    Dashboard
                </a>
            </li>

            <!-- Accounts -->
            <li class="uppercase text-xs px-2 mt-4">Accounts</li>
            <li><a href="admin_myAccount.php" class="block px-4 py-2 rounded hover:bg-gray-200">My Account</a></li>
            <li><a href="admin_users.php" class="block px-4 py-2 rounded bg-gray-300 font-semibold">Users</a></li>
            <li><a href="admin_profiles.php" class="block px-4 py-2 rounded hover:bg-gray-200">Profiles</a></li>

            <!-- Operations -->
            <li class="uppercase text-xs px-2 mt-4">Operations</li>
            <li><a href="admin_donations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donations / Requests</a></li>

            <!-- System -->
            <li class="uppercase text-xs px-2 mt-4">System</li>
            <li><a href="admin_items.php" class="block px-4 py-2 rounded hover:bg-gray-200">Item Management</a></li>
            <li><a href="admin_locations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Location Management</a></li>
            <li><a href="admin_donation_logs.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donation Logs</a></li>
            <li><a href="admin_activities.php" class="block px-4 py-2 rounded hover:bg-gray-200">Activity</a></li>
            <li><a href="admin_audit_trails.php" class="block px-4 py-2 rounded hover:bg-gray-200">Audit Trails</a></li>
            <li><a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Access Level Management</a></li>

            <!-- Support -->
            <li class="uppercase text-xs px-2 mt-4">Support</li>
            <li><a href="admin_feedback.php" class="block px-4 py-2 rounded hover:bg-gray-200">Feedback</a></li>
            <li><a href="admin_help.php" class="block px-4 py-2 rounded hover:bg-gray-200">Help / FAQ</a></li>

            <!-- Logout -->
            <li class="mt-6">
                <a href="admin_logout.php" class="block px-4 py-2 rounded bg-red-600 hover:bg-red-500 text-center">
                    Logout
                </a>
            </li>

        </ul>
    </nav>
</aside>

<!-- ================= MOBILE SIDE MENU ================= -->
<div id="side-menu"
    class="fixed inset-0 bg-white z-30 transform -translate-x-full transition-transform duration-300 md:hidden pt-20 overflow-y-auto">

    <button id="close-btn" class="absolute top-4 right-4 p-2 rounded bg-gray-200 hover:bg-gray-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M6 18L18 6M6 6l12 12"></path>
        </svg>
    </button>

    <ul class="flex flex-col gap-1 px-6">
        <li><a href="admin_dashboard.php" class="block px-4 py-2 rounded hover:bg-gray-200">Dashboard</a></li>
        <li><a href="admin_myAccount.php" class="block px-4 py-2 rounded hover:bg-gray-200">My Account</a></li>
        <li><a href="admin_users.php" class="block px-4 py-2 rounded hover:bg-gray-200">Users</a></li>
        <li><a href="admin_profiles.php" class="block px-4 py-2 rounded hover:bg-gray-200">Profiles</a></li>
        <li><a href="admin_donations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donations / Requests</a></li>
        <li><a href="admin_feedback.php" class="block px-4 py-2 rounded hover:bg-gray-200">Feedback</a></li>
        <li><a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Settings</a></li>
        <li><a href="admin_help.php" class="block px-4 py-2 rounded hover:bg-gray-200">Help / FAQ</a></li>
        <li><a href="admin_logout.php" class="block px-4 py-2 rounded bg-red-600 hover:bg-red-500">Logout</a></li>
    </ul>
</div>

<!-- ================= MAIN CONTENT ================= -->
<main class="pt-24 p-6 md:ml-64">

    <h2 class="text-2xl font-bold mb-6">User Management</h2>

    <!-- ================= FILTER TABS ================= -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="?filter=all" class="px-4 py-2 rounded <?= $filter=='all'?'bg-blue-600 text-white':'bg-gray-200 hover:bg-gray-300' ?>">All Users</a>
        <a href="?filter=regular" class="px-4 py-2 rounded <?= $filter=='regular'?'bg-blue-600 text-white':'bg-gray-200 hover:bg-gray-300' ?>">Regular Users</a>
        <a href="?filter=admin" class="px-4 py-2 rounded <?= $filter=='admin'?'bg-blue-600 text-white':'bg-gray-200 hover:bg-gray-300' ?>">Administrative Users</a>
        <a href="?filter=pending" class="px-4 py-2 rounded <?= $filter=='pending'?'bg-yellow-300 hover:bg-yellow-400 text-black font-semibold':'bg-gray-200 hover:bg-gray-300' ?>">Pending Admins</a>
    </div>

    <!-- ================= USER TABLE ================= -->
    <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <table class="w-full min-w-[900px] border-collapse text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3"><?= ($filter == 'pending') ? 'Admin User ID' : 'User ID' ?></th>
                    <th class="p-3">User</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Role</th>
                    <th class="p-3">Phone</th>
                    <th class="p-3">Location</th>
                    <th class="p-3">Joined</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach($users as $user): ?>
            <tr class="border-t hover:bg-gray-50 <?= ($filter == 'pending') ? 'bg-yellow-50 hover:bg-yellow-100' : '' ?>">
                <!-- User ID -->
                <td class="p-3"><?= $filter == 'pending' ? $user['pending_admin_id'] : $user['user_id'] ?></td>

                <!-- Name + Avatar -->
                <td class="p-3 align-middle">
                    <div class="flex items-center gap-2">
                        <img src="<?= isset($user['profile_pic']) ? '../'.$user['profile_pic'] : '../uploads/profile_pic_placeholder1.png' ?>" class="w-8 h-8 rounded-full object-cover">
                        <span class="max-w-[150px] break-words">
                            <?= $filter == 'pending' ? $user['first_name'].' '.$user['middle_name'].' '.$user['last_name'] : $user['first_name'].' '.$user['last_name'] ?>
                        </span>
                    </div>
                </td>

                <!-- Email -->
                <td class="p-3 max-w-[180px] break-words"><?= $user['email'] ?></td>

                <!-- Role -->
                <td class="p-3">
                    <span class="px-2 py-1 rounded text-xs 
                        <?= $filter == 'pending' ? 'bg-yellow-200 font-semibold' : ($user['role'] == 'User' ? 'bg-gray-200' : 'bg-blue-200') ?>">
                        <?= $filter == 'pending' ? 'Pending Admin' : $user['role'] ?>
                    </span>
                </td>

                <!-- Phone -->
                <td class="p-3">
                    <?= $filter == 'pending' || empty($user['phone_number']) ? '-' : $user['phone_number'] ?>
                </td>


                <!-- Location -->
                <td class="p-3 max-w-[180px] break-words">
                    <?php
                    if ($filter == 'pending') {
                        // If the user is pending, set location as '-'
                        $location = '-';
                    } else {
                        // For regular users, check if all location fields are set
                        $location = ($user['region_name'] && $user['province_name'] && $user['city_name'] && $user['barangay_name'])
                            ? $user['region_name'].' / '.$user['province_name'].' / '.$user['city_name'].' / '.$user['barangay_name']
                            : '-';
                    }
                    ?>
                    <?= $location ?>
                </td>


                <!-- Joined / Requested Date -->
                <td class="p-3"><?= $user['created_at'] ?></td>

                <!-- Actions -->
                <td class="p-3 text-center">
                    <div class="flex gap-1 justify-center whitespace-nowrap">
                        <?php if($filter == 'pending'): ?>
                            <button class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 approve-btn" data-id="<?= $user['pending_admin_id'] ?>">Approve</button>
                            <button class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 reject-btn" data-id="<?= $user['pending_admin_id'] ?>">Reject</button>
                            <?php else: ?>
                                <button class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 view-btn"
                                    data-user_id="<?= $user['user_id'] ?>"
                                    data-profile_pic="<?= isset($user['profile_pic']) ? '../'.$user['profile_pic'] : '../uploads/profile_pic_placeholder1.png' ?>"
                                    data-first_name="<?= $user['first_name'] ?>"
                                    data-middle_name="<?= $user['middle_name'] ?>"
                                    data-last_name="<?= $user['last_name'] ?>"
                                    data-dob="<?= $user['date_of_birth'] ?>"
                                    data-gender="<?= $user['gender'] ?>"
                                    data-zip="<?= $user['zip_code'] ?>"
                                    data-phone="<?= $user['phone_number'] ?>"
                                    data-email="<?= $user['email'] ?>"
                                    data-password="<?= $user['password'] ?>"
                                    data-role="<?= $user['role'] ?>"
                                    data-created="<?= $user['created_at'] ?>"
                                    data-region="<?= $user['region_name'] ?? $user['region_id'] ?>"
                                    data-province="<?= $user['province_name'] ?? $user['province_id'] ?>"
                                    data-city="<?= $user['city_name'] ?? $user['city_id'] ?>"
                                    data-barangay="<?= $user['barangay_name'] ?? $user['barangay_id'] ?>"
                                >View</button>


                                <button class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 edit-btn"
                                    data-user_id="<?= $user['user_id'] ?>"
                                    data-profile_pic="<?= isset($user['profile_pic']) ? '../'.$user['profile_pic'] : '../uploads/profile_pic_placeholder1.png' ?>"
                                    data-first_name="<?= $user['first_name'] ?>"
                                    data-middle_name="<?= $user['middle_name'] ?>"
                                    data-last_name="<?= $user['last_name'] ?>"
                                    data-dob="<?= $user['date_of_birth'] ?>"
                                    data-gender="<?= $user['gender'] ?>"
                                    data-zip="<?= $user['zip_code'] ?>"
                                    data-phone="<?= $user['phone_number'] ?>"
                                    data-email="<?= $user['email'] ?>"
                                    data-role="<?= $user['role'] ?>"
                                    data-region="<?= $user['region_id'] ?>"
                                    data-province="<?= $user['province_id'] ?>"
                                    data-city="<?= $user['city_id'] ?>"
                                    data-barangay="<?= $user['barangay_id'] ?>"
                                >Edit</button>

                                <button 
                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 remove-btn"
                                    data-user_id="<?= $user['user_id'] ?>">
                                    Remove
                                </button>
                            <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex justify-center gap-2">
        <?php if($page > 1): ?>
            <a href="?filter=<?= $filter ?>&page=<?= $page-1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">&laquo; Prev</a>
        <?php endif; ?>

        <?php for($i=1; $i<=$totalPages; $i++): ?>
            <a href="?filter=<?= $filter ?>&page=<?= $i ?>" 
            class="px-3 py-1 rounded <?= $i==$page ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
            <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if($page < $totalPages): ?>
            <a href="?filter=<?= $filter ?>&page=<?= $page+1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Next &raquo;</a>
        <?php endif; ?>
    </div>

</main>

<!-- ================= JS ================= -->
<script>
    const hamburger = document.getElementById('hamburger');
    const sideMenu = document.getElementById('side-menu');
    const closeBtn = document.getElementById('close-btn');

    hamburger.addEventListener('click', () => {
        sideMenu.classList.remove('-translate-x-full');
    });

    closeBtn.addEventListener('click', () => {
        sideMenu.classList.add('-translate-x-full');
    });

    document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', function () {
            const pendingAdminId = this.getAttribute("data-id");

            // Confirm approval
            if (!confirm("Are you sure you want to approve this admin?")) {
                return;
            }

            // Send the approval request to the server
            fetch("approve_admin.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "pending_admin_id=" + pendingAdminId
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    alert(data.message);
                    location.reload(); // Refresh the page to show updated data
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                alert("An error occurred: " + err);
            });
        });
    });

    document.querySelectorAll('.reject-btn').forEach(button => {
        button.addEventListener('click', function () {
            const pendingAdminId = this.getAttribute("data-id");

            // Confirm rejection
            if (!confirm("Are you sure you want to reject this admin?")) {
                return;
            }

            // Send the rejection request to the server
            fetch("reject_admin.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "pending_admin_id=" + pendingAdminId
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    alert(data.message);
                    location.reload(); // Refresh the page to show updated data
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                alert("An error occurred: " + err);
            });
        });
    });


</script>

<?php include 'user_view_modal.php'; ?>
<script>
    // Initialize view buttons after modal is included
    initViewButtons();
</script>

<?php include 'user_edit_modal.php'; ?>
<script>
    // Initialize edit buttons after modal is included
    initEditButtons();
</script>

<script>
document.querySelectorAll('.remove-btn').forEach(button => {
    button.addEventListener('click', function () {

        const userID = this.getAttribute("data-user_id");

        // Confirm delete
        if (!confirm("Are you sure you want to remove this user?")) {
            return;
        }

        fetch("user_remove.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "user_id=" + userID
        })
        .then(res => res.text())   // <-- READ RAW RESPONSE
        .then(text => {
            // console.log("SERVER RESPONSE:", text); // <-- SEE FULL OUTPUT
            // alert(text); // TEMPORARY

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                alert("Invalid JSON received.\n\n" + text);
                return;
            }

            if (data.status === "success") {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message + "\n\nERROR: " + (data.error ?? "none"));
            }
        })
        .catch(err => {
            alert("Fetch error: " + err);
        });


    });
});
</script>


</body>
</html>
