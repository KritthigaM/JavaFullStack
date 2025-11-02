<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zidio_intern";  // check in phpMyAdmin for exact name

$conn = new mysqli($servername, $username, $password, $dbname);

// ✅ Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
