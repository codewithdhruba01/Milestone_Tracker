<?php
require "config.php";

/* ==============================
   AGE GROUP FUNCTION (BACKEND)
================================ */
function getAgeGroup($dob) {
    $birth = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($birth)->y;

    if ($age <= 1) return "0-1";
    if ($age <= 2) return "1-2";
    if ($age <= 3) return "2-3";
    if ($age <= 4) return "3-4";
    if ($age <= 5) return "4-5";
    if ($age <= 6) return "5-6";
    return "6-7";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Start transaction for data integrity
    $conn->begin_transaction();

    try {
        /* ==============================
           IMAGE UPLOAD
        ================================ */
        if (!isset($_FILES["child_image"]) || $_FILES["child_image"]["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Image upload error: " . $_FILES["child_image"]["error"]);
        }

        $img = time() . "_" . basename($_FILES["child_image"]["name"]);
        $uploadPath = "uploads/img/" . $img;

        // Create uploads directory if it doesn't exist
        if (!is_dir("uploads/img")) {
            mkdir("uploads/img", 0755, true);
        }

        if (!move_uploaded_file($_FILES["child_image"]["tmp_name"], $uploadPath)) {
            throw new Exception("Failed to move uploaded file");
        }

        /* ==============================
           VALIDATE AND SANITIZE INPUT
        ================================ */
        $child_name = trim($_POST['child_name']);
        $dob        = $_POST['dob'];
        $gender     = $_POST['gender'];
        $center     = $_POST['center'];

        // Validate required fields
        if (empty($child_name)) throw new Exception("Child name is required");
        if (empty($dob)) throw new Exception("Date of birth is required");
        if (empty($gender)) throw new Exception("Gender is required");
        if (empty($center)) throw new Exception("Center selection is required");

        $age_group  = getAgeGroup($dob);

        /* ==============================
           INSERT CHILD BASIC DETAILS
        ================================ */
        $stmt = $conn->prepare("
            INSERT INTO children
            (child_name, dob, age_group, gender, center, child_image)
            VALUES (?,?,?,?,?,?)
        ");

        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $stmt->bind_param(
            "ssssss",
            $child_name,
            $dob,
            $age_group,
            $gender,
            $center,
            $img
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to insert child details: " . $stmt->error);
        }

        $child_id = $conn->insert_id;

        /* ==============================
           MILESTONES (QUESTION + ANSWER)
           STORE ALL FORM DATA
        ================================ */
        $domains = [
            "Language" => [
                ["q" => "lang_q1_question", "a" => "lang_q1"],
                ["q" => "lang_q2_question", "a" => "lang_q2"],
                ["q" => "lang_q3_question", "a" => "lang_q3"]
            ],
            "Motor" => [
                ["q" => "motor_q1_question", "a" => "motor_q1"],
                ["q" => "motor_q2_question", "a" => "motor_q2"],
                ["q" => "motor_q3_question", "a" => "motor_q3"]
            ],
            "Social" => [
                ["q" => "social_q1_question", "a" => "social_q1"],
                ["q" => "social_q2_question", "a" => "social_q2"],
                ["q" => "social_q3_question", "a" => "social_q3"]
            ],
            "Cognitive" => [
                ["q" => "cog_q1_question", "a" => "cog_q1"],
                ["q" => "cog_q2_question", "a" => "cog_q2"],
                ["q" => "cog_q3_question", "a" => "cog_q3"]
            ]
        ];

        $stmt2 = $conn->prepare("
            INSERT INTO child_milestones
            (child_id, domain, question, answer)
            VALUES (?,?,?,?)
        ");

        if (!$stmt2) {
            throw new Exception("Prepare milestone statement failed: " . $conn->error);
        }

        $milestones_inserted = 0;

        foreach ($domains as $domain => $items) {
            foreach ($items as $item) {
                $question = isset($_POST[$item['q']]) ? trim($_POST[$item['q']]) : '';
                $answer   = isset($_POST[$item['a']]) ? $_POST[$item['a']] : '';

                // Validate milestone data
                if (empty($question)) {
                    throw new Exception("Missing question for $domain domain");
                }
                if (!in_array($answer, ['yes', 'no'])) {
                    throw new Exception("Invalid answer for $domain domain: must be 'yes' or 'no'");
                }

                $stmt2->bind_param(
                    "isss",
                    $child_id,
                    $domain,
                    $question,
                    $answer
                );

                if (!$stmt2->execute()) {
                    throw new Exception("Failed to insert milestone for $domain: " . $stmt2->error);
                }

                $milestones_inserted++;
            }
        }

        // Verify all milestones were inserted (should be 12 total: 4 domains × 3 questions each)
        if ($milestones_inserted !== 12) {
            throw new Exception("Incomplete milestone data: expected 12 entries, got $milestones_inserted");
        }

        // Commit transaction
        $conn->commit();

        echo "<script>
            alert('Child profile and all milestone data saved successfully!\\n\\nChild ID: $child_id\\nMilestones recorded: $milestones_inserted');
            window.location='add_child.php';
        </script>";

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();

        // Clean up uploaded file if it exists
        if (isset($uploadPath) && file_exists($uploadPath)) {
            unlink($uploadPath);
        }

        error_log("Child registration error: " . $e->getMessage());

        echo "<script>
            alert('Error saving child data: " . addslashes($e->getMessage()) . "\\nPlease try again.');
            window.history.back();
        </script>";
    } finally {
        // Close statements
        if (isset($stmt)) $stmt->close();
        if (isset($stmt2)) $stmt2->close();
    }
}
?>
