<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get all teams
function getAllTeams($pdo) {
  $stmt = $pdo->query("SELECT * FROM teams");
  return $stmt->fetchAll();
}

// Get teams managed by a specific admin
function getTeamsByAdmin($pdo, $admin_id) {
  $stmt = $pdo->prepare("SELECT * FROM teams WHERE admin_id = ?");
  $stmt->execute([$admin_id]);
  return $stmt->fetchAll();
}

// Get all users
function getAllUsers($pdo) {
  $stmt = $pdo->query("SELECT * FROM users");
  return $stmt->fetchAll();
}

// Get tasks for a member
function getTasksByMember($pdo, $member_id) {
  $stmt = $pdo->prepare("
    SELECT tasks.*, users.username AS created_by_username 
    FROM tasks 
    JOIN users ON tasks.created_by = users.id 
    WHERE assigned_to = ?
  ");
  $stmt->execute([$member_id]);
  return $stmt->fetchAll();
}

// Get tasks for admin's teams
function getTasksByAdmin($pdo, $admin_id) {
  $stmt = $pdo->prepare("
    SELECT tasks.*, 
           u1.username AS created_by_username, 
           u2.username AS assigned_to_username
    FROM tasks 
    JOIN users u1 ON tasks.created_by = u1.id 
    JOIN users u2 ON tasks.assigned_to = u2.id
    WHERE tasks.team_id IN (SELECT id FROM teams WHERE admin_id = ?)
  ");
  $stmt->execute([$admin_id]);
  return $stmt->fetchAll();
}


// Get all members (for Super Admin)
function getAllMembers($pdo) {
  $stmt = $pdo->query("SELECT * FROM users WHERE role = 'member'");
  return $stmt->fetchAll();
}

// Get members in a specific team
function getMembersByTeam($pdo, $team_id) {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE team_id = ?");
  $stmt->execute([$team_id]);
  return $stmt->fetchAll();
}

function getCompletedTasksByAdmin($pdo, $admin_id) {
  try {
    $stmt = $pdo->prepare("
      SELECT tasks.*, users.username AS created_by_username 
      FROM tasks 
      JOIN users ON tasks.created_by = users.id 
      WHERE tasks.team_id IN (SELECT id FROM teams WHERE admin_id = ?)
        AND tasks.status = 'Completed'
    ");
    $stmt->execute([$admin_id]);
    return $stmt->fetchAll();
  } catch (PDOException $e) {
    error_log("getCompletedTasksByAdmin error: " . $e->getMessage());
    return []; // Return an empty array on error
  }
}

function getCompletedTasks($pdo) {
  try {
    $stmt = $pdo->prepare("
      SELECT tasks.*, users.username AS created_by_username 
      FROM tasks 
      JOIN users ON tasks.created_by = users.id 
      WHERE tasks.status = 'Completed'
    ");
    $stmt->execute();
    return $stmt->fetchAll();
  } catch (PDOException $e) {
    error_log("getCompletedTasks error: " . $e->getMessage());
    return []; // Return an empty array if an error occurs
  }
}

function getAllTasks($pdo) {
  try {
    $stmt = $pdo->query("
      SELECT tasks.*, 
             u1.username AS created_by_username,
             u2.username AS assigned_to_username
      FROM tasks
      JOIN users u1 ON tasks.created_by = u1.id
      JOIN users u2 ON tasks.assigned_to = u2.id
    ");
    return $stmt->fetchAll();
  } catch (PDOException $e) {
    error_log("getAllTasks error: " . $e->getMessage());
    return []; // Return an empty array if an error occurs
  }
}

?>
