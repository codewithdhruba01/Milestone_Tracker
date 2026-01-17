<?php
// Test script for chatbot functionality
include 'config/db.php';

echo "<h1>Chatbot Integration Test</h1>";

// Test database connection
if ($conn) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";

    // Check if chatbot_responses table exists
    $result = $conn->query("SHOW TABLES LIKE 'chatbot_responses'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✓ chatbot_responses table exists</p>";

        // Count existing records
        $count_result = $conn->query("SELECT COUNT(*) as total FROM chatbot_responses");
        if ($count_result) {
            $count = $count_result->fetch_assoc()['total'];
            echo "<p style='color: blue;'>📊 Total chatbot responses: $count</p>";
        }

        // Show recent responses
        $recent = $conn->query("SELECT * FROM chatbot_responses ORDER BY created_at DESC LIMIT 3");
        if ($recent && $recent->num_rows > 0) {
            echo "<h3>Recent Responses:</h3><ul>";
            while ($row = $recent->fetch_assoc()) {
                echo "<li><strong>" . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . "</strong> - " .
                     htmlspecialchars($row['child_name']) . " (" . $row['child_age'] . " years) - " .
                     date('M d, Y H:i', strtotime($row['created_at'])) . "</li>";
            }
            echo "</ul>";
        }

    } else {
        echo "<p style='color: orange;'>⚠ chatbot_responses table does not exist yet (will be created on first use)</p>";
    }

    // Test the API endpoint with sample data
    echo "<h3>Testing API Response Generation:</h3>";

    $test_queries = [
        "How does growth tracking work?",
        "What are milestones?",
        "How do I add a child?",
        "I need help with the charts"
    ];

    foreach ($test_queries as $query) {
        // Simulate the logic from save_chat_data.php
        $user_query = strtolower($query);

        if (strpos($user_query, 'growth') !== false || strpos($user_query, 'height') !== false || strpos($user_query, 'weight') !== false) {
            $response = "📊 I see you're interested in your child's growth! Our dashboard tracks height and weight measurements monthly...";
        } elseif (strpos($user_query, 'milestone') !== false || strpos($user_query, 'development') !== false) {
            $response = "🎯 Milestone tracking is important for child development! Click on the 'Milestone Tracker' card...";
        } elseif (strpos($user_query, 'add child') !== false || strpos($user_query, 'register') !== false) {
            $response = "👶 To add a new child, click the '+' button next to the child avatars...";
        } elseif (strpos($user_query, 'chart') !== false || strpos($user_query, 'graph') !== false) {
            $response = "📈 Our interactive charts show real-time growth data for each child...";
        } elseif (strpos($user_query, 'help') !== false || strpos($user_query, 'support') !== false) {
            $response = "🤝 I'm here to help! You can ask me about: child growth tracking...";
        } else {
            $response = "👋 Thank you for reaching out! Your query has been recorded...";
        }

        echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
        echo "<strong>Query:</strong> $query<br>";
        echo "<strong>Response:</strong> " . htmlspecialchars(substr($response, 0, 100)) . "...";
        echo "</div>";
    }

} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
}

$conn->close();
?>