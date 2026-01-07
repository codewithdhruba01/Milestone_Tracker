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

        echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Success - Child Registration</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: linear-gradient(135deg, #ffe9b3, #fff3d6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .success-popup {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            max-width: 500px;
            width: 90%;
            position: relative;
            animation: popup 0.6s ease-out;
            border: 3px solid #ffcc33;
        }

        @keyframes popup {
            0% {
                transform: scale(0.7);
                opacity: 0;
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            color: white;
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        }

        .success-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .success-message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .success-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid #ffcc33;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
        }

        .detail-value {
            color: #ff6b35;
            font-weight: 600;
        }

        .success-button {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
        }

        .success-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4);
        }

        .confetti {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .particle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: #ffcc33;
            border-radius: 50%;
            animation: float 3s ease-in-out infinite;
        }

        .particle:nth-child(2) { animation-delay: 0.5s; background: #4CAF50; }
        .particle:nth-child(3) { animation-delay: 1s; background: #2196F3; }
        .particle:nth-child(4) { animation-delay: 1.5s; background: #ff5722; }
        .particle:nth-child(5) { animation-delay: 2s; background: #9c27b0; }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            .success-popup {
                padding: 30px 20px;
                margin: 20px;
            }

            .success-title {
                font-size: 24px;
            }

            .success-message {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class='success-popup'>
        <div class='confetti'>
            <div class='particle' style='left: 10%;'></div>
            <div class='particle' style='left: 30%;'></div>
            <div class='particle' style='left: 50%;'></div>
            <div class='particle' style='left: 70%;'></div>
            <div class='particle' style='left: 90%;'></div>
        </div>

        <div class='success-icon'>✓</div>

        <h2 class='success-title'>Registration Successful!</h2>

        <p class='success-message'>
            Child profile and all milestone data have been saved successfully.
        </p>

        <div class='success-details'>
            <div class='detail-row'>
                <span class='detail-label'>Child ID:</span>
                <span class='detail-value'>$child_id</span>
            </div>
            <div class='detail-row'>
                <span class='detail-label'>Milestones Recorded:</span>
                <span class='detail-value'>$milestones_inserted</span>
            </div>
        </div>

        <button class='success-button' onclick='redirectToHome()'>Go to Home</button>
    </div>

    <script>
        function redirectToHome() {
            window.location.href = '../index.php';
        }

        // Auto redirect after 5 seconds
        setTimeout(redirectToHome, 5000);
    </script>
</body>
</html>";

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
