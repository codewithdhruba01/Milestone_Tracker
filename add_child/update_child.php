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
    // Validate required fields
    $child_name = trim($_POST['child_name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $center = $_POST['center'];

    if (empty($child_name)) throw new Exception("Child name is required");
    if (empty($dob)) throw new Exception("Date of birth is required");
    if (empty($gender)) throw new Exception("Gender is required");
    if (empty($center)) throw new Exception("Center selection is required");

    // Start transaction
    $conn->begin_transaction();

    // Get current child data to check if image needs updating
    $stmt = $conn->prepare("SELECT child_image FROM children WHERE child_id = ?");
    $stmt->bind_param("i", $child_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("Child not found");
    }

    $current_child = $result->fetch_assoc();
    $current_image = $current_child['child_image'];
    $stmt->close();

    $image_filename = $current_image; // Default to current image

    // Handle image upload if new image is provided
    if (isset($_FILES["child_image"]) && $_FILES["child_image"]["error"] === UPLOAD_ERR_OK) {
        // Generate new filename
        $new_image = time() . "_" . basename($_FILES["child_image"]["name"]);
        $uploadPath = "uploads/img/" . $new_image;

        // Create uploads directory if it doesn't exist
        if (!is_dir("uploads/img")) {
            mkdir("uploads/img", 0755, true);
        }

        if (!move_uploaded_file($_FILES["child_image"]["tmp_name"], $uploadPath)) {
            throw new Exception("Failed to upload new image");
        }

        $image_filename = $new_image;

        // Delete old image file if it exists and is different
        if (!empty($current_image) && $current_image !== $new_image) {
            $old_image_path = "uploads/img/" . $current_image;
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
    }

    // Calculate age group
    $birth = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birth)->y;

    if ($age <= 1) $age_group = "0-1";
    else if ($age <= 2) $age_group = "1-2";
    else if ($age <= 3) $age_group = "2-3";
    else if ($age <= 4) $age_group = "3-4";
    else if ($age <= 5) $age_group = "4-5";
    else if ($age <= 6) $age_group = "5-6";
    else $age_group = "6-7";

    // Update child record
    $stmt2 = $conn->prepare("
        UPDATE children
        SET child_name = ?, dob = ?, age_group = ?, gender = ?, center = ?, child_image = ?
        WHERE child_id = ?
    ");

    $stmt2->bind_param(
        "ssssssi",
        $child_name,
        $dob,
        $age_group,
        $gender,
        $center,
        $image_filename,
        $child_id
    );

    if (!$stmt2->execute()) {
        throw new Exception("Failed to update child record: " . $stmt2->error);
    }

    $stmt2->close();

    // Commit transaction
    $conn->commit();

    // Calculate age display for response
    $years = $age;
    $months = $today->diff($birth)->m;
    $age_display = $years . ' Years ' . $months . ' Months';

    echo json_encode([
        'success' => true,
        'message' => 'Child profile updated successfully',
        'data' => [
            'child_id' => $child_id,
            'child_name' => $child_name,
            'dob' => $dob,
            'age_group' => $age_group,
            'gender' => $gender,
            'center' => $center,
            'child_image' => $image_filename,
            'age_display' => $age_display,
            'age_years_only' => $years
        ]
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();

    // Clean up uploaded file if it exists
    if (isset($uploadPath) && file_exists($uploadPath)) {
        unlink($uploadPath);
    }

    error_log("Update child error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update child profile: ' . $e->getMessage()
    ]);
}
?>