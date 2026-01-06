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

    /* ==============================
       IMAGE UPLOAD
    ================================ */
    $img = time() . "_" . basename($_FILES["child_image"]["name"]);
    $uploadPath = "uploads/img/" . $img;

    if (!move_uploaded_file($_FILES["child_image"]["tmp_name"], $uploadPath)) {
        die("Image upload failed");
    }

    /* ==============================
       CHILD BASIC DETAILS
    ================================ */
    $child_name = $_POST['child_name'];
    $dob        = $_POST['dob'];
    $gender     = $_POST['gender'];
    $center     = $_POST['center'];
    $age_group  = getAgeGroup($dob);

    $stmt = $conn->prepare("
        INSERT INTO children
        (child_name, dob, age_group, gender, center, child_image)
        VALUES (?,?,?,?,?,?)
    ");

    $stmt->bind_param(
        "ssssss",
        $child_name,
        $dob,
        $age_group,
        $gender,
        $center,
        $img
    );

    $stmt->execute();
    $child_id = $conn->insert_id;

    /* ==============================
       MILESTONES (QUESTION + ANSWER)
       NO DUPLICATION
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

    foreach ($domains as $domain => $items) {
        foreach ($items as $item) {
            $question = $_POST[$item['q']];
            $answer   = $_POST[$item['a']];

            $stmt2->bind_param(
                "isss",
                $child_id,
                $domain,
                $question,
                $answer
            );
            $stmt2->execute();
        }
    }

    echo "<script>
        alert('Child Added Successfully');
        window.location='add_child.php';
    </script>";
}
?>
