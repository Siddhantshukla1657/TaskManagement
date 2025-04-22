<?php
session_start();
include('includes/db.php');
include('includes/helpers.php');

// Verify the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: index.php');
  exit;
}

// Fetch tasks for admin dashboard
$teams = getTeamsByAdmin($pdo, $_SESSION['user_id']);
$tasks = getTasksByAdmin($pdo, $_SESSION['user_id']);

// Fetch completed tasks using the new helper function
$completedTasks = getCompletedTasksByAdmin($pdo, $_SESSION['user_id']);
?>
<?php include('includes/header.php'); ?>

<!-- Dark Theme Styles -->
<style>
  body {
    background-color: #121212;
    color: #e0e0e0;
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
  }
  
  .dashboard {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
  }
  
  .accent {
    color: #bb86fc;
  }
  
  .card {
    background: #1e1e1e;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
  }
  
  .team-grid, .task-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
  }
  .task-meta p {
    margin: 5px 0;
  }

  .team-item, .task-item {
    background: #1e1e1e;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3);
  }
  
  .task-meta {
    font-size: 0.9em;
    margin: 5px 0;
  }
  
  /* Status color classes */
  .status-not-yet-started { color: #ffab00; }
  .status-started { color: #00b0ff; }
  .status-midway { color: #8bc34a; }
  .status-completed { color: #388e3c; }
  
  input, textarea, select {
    background: #333;
    color: #e0e0e0;
    border: 1px solid #555;
    border-radius: 4px;
    padding: 10px;
    width: 100%;
    margin-bottom: 10px;
  }
  
  button {
    background: #bb86fc;
    color: #121212;
    border: none;
    border-radius: 4px;
    padding: 10px;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  
  button:hover {
    background: #9b69d2;
  }
  
  .success-msg {
    background: #388e3c;
    color: #fff;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 20px;
  }
</style>

<div class="dashboard">
  <h1><span class="accent">Admin</span> Dashboard</h1>
  
  <?php if (isset($_GET['task_created'])): ?>
    <div class="success-msg">✓ Task created successfully!</div>
  <?php endif; ?>
  
  <!-- Team Creation Card -->
  <div class="card">
    <h2>Create New Team</h2>
    <form action="actions/create_team.php" method="post">
      <input type="text" name="team_name" placeholder="Team Name" required>
      <button type="submit">Create Team</button>
    </form>
  </div>
  
  <!-- Task Creation Card -->
  <div class="card">
    <h2>Create New Task</h2>
    <form action="actions/create_task.php" method="post">
      <input type="text" name="title" placeholder="Task Title" required>
      <textarea name="description" placeholder="Task Description"></textarea>
      <input type="datetime-local" name="deadline" required>
      <label>Assign to Member (from your teams):</label>
      <select name="assigned_to" required>
        <?php foreach ($teams as $team): ?>
          <?php $members = getMembersByTeam($pdo, $team['id']); ?>
          <?php if (!empty($members)): ?>
            <optgroup label="<?= htmlspecialchars($team['name']) ?>">
              <?php foreach ($members as $member): ?>
                <option value="<?= $member['id'] ?>"><?= htmlspecialchars($member['username']) ?></option>
              <?php endforeach; ?>
            </optgroup>
          <?php endif; ?>
        <?php endforeach; ?>
      </select>
      <button type="submit">Create Task</button>
    </form>
  </div>
  <!-- Team List with Members Toggle -->
<div class="card">
  <h2>Your Teams</h2>
  <div class="team-grid">
    <?php foreach ($teams as $team): ?>
      <div class="team-item">
        <h3><?= htmlspecialchars($team['name']) ?></h3>
        <button class="toggle-members" data-team-id="<?= $team['id'] ?>">View Members</button>
        <div class="team-members" id="team-members-<?= $team['id'] ?>" style="display:none; margin-top:10px;">
          <ul>
            <?php $members = getMembersByTeam($pdo, $team['id']); ?>
            <?php foreach ($members as $member): ?>
              <li><?= htmlspecialchars($member['username']) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

  <!-- Task List Card -->
  <div class="card">
    <h2>Manage Tasks</h2>
    <div class="task-grid">
      <?php foreach ($tasks as $task): ?>
        <div class="task-item">
          <h3><?= $task['title'] ?></h3>
          <p><?= $task['description'] ?></p>
          <div class="task-meta">
            <span>Deadline: <?= date('M j, Y H:i', strtotime($task['deadline'])) ?></span>
            <span>Status: <?= $task['status'] ?></span>
          </div>
          <div class="task-meta">
            <p><strong>Created by:</strong> <?= $task['created_by_username'] ?></p>
            <p><strong>Assigned to:</strong> <?= $task['assigned_to_username'] ?></p>
            <p><strong>Deadline:</strong> <?= date('M j, Y H:i', strtotime($task['deadline'])) ?></p>
            <p>
              <strong>Status:</strong>
              <span class="status-<?= str_replace(' ', '-', strtolower($task['status'])) ?>">
                <?= $task['status'] ?>
              </span>
            </p>
          </div>


          <!-- Reassignment Form -->
          <form action="actions/reassign_task.php" method="post">
            <select name="new_member">
              <?php foreach (getMembersByTeam($pdo, $task['team_id']) as $member): ?>
                <option value="<?= $member['id'] ?>"><?= $member['username'] ?></option>
              <?php endforeach; ?>
            </select>
            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
            <button type="submit" class="btn-reassign">Reassign</button>
          </form>
          <!-- Delete Task Form -->
          <form action="actions/delete_task.php" method="post">
            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
            <button type="submit" class="btn-delete">Delete Task</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  
  <!-- Completed Tasks Card -->
  <div class="card">
    <h2>Completed Tasks</h2>
    <?php if (!empty($completedTasks)): ?>
      <ul>
        <?php foreach ($completedTasks as $task): ?>
          <li>
            <?= $task['title'] ?> — Completed on <?= date('M j, Y H:i', strtotime($task['deadline'])) ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>No completed tasks yet.</p>
    <?php endif; ?>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var toggleButtons = document.querySelectorAll('.toggle-members');
    
    toggleButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        var teamId = button.getAttribute('data-team-id');
        var memberDiv = document.getElementById('team-members-' + teamId);
        
        // Toggle display state
        if (memberDiv.style.display === 'none' || memberDiv.style.display === '') {
          memberDiv.style.display = 'block';
          button.textContent = 'Hide Members';
        } else {
          memberDiv.style.display = 'none';
          button.textContent = 'View Members';
        }
      });
    });
  });
</script>

<?php include('includes/footer.php'); ?>
