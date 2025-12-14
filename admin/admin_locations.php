<?php
require '../admin_connect.php';

$user_id = $_SESSION['user_id'];
$php_role = $_SESSION['role'] ?? 'Staff'; // Default to Staff

// ----------------- ACTIVATE MYSQL ROLE -----------------
if (in_array($php_role, ['Staff', 'Admin', 'Superuser'])) {
    $conn->query("SET ROLE " . strtolower($php_role));
}

$roleSql = "SELECT role FROM users WHERE user_id = ?";
$roleStmt = $conn->prepare($roleSql);
$roleStmt->bind_param("i", $user_id);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
$roleRow = $roleResult->fetch_assoc();

$disabledClass = 'opacity-50 cursor-not-allowed pointer-events-none bg-gray-200';
$currentRole = $roleRow['role'] ?? 'User';

$isStaff = ($currentRole === 'Staff');
$isAdmin = ($currentRole === 'Admin');
$isSuperuser = ($currentRole === 'Superuser');

// Fetch regions
$regions_query = "SELECT * FROM regions ORDER BY name";
$regions_result = $conn->query($regions_query);

// Fetch provinces
$provinces_query = "SELECT * FROM provinces ORDER BY name";
$provinces_result = $conn->query($provinces_query);

// Fetch cities
$cities_query = "SELECT * FROM cities ORDER BY name";
$cities_result = $conn->query($cities_query);

// Fetch barangays
$barangays_query = "SELECT * FROM barangays ORDER BY name";
$barangays_result = $conn->query($barangays_query);

// Prepare data for JavaScript
$regions_data = [];
$provinces_data = [];
$cities_data = [];
$barangays_data = [];

while ($region = $regions_result->fetch_assoc()) {
    $regions_data[] = $region;
}

while ($province = $provinces_result->fetch_assoc()) {
    $provinces_data[] = $province;
}

while ($city = $cities_result->fetch_assoc()) {
    $cities_data[] = $city;
}

while ($barangay = $barangays_result->fetch_assoc()) {
    $barangays_data[] = $barangay;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Management</title>

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
            <li><a href="admin_users.php" class="block px-4 py-2 rounded hover:bg-gray-200">Users</a></li>
            <li><a href="admin_profiles.php" class="block px-4 py-2 rounded hover:bg-gray-200">Profiles</a></li>

            <!-- Operations -->
            <li class="uppercase text-xs px-2 mt-4">Operations</li>
            <li><a href="admin_donations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donations / Requests</a></li>

            <!-- System -->
            <li class="uppercase text-xs px-2 mt-4">System</li>
            <li><a href="admin_items.php" class="block px-4 py-2 rounded hover:bg-gray-200">Item Management</a></li>
            <li><a href="admin_locations.php" class="block px-4 py-2 rounded bg-gray-300 font-semibold">Location Management</a></li>
            <li><a href="admin_donation_logs.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donation Logs</a></li>
            <li><a href="admin_activities.php" class="block px-4 py-2 rounded hover:bg-gray-200">Activity</a></li>
            <li class="<?= ($isStaff || $isAdmin || $incomplete) ? $disabledClass : '' ?>">
                <a href="admin_audit_trails.php" class="block px-4 py-2 rounded">Audit Trails</a>
            </li>
            <li class="<?= ($isStaff || $incomplete) ? $disabledClass : '' ?>">
                <a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Access Level Management</a>
            </li>

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

    <h2 class="text-2xl font-bold mb-6">Location Management</h2>

    <!-- ================= ADD LOCATION FORMS ================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <!-- Add Region -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Region</h3>
            <form id="add-region-form" class="flex gap-2">
                <input type="text" id="region-name" placeholder="Region Name" class="flex-1 p-1 text-sm border rounded">
                <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

        <!-- Add Province -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Province</h3>
            <form id="add-province-form" class="flex gap-2">
                <div class="relative flex-1">
                    <select id="select-region" class="p-1 text-sm border rounded w-full overflow-auto">
                        <option value="">Select Region</option>
                        <!-- Regions will be populated here -->
                    </select>
                </div>
                <input type="text" id="province-name" placeholder="Province Name" class="flex-1 p-1 text-sm border rounded">
                <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

        <!-- Add City -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add City / Municipality</h3>
            <form id="add-city-form" class="flex gap-2">
                <div class="relative flex-1">
                    <select id="select-province" class="p-1 text-sm border rounded w-full overflow-auto">
                        <option value="">Select Province</option>
                        <!-- Provinces will be populated here -->
                    </select>
                </div>
                <input type="text" id="city-name" placeholder="City Name" class="flex-1 p-1 text-sm border rounded">
                <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

        <!-- Add Barangay -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Barangay</h3>
            <form id="add-barangay-form" class="flex gap-2">
                <div class="relative flex-1">
                    <select id="select-city" class="p-1 text-sm border rounded w-full overflow-auto">
                        <option value="">Select City / Municipality</option>
                        <!-- Cities will be populated here -->
                    </select>
                </div>
                <input type="text" id="barangay-name" placeholder="Barangay Name" class="flex-1 p-1 text-sm border rounded">
                <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>
    </div>

    <!-- ================= EXISTING LOCATIONS TABLE ================= -->
    
    <h2 class="text-2xl font-bold mb-6">Existing Locations</h2>

    <div class="grid grid-cols-4 gap-4">

    <!-- Regions Section -->
    <div class="bg-white rounded-lg shadow p-4 h-[500px] overflow-y-auto">
        <h3 class="font-bold mb-2 text-lg">Regions</h3>
        <ul id="regions" class="space-y-2">
        <?php while($region = $regions_result->fetch_assoc()): ?>
            <li class="flex justify-between items-center p-2 rounded cursor-pointer hover:bg-blue-100 transition">
                <span onclick="selectRegion('<?= $region['id']; ?>')"><?= $region['name']; ?></span>
                <div class="flex gap-1">
                    <button class="px-2 py-0.5 bg-yellow-500 text-white rounded text-xs" onclick="editItem('<?= $region['id']; ?>', 'region')">Edit</button>
                    <button class="px-2 py-0.5 bg-red-500 text-white rounded text-xs" onclick="deleteItem('<?= $region['id']; ?>', 'region')">Delete</button>
                </div>
            </li>
        <?php endwhile; ?>
        </ul>
    </div>

    <!-- Provinces Section -->
    <div class="bg-white rounded-lg shadow p-4 h-[500px] overflow-y-auto">
        <h3 class="font-bold mb-2 text-lg">Provinces</h3>
        <ul id="provinces" class="space-y-2">
            <li class="text-xs cursor-pointer text-gray-500">Select a Region</li> <!-- Placeholder for Provinces -->
        </ul>
    </div>

    <!-- Cities/Municipalities Section -->
    <div class="bg-white rounded-lg shadow p-4 h-[500px] overflow-y-auto">
        <h3 class="font-bold mb-2 text-lg">Cities / Municipalities</h3>
        <ul id="cities" class="space-y-2">
            <li class="text-xs cursor-pointer text-gray-500">Select a Province</li> <!-- Placeholder for Cities -->
        </ul>
    </div>

    <!-- Barangays Section -->
    <div class="bg-white rounded-lg shadow p-4 h-[500px] overflow-y-auto">
        <h3 class="font-bold mb-2 text-lg">Barangays</h3>
        <ul id="barangays" class="space-y-1 text-sm">
            <li class="text-xs cursor-pointer text-gray-500">Select a City / Municipality</li> <!-- Placeholder for Barangays -->
        </ul>
    </div>


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
</script>

<script>
    // Data from PHP
    const regions = <?php echo json_encode($regions_data); ?>;
    const provinces = <?php echo json_encode($provinces_data); ?>;
    const cities = <?php echo json_encode($cities_data); ?>;
    const barangays = <?php echo json_encode($barangays_data); ?>;

    let selectedRegion = null;
    let selectedProvince = null;
    let selectedCity = null;

    // Organize data for easier access
    const regionProvinces = {};
    const provinceCities = {};
    const cityBarangays = {};

    provinces.forEach(province => {
        if (!regionProvinces[province.region_id]) regionProvinces[province.region_id] = [];
        regionProvinces[province.region_id].push(province);
    });

    cities.forEach(city => {
        if (!provinceCities[city.province_id]) provinceCities[city.province_id] = [];
        provinceCities[city.province_id].push(city);
    });

    barangays.forEach(barangay => {
        if (!cityBarangays[barangay.city_id]) cityBarangays[barangay.city_id] = [];
        cityBarangays[barangay.city_id].push(barangay);
    });

    // Populate Regions in the UI
    function populateRegions() {
        const regionSelect = document.getElementById('regions');
        regions.forEach(region => {
            const li = document.createElement('li');
            li.classList.add('text-xs', 'cursor-pointer', 'hover:bg-blue-100', 'transition','p-2');
            li.setAttribute('data-id', region.id);  // Assigning data-id to each list item

            // Create the region name span
            const regionSpan = document.createElement('span');
            regionSpan.innerHTML = region.name;
            regionSpan.onclick = function() {
                selectRegion(region.id);
            };

            // Create the edit and delete buttons
            const buttonContainer = document.createElement('div');
            buttonContainer.classList.add('flex', 'p-1', 'gap-2', 'justify-center', 'items-center');

            const editButton = document.createElement('button');
            editButton.classList.add('px-2', 'py-0.5', 'bg-yellow-500', 'text-white', 'rounded', 'text-xs');
            editButton.innerHTML = 'Edit';
            editButton.onclick = function() {
                editItem(region.id, 'region', region.name);
            };

            const deleteButton = document.createElement('button');
            deleteButton.classList.add('px-2', 'py-0.5', 'bg-red-500', 'text-white', 'rounded', 'text-xs');
            deleteButton.innerHTML = 'Delete';
            deleteButton.onclick = function() {
                deleteItem(region.id, 'region', region.name);
            };

            // Append buttons to the container
            buttonContainer.appendChild(editButton);
            buttonContainer.appendChild(deleteButton);

            // Append everything to the <li>
            li.appendChild(regionSpan);
            li.appendChild(buttonContainer);

            // Append the <li> to the regionSelect <ul>
            regionSelect.appendChild(li);
        });
    }

    // Handle Region Selection
    function selectRegion(regionId) {
        selectedRegion = regionId;
        const provinceSelect = document.getElementById('provinces');
        const citySelect = document.getElementById('cities');
        const barangaySelect = document.getElementById('barangays');
        
        // Reset selections
        provinceSelect.innerHTML = '';
        citySelect.innerHTML = '<li class="text-xs cursor-pointer text-gray-500">Select a Province</li>';
        barangaySelect.innerHTML = '<li class="text-xs cursor-pointer text-gray-500">Select a City / Municipality</li>';
        
        // Reset highlights for all regions
        const regionItems = document.querySelectorAll('#regions li');
        regionItems.forEach(item => {
            item.classList.remove('bg-blue-100', 'text-blue-800', 'font-semibold');
        });

        // Find region name and highlight the selected region
        const regionName = regions.find(region => region.id === regionId)?.name || 'Unknown Region';
        const selectedRegionItem = document.querySelector(`#regions li[data-id="${regionId}"]`);
        if (selectedRegionItem) {
            selectedRegionItem.classList.add('bg-blue-100', 'text-blue-800', 'font-semibold');
        }

        // Populate provinces based on selected region
        if (regionProvinces[regionId] && regionProvinces[regionId].length > 0) {
            regionProvinces[regionId].forEach(province => {
                const li = document.createElement('li');
                li.classList.add('text-xs', 'cursor-pointer', 'hover:bg-green-100', 'transition', 'flex', 'justify-between', 'items-center', 'p-2', 'space-x-4');
                li.setAttribute('data-id', province.id); // Set unique ID for province

                // Create the province name span
                const provinceSpan = document.createElement('span');
                provinceSpan.innerHTML = province.name;
                provinceSpan.onclick = function() {
                    selectProvince(province.id, province.name);
                };

                // Create the edit and delete buttons
                const buttonContainer = document.createElement('div');
                buttonContainer.classList.add('flex', 'gap-2', 'justify-between', 'items-center');

                const editButton = document.createElement('button');
                editButton.classList.add('px-2', 'py-0.5', 'bg-yellow-500', 'text-white', 'rounded', 'text-xs');
                editButton.innerHTML = 'Edit';
                editButton.onclick = function() {
                    editItem(province.id, 'province', province.name);
                };

                const deleteButton = document.createElement('button');
                deleteButton.classList.add('px-2', 'py-0.5', 'bg-red-500', 'text-white', 'rounded', 'text-xs');
                deleteButton.innerHTML = 'Delete';
                deleteButton.onclick = function() {
                    deleteItem(province.id, 'province', province.name);
                };

                // Append buttons to the container
                buttonContainer.appendChild(editButton);
                buttonContainer.appendChild(deleteButton);

                // Append everything to the <li>
                li.appendChild(provinceSpan);
                li.appendChild(buttonContainer);

                // Append the <li> to the provinceSelect <ul>
                provinceSelect.appendChild(li);
            });
        } else {
            provinceSelect.innerHTML = `<li class="text-xs cursor-pointer text-gray-500">No existing provinces in ${regionName}</li>`;
        }
    }

    // Handle Province Selection
    function selectProvince(provinceId, provinceName) {
        selectedProvince = provinceId;
        const citySelect = document.getElementById('cities');
        const barangaySelect = document.getElementById('barangays');

        // Reset selections
        citySelect.innerHTML = '';
        barangaySelect.innerHTML = '<li class="text-xs cursor-pointer text-gray-500">Select a City / Municipality</li>';

        // Reset highlights for all provinces
        const provinceItems = document.querySelectorAll('#provinces li');
        provinceItems.forEach(item => {
            item.classList.remove('bg-yellow-100', 'text-yellow-800', 'font-semibold');
        });

        // Highlight the selected province
        const selectedProvinceItem = document.querySelector(`#provinces li[data-id="${provinceId}"]`);
        if (selectedProvinceItem) {
            selectedProvinceItem.classList.add('bg-yellow-100', 'text-yellow-800', 'font-semibold');
        }

        // Create city items with edit and delete buttons
        if (provinceCities[provinceId] && provinceCities[provinceId].length > 0) {
            provinceCities[provinceId].forEach(city => {
                const li = document.createElement('li');
                li.classList.add('text-xs', 'cursor-pointer', 'hover:bg-yellow-100', 'transition', 'flex', 'justify-between', 'items-center', 'p-2', 'space-x-4');
                li.setAttribute('data-id', city.id);

                // Create the city name span
                const citySpan = document.createElement('span');
                citySpan.innerHTML = city.name;
                citySpan.onclick = function() {
                    selectCity(city.id, city.name);
                };

                // Create the edit and delete buttons
                const buttonContainer = document.createElement('div');
                buttonContainer.classList.add('flex', 'gap-2', 'justify-between', 'items-center');

                const editButton = document.createElement('button');
                editButton.classList.add('px-2', 'py-0.5', 'bg-yellow-500', 'text-white', 'rounded', 'text-xs');
                editButton.innerHTML = 'Edit';
                editButton.onclick = function() {
                    editItem(city.id, 'city', city.name);
                };

                const deleteButton = document.createElement('button');
                deleteButton.classList.add('px-2', 'py-0.5', 'bg-red-500', 'text-white', 'rounded', 'text-xs');
                deleteButton.innerHTML = 'Delete';
                deleteButton.onclick = function() {
                    deleteItem(city.id, 'city', city.name);
                };

                // Append buttons to the container
                buttonContainer.appendChild(editButton);
                buttonContainer.appendChild(deleteButton);

                // Append everything to the <li>
                li.appendChild(citySpan);
                li.appendChild(buttonContainer);

                // Append the <li> to the citySelect <ul>
                citySelect.appendChild(li);
            });
        } else {
            citySelect.innerHTML = `<li class="text-xs cursor-pointer text-gray-500">No existing cities/municipalities in ${provinceName}</li>`;
        }
    }

    // Handle City Selection
    function selectCity(cityId, cityName) {
        selectedCity = cityId;
        const barangaySelect = document.getElementById('barangays');

        // Reset barangay selections
        barangaySelect.innerHTML = '';

        // Reset highlights for all cities
        const cityItems = document.querySelectorAll('#cities li');
        cityItems.forEach(item => {
            item.classList.remove('bg-blue-200', 'text-blue-800', 'font-semibold');
        });

        // Highlight the selected city
        const selectedCityItem = document.querySelector(`#cities li[data-id="${cityId}"]`);
        if (selectedCityItem) {
            selectedCityItem.classList.add('bg-blue-200', 'text-blue-800', 'font-semibold');
        }

        // Create barangay items with edit and delete buttons
        if (cityBarangays[cityId] && cityBarangays[cityId].length > 0) {
            cityBarangays[cityId].forEach(barangay => {
                const li = document.createElement('li');
                li.classList.add('text-xs', 'cursor-pointer', 'hover:bg-blue-200', 'transition', 'flex', 'justify-between', 'items-center', 'p-2', 'space-x-4');
                li.setAttribute('data-id', barangay.id);

                // Create the barangay name span
                const barangaySpan = document.createElement('span');
                barangaySpan.innerHTML = barangay.name;

                // Create the edit and delete buttons
                const buttonContainer = document.createElement('div');
                buttonContainer.classList.add('flex', 'gap-2', 'justify-between', 'items-center');

                // Edit button
                const editButton = document.createElement('button');
                editButton.classList.add('px-2', 'py-0.5', 'bg-yellow-500', 'text-white', 'rounded', 'text-xs');
                editButton.innerHTML = 'Edit';
                editButton.onclick = function() {
                    editItem(barangay.id, 'barangay', barangay.name);
                };

                // Delete button
                const deleteButton = document.createElement('button');
                deleteButton.classList.add('px-2', 'py-0.5', 'bg-red-500', 'text-white', 'rounded', 'text-xs');
                deleteButton.innerHTML = 'Delete';
                deleteButton.onclick = function() {
                    deleteItem(barangay.id, 'barangay', barangay.name);
                };

                // Append buttons to the container
                buttonContainer.appendChild(editButton);
                buttonContainer.appendChild(deleteButton);

                // Append everything to the <li>
                li.appendChild(barangaySpan);
                li.appendChild(buttonContainer);

                // Append the <li> to the barangaySelect <ul>
                barangaySelect.appendChild(li);
            });
        } else {
            barangaySelect.innerHTML = `<li class="text-xs cursor-pointer text-gray-500">No existing barangays in ${cityName}</li>`;
        }
    }



    // Separate Edit Logic for Region/Province/City/Barangay
    function editItem(id, type, name) {
        // Prompt user for new input (e.g., name)
        const newName = prompt(`Enter a new name for the ${type}: ${name}`, name);

        // If the user provided a new name (not null or empty)
        if (newName && newName.trim() !== '' && newName) {
            // alert(`Updating ${type} with ID: ${id} to new name: ${newName}`);

            // Send the data to edit_location.php using fetch
            fetch('edit_location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    newName: newName,
                    type: type,
                }),
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response from the backend
                if (data.success) {
                    alert(`${type} updated successfully!`);
                    window.location.reload();
                } else {
                    alert(`Failed to update ${type}.`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating the location.');
            });
        } else if(newName!=null){
            alert("No valid name entered. Operation cancelled.");
        }
    }


    // Separate Delete Logic for Region/Province/City/Barangay
    function deleteItem(id, type, name) {
        // Ask for confirmation with two options
        const confirmDelete = confirm(`Are you sure you want to delete this ${type}: ${name}?`);

        // If the user confirmed the deletion
        if (confirmDelete) {
            // alert(`${type} with ID: ${id} will be deleted.`);

            // Send the delete request to delete_location.php using fetch
            fetch('delete_location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    type: type,
                }),
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response from the backend
                if (data.success) {
                    alert(`${type} deleted successfully!`);
                    window.location.reload();
                } else {
                    alert(`Failed to delete ${type}.`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting the location.');
            });
        }
    }

    // Load Regions in the UI
    function loadRegions() {
        const regionSelect = document.getElementById('select-region');
        regions.forEach(region => {
            const option = document.createElement('option');
            option.value = region.id;
            option.textContent = region.name;
            regionSelect.appendChild(option);
        });
    }

    // Load Provinces in the UI
    function loadProvinces() {
        const provinceSelect = document.getElementById('select-province');
        provinces.forEach(province => {  // Use the correct array name
            const option = document.createElement('option');
            option.value = province.id;  // Reference `province.id`
            option.textContent = province.name;  // Reference `province.name`
            provinceSelect.appendChild(option);
        });
    }


    // Load Regions in the UI
    function loadCities() {
        const citySelect = document.getElementById('select-city');
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city.id;
            option.textContent = city.name;
            citySelect.appendChild(option);
        });
    }


    // Handle Region Form Submission
    document.getElementById('add-region-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const regionName = document.getElementById('region-name').value.trim();

        if (!regionName) {
            alert("Region name cannot be empty.");
            return;
        }

        // Send the data to add_region.php
        fetch('add_region.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ region_name: regionName })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.reload();
                // Optionally, reset the form or update the UI
                document.getElementById('add-region-form').reset();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Error adding region: ' + error);
        });
    });

    // Handle Province Form Submission
    document.getElementById('add-province-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const provinceName = document.getElementById('province-name').value.trim();
        const selectedRegion = document.getElementById('select-region').value;

        if (!selectedRegion) {
            alert("Please select a region.");
            return;
        }

        if (!provinceName) {
            alert("Province name cannot be empty.");
            return;
        }

        // Send the data to add_province.php
        fetch('add_province.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ province_name: provinceName, region_id: selectedRegion })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.reload();
                // Optionally, reset the form or update the UI
                document.getElementById('add-province-form').reset();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Error adding province: ' + error);
        });
    });

    // Handle City Form Submission
    document.getElementById('add-city-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const cityName = document.getElementById('city-name').value.trim();
        const selectedProvince = document.getElementById('select-province').value;

        if (!selectedProvince) {
            alert("Please select a province.");
            return;
        }

        if (!cityName) {
            alert("City name cannot be empty.");
            return;
        }

        // Send the data to add_city.php
        fetch('add_city.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ city_name: cityName, province_id: selectedProvince })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.reload();
                // Optionally, reset the form or update the UI
                document.getElementById('add-city-form').reset();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Error adding city: ' + error);
        });
    });

    // Handle Barangay Form Submission
    document.getElementById('add-barangay-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const barangayName = document.getElementById('barangay-name').value.trim();
        const selectedCity = document.getElementById('select-city').value;

        if (!selectedCity) {
            alert("Please select a city.");
            return;
        }

        if (!barangayName) {
            alert("Barangay name cannot be empty.");
            return;
        }

        // Send the data to add_barangay.php
        fetch('add_barangay.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ barangay_name: barangayName, city_id: selectedCity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                window.location.reload();
                // Optionally, reset the form or update the UI
                document.getElementById('add-barangay-form').reset();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Error adding barangay: ' + error);
        });
    });





    // Initialize the page
    window.onload = function() {
        populateRegions();
        loadRegions();
        loadProvinces();
        loadCities();
    }
</script>

</body>
</html>
