<?php
// register.php
session_start();
require 'db_connect.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'student';
    // only allow student or recruiter (admin not from form)
    if (!in_array($role, ['student','recruiter'])) {
        $role = 'student';
    }

    // Validation
    if ($name === '') { $errors[] = "Please enter name."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Please enter a valid email."; }
    if (strlen($password) < 6) { $errors[] = "Password must be at least 6 characters."; }

    // Check duplicate email
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "This email is already registered. Try logging in.";
            }
            $stmt->close();
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }

    // Insert user
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?,?,?,?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $hashed, $role);
            if ($stmt->execute()) {
                $success = "Registration successful! You can <a href='login.php'>login here</a>.";
            } else {
                $errors[] = "Could not save. Please try again.";
            }
            $stmt->close();
        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title></head>
<body>
  <h2>Registration</h2>

  <?php if (!empty($errors)): ?>
    <div style="color:red;">
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?php echo htmlspecialchars($e); ?></li>
        <?php endforeach; ?>
      </ul>
      <p><a href="signup.html">Back to Sign Up</a></p>
    </div>
  <?php elseif ($success): ?>
    <div style="color:green;">
      <?php echo $success; ?>
    </div>
  <?php else: ?>
    <p>Fill the form first from <a href="signup.html">Sign Up page</a>.</p>
  <?php endif; ?>

</body>
</html>
