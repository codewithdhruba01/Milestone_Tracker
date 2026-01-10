<?php
require "config.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$child_id = isset($_POST['child_id']) ? intval($_POST['child_id']) : 0;

if ($child_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid child ID']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();

    // First, get the child image filename to delete it later
    $stmt = $conn->prepare("SELECT child_image FROM children WHERE child_id = ?");
    $stmt->bind_param("i", $child_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Child not found");
    }

    $child = $result->fetch_assoc();
    $image_filename = $child['child_image'];
    $stmt->close();

    // Delete child milestones first (due to foreign key constraint)
    $stmt2 = $conn->prepare("DELETE FROM child_milestones WHERE child_id = ?");
    $stmt2->bind_param("i", $child_id);
    $stmt2->execute();
    $stmt2->close();

    // Delete the child record
    $stmt3 = $conn->prepare("DELETE FROM children WHERE child_id = ?");
    $stmt3->bind_param("i", $child_id);

    if (!$stmt3->execute()) {
        throw new Exception("Failed to delete child record");
    }

    $stmt3->close();

    // Commit transaction
    $conn->commit();

    // Delete the image file if it exists
    if (!empty($image_filename)) {
        $image_path = "uploads/img/" . $image_filename;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Child profile deleted successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();

    error_log("Delete child error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete child profile: ' . $e->getMessage()
    ]);
}
?>