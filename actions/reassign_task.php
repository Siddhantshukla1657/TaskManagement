<?php
session_start();
include('../includes/db.php');
include('../includes/helpers.php');

// Verify the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'] ?? '';
    $new_member = $_POST['new_member'] ?? '';

    // Check if required data is provided
    if (empty($task_id) || empty($new_member)) {
        header("Location: ../admin_dashboard.php?task_reassign_error=missing_data");
        exit;
    }

    // Update the task's assigned member
    $stmt = $pdo->prepare("UPDATE tasks SET assigned_to = ? WHERE id = ?");
    if ($stmt->execute([$new_member, $task_id])) {
        $rowCount = $stmt->rowCount();
        // Check if any row was updated
        if ($rowCount > 0) {
            header("Location: ../admin_dashboard.php?task_reassigned=1");
            exit;
        } else {
            // Possibly the new member is already assigned or task_id is invalid
            header("Location: ../admin_dashboard.php?task_reassign_error=no_update");
            exit;
        }
    } else {
        // Log error info if query fails
        $errorInfo = $stmt->errorInfo();
        error_log("Reassign Task Error: " . $errorInfo[2]);
        header("Location: ../admin_dashboard.php?task_reassign_error=query_fail");
        exit;
    }
} else {
    // If not a POST request, simply redirect back to the dashboard
    header("Location: ../admin_dashboard.php");
    exit;
}
?>
