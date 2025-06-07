<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Or your DB password
$dbname = 'library'; // Change if your DB is named differently

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
