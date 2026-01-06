<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Add a Child</title>

<link href="https://fonts.googleapis.com/css2?family=Livvic:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<style>
/* GLOBAL */
body {
    margin: 0;
    background: #fef5dd;
    font-family: "Livvic", sans-serif;
}

.page {
    padding: 20px;
    max-width: 450px;
    margin: auto;
}

/* PROGRESS */
.progress-container {
    width: 100%;
}
.step {
    font-size: 14px;
    color: #f1a01d;
}
.progress-header {
    display: flex;
    justify-content: space-between;
    color: #444;
}
.progress-bar {
    width: 100%;
    height: 8px;
    background: #e6e6e6;
    border-radius: 20px;
    overflow: hidden;
}
.progress-fill {
    width: 20%;
    height: 8px;
    background: #f4b400;
    border-radius: 20px;
    transition: 0.3s;
}

/* TITLES */
.title {
    text-align: center;
    font-size: 24px;
}
.title-underline {
    width: 120px;
    height: 4px;
    background: #ff9900;
    margin: 5px auto 20px;
    border-radius: 8px;
}

/* CARD */
.card {
    background: #ffffff;
    padding: 30px;
    border-radius: 12px;
    border: 2px solid #d3d3d3;
}

/* UPLOAD */
.upload-box {
    display: flex;
    align-items: center;
    border: 2px solid #ffcc7f;
    border-radius: 12px;
    padding: 10px;
    margin-bottom: 20px;
    background: #fff6e5;
    width: 91%;
}
.upload-btn {
    background: #ff9f0a;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    color: white;
    font-weight: 600;
    cursor: pointer;
}
.upload-text {
    padding-left: 10px;
    color: #5a5a5a;
}

/* INPUTS */
.label { font-weight: 600; color: #5a5a5a; }
.input {
    width: 90%;
    padding: 12px;
    margin: 8px 0 18px;
    border-radius: 12px;
    border: 2px solid #ffcc7f;
}

/* DOB */
.dob-wrapper { position: relative; }
.dob-icon {
    position: absolute;
    right: 16px;
    top: 12px;
    background: #fe9900;
    padding: 6px 8px;
    border-radius: 8px;
    color: white;
    font-size: 18px;
}

/* CENTER DROPDOWN WRAPPER */
.center-wrapper {
    display: flex;
    align-items: center;
    margin: 22px 0 5px;
    width: 97%;
}

/* LEFT YELLOW TAB */
.center-label-box {
    background: linear-gradient(90deg, #E58D00, #F3C73F);
    padding: 13.4px 22px;
    border-radius: 15px 0 0 15px;
    color: #000;
    font-weight: 600;
    box-shadow: 0px 3px 10px rgba(0,0,0,0.15);
    font-size: 15px;
}

/* SELECT BOX */
.center-select-box {
    flex: 1;
    background: #F9F9F9;
    border: 2px solid #F3C73F;
    border-left: none;
    border-radius: 0 15px 15px 0;
    padding: 0;
    position: relative;
    box-shadow: 0px 3px 10px rgba(0,0,0,0.12);

}

/* SELECT */
.center-select-box select {
    width: 100%;
    padding: 13px 15px;
    border: none;
    background: transparent;
    font-size: 15px;
    font-weight: 500;
    appearance: none;
    color: #555;
}

/* CUSTOM DROPDOWN ARROW */
.center-select-box::after {
    content: "▾";
    font-size: 18px;
    color: #222;
    position: absolute;
    right: 15px;
    top: 13px;
    pointer-events: none;
}

/* OPTION DROPDOWN LIST */
.center-select-box select option {
    padding: 12px;
    font-size: 15px;
    border-bottom: 1px solid #ddd;
}

/* OPTIONAL: Remove last line */
.center-select-box select option:last-child {
    border-bottom: none;
}

/* RADIO QUESTION STYLE */
.question {
    margin-bottom: 20px;
}
.question p {
    font-weight: 600;
    color: #5a5a5a;
}
.option-row {
    display: flex;
    justify-content: space-between;
    padding: 12px;
    border: 2px solid #ffcc7f;
    border-radius: 12px;
    margin-top: 8px;
    width: 90%;
}

/* BUTTON ROW */
.button-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 25px;
    padding: 0 5px;
}

/* PREVIOUS BUTTON */
.prev-btn {
    background: #FFFFFF;
    color: #444;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.18);
    display: flex;
    align-items: center;
    gap: 6px;
}

/* NEXT BUTTON */
.next-btn {
    background: #F2A623; /* your orange */
    color: #fff;
    padding: 10px 25px;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(242, 166, 35, 0.45);
}

/* Arrows */
.arrow {
    font-size: 18px;
    font-weight: 700;
}


/* Center Submit */
.submit-btn {
    background: #ff9600;
    border: none;
    color: white;
    padding: 14px 40px;
    border-radius: 25px;
    font-size: 18px;
    cursor: pointer;
    margin: 25px auto;
    display: none;          /* Hidden initially */
}

.form-step { display: none; }
#step1 { display: block; }
</style>
</head>

<body>

<div class="page">

<!-- PROGRESS -->
<div class="progress-container">
    <div class="progress-header">
        <p>Progress</p>
        <p class="step">1 of 5</p>
    </div>
    <div class="progress-bar">
        <div class="progress-fill"></div>
    </div>
</div>


<form id="childForm" action="save_child.php" method="POST" enctype="multipart/form-data">


<!-- STEP 1 – PERSONAL DETAILS -->
<div class="form-step" id="step1">
    <h2 class="title">Personal Details</h2>
    <div class="title-underline"></div>

    <div class="card">

        <div class="upload-box">
            <input type="file" id="fileInput" name="child_image" accept="image/*" hidden required>
            <button type="button" class="upload-btn" id="uploadBtn">Upload</button>
            <span class="upload-text" id="fileName">Upload child’s picture</span>
        </div>

        <label class="label">Name</label>
        <input type="text" class="input" id="childName" name="child_name" placeholder="Enter Child Name" required>

        <label class="label">DOB</label>
        <div class="dob-wrapper">
            <input type="text" class="input dob-input" id="childDob" name="dob" placeholder="Select DOB" readonly required>
            <span class="dob-icon">📅</span>
        </div>

        <label class="label">Gender</label>
        <div class="option-row">
            <label><input type="radio" name="gender" value="Male" required> Male</label>
            <label><input type="radio" name="gender" value="Female" required> Female</label>
        </div>

        <div class="center-wrapper">
            <div class="center-label-box">Center</div>
            <div class="center-select-box">
                <select id="childCenter" name="center" required>
                    <option value="">Choose nearby center</option>
                    <option>Dhayari</option>
                    <option>Khed Shivapur</option>
                    <option>Karve Nagar</option>
                </select>
            </div>
        </div>

    </div>
</div>


<!-- STEP 2 – LANGUAGE (NO FORM TAG HERE) -->
<div class="form-step" id="step2">
    <h2 class="title">Language Development</h2>
    <div class="title-underline"></div>

    <div class="card">
        <div class="question">
            <p id="lang_q1_text">Loading...</p>
            <input type="hidden" name="lang_q1_question" id="lang_q1_question">
            <div class="option-row">
                <label><input type="radio" name="lang_q1" value="yes" required> Yes</label>
                <label><input type="radio" name="lang_q1" value="no"> No</label>
            </div>
        </div>

        <div class="question">
            <p id="lang_q2_text">Loading...</p>
            <input type="hidden" name="lang_q2_question" id="lang_q2_question">
            <div class="option-row">
                <label><input type="radio" name="lang_q2" value="yes" required> Yes</label>
                <label><input type="radio" name="lang_q2" value="no"> No</label>
            </div>
            
        </div>

        <div class="question">
            <p id="lang_q3_text">Loading...</p>
            <input type="hidden" name="lang_q3_question" id="lang_q3_question">
            <div class="option-row">
                <label><input type="radio" name="lang_q3" value="yes" required> Yes</label>
                <label><input type="radio" name="lang_q3" value="no"> No</label>
            </div>
        </div>
    </div>
</div>


<!-- STEP 3 – MOTOR -->
<div class="form-step" id="step3">
    <h2 class="title">Motor Development</h2>
    <div class="title-underline"></div>

    <div class="card">
        <div class="question">
            <p id="motor_q1_text">Loading...</p>
            <input type="hidden" name="motor_q1_question" id="motor_q1_question">
            <div class="option-row">
                <label><input type="radio" name="motor_q1" value="yes" required> Yes</label>
                <label><input type="radio" name="motor_q1" value="no"> No</label>
            </div>
        </div>

        <div class="question">
            <p id="motor_q2_text">Loading...</p>
            <input type="hidden" name="motor_q2_question" id="motor_q2_question">
            <div class="option-row">
                <label><input type="radio" name="motor_q2" value="yes" required> Yes</label>
                <label><input type="radio" name="motor_q2" value="no"> No</label>
            </div>
        </div>

        <div class="question">
            <p id="motor_q3_text">Loading...</p>
            <input type="hidden" name="motor_q3_question" id="motor_q3_question">
            <div class="option-row">
                <label><input type="radio" name="motor_q3" value="yes" required> Yes</label>
                <label><input type="radio" name="motor_q3" value="no"> No</label>
            </div>
        </div>
    </div>
</div>


<!-- STEP 4 – SOCIAL -->
<div class="form-step" id="step4">
    <h2 class="title">Social Skills</h2>
    <div class="title-underline"></div>

    <div class="card">
        <div class="question">
            <p id="social_q1_text">Loading...</p>
            <input type="hidden" name="social_q1_question" id="social_q1_question">
            <div class="option-row">
                <label><input type="radio" name="social_q1" value="yes" required> Yes</label>
                <label><input type="radio" name="social_q1" value="no"> No</label>
            </div>
        </div>

        <div class="question">
            <p id="social_q2_text">Loading...</p>
            <input type="hidden" name="social_q2_question" id="social_q2_question">
            <div class="option-row">
                <label><input type="radio" name="social_q2" value="yes" required> Yes</label>
                <label><input type="radio" name="social_q2" value="no"> No</label>
            </div>
        </div>

        <div class="question">
            <p id="social_q3_text">Loading...</p>
            <input type="hidden" name="social_q3_question" id="social_q3_question">
            <div class="option-row">
                <label><input type="radio" name="social_q3" value="yes" required> Yes</label>
                <label><input type="radio" name="social_q3" value="no"> No</label>
            </div>
        </div>
    </div>
</div>


<!-- STEP 5 – COGNITIVE -->
<div class="form-step" id="step5">
    <h2 class="title">Cognitive</h2>
    <div class="title-underline"></div>

    <div class="card">
        <div class="question">
            <p id="cog_q1_text">Loading...</p>
            <input type="hidden" name="cog_q1_question" id="cog_q1_question">
            <div class="option-row">
                <label><input type="radio" name="cog_q1" value="yes" required> Yes</label>
                <label><input type="radio" name="cog_q1" value="no"> No</label>
            </div>
        </div>

        <div class="question">
            <p id="cog_q2_text">Loading...</p>
            <input type="hidden" name="cog_q2_question" id="cog_q2_question">
            <div class="option-row">
                <label><input type="radio" name="cog_q2" value="yes" required> Yes</label>
                <label><input type="radio" name="cog_q2" value="no"> No</label>
            </div>
        </div>

        <div class="question">
            <p id="cog_q3_text">Loading...</p>
            <input type="hidden" name="cog_q3_question" id="cog_q3_question">
            <div class="option-row">
                <label><input type="radio" name="cog_q3" value="yes" required> Yes</label>
                <label><input type="radio" name="cog_q3" value="no"> No</label>
            </div>
        </div>
    </div>
</div>

</form>


<!-- BUTTONS -->
<div class="button-row">
    <button class="prev-btn"><span class="arrow">&#8249;</span> Back</button>
    <button class="next-btn">Next <span class="arrow">&#8250;</span></button>
</div>


<button class="submit-btn">Submit</button>


</div>
<script src="add_child.js"></script>

</body>
</html>





