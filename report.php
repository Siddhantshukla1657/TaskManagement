<?php
session_start();
include('includes/db.php');
include('includes/helpers.php');

// Verify the user is a super_admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    header('Location: index.php');
    exit;
}

$tasks = [];
$username = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    if (!empty($username)) {
        // Query tasks joined with the users table to get both assigned user and created by username.
        $sql = "SELECT t.*, ua.username AS created_by_username, u.username AS assigned_to_username 
                FROM tasks t
                JOIN users u ON t.assigned_to = u.id
                JOIN users ua ON t.created_by = ua.id
                WHERE u.username = :username AND t.status = 'completed'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<?php include('includes/header.php'); ?>

<!-- Dark Theme Styles (matching the super admin dashboard) -->
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
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #333;
  }
</style>

<div class="dashboard">
    <h1><span class="accent">Super Admin</span> Report: Completed Tasks by User</h1>
    
    <!-- User Report Form -->
    <div class="card">
        <form method="post" action="report.php">
            <input type="text" name="username" placeholder="Enter username" value="<?= htmlspecialchars($username) ?>" required>
            <button type="submit">Get Report</button>
        </form>
    </div>
    
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="card">
            <h2>Report for <?= htmlspecialchars($username); ?></h2>
            <?php if (count($tasks) > 0): ?>
                <table>
                    <tr>
                        <th>Task</th>
                        <th>Assigned By</th>
                        <th>Assigned Date</th>
                        <th>Completed On</th>
                        <th>Deadline</th>
                    </tr>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= htmlspecialchars($task['title']); ?></td>
                            <td><?= htmlspecialchars($task['created_by_username']); ?></td>
                            <td><?= date('M j, Y H:i', strtotime($task['created_at'])); ?></td>
                            <td><?= date('M j, Y H:i', strtotime($task['completed_on'])); ?></td>
                            <td><?= date('M j, Y H:i', strtotime($task['deadline'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No completed tasks found for this user.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>
