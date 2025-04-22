<?php
session_start();
include('includes/db.php');

// If the user is already logged in, redirect them based on their role
if (isset($_SESSION['user_id'])) {
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
            session_destroy();
            header('Location: index.php');
    }
    exit;
}

// Process POST request (the login form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                header('Location: index.php');
                break;
        }
        exit;
    } else {
        // Set an error message to display later
        $error = "Invalid credentials! Please try again.";
    }
}
?>
<?php include('includes/header.php'); ?>

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
    max-width: 400px;
    margin: 50px auto;
    padding: 20px;
  }
  
  .card {
    background: #1e1e1e;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
  }
  
  h2 {
    color: #bb86fc;
    margin-bottom: 20px;
  }
  
  input {
    background: #333;
    color: #e0e0e0;
    border: 1px solid #555;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 10px;
    width: 100%;
  }
  
  button {
    background: #bb86fc;
    color: #121212;
    border: none;
    border-radius: 4px;
    padding: 10px;
    width: 100%;
    cursor: pointer;
    transition: background 0.3s ease;
  }
  
  button:hover {
    background: #9b69d2;
  }
  
  .error-msg {
    background: #b00020;
    color: #fff;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 20px;
  }
</style>

<div class="dashboard">
  <div class="card">
    <h2>Login</h2>
    <?php if (isset($error)): ?>
      <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>
    <form action="dashboard.php" method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</div>

<?php include('includes/footer.php'); ?>
