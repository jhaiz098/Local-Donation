<?php
session_start();

// DB connection
$host = "localhost";
$db = "local_donation";
$user = "admin_user";
$pass = "password";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get PHP role of logged-in user
$php_role = $_SESSION['role'] ?? 'Staff'; // default Staff

// Activate MySQL role dynamically
if (in_array($php_role, ['Staff', 'Admin', 'Superuser'])) {
    $conn->query("SET ROLE " . strtolower($php_role));
}
?>