<?php
session_start();
include('includes/db.php');

// If request is GET and user is logged in, decide which dashboard to send them to:
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  if (isset($_SESSION['user_id'])) {
    // Already logged in, redirect by role
    switch ($_SESSION['role']) {
      case 'admin':
        header('Location: admin_dashboard.php');
        break;
      case 'super_admin':
        header('Location: super_admin_dashboard.php');
        break;
      case 'member':
        header('Location: member_dashboard.php');
        break;
      default:
        // If role is unknown, log out
        session_destroy();
        header('Location: index.php');
    }
  } else {
    // Not logged in, go to index
    header('Location: index.php');
  }
  exit;
}

// Process POST request (the login form submission)
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Check DB for matching user
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Basic password check (use password_verify() in production)
if ($user && $user['password'] === $password) {
  $_SESSION['user_id'] = $user['id'];
  $_SESSION['role'] = $user['role'];
  
  // Redirect by role
  switch ($user['role']) {
    case 'admin':
      header('Location: admin_dashboard.php');
      break;
    case 'super_admin':
      header('Location: super_admin_dashboard.php');
      break;
    case 'member':
      header('Location: member_dashboard.php');
      break;
    default:
      // Unknown role, go back to index
      header('Location: index.php');
      break;
  }
  exit;
} else {
  // Invalid credentials
  include('includes/header.php');
  ?>
  <!-- Dark Theme Styles -->
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
    
    .card {
      background: #1e1e1e;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    }
    
    a {
      color: #bb86fc;
      text-decoration: none;
    }
    
    a:hover {
      text-decoration: underline;
    }
  </style>
  
  <div class="dashboard">
    <div class="card" style="max-width: 400px; margin: 50px auto;">
      <h2>Login Error</h2>
      <p>Invalid credentials! Please try again.</p>
      <a href="index.php">Back to Login</a>
    </div>
  </div>
  <?php
  include('includes/footer.php');
  exit;
}
?>
