<?php

session_start();

// Database connection
$host = "localhost";
$db = "local_donation";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>