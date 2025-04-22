<?php
session_start();
include('../includes/db.php');

$team_id = $_POST['team_id'];

if ($_SESSION['role'] === 'admin') {
  // Admins can only delete their own teams
  $stmt = $pdo->prepare("DELETE FROM teams WHERE id = ? AND admin_id = ?");
  $stmt->execute([$team_id, $_SESSION['user_id']]);
} elseif ($_SESSION['role'] === 'super_admin') {
  // Super Admin can delete any team
  $stmt = $pdo->prepare("DELETE FROM teams WHERE id = ?");
  $stmt->execute([$team_id]);
} else {
  header('Location: ../index.php');
  exit;
}

header("Location: ../super_admin_dashboard.php?team_deleted=1");
?>