<?php
session_start();
include('../includes/db.php');

if ($_SESSION['role'] !== 'super_admin') {
  header('Location: ../index.php');
  exit;
}

$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];
$team_id = $_POST['team_id'] ?? null; // Optional for members/admins

// Insert user
$stmt = $pdo->prepare("INSERT INTO users (username, password, role, team_id) VALUES (?, ?, ?, ?)");
$stmt->execute([$username, $password, $role, $team_id]);

header("Location: ../super_admin_dashboard.php?user_created=1");
?>