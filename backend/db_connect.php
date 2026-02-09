<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "vehicle"; // Correct database name from your SQL file

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
