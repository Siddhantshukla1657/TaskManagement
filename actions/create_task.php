<?php
session_start();
include('../includes/db.php');
include('../includes/helpers.php');

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form values
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $deadline = $_POST['deadline'] ?? '';
    $assigned_to = $_POST['assigned_to'] ?? '';
    
    // For tasks created by admins, we assume the assigned member is in one of their teams.
    // Let's fetch the team_id of the assigned member (assuming a member belongs to one team).
    $stmt = $pdo->prepare("SELECT team_id FROM users WHERE id = ?");
    $stmt->execute([$assigned_to]);
    $memberData = $stmt->fetch();
    $team_id = $memberData ? $memberData['team_id'] : null;
    
    // The admin creating the task is the creator.
    $created_by = $_SESSION['user_id'];
    $status = 'Not yet started';  // Default status
    
    // Insert the new task into the tasks table.
    $stmt = $pdo->prepare("INSERT INTO tasks (title, description, deadline, assigned_to, created_by, team_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $deadline, $assigned_to, $created_by, $team_id, $status]);
    
    // Redirect back to the admin dashboard with a success flag.
    header("Location: ../admin_dashboard.php?task_created=1");
    exit;
}
?>
