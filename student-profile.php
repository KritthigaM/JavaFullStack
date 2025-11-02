<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'db_connect.php';

$errors = [];
$success = '';

// Check if student is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT name, email, password FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $hashed_password);
$stmt->fetch();
$stmt->close();

// --------------------------
// Handle profile update
// --------------------------
if (isset($_POST['update_profile'])) {
    $new_name = trim($_POST['name'] ?? '');
    $new_email = strtolower(trim($_POST['email'] ?? ''));

    if ($new_name === '') { $errors[] = "Name cannot be empty"; }
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Enter a valid email"; }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $new_name, $new_email, $user_id);
        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            $name = $new_name;
            $email = $new_email;
            $_SESSION['user_name'] = $new_name; // update session
        } else {
            $errors[] = "Could not update profile. Try again.";
        }
        $stmt->close();
    }
}

// --------------------------
// Handle password change
// --------------------------
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!password_verify($current_password, $hashed_password)) {
        $errors[] = "Current password is incorrect.";
    } elseif (strlen($new_password) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    } elseif ($new_password !== $confirm_password) {
        $errors[] = "New password and confirm password do not match.";
    } else {
        $new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $new_hashed, $user_id);
        if ($stmt->execute()) {
            $success = "Password changed successfully!";
        } else {
            $errors[] = "Could not change password. Try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Profile</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f5f6fa;
    padding: 20px;
}
h2, h3 { color: #2d3436; }
form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
label { display: block; margin-bottom: 10px; }
input[type="text"], input[type="email"], input[type="password"] {
    width: 100%;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #b2bec3;
    margin-top: 5px;
}
button {
    background-color: #0984e3;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}
button:hover { background-color: #74b9ff; }
.success { color: green; }
.error { color: red; }
a { color: #0984e3; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>

<h2>Student Profile</h2>

<?php if ($success) echo "<p class='success'>{$success}</p>"; ?>
<?php if (!empty($errors)) echo "<ul class='error'><li>".implode("</li><li>", $errors)."</li></ul>"; ?>

<!-- Profile Update Form -->
<h3>Update Profile</h3>
<form method="post">
    <label>Name:
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
    </label>
    <label>Email:
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
    </label>
    <button type="submit" name="update_profile">Update Profile</button>
</form>

<!-- Change Password Form -->
<h3>Change Password</h3>
<form method="post">
    <label>Current Password:
        <input type="password" name="current_password" required>
    </label>
    <label>New Password:
        <input type="password" name="new_password" required minlength="6">
    </label>
    <label>Confirm New Password:
        <input type="password" name="confirm_password" required minlength="6">
    </label>
    <button type="submit" name="change_password">Change Password</button>
</form>

<p><a href="dashboard.php">Back to Dashboard</a> | <a href="logout.php">Logout</a></p>

</body>
</html>
