<?php
session_start();
include('includes/db.php');
include('includes/helpers.php');

// Verify the user is a super_admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: index.php');
    exit;
}

// Fetch data and use null coalescing to ensure arrays
$teams = getAllTeams($pdo) ?? [];
$all_tasks = getAllTasks($pdo) ?? [];
$completedTasks = getCompletedTasks($pdo) ?? [];
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
  .task-meta p {
    margin: 5px 0;
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
</style>

<div class="dashboard">
  <h1><span class="accent">Super Admin</span> Dashboard</h1>
  
  <!-- User Creation Card -->
  <div class="card">
    <h2>Create New User</h2>
    <form action="actions/create_user.php" method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      
      <select name="role" id="roleSelect" required>
        <option value="">Select Role</option>
        <option value="admin">Admin</option>
        <option value="member">Member</option>
        <option value="super_Admin">Super Admin</option>
      </select>
      
      <!-- Team selection dropdown (only applicable for members) -->
      <select name="team_id" id="teamSelect" style="display: none;">
        <option value="">Select Team</option>
        <?php foreach ($teams as $team): ?>
          <option value="<?= $team['id'] ?>"><?= $team['name'] ?></option>
        <?php endforeach; ?>
      </select>
      
      <button type="submit">Create User</button>
    </form>
  </div>
  
  <!-- Team Management Card -->
  <div class="card">
    <h2>All Teams</h2>
    <div class="team-grid">
      <?php foreach ($teams as $team): ?>
        <div class="team-item">
          <h3><?= htmlspecialchars($team['name']) ?></h3>
          <p>Managed by Admin ID: <?= $team['admin_id'] ?></p>
          <!-- View Members Button -->
          <button class="toggle-members" data-team-id="<?= $team['id'] ?>">View Members</button>
          <!-- Hidden team members list -->
          <div class="team-members" id="team-members-<?= $team['id'] ?>" style="display: none; margin-top: 10px;">
            <ul>
              <?php $members = getMembersByTeam($pdo, $team['id']); ?>
              <?php foreach ($members as $member): ?>
                <li><?= htmlspecialchars($member['username']) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <form action="actions/delete_team.php" method="post">
            <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
            <button type="submit" class="btn-delete">Delete Team</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  
  <!-- Task Creation Card -->
  <div class="card">
    <h2>Create Task (Any Team)</h2>
    <form action="actions/create_task.php" method="post">
      <input type="text" name="title" placeholder="Task Title" required>
      <textarea name="description" placeholder="Task Description"></textarea>
      <input type="datetime-local" name="deadline" required>
      
      <!-- Choose the team to assign the task to -->
      <select name="team_id" required>
        <?php foreach ($teams as $team): ?>
          <option value="<?= $team['id'] ?>"><?= $team['name'] ?></option>
        <?php endforeach; ?>
      </select>
      
      <!-- Choose which member to assign it to -->
      <select name="assigned_to" required>
        <?php foreach (getAllMembers($pdo) as $member): ?>
          <option value="<?= $member['id'] ?>"><?= $member['username'] ?></option>
        <?php endforeach; ?>
      </select>
      
      <button type="submit">Create Task</button>
    </form>
  </div>
  
  <!-- All Tasks Card -->
  <div class="card">
    <h2>All Tasks</h2>
    <div class="task-grid">
      <?php foreach ($all_tasks as $task): ?>
        <div class="task-item">
          <h3><?= $task['title'] ?></h3>
          <p><?= $task['description'] ?></p>
          <div class="task-meta">
            <p><strong>Created by:</strong> <?= $task['created_by_username'] ?></p>
            <p><strong>Assigned to:</strong> <?= $task['assigned_to_username'] ?></p>
            <p><strong>Deadline:</strong> <?= date('M j, Y H:i', strtotime($task['deadline'])) ?></p>
            <p><strong>Status:</strong> <span class="status-<?= str_replace(' ', '-', strtolower($task['status'])) ?>"><?= $task['status'] ?></span></p>
          </div>
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
  
  <!-- User Report Card -->
  <div class="card">
    <h2>User Task Report</h2>
    <a href="report.php"><button type="button">View User Report</button></a>
  </div>
</div>

<!-- JavaScript to show/hide team selection based on role -->
<script>
  document.getElementById('roleSelect').addEventListener('change', function() {
    var role = this.value;
    var teamSelect = document.getElementById('teamSelect');
    if (role === 'member') {
      teamSelect.style.display = 'block';
      teamSelect.setAttribute('required', 'required');
    } else {
      teamSelect.style.display = 'none';
      teamSelect.removeAttribute('required');
      teamSelect.value = '';
    }
  });

  // JavaScript to toggle team members display
  document.addEventListener('DOMContentLoaded', function() {
    var toggleButtons = document.querySelectorAll('.toggle-members');
    toggleButtons.forEach(function(button) {
      button.addEventListener('click', function() {
        var teamId = button.getAttribute('data-team-id');
        var memberDiv = document.getElementById('team-members-' + teamId);
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
