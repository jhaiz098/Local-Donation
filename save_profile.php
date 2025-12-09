<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

include 'db_connect.php';

if(!isset($_SESSION['user_id'])){
    echo json_encode(['success'=>false,'message'=>'User not logged in']);
    exit;
}


$user_id = $_SESSION['user_id'];
$type = $_POST['profileType'] ?? '';

if(!$type){
    echo json_encode(['success'=>false,'message'=>'Profile type is required']);
    exit;
}

// Set default profile name based on type
$profile_name = "New Profile";
if($type === 'individual') $profile_name = ($_POST['firstName'] ?? '') . ' ' . ($_POST['lastName'] ?? '');
elseif($type === 'family') $profile_name = $_POST['profileName'] ?? 'Unnamed Family';
elseif($type === 'institution') $profile_name = $_POST['profileName'] ?? 'Unnamed Institution';
elseif($type === 'organization') $profile_name = $_POST['profileName'] ?? 'Unnamed Organization';

// Insert into profiles
$stmt = $conn->prepare("INSERT INTO profiles (user_id, profile_type, profile_name) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $type, $profile_name);
if(!$stmt->execute()){
    echo json_encode(['success'=>false,'message'=>$stmt->error]);
    exit;
}
$profile_id = $stmt->insert_id;

// Insert into specific profile table
if($type === 'individual'){
    $firstName = $_POST['firstName'] ?? '';
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $region = $_POST['region'] ?? 0;
    $province = $_POST['province'] ?? 0;
    $city = $_POST['city'] ?? 0;
    $barangay = $_POST['barangay'] ?? 0;
    $zip = $_POST['zip'] ?? '';

    $stmt2 = $conn->prepare("INSERT INTO profiles_individual 
        (profile_id, first_name, middle_name, last_name, date_of_birth, gender, phone_number, email, region_id, province_id, city_id, barangay_id, zip_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt2->bind_param(
        "isssssssiiiis",
        $profile_id,
        $firstName,
        $middleName,
        $lastName,
        $dob,
        $gender,
        $phone,
        $email,
        $region,
        $province,
        $city,
        $barangay,
        $zip
    );
    $stmt2->execute();
}
elseif($type === 'family'){
    $profileName = $_POST['profileName'] ?? 'Unnamed Family';
    $contactPerson = $_POST['contactPerson'] ?? '';
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $emailAddress = $_POST['emailAddress'] ?? '';
    $region = $_POST['region'] ?? 0;
    $province = $_POST['province'] ?? 0;
    $city = $_POST['city'] ?? 0;
    $barangay = $_POST['barangay'] ?? 0;
    $zip = $_POST['zip'] ?? '';

    $stmt2 = $conn->prepare("INSERT INTO profiles_family 
        (profile_id, household_name, primary_contact_person, contact_number, email, region_id, province_id, city_id, barangay_id, zip_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt2->bind_param(
        "issssiiiis",
        $profile_id,
        $profile_name,
        $contactPerson,
        $phoneNumber,
        $emailAddress,
        $region,
        $province,
        $city,
        $barangay,
        $zip
    );
    $stmt2->execute();
}
elseif($type === 'institution'){

    $institutionType = $_POST['institutionType'] ?? '';
    $institutionName = $_POST['institutionName'] ?? '';
    $contactPerson = $_POST['contactPerson'] ?? '';
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $emailAddress = $_POST['emailAddress'] ?? '';
    $region = $_POST['region'] ?? 0;
    $province = $_POST['province'] ?? 0;
    $city = $_POST['city'] ?? 0;
    $barangay = $_POST['barangay'] ?? 0;
    $zip = $_POST['zip'] ?? '';

    $stmt2 = $conn->prepare("INSERT INTO profiles_institution 
        (profile_id, institution_type, institution_name, official_contact_person, official_contact_number, official_email, region_id, province_id, city_id, barangay_id, zip_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt2->bind_param(
        "isssssiiiis",
        $profile_id,
        $institutionType,        // institution type selected from dropdown
        $institutionName,
        $contactPerson,
        $phoneNumber,
        $emailAddress,
        $region,
        $province,
        $city,
        $barangay,
        $zip
    );
    $stmt2->execute();
}
elseif($type === 'organization'){

    $organizationType = $_POST['organizationType'] ?? '';
    $organizationName = $_POST['organizationName'] ?? '';
    $contactPerson = $_POST['contactPerson'] ?? '';
    $phoneNumber = $_POST['phoneNumber'] ?? '';
    $emailAddress = $_POST['emailAddress'] ?? '';
    $registration = $_POST['registration'] ?? '';
    $region = $_POST['region'] ?? 0;
    $province = $_POST['province'] ?? 0;
    $city = $_POST['city'] ?? 0;
    $barangay = $_POST['barangay'] ?? 0;
    $zip = $_POST['zip'] ?? '';

    $stmt2 = $conn->prepare("INSERT INTO profiles_organization 
        (profile_id, organization_type, organization_name, contact_person, contact_number, email, registration_number, region_id, province_id, city_id, barangay_id, zip_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt2->bind_param(
        "issssssiiiis",
        $profile_id,
        $organizationType,        // organization type selected from dropdown
        $organizationName,
        $contactPerson,
        $phoneNumber,
        $emailAddress,
        $registration,
        $region,
        $province,
        $city,
        $barangay,
        $zip
    );
    $stmt2->execute();
}

// Set default profile picture
if($type === 'individual') {
    // For individuals, use their existing user profile picture from POST
    // Make sure your JS sends userAccount.profilePic as 'userProfilePic'
    $profile_pic = $_POST['userProfilePic'] ?? 'uploads/profile_pic_placeholder1.png';
} else {
    // For other profiles, default
    $profile_pic = 'uploads/profile_pic_placeholder1.png';

    // If a file is uploaded, save it
    if(isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === 0){
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = basename($_FILES['profilePic']['name']);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $newName = uniqid('profile_', true) . '.' . $ext;
        $targetFile = $uploadDir . $newName;

        if(move_uploaded_file($_FILES['profilePic']['tmp_name'], $targetFile)){
            $profile_pic = $targetFile;
        }
    }
}

// Save profile picture path to database (works for all types)
$stmt3 = $conn->prepare("UPDATE profiles SET profile_pic = ? WHERE profile_id = ?");
$stmt3->bind_param("si", $profile_pic, $profile_id);
$stmt3->execute();

// Return success
echo json_encode([
    'success' => true,
    'profile_id' => $profile_id,
    'profile_pic' => $profile_pic
]);

?>
