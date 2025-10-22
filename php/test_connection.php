<?php
$conn = new mysqli("localhost", "root", "", "football_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully!";
?>
