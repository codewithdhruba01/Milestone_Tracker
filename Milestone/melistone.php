<?php
  // Include database connection
  include '../config/db.php';


  // Get child ID from URL parameter or default to first child
  $selected_child_id = isset($_GET['child_id']) ? (int)$_GET['child_id'] : null;

  // Fetch all children
  $children = [];
  $sql = "SELECT child_id, child_name, dob FROM children ORDER BY created_at DESC";
  $result = $conn->query($sql);

  if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $children[] = $row;
    }
  }

  // If no child selected, use the first one
  if (!$selected_child_id && !empty($children)) {
    $selected_child_id = $children[0]['child_id'];
  }

  // Fetch milestone data for selected child
  $milestones = [];
  $total_completed = 0;
  $total_milestones = 0;

  if ($selected_child_id) {
    $sql = "SELECT domain, question, answer FROM child_milestones WHERE child_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $selected_child_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
      $milestones[] = $row;
      $total_milestones++;
      if ($row['answer'] === 'yes') {
        $total_completed++;
      }
    }
    $stmt->close();
  }

  // Calculate completion percentage
  $completion_percentage = $total_milestones > 0 ? round(($total_completed / $total_milestones) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Completion Status & Tasks - <?php echo $selected_child_id ? 'Child Milestones' : 'Milestone Tracker'; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../Css/style.css">
<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family:'Poppins',sans-serif;
}

body{
  background:#fff7da;
  overflow-x:hidden;
}

/* WRAPPER */
.wrapper{
  max-width:1100px;
  margin:auto;
  padding:30px 15px;
}

/* ================= CHILD SELECTOR ================= */
.child-selector{
  background:#fff;
  border-radius:15px;
  padding:20px;
  margin-bottom:30px;
  box-shadow:0 5px 15px rgba(0,0,0,.1);
}

.child-selector h3{
  margin-bottom:15px;
  color:#333;
}

.child-buttons{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}

.child-btn{
  background:#ff9800;
  color:#fff;
  border:none;
  padding:10px 20px;
  border-radius:25px;
  cursor:pointer;
  transition:0.3s;
}

.child-btn:hover,
.child-btn.active{
  background:#e68900;
  transform:translateY(-2px);
}

/* ================= COMPLETION STATUS ================= */
.completion{
  background:linear-gradient(135deg,#f6a623,#f8d350);
  border-radius:20px;
  padding:25px;
  box-shadow:0 10px 25px rgba(0,0,0,.15);
}

.completion h2{
  text-align:center;
  color:#fff;
  margin-bottom:20px;
}

.status-row{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:20px;
  flex-wrap:wrap;
}

.status-card{
  background:#fff;
  border-radius:14px;
  padding:18px;
  width:200px;
  text-align:center;
}

.status-card p{
  color:#ff7a00;
  font-size:14px;
}

.status-card h3{
  color:#ff7a00;
  font-size:32px;
}

.circle{
  width:120px;
  height:120px;
  background:#f3c623;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
  font-size:26px;
  font-weight:700;
}

/* ================= TASK SECTIONS ================= */
.tasks{
  margin-top:35px;
  background:#fffbe6;
  border-radius:20px;
  padding:25px;
  box-shadow:0 8px 20px rgba(0,0,0,.12);
}

.tasks.past-tasks{
  background:transparent;
  box-shadow:none;
  padding:10px 0;
}

/* ================= TASK CARD (SAME FOR ALL) ================= */
.task{
  background:#fff;
  border:2px solid #f6c14b;
  border-radius:16px;
  padding:16px;
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:15px;
  margin-bottom:14px;
  opacity:1;
}

/* completed */
.task.completed p{
  text-decoration:line-through;
  color:#888;
}

/* failed (LOOK SAME) */
.task.failed{
  border:2px solid #f6c14b;
  opacity:1;
}

/* ================= TASK INFO ================= */
.task-info p{
  font-size:15px;
  line-height:1.4;
}

/* TAGS */
.tag{
  display:inline-block;
  margin-top:6px;
  padding:4px 12px;
  border-radius:18px;
  font-size:12px;
  font-weight:500;
}

.language{background:#ffe0a3;}
.motor{background:#d4fff4;}
.cognitive{background:#ffb7b7;}
.social{background:#ccffb8;}

/* ================= CHECK ================= */
.check{
  width:22px;
  height:22px;
  border:2px solid #f6a623;
  border-radius:6px;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
}

.check.active{
  background:#f6a623;
  color:#fff;
}

/* disable failed cross */
.check.cross{
  background:#ff6b6b;
  color:#fff;
  border-color:#ff6b6b;
  pointer-events:none;
}

/* ================= MILESTONE ================= */
.milestone{
  background:#ffc857;
  border-radius:16px;
  padding:18px;
  margin-top:20px;
}

.milestone-title{
  font-weight:600;
  margin-bottom:10px;
}

.milestone-box{
  background:#fff;
  border-radius:14px;
  padding:14px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:12px;
}

/* UPLOAD */
.upload{
  width:48px;
  height:48px;
  background:#fff;
  border:2px solid #f6a623;
  border-radius:12px;
  cursor:pointer;
  display:flex;
  align-items:center;
  justify-content:center;
  position:relative;
}

.upload input{
  position:absolute;
  inset:0;
  opacity:0;
}

/* VIDEO BADGE */
.video-badge{
  background:#4caf50;
  color:#fff;
  padding:6px 10px;
  border-radius:10px;
  font-size:12px;
  white-space:nowrap;
}

/* DATE */
.date-title{
  text-align:center;
  font-weight:600;
  margin:20px 0 15px;
  color:#333;
}

/* ================= RESPONSIVE ================= */
@media(max-width:768px){
  .status-row{
    justify-content:center;
  }

  .status-card{
    width:100%;
  }

  .task{
    flex-direction:column;
  }

  .check{
    align-self:flex-end;
  }

  .milestone-box{
    flex-direction:column;
    align-items:flex-start;
  }

  .child-buttons{
    justify-content:center;
  }
}
</style>
</head>

<body>

<div class="wrapper">

<!-- CHILD SELECTOR -->
<?php if (!empty($children)): ?>
<div class="child-selector">
  <h3>Select Child</h3>
  <div class="child-buttons">
    <?php foreach ($children as $child): ?>
      <button class="child-btn <?php echo ($selected_child_id == $child['child_id']) ? 'active' : ''; ?>"
              onclick="selectChild(<?php echo $child['child_id']; ?>)">
        <?php echo htmlspecialchars($child['child_name']); ?>
      </button>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- COMPLETION STATUS -->
<div class="completion">
  <h2>Completion Status</h2>
  <div class="status-row">
    <div class="status-card">
      <p>Total Milestones</p>
      <h3><?php echo $total_milestones; ?></h3>
    </div>

    <div class="circle"><?php echo $total_completed; ?>/<?php echo $total_milestones; ?></div>

    <div class="status-card">
      <p>Completed Tasks</p>
      <h3><?php echo $total_completed; ?></h3>
    </div>
  </div>
</div>

<!-- MILESTONES -->
<div class="tasks">
  <h2>Milestone Progress</h2>

  <?php if (!empty($milestones)): ?>
    <?php foreach ($milestones as $milestone): ?>
      <div class="task <?php echo ($milestone['answer'] === 'yes') ? 'completed' : ''; ?>">
        <div class="task-info">
          <p><?php echo htmlspecialchars($milestone['question']); ?></p>
          <span class="tag <?php echo strtolower($milestone['domain']); ?>">
            <?php echo htmlspecialchars($milestone['domain']); ?>
          </span>
        </div>
        <div class="check <?php echo ($milestone['answer'] === 'yes') ? 'active' : ''; ?>">
          <?php echo ($milestone['answer'] === 'yes') ? '✓' : ''; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="task">
      <div class="task-info">
        <p>No milestones found for this child. Please add milestone data first.</p>
      </div>
    </div>
  <?php endif; ?>

  <!-- SAMPLE MILESTONE TASK -->
  <div class="milestone">
    <div class="milestone-title">🏆 Milestone Task</div>
    <div class="milestone-box">
      <div>
        <p>Complete a milestone task - Record progress video</p>
        <span class="tag cognitive">Cognitive</span>
      </div>
      <label class="upload">
        <span class="icon">⬆</span>
        <input type="file" accept="video/*">
      </label>
    </div>
  </div>
</div>

<!-- PAST TASKS SAMPLE -->
<div class="tasks past-tasks" style="margin-top:20px">
  <h3 class="date-title"><?php echo date('d M Y', strtotime('-1 day')); ?></h3>

  <div class="task completed">
    <div class="task-info">
      <p>Sample completed task from yesterday</p>
      <span class="tag language">Language</span>
    </div>
    <div class="check active">✓</div>
  </div>

  <!-- MILESTONE -->
  <div class="milestone">
    <div class="milestone-title">🏆 Milestone Task</div>
    <div class="milestone-box">
      <div class="task-info">
        <p>Sample milestone from yesterday</p>
        <span class="tag motor">Motor</span>
      </div>
      <span class="video-badge">Video Verified</span>
    </div>
  </div>
</div>

</div>

<script>
// Child selection functionality
function selectChild(childId) {
  window.location.href = 'melistone.php?child_id=' + childId;
}

/* ===== TASK CHECK / UNCHECK + PROGRESS ===== */
const checks = document.querySelectorAll('.check');
const progressText = document.querySelector('.circle');
const totalTasksBox = document.querySelector('.status-card:last-child h3');

const totalTasks = document.querySelectorAll('.task').length;
if (totalTasksBox) {
  totalTasksBox.innerText = totalTasks;
}

function updateProgress(){
  let done = 0;

  checks.forEach(check => {
    const task = check.closest('.task');

    if(check.classList.contains('active')){
      done++;
      task.classList.add('completed');
      check.innerHTML = '✓';
    }else{
      task.classList.remove('completed');
      check.innerHTML = '';
    }
  });

  if (progressText) {
    progressText.innerText = done + '/' + totalTasks;
  }
}

checks.forEach(check => {
  check.addEventListener('click', () => {
    // ❌ failed / cross task disable
    if(check.classList.contains('cross')) return;

    check.classList.toggle('active');
    updateProgress();
  });
});

updateProgress();

/* ===== UPLOAD CLICK FIX ===== */
const uploadBtn = document.querySelector('.upload');
if(uploadBtn){
  uploadBtn.addEventListener('click', () => {
    const fileInput = uploadBtn.querySelector('input[type="file"]');
    if(fileInput) fileInput.click();
  });
}
</script>

</body>
</html>
<?php $conn->close(); ?>