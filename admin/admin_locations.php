<?php
include('../db_connect.php');

// Fetch regions
$regions_query = "SELECT * FROM regions";
$regions_result = $conn->query($regions_query);

// Fetch provinces
$provinces_query = "SELECT * FROM provinces";
$provinces_result = $conn->query($provinces_query);

// Fetch cities
$cities_query = "SELECT * FROM cities";
$cities_result = $conn->query($cities_query);

// Fetch barangays
$barangays_query = "SELECT * FROM barangays";
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
                <a href="admin_dashboard.php" class="block px-4 py-2 rounded bg-gray-300 font-semibold">
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
            <li><a href="admin_locations.php" class="block px-4 py-2 rounded hover:bg-gray-200">Location Management</a></li>
            <li><a href="admin_donation_logs.php" class="block px-4 py-2 rounded hover:bg-gray-200">Donation Logs</a></li>
            <li><a href="admin_activities.php" class="block px-4 py-2 rounded hover:bg-gray-200">Activity</a></li>
            <li><a href="admin_audit_trails.php" class="block px-4 py-2 rounded hover:bg-gray-200">Audit Trails</a></li>
            <li><a href="admin_settings.php" class="block px-4 py-2 rounded hover:bg-gray-200">Settings</a></li>

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
            <form class="flex gap-2">
                <input type="text" placeholder="Region Name" class="flex-1 p-1 text-sm border rounded">
                <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

        <!-- Add Province -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Province</h3>
            <form class="flex gap-2">
                <select class="p-1 text-sm border rounded flex-1">
                    <option>Select Region</option>
                    <option>Region 1</option>
                </select>
                <input type="text" placeholder="Province Name" class="flex-1 p-1 text-sm border rounded">
                <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

        <!-- Add City -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add City / Municipality</h3>
            <form class="flex gap-2">
                <select class="p-1 text-sm border rounded flex-1">
                    <option>Select Province</option>
                    <option>Province A</option>
                </select>
                <input type="text" placeholder="City Name" class="flex-1 p-1 text-sm border rounded">
                <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

        <!-- Add Barangay -->
        <div class="bg-white rounded-xl shadow-md p-4">
            <h3 class="text-lg font-semibold mb-2">Add Barangay</h3>
            <form class="flex gap-2">
                <select class="p-1 text-sm border rounded flex-1">
                    <option>Select City / Municipality</option>
                    <option>City X</option>
                </select>
                <input type="text" placeholder="Barangay Name" class="flex-1 p-1 text-sm border rounded">
                <button class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Add</button>
            </form>
        </div>

    </div>

    <!-- ================= EXISTING LOCATIONS TABLE ================= -->
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
            <li class="cursor-pointer text-gray-500">Select a Region</li> <!-- Placeholder for Provinces -->
        </ul>
    </div>

    <!-- Cities/Municipalities Section -->
    <div class="bg-white rounded-lg shadow p-4 h-[500px] overflow-y-auto">
        <h3 class="font-bold mb-2 text-lg">Cities / Municipalities</h3>
        <ul id="cities" class="space-y-2">
            <li class="cursor-pointer text-gray-500">Select a Province</li> <!-- Placeholder for Cities -->
        </ul>
    </div>

    <!-- Barangays Section -->
    <div class="bg-white rounded-lg shadow p-4 h-[500px] overflow-y-auto">
        <h3 class="font-bold mb-2 text-lg">Barangays</h3>
        <ul id="barangays" class="space-y-1 text-sm">
            <li class="cursor-pointer text-gray-500">Select a City / Municipality</li> <!-- Placeholder for Barangays -->
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
            li.classList.add('cursor-pointer', 'hover:bg-blue-100', 'transition');

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
        citySelect.innerHTML = '<li class="cursor-pointer text-gray-500">Select a Province</li>';
        barangaySelect.innerHTML = '<li class="cursor-pointer text-gray-500">Select a City / Municipality</li>';

        // Find region name
        const regionName = regions.find(region => region.id === regionId)?.name || 'Unknown Region';

        // Populate provinces based on selected region
        if (regionProvinces[regionId] && regionProvinces[regionId].length > 0) {
            regionProvinces[regionId].forEach(province => {
                const li = document.createElement('li');
                li.classList.add('cursor-pointer', 'hover:bg-green-100', 'transition', 'flex', 'justify-between', 'items-center', 'p-2', 'space-x-4');

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
            provinceSelect.innerHTML = `<li class="cursor-pointer text-gray-500">No existing provinces in ${regionName}</li>`;
        }
    }




    // Handle Province Selection
    function selectProvince(provinceId, provinceName) {
        selectedProvince = provinceId;
        const citySelect = document.getElementById('cities');
        const barangaySelect = document.getElementById('barangays');

        // Reset selections
        citySelect.innerHTML = '';
        barangaySelect.innerHTML = '<li class="cursor-pointer text-gray-500">Select a City / Municipality</li>';

        // Create a province item with edit and delete buttons
        if (provinceCities[provinceId] && provinceCities[provinceId].length > 0) {
            provinceCities[provinceId].forEach(city => {
                const li = document.createElement('li');
                li.classList.add('cursor-pointer', 'hover:bg-yellow-100', 'transition', 'flex', 'justify-between', 'items-center', 'p-2', 'space-x-4');

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
            citySelect.innerHTML = `<li class="cursor-pointer text-gray-500">No existing cities/municipalities in ${provinceName}</li>`;
        }
    }



    // Handle City Selection
    function selectCity(cityId, cityName) {
        selectedCity = cityId;
        const barangaySelect = document.getElementById('barangays');

        // Reset barangay selections
        barangaySelect.innerHTML = '';

        // Create a city item with edit and delete buttons
        if (cityBarangays[cityId] && cityBarangays[cityId].length > 0) {
            cityBarangays[cityId].forEach(barangay => {
                const li = document.createElement('li');
                li.classList.add('cursor-pointer', 'hover:bg-blue-200', 'transition', 'flex', 'justify-between', 'items-center', 'p-2', 'space-x-4');
                
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
                    editItem(barangay.id, 'barangay', barangay.name); // Call the editItem function
                };

                // Delete button
                const deleteButton = document.createElement('button');
                deleteButton.classList.add('px-2', 'py-0.5', 'bg-red-500', 'text-white', 'rounded', 'text-xs');
                deleteButton.innerHTML = 'Delete';
                deleteButton.onclick = function() {
                    deleteItem(barangay.id, 'barangay',barangay.name); // Call the deleteItem function
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
            barangaySelect.innerHTML = `<li class="cursor-pointer text-gray-500">No existing barangays in ${cityName}</li>`;
        }
    }


    // Separate Edit Logic for Region/Province/City/Barangay
    function editItem(id, type, name) {
        // Prompt user for new input (e.g., name)
        const newName = prompt(`Enter a new name for the ${type}: ${name}`);

        // If the user provided a new name (not null or empty)
        if (newName && newName.trim() !== '') {
            alert(`Updating ${type} with ID: ${id} to new name: ${newName}`);

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
                } else {
                    alert(`Failed to update ${type}.`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating the location.');
            });
        } else {
            alert("No valid name entered. Operation cancelled.");
        }
    }


    // Separate Delete Logic for Region/Province/City/Barangay
    function deleteItem(id, type, name) {
        // Ask for confirmation with two options
        const confirmDelete = confirm(`Are you sure you want to delete this ${type}: ${name}?`);

        // If the user confirmed the deletion
        if (confirmDelete) {
            alert(`${type} with ID: ${id} will be deleted.`);

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
                } else {
                    alert(`Failed to delete ${type}.`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting the location.');
            });
        } else {
            alert("Deletion cancelled.");
        }
    }




    // Initialize the page
    window.onload = function() {
        populateRegions();
    }
</script>

</body>
</html>
