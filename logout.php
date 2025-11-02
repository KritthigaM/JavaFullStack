<?php
session_start();
session_destroy();
header("Location: login.php");
exit;
session_start();
session_destroy();
header("Location: recruiter-login.php");
exit();
?>
