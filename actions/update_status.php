<?php
session_start();
include('../includes/db.php');

if ($_SESSION['role'] !== 'member') {
  header('Location: ../index.php');
  exit;
}

$task_id = $_POST['task_id'];
$status = $_POST['status'];

// If the status is updated to 'completed', set the 'completed_on' field to the current timestamp.
if ($status === 'completed') {
    $completedTime = date("Y-m-d H:i:s");
    $stmt = $pdo->prepare("UPDATE tasks SET status = ?, completed_on = ? WHERE id = ?");
    $stmt->execute([$status, $completedTime, $task_id]);
} else {
    // Optionally, you can clear the completed_on field if the status is not 'completed'
    // $stmt = $pdo->prepare("UPDATE tasks SET status = ?, completed_on = NULL WHERE id = ?");
    // Or simply update the status field only:
    $stmt = $pdo->prepare("UPDATE tasks SET status = ? WHERE id = ?");
    $stmt->execute([$status, $task_id]);
}

header("Location: ../member_dashboard.php?status_updated=1");
exit;
?>
