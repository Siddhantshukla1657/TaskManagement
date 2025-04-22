<?php
session_start();
include('../includes/db.php');

// if ($_SESSION['role'] !== 'admin') {
//   header('Location: ../index.php');
//   exit;
// }
if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'super_admin') {
  $task_id = $_POST['task_id'];

// Delete task
$stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
}

$task_id = $_POST['task_id'];

// Delete task
$stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);

header("Location: ../admin_dashboard.php?task_deleted=1");
?>