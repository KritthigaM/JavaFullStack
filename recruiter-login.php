<?php
session_start();
include 'db.php';

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM recruiters WHERE email=? AND password=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $email, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0){
        $recruiter = mysqli_fetch_assoc($result);
        $_SESSION['recruiter_id'] = $recruiter['id'];
        header("Location: recruiter-dashboard.php");
        exit();
    } else {
        echo "<script>alert('Email or password is wrong');</script>";
    }
}
?>

<h2>Recruiter Login</h2>
<form method="post">
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <input type="submit" name="login" value="Login">
</form>
