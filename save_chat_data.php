<?php
header('Content-Type: application/json');
include 'config/db.php';

// ================================
// READ INPUT JSON
// ================================
$input = json_decode(file_get_contents("php://input"), true);

if (!$input) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request data."
    ]);
    exit;
}

// ================================
// VALIDATION
// ================================
$required_fields = ['first_name', 'last_name', 'email', 'phone', 'child_name', 'child_age', 'child_gender', 'parent_query'];

foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required field: $field"
        ]);
        exit;
    }
}

// Email validation
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid email address"
    ]);
    exit;
}

// Phone validation (10 digits)
if (!preg_match('/^\d{10}$/', $input['phone'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Phone number must be 10 digits"
    ]);
    exit;
}

// Age validation
$age = (int)$input['child_age'];
if ($age < 1 || $age > 18) {
    echo json_encode([
        "status" => "error",
        "message" => "Child age must be between 1 and 18"
    ]);
    exit;
}

// Gender validation
$valid_genders = ['male', 'female', 'other'];
if (!in_array(strtolower($input['child_gender']), $valid_genders)) {
    echo json_encode([
        "status" => "error",
        "message" => "Gender must be Male, Female, or Other"
    ]);
    exit;
}

// ================================
// SAVE TO DATABASE
// ================================
// We'll save to the existing child_milestones table or create a new table for chatbot data
// For now, let's create a simple chatbot_responses table

// Create table if it doesn't exist
$create_table_sql = "CREATE TABLE IF NOT EXISTS chatbot_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL,
    child_name VARCHAR(50) NOT NULL,
    child_age INT NOT NULL,
    child_gender VARCHAR(10) NOT NULL,
    parent_query TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($create_table_sql)) {
    echo json_encode([
        "status" => "error",
        "message" => "Database setup failed: " . $conn->error
    ]);
    exit;
}

// Insert data
$sql = "INSERT INTO chatbot_responses
    (first_name, middle_name, last_name, email, phone, child_name, child_age, child_gender, parent_query)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssssssiss",
    $input['first_name'],
    $input['middle_name'],
    $input['last_name'],
    $input['email'],
    $input['phone'],
    $input['child_name'],
    $input['child_age'],
    $input['child_gender'],
    $input['parent_query']
);

if (!$stmt->execute()) {
    if ($conn->errno == 1062) { // Duplicate entry
        echo json_encode([
            "status" => "error",
            "message" => "This information has already been submitted."
        ]);
        exit;
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Failed to save data: " . $stmt->error
        ]);
        exit;
    }
}

$stmt->close();

// ================================
// GENERATE AI RESPONSE
// ================================
// For now, we'll provide a simple response. In a real implementation,
// you would integrate with Gemini AI or another AI service.

$user_query = strtolower($input['parent_query']);

// Generate contextual response based on query keywords
if (strpos($user_query, 'growth') !== false || strpos($user_query, 'height') !== false || strpos($user_query, 'weight') !== false) {
    $ai_response = "📊 I see you're interested in your child's growth! Our dashboard tracks height and weight measurements monthly. You can add new measurements using the form in each child's profile section. The growth charts automatically update to show your child's progress over time. 🌱";
} elseif (strpos($user_query, 'milestone') !== false || strpos($user_query, 'development') !== false) {
    $ai_response = "🎯 Milestone tracking is important for child development! Click on the 'Milestone Tracker' card to view and manage developmental milestones for each child. You can track progress in Language, Motor, Cognitive, and Social skills. 📈";
} elseif (strpos($user_query, 'add child') !== false || strpos($user_query, 'register') !== false) {
    $ai_response = "👶 To add a new child, click the '+' button next to the child avatars. Fill in all the required information including name, date of birth, gender, and center. Once added, you can track their growth and milestones! ✨";
} elseif (strpos($user_query, 'chart') !== false || strpos($user_query, 'graph') !== false) {
    $ai_response = "📈 Our interactive charts show real-time growth data for each child. Switch between children to see individual growth patterns. The charts automatically update when you add new measurements. 📊";
} elseif (strpos($user_query, 'help') !== false || strpos($user_query, 'support') !== false) {
    $ai_response = "🤝 I'm here to help! You can ask me about: child growth tracking, milestone management, adding children, viewing charts, or any other features. Feel free to ask specific questions about the application! 💬";
} else {
    $ai_response = "👋 Thank you for reaching out! Your query has been recorded and our team will get back to you soon. In the meantime, feel free to explore the dashboard features - you can track growth, manage milestones, and add multiple children. We're here to support your child's development journey! 🌟";
}

// ================================
// FINAL RESPONSE
// ================================
echo json_encode([
    "status" => "success",
    "reply" => $ai_response,
    "message" => "Data saved successfully"
]);

$conn->close();
?>