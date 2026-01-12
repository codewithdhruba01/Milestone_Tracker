<?php
header('Content-Type: application/json');
include 'config/db.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['child_id']) || !isset($_POST['height']) || !isset($_POST['weight'])) {
        throw new Exception('Missing required fields');
    }

    $child_id = (int)$_POST['child_id'];
    $height = (float)$_POST['height'];
    $weight = (float)$_POST['weight'];
    $check_date = date('Y-m-d');

    if ($child_id <= 0 || $height <= 0 || $weight <= 0) {
        throw new Exception('Invalid data values');
    }

    // Check if child exists
    $check_sql = "SELECT child_id FROM children WHERE child_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $child_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        throw new Exception('Child not found');
    }
    $check_stmt->close();

    // Insert growth record (allow multiple entries for same date if needed)
    $sql = "INSERT INTO child_growth_records (child_id, height, weight, check_date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("idds", $child_id, $height, $weight, $check_date);

    if (!$stmt->execute()) {
        throw new Exception('Failed to save growth data: ' . $stmt->error);
    }

    $stmt->close();

    echo json_encode([
        'success' => true,
        'message' => 'Growth data saved successfully',
        'data' => [
            'child_id' => $child_id,
            'height' => $height,
            'weight' => $weight,
            'check_date' => $check_date
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