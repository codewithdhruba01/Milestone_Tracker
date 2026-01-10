<?php
  include 'config/db.php';

  // Fetch all children from database
  $children = [];

  try {
    $sql = "SELECT child_id, child_name, dob, age_group, gender, center, child_image, created_at FROM children ORDER BY created_at DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        // Calculate age in years and months
        $birth_date = new DateTime($row['dob']);
        $current_date = new DateTime();
        $age_interval = $current_date->diff($birth_date);

        $years = $age_interval->y;
        $months = $age_interval->m;

        $row['age_display'] = $years . ' Years ' . $months . ' Months';
        $row['age_years_only'] = $years;
        $children[] = $row;
      }
    }
  } catch (Exception $e) {
    // If database fails, show empty array (no children)
    $children = [];
  }

  // Get the first child as default selected
  $selected_child = !empty($children) ? $children[0] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Parent Dashboard Website</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<style>


/* GLOBAL */
body, html {
  margin: 0;
  padding: 0;
  font-family: Arial, sans-serif;
  background:#fff4cc;
}

.container {
  max-width: 100%;
  width: 100%;
  margin: 0 auto;
  padding: 0 20px;
  box-sizing: border-box;
  object-fit: cover;
  /* font-family:'Poppins',sans-serif; */
}


/************ HERO SECTION **************/

.hero{
  background: linear-gradient(135deg, #ffe9b3, #fff3d6);
  padding:60px 0px;
  box-shadow: 0px 4px 4px 0px #00000040;
}

.hero-info{
  display:flex;
  align-items:center;
  justify-content:space-between;
  flex-wrap:nowrap;
  margin: 0px -15px;
}

.hero-content, .hero-image{
  width: 50%;
  padding: 0px 15px;
}

.hero-tag{
  display:inline-block;
  background:#ffa200;
  color:#fff;
  padding:6px 14px;
  border-radius:20px;
  font-size:13px;
  font-family:'Poppins',sans-serif;
}

.hero-content h1{
  font-size:40px;
  font-weight:700;
  font-family:"Poppins", sans-serif;
  letter-spacing: 2%;
  line-height:1.2;
  color:#222;
}

.hero-content p{
  margin:18px 0 28px;
  color:#444;
  font-size:16px;
  font-family:'Poppins',sans-serif;
  line-height:1.6;
}

.hero-btn{
  display:inline-block;
  background: linear-gradient(106.58deg, #F98C01 53.83%, #935301 133.12%);
  box-shadow: 0px 3.89px 3.89px 0px #00000040;
  color:#fff;
  padding:12px 26px;
  border-radius:8px;
  font-weight:500;
  text-decoration:none;
  font-family:'Poppins',sans-serif;
  transition:0.3s;
}


.hero-image{
  width: 50%;
  padding: 0px 15px;
}

.hero-image img {
    width: 100%;
    border-radius: 100%;
    height: 100%;
    background-color: #ffb703;
    padding: 5px;
    object-fit: cover;
}
/************ HERO SECTION RESPONSIVE **************/
@media(max-width:768px){
  .hero-container{
    text-align:center;
  }

  .hero-content h1{
    font-size:32px;
  }

  .hero-image img{
    width:100%;
    max-width:320px;
  }
}

/************ PROFILE SECTION **************/
.section-title{
 text-align:center;
 font-size:26px;
 margin-top:40px;
 font-weight:700;
}

.profile-area{
 width:90%;
 max-width:950px;
 margin:30px auto;
 background:#fff;
 border:1px solid #ffe9b3;
 padding:25px;
 border-radius:10px;
 box-shadow:0 10px 25px rgba(0,0,0,.12);
}

/********** CHILD ICONS **********/
.child-switch{
 display:flex;
 align-items:center;
 gap:12px;
 margin-bottom:10px;
}

.child-switch img{
 width:60px;
 height:60px;
 border-radius:50%;
 object-fit:cover;
 border:3px solid #fff;
 box-shadow:0 6px 18px rgba(0,0,0,.2);
}

/* ADD CHILD CIRCLE */
.add-child{
 width:60px;
 height:60px;
 border-radius:50%;
 border:2px dashed #ff9800;
 color:#ff9800;
 font-size:30px;
 font-weight:700;
 display:flex;
 justify-content:center;
 align-items:center;
 text-decoration:none;
 background:#ffe9b3;
 box-shadow:0 6px 18px rgba(0,0,0,.2);
 transition:.3s;
}

.add-child:hover{
 background:#ff9800;
 color:#fff;
}

/* CHILD SWITCHING STYLES */
.child-avatar {
 cursor: pointer;
 transition: all 0.3s ease;
 position: relative;
}

.child-avatar.active {
 transform: scale(1.1);
 box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4);
 border: 3px solid #ff9800;
}

.child-avatar:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.child-avatar.active::after {
  content: '';
  position: absolute;
  bottom: -5px;
  left: 50%;
  transform: translateX(-50%);
  width: 8px;
  height: 8px;
  background: #ff9800;
  border-radius: 50%;
}

/* CHILD AVATAR CONTAINER AND ACTIONS */
.child-avatar-container {
  position: relative;
  display: inline-block;
}

.child-actions {
  position: absolute;
  top: -5px;
  right: -5px;
  display: flex;
  gap: 2px;
  opacity: 0;
  transform: scale(0.8);
  transition: all 0.3s ease;
}

.child-avatar-container:hover .child-actions {
  opacity: 1;
  transform: scale(1);
}

.edit-child-btn, .delete-child-btn {
  width: 24px;
  height: 24px;
  border: none;
  border-radius: 50%;
  cursor: pointer;
  font-size: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.edit-child-btn {
  background: #2196F3;
  color: white;
}

.edit-child-btn:hover {
  background: #1976D2;
  transform: scale(1.1);
}

.delete-child-btn {
  background: #f44336;
  color: white;
}

.delete-child-btn:hover {
  background: #d32f2f;
  transform: scale(1.1);
}

/******** INFO **********/
.info-row{
 margin-top:15px;
 font-size:15px;
}

.highlight{
 color:#ff6d00;
 font-weight:600;
}

.update-box{
 margin-top:12px;
 background:#fff5d6;
 padding:10px;
 border-left:5px solid #ffa200;
}

.inputs{
 margin-top:12px;
}

.inputs input{
 width:120px;
 padding:7px;
 border-radius:8px;
 border:2px solid #ffcc7a;
 outline:none;
 text-align:center;
 font-weight:600;
}

.submit-btn{
 margin-top:12px;
 padding:9px 25px;
 background:#ff8c00;
 border:none;
 color:white;
 border-radius:6px;
 cursor:pointer;
 transition:.3s;
}

.submit-btn:hover{
 background:#e67400;
}

/************** MILESTONE BOX **************/
.milestone-box{
 width:90%;
 max-width:950px;
 margin:25px auto;
 padding:18px;
 border-radius:10px;
 background:linear-gradient(135deg,#ff9800);
 color:#fff;
 display:flex;
 justify-content:space-between;
 align-items:center;
}

.tag{
 background:#ffe9b3;
 color:#ff9800;
 padding:4px 10px;
 border-radius:20px;
 font-size:11px;
 font-weight:700;
 display:inline-block;
 margin-top:5px;
}

.game-box{
 margin-top:10px;
 background:rgba(255,255,255,.25);
 padding:8px 14px;
 border-radius:8px;
 font-size:13px;
 font-weight:600;
 display:inline-block;
}

.progress-section{
    text-align:center;
    padding:50px 0;
    font-family:Poppins, Arial;
}

.title{
    font-size:28px;
    font-weight:700;
    margin-bottom:35px;
}

.progress-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:25px;
    width:85%;
    margin:auto;
}

.progress-card{
    background:#ffe9b3;
    padding:25px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
}

.circle{
    width:110px;
    height:110px;
    border-radius:50%;
    margin:auto;
    display:flex;
    justify-content:center;
    align-items:center;
    font-weight:600;
    border:8px solid #ccc;
}

/* Responsive */
@media(max-width:992px){
    .progress-grid{ grid-template-columns:repeat(2,1fr); }
}
@media(max-width:600px){
    .progress-grid{ grid-template-columns:1fr; }
}
.observer-section{
    width:100%;
    padding:40px 0;
    background:#fff4d4;
    text-align:center;
    font-family:Poppins, Arial;
}

/* Title */
.observer-title{
    font-size:26px;
    font-weight:700;
    margin-bottom:15px;
    position:relative;
}

/* Underline effect */
.observer-title::after{
    content:"";
    width:120px;
    height:4px;
    background:linear-gradient(to right,#ffcc66,#ff8c00);
    display:block;
    margin:8px auto 0;
    border-radius:3px;
}

/* Note Box */
.observer-box{
    width:70%;
    margin:auto;
    background:#ffe9b3;
    padding:35px 10px;
    border-radius:12px;
    border:2px solid #ffd36b;
    font-size:18px;
    font-style:italic;
    font-weight:500;
    color:#333;
    box-shadow:0 5px 20px rgba(0,0,0,0.1);
}

/* Responsive */
@media(max-width:768px){
    .observer-box{
        width:90%;
        font-size:16px;
    }
}
body{
  margin:0;
  font-family:Arial, sans-serif;
  background:#fff4cc;
}

.growth-section{
  padding:60px 5%;
}

.section-title{
  text-align:center;
  font-size:30px;
  margin-bottom:40px;
}

.growth-wrapper{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(350px,1fr));
  gap:30px;
}

.growth-card{
  background:#fff;
  border-radius:20px;
  padding:25px;
  border:2px solid #ffcc33;
}

.chart{
  display:flex;
}

.y-axis{
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  font-size:13px;
  margin-right:10px;
}

.graph-area{
  position:relative;
  flex:1;
  height:230px;
  border-left:2px solid #777;
  border-bottom:2px solid #777;
}

.line{
  position:absolute;
  inset:0;
  cursor:pointer;
}

.points span{
  position:absolute;
  width:10px;
  height:10px;
  background:#ffcc00;
  border-radius:50%;
  transform:translate(-50%,50%);
  cursor:pointer;
}

.months{
  position:absolute;
  bottom:-28px;
  width:100%;
  display:flex;
  justify-content:space-between;
  font-size:13px;
}

/* Tooltip */
#tooltip{
  position:absolute;
  background:#000;
  color:#fff;
  padding:6px 10px;
  font-size:13px;
  border-radius:6px;
  display:none;
  pointer-events:none;
  z-index:999;
}

/* MODAL STYLES */
.modal {
  display: none;
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
  backdrop-filter: blur(5px);
}

.modal-content {
  background-color: #fff;
  margin: 5% auto;
  padding: 0;
  border-radius: 15px;
  box-shadow: 0 20px 60px rgba(0,0,0,0.3);
  width: 90%;
  max-width: 500px;
  animation: modalFadeIn 0.3s ease-out;
}

.small-modal {
  max-width: 400px;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(-50px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 25px;
  border-bottom: 1px solid #eee;
  background: linear-gradient(135deg, #ffe9b3, #fff3d6);
  border-radius: 15px 15px 0 0;
}

.modal-header h2 {
  margin: 0;
  color: #333;
  font-size: 24px;
  font-weight: 700;
}

.close-modal {
  font-size: 28px;
  font-weight: bold;
  color: #666;
  cursor: pointer;
  transition: color 0.3s;
}

.close-modal:hover {
  color: #ff9800;
}

.modal-body {
  padding: 25px;
  text-align: center;
}

.warning-text {
  color: #f44336;
  font-weight: 600;
  margin-top: 10px;
}

/* FORM STYLES */
.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #555;
  font-size: 14px;
}

.form-group input[type="text"],
.form-group input[type="date"],
.form-group select {
  width: 100%;
  padding: 12px 15px;
  border: 2px solid #ffcc7f;
  border-radius: 10px;
  font-size: 14px;
  font-family: inherit;
  transition: border-color 0.3s, box-shadow 0.3s;
}

.form-group input[type="text"]:focus,
.form-group input[type="date"]:focus,
.form-group select:focus {
  outline: none;
  border-color: #ff9800;
  box-shadow: 0 0 0 3px rgba(255, 152, 0, 0.1);
}

.form-group input[type="file"] {
  width: 100%;
  padding: 8px;
  border: 2px solid #ffcc7f;
  border-radius: 10px;
  background: #fff8e5;
}

.current-image {
  margin-top: 5px;
}

.current-image small {
  color: #666;
  font-style: italic;
}

.radio-group {
  display: flex;
  gap: 20px;
  margin-top: 8px;
}

.radio-group label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
  color: #555;
  cursor: pointer;
}

.radio-group input[type="radio"] {
  margin: 0;
}

/* MODAL ACTIONS */
.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 20px 25px;
  border-top: 1px solid #eee;
  background: #f9f9f9;
  border-radius: 0 0 15px 15px;
}

.cancel-btn, .save-btn, .delete-btn {
  padding: 10px 20px;
  border: none;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.cancel-btn {
  background: #9e9e9e;
  color: white;
}

.cancel-btn:hover {
  background: #757575;
}

.save-btn {
  background: #4caf50;
  color: white;
}

.save-btn:hover {
  background: #45a049;
  transform: translateY(-1px);
}

.delete-btn {
  background: #f44336;
  color: white;
}

.delete-btn:hover {
  background: #d32f2f;
  transform: translateY(-1px);
}


</style>
</head>

<body>

<section class="hero">
  <div class="container">
    <div class="hero-info">
      <div class="hero-content">
        <span class="hero-tag">Parent Dashboard</span>
        <h1>Track Your Child's <br>Growth & Development </h1>
        <p>Monitor learning milestones, physical growth, and development progress in one simple and smart dashboard.</p>
        <a href="#milestone" class="hero-btn">
          View Milestone Tracker
        </a>
      </div>

      <div class="hero-image">
        <img src="./Assets/img/hero_bg.png" alt="Child development">
      </div>
    </div>
  </div>
</section>

<section>
  <div class="container">
    <h2 class="section-title">Child Profile</h2>
    <div class="profile-area">

    <!--  HERE — Dynamic Child Photos + Add Button -->
    <div class="child-switch" id="child-switch">
      <?php if (!empty($children)): ?>
        <?php foreach ($children as $index => $child): ?>
          <div class="child-avatar-container">
            <img src="add_child/uploads/img/<?php echo htmlspecialchars($child['child_image']); ?>"
                 alt="<?php echo htmlspecialchars($child['child_name']); ?>"
                 class="child-avatar <?php echo $index === 0 ? 'active' : ''; ?>"
                 data-child-id="<?php echo $child['child_id']; ?>"
                 data-child-name="<?php echo htmlspecialchars($child['child_name']); ?>"
                 data-child-age="<?php echo htmlspecialchars($child['age_display']); ?>"
                 data-child-center="<?php echo htmlspecialchars($child['center']); ?>"
                 data-child-age-years="<?php echo $child['age_years_only']; ?>"
                 data-child-gender="<?php echo htmlspecialchars($child['gender']); ?>"
                 data-child-dob="<?php echo htmlspecialchars($child['dob']); ?>"
                 data-child-image="<?php echo htmlspecialchars($child['child_image']); ?>"
                 onclick="switchChild(this)">
            <div class="child-actions">
              <button class="edit-child-btn" onclick="editChild(event, <?php echo $child['child_id']; ?>)" title="Edit Child">
                ✏️
              </button>
              <button class="delete-child-btn" onclick="deleteChild(event, <?php echo $child['child_id']; ?>, '<?php echo htmlspecialchars($child['child_name']); ?>')" title="Delete Child">
                🗑️
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- No children found - show placeholder -->
        <div style="background: #f0f0f0; padding: 20px; border-radius: 10px; text-align: center; color: #666;">
          No children registered yet. <a href="add_child/add_child.php" style="color: #ff9800;">Add your first child</a>
        </div>
      <?php endif; ?>

      <!-- Add Child Button Link -->
      <a href="add_child/add_child.php" class="add-child">+</a>
    </div>

    <div class="info-row">
      <b>Name:</b> <span id="child-name"><?php echo $selected_child ? htmlspecialchars($selected_child['child_name']) : 'No children added'; ?></span> |
      <span class="highlight" id="child-age"><?php echo $selected_child ? htmlspecialchars($selected_child['age_display']) : ''; ?></span>
    </div>

    <div class="info-row">
      Center: <span id="child-center"><?php echo $selected_child ? htmlspecialchars($selected_child['center']) : 'No center assigned'; ?></span>
    </div>

    <div class="info-row" id="displayData">
      Height: 110cm | Weight: 19kg
    </div>

    <div class="update-box">
      Last Check: 1 Month Ago | Next Check: Today <br>
      <b>Today physical data update required</b>
    </div>

    <div class="inputs">
      Height: <input type="number" id="heightInput" placeholder="cm">
      Weight: <input type="number" id="weightInput" placeholder="kg">
    </div>

    <button class="submit-btn" onclick="updateData()">Submit</button>
    </div>

    <a href="milestone-tracker.php" style="text-decoration:none; color:inherit;">
      <div class="milestone-box">
      <div>
        <h3>Milestone Tracker</h3>
        <span class="tag">Today's Task Completed</span>
        <p>Daily tasks to support your child's development</p>

        <div class="game-box">🎮 Play Development Game →</div>
      </div>

      <h1>3/6</h1>
      </div>
      </a>
  </div>
</section>



<section class="progress-section">
    <h2 class="title">Development Progress</h2>

    <div class="progress-grid">

        <div class="progress-card" id="language-card" data-score="70">
            <div class="circle"><span></span></div>
            <h3>Language</h3>
            <p>Your children's speaking or linguistic skills</p>
        </div>

        <div class="progress-card" id="motor-card" data-score="90">
            <div class="circle"><span></span></div>
            <h3>Motor</h3>
            <p>Your children's physical abilities that allow them to use muscles</p>
        </div>

        <div class="progress-card" id="cognitive-card" data-score="40">
            <div class="circle"><span></span></div>
            <h3>Cognitive</h3>
            <p>Your children mental abilities that enable them to think</p>
        </div>

        <div class="progress-card" id="social-card" data-score="100">
            <div class="circle"><span></span></div>
            <h3>Social</h3>
            <p>Your children abilities that enable people to communicate</p>
        </div>

    </div>
</section>
<section class="observer-section">
    <h2 class="observer-title">Observer's Note</h2>

    <div class="observer-box">
        "Karan should focus
        <br>
        on developing his cognitive skills"
    </div>
</section>
<section class="growth-section">

  <h2 class="section-title">Growth Progress</h2>

  <div class="growth-wrapper">

    <!-- HEIGHT CARD -->
    <div class="growth-card">
      <h3>🙂 Children Height Growth</h3>

      <div class="chart">
        <div class="y-axis">
          <span>110</span>
          <span>80</span>
          <span>50</span>
          <span>0</span>
        </div>

        <div class="graph-area">
          <div class="line"></div>

          <div class="points">
            <span data-x="5"  data-value="April : 50 cm"  style="left:5%;  bottom:40%"></span>
            <span data-x="25" data-value="May : 60 cm"    style="left:25%; bottom:55%"></span>
            <span data-x="45" data-value="June : 65 cm"   style="left:45%; bottom:60%"></span>
            <span data-x="65" data-value="July : 78 cm"   style="left:65%; bottom:70%"></span>
            <span data-x="85" data-value="August : 90 cm" style="left:85%; bottom:80%"></span>
          </div>

          <div class="months">
            <span>Apr</span>
            <span>May</span>
            <span>Jun</span>
            <span>Jul</span>
            <span>Aug</span>
          </div>
        </div>
      </div>
    </div>

    <!-- WEIGHT CARD -->
    <div class="growth-card">
      <h3>🙂 Children Weight Growth</h3>

      <div class="chart">
        <div class="y-axis">
          <span>20</span>
          <span>16</span>
          <span>12</span>
          <span>0</span>
        </div>

        <div class="graph-area">
          <div class="line"></div>

          <div class="points">
            <span data-x="5"  data-value="April : 10 kg"  style="left:5%;  bottom:35%"></span>
            <span data-x="25" data-value="May : 12 kg"    style="left:25%; bottom:45%"></span>
            <span data-x="45" data-value="June : 13 kg"   style="left:45%; bottom:50%"></span>
            <span data-x="65" data-value="July : 16 kg"   style="left:65%; bottom:65%"></span>
            <span data-x="85" data-value="August : 18 kg" style="left:85%; bottom:75%"></span>
          </div>

          <div class="months">
            <span>Apr</span>
            <span>May</span>
            <span>Jun</span>
            <span>Jul</span>
            <span>Aug</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Tooltip -->
<div id="tooltip"></div>

<!-- Edit Child Modal -->
<div id="editChildModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Edit Child Details</h2>
      <span class="close-modal" onclick="closeEditModal()">&times;</span>
    </div>
    <form id="editChildForm" enctype="multipart/form-data">
      <input type="hidden" id="editChildId" name="child_id">

      <div class="form-group">
        <label for="editChildName">Child Name *</label>
        <input type="text" id="editChildName" name="child_name" required>
      </div>

      <div class="form-group">
        <label for="editChildDob">Date of Birth *</label>
        <input type="date" id="editChildDob" name="dob" required>
      </div>

      <div class="form-group">
        <label>Gender *</label>
        <div class="radio-group">
          <label><input type="radio" name="gender" value="Male" required> Male</label>
          <label><input type="radio" name="gender" value="Female" required> Female</label>
        </div>
      </div>

      <div class="form-group">
        <label for="editChildCenter">Center *</label>
        <select id="editChildCenter" name="center" required>
          <option value="">Choose nearby center</option>
          <option>Dhayari</option>
          <option>Khed Shivapur</option>
          <option>Karve Nagar</option>
        </select>
      </div>

      <div class="form-group">
        <label for="editChildImage">Update Image (optional)</label>
        <input type="file" id="editChildImage" name="child_image" accept="image/*">
        <div class="current-image">
          <small>Current image: <span id="currentImageName"></span></small>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
        <button type="submit" class="save-btn">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="modal">
  <div class="modal-content small-modal">
    <div class="modal-header">
      <h2>Confirm Delete</h2>
      <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
    </div>
    <div class="modal-body">
      <p>Are you sure you want to delete <strong id="deleteChildName"></strong>'s profile?</p>
      <p class="warning-text">This action cannot be undone and will remove all associated data.</p>
    </div>
    <div class="modal-actions">
      <button type="button" class="cancel-btn" onclick="closeDeleteModal()">Cancel</button>
      <button type="button" class="delete-btn" id="confirmDeleteBtn">Delete</button>
    </div>
  </div>
</div>

<script>
function updateData(){
 let h = document.getElementById("heightInput").value;
 let w = document.getElementById("weightInput").value;

 if(h === "" || w === ""){
   alert("Please enter Height and Weight");
   return;
 }

 document.getElementById("displayData").innerHTML =
   "Height: " + h + "cm | Weight: " + w + "kg";

 //Input clear after submit
 document.getElementById("heightInput").value = "";
 document.getElementById("weightInput").value = "";
}

// Child switching functionality
function switchChild(selectedAvatar) {
 // Remove active class from all avatars
 document.querySelectorAll('.child-avatar').forEach(avatar => {
   avatar.classList.remove('active');
 });

 // Add active class to selected avatar
 selectedAvatar.classList.add('active');

 // Update profile information
 const childName = selectedAvatar.getAttribute('data-child-name');
 const childAge = selectedAvatar.getAttribute('data-child-age');
 const childCenter = selectedAvatar.getAttribute('data-child-center');
 const childAgeYears = parseInt(selectedAvatar.getAttribute('data-child-age-years'));

 document.getElementById('child-name').textContent = childName;
 document.getElementById('child-age').textContent = childAge;
 document.getElementById('child-center').textContent = childCenter;

 // Update development progress cards based on age
 updateDevelopmentProgress(childAgeYears);

 // Update growth charts based on child ID
 const childId = selectedAvatar.getAttribute('data-child-id');
 updateGrowthCharts(childId);
}

// Update development progress based on child's age
function updateDevelopmentProgress(ageInYears) {
 const progressCards = document.querySelectorAll('.progress-card');

 progressCards.forEach((card, index) => {
   const circle = card.querySelector('.circle span');
   let score = 0;
   let status = '';

   // Different progress based on age and development area
   switch(index) {
     case 0: // Language
       score = ageInYears >= 2 ? 85 : ageInYears >= 1 ? 65 : 45;
       status = score >= 85 ? 'Excellent' : score >= 70 ? 'Good' : 'Developing';
       break;
     case 1: // Motor
       score = ageInYears >= 2 ? 90 : ageInYears >= 1 ? 75 : 50;
       status = score >= 85 ? 'Perfect' : score >= 70 ? 'Excellent' : 'Good';
       break;
     case 2: // Cognitive
       score = ageInYears >= 2 ? 40 : ageInYears >= 1 ? 55 : 30;
       status = score >= 70 ? 'Excellent' : score >= 50 ? 'Good' : 'Developing';
       break;
     case 3: // Social
       score = ageInYears >= 2 ? 100 : ageInYears >= 1 ? 80 : 60;
       status = score >= 85 ? 'Perfect' : score >= 70 ? 'Excellent' : 'Good';
       break;
   }

   circle.textContent = status;
   card.setAttribute('data-score', score);
 });
}

// Update growth charts based on child data (placeholder for now)
function updateGrowthCharts(childId) {
 // This would fetch real data from database in a full implementation
 // For now, we'll keep the existing chart data
 console.log('Updating charts for child ID:', childId);
}

/* ===========================================
   EDIT AND DELETE CHILD FUNCTIONS
=========================================== */

// Edit Child Functions
function editChild(event, childId) {
 event.stopPropagation(); // Prevent triggering switchChild

 const childAvatar = event.target.closest('.child-avatar-container').querySelector('.child-avatar');
 const childData = {
   id: childAvatar.getAttribute('data-child-id'),
   name: childAvatar.getAttribute('data-child-name'),
   dob: childAvatar.getAttribute('data-child-dob'),
   gender: childAvatar.getAttribute('data-child-gender'),
   center: childAvatar.getAttribute('data-child-center'),
   image: childAvatar.getAttribute('data-child-image')
 };

 // Populate modal with current data
 document.getElementById('editChildId').value = childData.id;
 document.getElementById('editChildName').value = childData.name;
 document.getElementById('editChildDob').value = childData.dob;
 document.querySelector(`input[name="gender"][value="${childData.gender}"]`).checked = true;
 document.getElementById('editChildCenter').value = childData.center;
 document.getElementById('currentImageName').textContent = childData.image;

 // Show modal
 document.getElementById('editChildModal').style.display = 'block';
 document.body.style.overflow = 'hidden';
}

function closeEditModal() {
 document.getElementById('editChildModal').style.display = 'none';
 document.body.style.overflow = 'auto';
 document.getElementById('editChildForm').reset();
}

// Delete Child Functions
function deleteChild(event, childId, childName) {
 event.stopPropagation(); // Prevent triggering switchChild

 document.getElementById('deleteChildName').textContent = childName;
 document.getElementById('confirmDeleteBtn').onclick = () => confirmDelete(childId);

 // Show modal
 document.getElementById('deleteConfirmModal').style.display = 'block';
 document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
 document.getElementById('deleteConfirmModal').style.display = 'none';
 document.body.style.overflow = 'auto';
}

function confirmDelete(childId) {
 // Disable button to prevent multiple clicks
 const deleteBtn = document.getElementById('confirmDeleteBtn');
 deleteBtn.disabled = true;
 deleteBtn.textContent = 'Deleting...';

 // Send delete request
 const formData = new FormData();
 formData.append('child_id', childId);

 fetch('add_child/delete_child.php', {
   method: 'POST',
   body: formData
 })
 .then(response => response.json())
 .then(data => {
   if (data.success) {
     // Remove child from UI
     const childContainer = document.querySelector(`[data-child-id="${childId}"]`).closest('.child-avatar-container');
     childContainer.remove();

     // Check if this was the active child
     const wasActive = childContainer.querySelector('.child-avatar').classList.contains('active');

     if (wasActive) {
       // Select the first remaining child or show no children message
       const remainingChildren = document.querySelectorAll('.child-avatar');
       if (remainingChildren.length > 0) {
         switchChild(remainingChildren[0]);
       } else {
         // No children left
         document.getElementById('child-name').textContent = 'No children added';
         document.getElementById('child-age').textContent = '';
         document.getElementById('child-center').textContent = 'No center assigned';

         // Show placeholder
         const childSwitch = document.getElementById('child-switch');
         childSwitch.innerHTML = `
           <div style="background: #f0f0f0; padding: 20px; border-radius: 10px; text-align: center; color: #666;">
             No children registered yet. <a href="add_child/add_child.php" style="color: #ff9800;">Add your first child</a>
           </div>
           <a href="add_child/add_child.php" class="add-child">+</a>
         `;
       }
     }

     alert('Child profile deleted successfully!');
     closeDeleteModal();
   } else {
     throw new Error(data.message || 'Failed to delete child');
   }
 })
 .catch(error => {
   console.error('Delete error:', error);
   alert('Error deleting child: ' + error.message);
 })
 .finally(() => {
   // Re-enable button
   deleteBtn.disabled = false;
   deleteBtn.textContent = 'Delete';
 });
}

// Handle Edit Form Submission
document.getElementById('editChildForm').addEventListener('submit', function(e) {
 e.preventDefault();

 const submitBtn = this.querySelector('.save-btn');
 const originalText = submitBtn.textContent;
 submitBtn.disabled = true;
 submitBtn.textContent = 'Saving...';

 const formData = new FormData(this);

 fetch('add_child/update_child.php', {
   method: 'POST',
   body: formData
 })
 .then(response => response.json())
 .then(data => {
   if (data.success) {
     // Update the child avatar in UI
     const childId = data.data.child_id;
     const childAvatar = document.querySelector(`[data-child-id="${childId}"]`);

     if (childAvatar) {
       // Update all data attributes
       childAvatar.setAttribute('data-child-name', data.data.child_name);
       childAvatar.setAttribute('data-child-dob', data.data.dob);
       childAvatar.setAttribute('data-child-gender', data.data.gender);
       childAvatar.setAttribute('data-child-center', data.data.center);
       childAvatar.setAttribute('data-child-image', data.data.child_image);
       childAvatar.setAttribute('data-child-age', data.data.age_display);
       childAvatar.setAttribute('data-child-age-years', data.data.age_years_only);

       // Update image source if changed
       if (data.data.child_image !== childAvatar.getAttribute('data-child-image')) {
         childAvatar.src = `add_child/uploads/img/${data.data.child_image}`;
       }

       // Update alt text
       childAvatar.alt = data.data.child_name;

       // If this is the currently active child, update the profile info
       if (childAvatar.classList.contains('active')) {
         document.getElementById('child-name').textContent = data.data.child_name;
         document.getElementById('child-age').textContent = data.data.age_display;
         document.getElementById('child-center').textContent = data.data.center;

         // Update development progress
         updateDevelopmentProgress(data.data.age_years_only);
       }
     }

     alert('Child profile updated successfully!');
     closeEditModal();
   } else {
     throw new Error(data.message || 'Failed to update child');
   }
 })
 .catch(error => {
   console.error('Update error:', error);
   alert('Error updating child: ' + error.message);
 })
 .finally(() => {
   submitBtn.disabled = false;
   submitBtn.textContent = originalText;
 });
});

// Close modals when clicking outside
window.addEventListener('click', function(event) {
 const editModal = document.getElementById('editChildModal');
 const deleteModal = document.getElementById('deleteConfirmModal');

 if (event.target === editModal) {
   closeEditModal();
 }
 if (event.target === deleteModal) {
   closeDeleteModal();
 }
});
</script>
// Initialize progress cards with dynamic data
function initializeProgressCards() {
 const defaultAge = <?php echo $selected_child ? $selected_child['age_years_only'] : 2; ?>;
 updateDevelopmentProgress(defaultAge);
}

// Initialize on page load
initializeProgressCards();
// Company / backend thi score automatic aavse
// Ahiya example score data attribute mathi lese

document.querySelectorAll(".progress-card").forEach(card=>{

    let score = parseInt(card.getAttribute("data-score"));
    let circle = card.querySelector(".circle");
    let text = card.querySelector(".circle span");

    if(score >= 85){
        text.innerText = "Perfect";
        circle.style.borderTopColor = "#04c85a";
    }
    else if(score >= 70){
        text.innerText = "Excellent";
        circle.style.borderTopColor = "#00d0c4";
    }
    else if(score >= 50){
        text.innerText = "Good";
        circle.style.borderTopColor = "#ffb400";
    }
    else{
        text.innerText = "Developing";
        circle.style.borderTopColor = "#ff3b3b";
    }
});
</script>
<script>
    const tooltip = document.getElementById("tooltip");

function showTip(x,y,text){
  tooltip.innerText = text;
  tooltip.style.left = x + "px";
  tooltip.style.top = y - 35 + "px";
  tooltip.style.display = "block";
}

// Point hover & click
document.querySelectorAll(".points span").forEach(point=>{
  point.addEventListener("mouseenter",e=>{
    showTip(e.pageX, e.pageY, point.dataset.value);
  });

  point.addEventListener("click",e=>{
    showTip(e.pageX, e.pageY, point.dataset.value);
  });
});

// Line / graph ANYWHERE hover
document.querySelectorAll(".graph-area").forEach(graph=>{
  const points = [...graph.querySelectorAll(".points span")];

  graph.addEventListener("mousemove",e=>{
    const rect = graph.getBoundingClientRect();
    const percentX = ((e.clientX - rect.left) / rect.width) * 100;

    let nearest = points.reduce((a,b)=>{
      return Math.abs(b.dataset.x - percentX) <
             Math.abs(a.dataset.x - percentX) ? b : a;
    });

    showTip(e.pageX, e.pageY, nearest.dataset.value);
  });

  graph.addEventListener("mouseleave",()=>{
    tooltip.style.display = "none";
  });
});

</script>
</body>
</html>
