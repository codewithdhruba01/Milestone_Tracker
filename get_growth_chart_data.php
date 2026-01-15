<?php
header('Content-Type: application/json');
include 'config/db.php';

try {
    if (!isset($_GET['child_id'])) {
        throw new Exception('Child ID is required');
    }

    $child_id = (int)$_GET['child_id'];

    // Fetch all growth records for this child, ordered by date
    $sql = "SELECT height, weight, check_date FROM child_growth_records
            WHERE child_id = ? ORDER BY check_date ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $child_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $height_data = [];
    $weight_data = [];
    $labels = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $date = new DateTime($row['check_date']);
            $month_name = $date->format('M Y'); // e.g., "Jan 2024"

            $labels[] = $month_name;
            $height_data[] = (float)$row['height'];
            $weight_data[] = (float)$row['weight'];
        }
    }

    // If no data, return empty arrays
    echo json_encode([
        'success' => true,
        'chart_data' => [
            'labels' => $labels,
            'height' => $height_data,
            'weight' => $weight_data
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>