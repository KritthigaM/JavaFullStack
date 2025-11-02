<?php
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user info from session
$name = $_SESSION['user_name'];
$role = $_SESSION['user_role'];

// Redirect based on role
switch ($role) {
    case 'student':
        $dashboard_page = 'student-dashboard.php';
        break;
    case 'recruiter':
        $dashboard_page = 'recruiter-dashboard.php';
        break;
    case 'admin':
        header("Location: admin-panel.php");
        exit;
    default:
        echo "Unknown role!";
        exit;
}
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo ucfirst($role); ?> Dashboard</title>
  <style>
    body {
      font-family: Arial;
      padding: 20px;
      background-color: #f5f5f5ff;
      margin: 0;
    }
    .welcome-box {
      background-color: #e0f7fa;
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    h2 {
      margin: 0;
    }
    .links {
      margin-top: 10px;
    }
    .links a {
      text-decoration: none;
      color: #007bff;
      margin-right: 15px;
      font-weight: bold;
    }
    .links a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<!-- Welcome message only once -->
<div class="welcome-box">
  <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
  <p>Your role: <?php echo htmlspecialchars($role); ?></p>
  <div class="links">
    <a href="profile.php">My Profile</a> 
    <a href="logout.php">Logout</a>
  </div>
</div>

<?php
// Include the correct dashboard (without duplicate profile/logout links)
if (file_exists($dashboard_page)) {
    include($dashboard_page);
} else {
    echo "<p>Dashboard page not found!</p>";
}
?>

</body>
</html>
