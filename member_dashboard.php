<?php
session_start();
include('includes/db.php');
include('includes/helpers.php');

// Verify the user is a member
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
  header('Location: index.php');
  exit;
}

// Fetch tasks for the member
$tasks = getTasksByMember($pdo, $_SESSION['user_id']);
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
  
  .task-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
  }
  
  .task-item {
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
  .status-issue { color: rgb(255, 0, 0); }
  
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
  <h1><span class="accent">Member</span> Dashboard</h1>
  
  <?php if (isset($_GET['status_updated'])): ?>
    <div class="success-msg">✓ Status updated successfully!</div>
  <?php endif; ?>
  
  <!-- Task List Card -->
  <div class="card">
    <h2>Your Tasks</h2>
    <div class="task-grid">
      <?php foreach ($tasks as $task): ?>
        <div class="task-item">
          <h3><?= htmlspecialchars($task['title']); ?></h3>
          <p><?= htmlspecialchars($task['description']); ?></p>
          <div class="task-meta">
            <span>Deadline: <?= date('M j, Y H:i', strtotime($task['deadline'])); ?></span>
            <span class="status-<?= str_replace(' ', '-', strtolower($task['status'])); ?>">
              <?= htmlspecialchars($task['status']); ?>
            </span>
          </div>
          <div class="task-meta">
            <span>Assigned by: <?= htmlspecialchars($task['created_by_username']); ?></span>
          </div>
          <!-- Status Update Form -->
          <form action="actions/update_status.php" method="post">
            <select name="status" class="status-select" required>
              <option value="Not yet started" <?= $task['status'] == 'Not yet started' ? 'selected' : '' ?>>Not yet started</option>
              <option value="Started" <?= $task['status'] == 'Started' ? 'selected' : '' ?>>Started</option>
              <option value="Midway" <?= $task['status'] == 'Midway' ? 'selected' : '' ?>>Midway</option>
              <!-- Note: Update_status.php checks for "completed" in lowercase -->
              <option value="completed" <?= strtolower($task['status']) == 'completed' ? 'selected' : '' ?>>Completed</option>
              <option value="Issue" <?= $task['status'] == 'Issue' ? 'selected' : '' ?>>Issue</option>
            </select>
            <input type="hidden" name="task_id" value="<?= $task['id']; ?>">
            <button type="submit" class="btn-update">Update Status</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
