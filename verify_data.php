<?php
/* ==============================
   DATA VERIFICATION SCRIPT
   View stored child data and milestones
================================ */

require "add_child/config.php";

echo "<h2>Child Data Verification</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .domain { font-weight: bold; color: #333; }
</style>";

// Get all children
$result = $conn->query("SELECT * FROM children ORDER BY created_at DESC");

if ($result->num_rows > 0) {
    echo "<h3>Children Records:</h3>";
    echo "<table>";
    echo "<tr><th>Child ID</th><th>Name</th><th>DOB</th><th>Age Group</th><th>Gender</th><th>Center</th><th>Image</th><th>Created</th></tr>";

    while ($child = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $child['child_id'] . "</td>";
        echo "<td>" . htmlspecialchars($child['child_name']) . "</td>";
        echo "<td>" . $child['dob'] . "</td>";
        echo "<td>" . $child['age_group'] . "</td>";
        echo "<td>" . $child['gender'] . "</td>";
        echo "<td>" . $child['center'] . "</td>";
        echo "<td>" . ($child['child_image'] ? "<img src='add_child/uploads/img/" . $child['child_image'] . "' width='50' height='50' style='object-fit: cover;'>" : "No image") . "</td>";
        echo "<td>" . $child['created_at'] . "</td>";
        echo "</tr>";

        // Show milestones for this child
        $milestones = $conn->query("SELECT domain, question, answer FROM child_milestones WHERE child_id = " . $child['child_id'] . " ORDER BY domain, id");

        if ($milestones->num_rows > 0) {
            echo "<tr><td colspan='8'>";
            echo "<strong>Milestones for " . htmlspecialchars($child['child_name']) . ":</strong><br>";
            echo "<table style='margin-left: 20px; margin-top: 10px; width: 95%;'>";
            echo "<tr><th>Domain</th><th>Question</th><th>Answer</th></tr>";

            $current_domain = "";
            while ($milestone = $milestones->fetch_assoc()) {
                echo "<tr>";
                echo "<td class='domain'>" . ($milestone['domain'] != $current_domain ? $milestone['domain'] : "") . "</td>";
                echo "<td>" . htmlspecialchars($milestone['question']) . "</td>";
                echo "<td>" . $milestone['answer'] . "</td>";
                echo "</tr>";
                $current_domain = $milestone['domain'];
            }
            echo "</table>";
            echo "</td></tr>";
        }
    }
    echo "</table>";
} else {
    echo "<p>No child records found in the database.</p>";
}

$conn->close();

echo "<br><a href='add_child/add_child.php'>← Back to Add Child Form</a>";
?>
