<?php
$host = "localhost";
$username = "root";  // or your MySQL username
$password = "";      // or your MySQL password
$database = "autoparts";  // make sure this DB exists

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>