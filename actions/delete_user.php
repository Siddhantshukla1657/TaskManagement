<?php
session_start();
include('../includes/db.php');

if ($_SESSION['role'] !== 'super_admin') {
  header('Location: ../index.php');
  exit;
}

$user_id = $_POST['user_id'];

// Delete user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

header("Location: ../super_admin_dashboard.php?user_deleted=1");
?>