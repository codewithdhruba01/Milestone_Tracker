<?php
// Database configuration
$host = "localhost";
$user = "root";
$password = "Avani@1234";
$database = "parent_toddler_tracker";

// Create connection
$conn = mysqli_connect("localhost", "root", "Avani@1234", "parent_toddler_tracker");

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// NOTE:
// Yahan echo mat karo
// Ye file sirf connection ke liye hoti hai
?>
