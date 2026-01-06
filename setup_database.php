<?php
/* ==============================
   DATABASE SETUP SCRIPT
   Run this once to create database and tables
================================ */

// Database configuration
$host = "localhost";
$user = "root";
$pass = "";

// Connect without specifying database first
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Database Setup</h2>";

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS child_management";
if ($conn->query($sql) === TRUE) {
    echo "✓ Database 'child_management' created or already exists<br>";
} else {
    echo "✗ Error creating database: " . $conn->error . "<br>";
}

// Select the database
$conn->select_db("child_management");

// Create children table
$sql = "CREATE TABLE IF NOT EXISTS children (
    child_id INT AUTO_INCREMENT PRIMARY KEY,
    child_name VARCHAR(100) NOT NULL,
    dob DATE NOT NULL,
    age_group VARCHAR(10) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    center VARCHAR(50) NOT NULL,
    child_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Table 'children' created or already exists<br>";
} else {
    echo "✗ Error creating children table: " . $conn->error . "<br>";
}

// Create child_milestones table
$sql = "CREATE TABLE IF NOT EXISTS child_milestones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    child_id INT NOT NULL,
    domain VARCHAR(20) NOT NULL,
    question TEXT NOT NULL,
    answer ENUM('yes','no') NOT NULL,
    FOREIGN KEY (child_id) REFERENCES children(child_id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Table 'child_milestones' created or already exists<br>";
} else {
    echo "✗ Error creating child_milestones table: " . $conn->error . "<br>";
}

echo "<br><strong>Setup completed!</strong><br>";
echo "You can now use the child registration system.<br>";
echo "All form data will be stored in the database when submitted.<br>";

$conn->close();
?>
