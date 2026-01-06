<?php
// Database configuration
$host = "localhost";
$user = "root";
$password = ""; // XAMPP default: no password for root
$database = "parent_toddler_tracker";

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// NOTE:
// Yahan echo mat karo
// Ye file sirf connection ke liye hoti hai
?>
