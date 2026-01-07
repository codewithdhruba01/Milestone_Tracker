<?php
include 'config/db.php';

echo "<h2>Database Connection Test</h2>";

// Test connection
if ($conn) {
    echo "Database connected successfully<br>";

    // Check if database exists
    $result = $conn->query("SHOW DATABASES LIKE 'child_management'");
    if ($result && $result->num_rows > 0) {
        echo "Database 'child_management' exists<br>";
    } else {
        echo "Database 'child_management' not found<br>";
    }

    // Check if tables exist
    $conn->select_db("child_management");

    $tables = ['children', 'child_milestones'];
    foreach ($tables as $table) {
        $result = $conn->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "Table '$table' exists<br>";
        } else {
            echo "Table '$table' not found<br>";
        }
    }

    // Count children
    $result = $conn->query("SELECT COUNT(*) as count FROM children");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Total children in database: " . $row['count'] . "<br>";
    }

} else {
    echo " Database connection failed: " . mysqli_connect_error();
}

$conn->close();
?>
