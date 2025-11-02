<?php
session_start();
require 'db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email.";
    }
    if ($password === '') {
        $errors[] = "Enter password.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $name, $hashed, $role);
            $stmt->fetch();
            if (password_verify($password, $hashed)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_role'] = $role;
                header("Location: dashboard.php");
                exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "Email not registered.";
        }
        $stmt->close();
    }
}
?>

<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title></head>
<body>
<h2>Login</h2>

<?php if (!empty($errors)): ?>
    <div style="color:red;">
        <ul><?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<form method="post" action="">
    <label>Email:<br><input type="email" name="email" required></label><br><br>
    <label>Password:<br><input type="password" name="password" required></label><br><br>
    <button type="submit">Login</button>
</form>

<p>Not registered? <a href="signup.html">Sign Up</a></p>
</body>
</html>
