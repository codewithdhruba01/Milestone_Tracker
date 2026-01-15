<?php
// Test script for growth charts functionality
include 'config/db.php';

echo "<h1>Growth Charts Test</h1>";

// Create test data for child_id = 1
$test_data = [
    [1, 45.0, 8.5, '2024-01-15'],
    [1, 52.0, 9.2, '2024-02-15'],
    [1, 58.0, 10.1, '2024-03-15'],
    [1, 63.0, 11.0, '2024-04-15'],
    [1, 68.0, 12.2, '2024-05-15'],
    [1, 72.0, 13.1, '2024-06-15'],
];

echo "<h2>Inserting Test Data for Child ID 1</h2>";
foreach ($test_data as $data) {
    $sql = "INSERT INTO child_growth_records (child_id, height, weight, check_date) VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE height=?, weight=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iddsdd", $data[0], $data[1], $data[2], $data[3], $data[1], $data[2]);
    if ($stmt->execute()) {
        echo "✓ Inserted: Height {$data[1]}cm, Weight {$data[2]}kg on {$data[3]}<br>";
    } else {
        echo "✗ Failed to insert data<br>";
    }
    $stmt->close();
}

echo "<h2>Fetching Chart Data for Child ID 1</h2>";

// Fetch data using our API
$url = "http://localhost/parent_toddler1/get_growth_chart_data.php?child_id=1";
$context = stream_context_create([
    "http" => [
        "method" => "GET",
        "header" => "Accept: application/json\r\n"
    ]
]);

$response = file_get_contents($url, false, $context);
if ($response === FALSE) {
    echo "❌ Could not fetch data from API. Make sure Apache is running.<br>";
    echo "Manual test - direct database query:<br>";

    // Manual query to show data
    $sql = "SELECT height, weight, check_date FROM child_growth_records WHERE child_id = 1 ORDER BY check_date ASC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<table border='1'><tr><th>Date</th><th>Height (cm)</th><th>Weight (kg)</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>{$row['check_date']}</td><td>{$row['height']}</td><td>{$row['weight']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No data found in database<br>";
    }
} else {
    $data = json_decode($response, true);
    if ($data['success']) {
        echo "<h3>API Response Success!</h3>";
        echo "<strong>Labels:</strong> " . implode(", ", $data['chart_data']['labels']) . "<br>";
        echo "<strong>Height Data:</strong> " . implode(", ", $data['chart_data']['height']) . " cm<br>";
        echo "<strong>Weight Data:</strong> " . implode(", ", $data['chart_data']['weight']) . " kg<br>";
    } else {
        echo "❌ API Error: " . $data['message'] . "<br>";
    }
}

$conn->close();
?>