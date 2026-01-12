<?php
header('Content-Type: application/json');
include 'config/db.php';

try {
    if (!isset($_GET['child_id'])) {
        throw new Exception('Child ID is required');
    }

    $child_id = (int)$_GET['child_id'];

    // Fetch latest growth record for this child
    $sql = "SELECT height, weight, check_date FROM child_growth_records
            WHERE child_id = ? ORDER BY check_date DESC LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $child_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = [];

    if ($result && $result->num_rows > 0) {
        $growth_data = $result->fetch_assoc();

        // Calculate next check date (1 month after last check)
        $last_check = new DateTime($growth_data['check_date']);
        $next_check = clone $last_check;
        $next_check->modify('+1 month');

        $response = [
            'height' => $growth_data['height'] . 'cm',
            'weight' => $growth_data['weight'] . 'kg',
            'last_check' => date('d M Y', strtotime($growth_data['check_date'])),
            'next_check' => date('d M Y', strtotime($next_check->format('Y-m-d'))),
            'status' => (strtotime($next_check->format('Y-m-d')) <= time()) ?
                'Today physical data update required' :
                'Next check due on ' . date('d M Y', strtotime($next_check->format('Y-m-d')))
        ];
    } else {
        $response = [
            'height' => 'Not recorded',
            'weight' => 'Not recorded',
            'last_check' => 'No records yet',
            'next_check' => 'Pending first check',
            'status' => 'Add first growth record'
        ];
    }

    $stmt->close();

    echo json_encode([
        'success' => true,
        'growth_data' => $response
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>