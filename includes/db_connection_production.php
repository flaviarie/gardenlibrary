<?php
// Production-ready database connection
$host = 'sql308.infinityfree.com'; // Update this for your hosting provider
$dbname = 'if0_39394988_XXX'; // Update this for your hosting provider
$username = 'if0_39394988'; // Update this for your hosting provider
$password = 'JQ6nZouzo9kh7nN'; // Update this for your hosting provider

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
