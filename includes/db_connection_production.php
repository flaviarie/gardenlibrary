<?php
// Production-ready database connection
$host = 'localhost'; // Update this for your hosting provider
$dbname = 'library_system'; // Update this for your hosting provider
$username = 'root'; // Update this for your hosting provider
$password = ''; // Update this for your hosting provider

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Remove debug output for production
    // You can optionally log this to a file instead of displaying it
    
} catch (PDOException $e) {
    // Log error instead of displaying it in production
    error_log("Database connection failed: " . $e->getMessage());
    die("Connection failed. Please contact administrator.");
}
?>
