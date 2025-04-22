<?php
session_start();
include('../includes/db.php');

if ($_SESSION['role'] !== 'admin'||) {
  header('Location: ../index.php');
  exit;
}

$team_name = $_POST['team_name'];
$admin_id = $_SESSION['user_id'];

// Create new team
$stmt = $pdo->prepare("INSERT INTO teams (name, admin_id) VALUES (?, ?)");
$stmt->execute([$team_name, $admin_id]);

header("Location: ../admin_dashboard.php?team_created=1");
?>