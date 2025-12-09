<?php
require_once '../db_connect.php';

header("Content-Type: application/json");

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Missing ID"]);
    exit;
}

$profile_id = intval($_GET['id']);

// STEP 1: Get profile basic details
$query = $conn->prepare("
    SELECT * 
    FROM profiles
    WHERE profile_id = ?
");
$query->bind_param("i", $profile_id);
$query->execute();
$result = $query->get_result();
$profile = $result->fetch_assoc();

if (!$profile) {
    echo json_encode(["error" => "Profile not found"]);
    exit;
}

$profile_type = $profile['profile_type'];
$location = null;

// STEP 2: Determine which table to query for location IDs
switch ($profile_type) {
    case 'individual':
        $table = "profiles_individual";
        break;
    case 'family':
        $table = "profiles_family";
        break;
    case 'institution':
        $table = "profiles_institution";
        break;
    case 'organization':
        $table = "profiles_organization";
        break;
    default:
        echo json_encode(["error" => "Unknown profile type"]);
        exit;
}

// STEP 3: Fetch location IDs
$query2 = $conn->prepare("
    SELECT *
    FROM $table
    WHERE profile_id = ?
");
$query2->bind_param("i", $profile_id);
$query2->execute();
$locationResult = $query2->get_result();
$location = $locationResult->fetch_assoc();

// STEP 4: Resolve names for each location ID
$regionName = $provinceName = $cityName = $barangayName = null;

if (!empty($location)) {
    // Region
    if (!empty($location['region_id'])) {
        $stmt = $conn->prepare("SELECT name FROM regions WHERE id = ?");
        $stmt->bind_param("i", $location['region_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $region = $res->fetch_assoc();
        $regionName = $region['name'] ?? null;
        $stmt->close();
    }

    // Province
    if (!empty($location['province_id'])) {
        $stmt = $conn->prepare("SELECT name FROM provinces WHERE id = ?");
        $stmt->bind_param("i", $location['province_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $province = $res->fetch_assoc();
        $provinceName = $province['name'] ?? null;
        $stmt->close();
    }

    // City
    if (!empty($location['city_id'])) {
        $stmt = $conn->prepare("SELECT name FROM cities WHERE id = ?");
        $stmt->bind_param("i", $location['city_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $city = $res->fetch_assoc();
        $cityName = $city['name'] ?? null;
        $stmt->close();
    }

    // Barangay
    if (!empty($location['barangay_id'])) {
        $stmt = $conn->prepare("SELECT name FROM barangays WHERE id = ?");
        $stmt->bind_param("i", $location['barangay_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $barangay = $res->fetch_assoc();
        $barangayName = $barangay['name'] ?? null;
        $stmt->close();
    }

    // Add names to the location array
    $location['region_name'] = $regionName;
    $location['province_name'] = $provinceName;
    $location['city_name'] = $cityName;
    $location['barangay_name'] = $barangayName;
}

// STEP 5: Combine all data and return JSON
$response = [
    "profile" => $profile,
    "location" => $location
];

echo json_encode($response);

// STEP 6: Close connections
$query->close();
$query2->close();
$conn->close();
