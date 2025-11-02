<?php
$conn = new mysqli("localhost", "root", "", "zidio_intern");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
echo "✅ Connected successfully to zidio_intern!";
?>
