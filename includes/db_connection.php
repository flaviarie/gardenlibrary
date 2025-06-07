<?php
$host = 'localhost';
$dbname = 'library_system';
$username = 'root'; // Default XAMPP username
$password = ''; // Default XAMPP password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Debug for testing by listing all tables.
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    if (empty($tables)) {
        die("Error: Database is empty. Did you import library_system.sql?");
    }
    echo "Connected successfully. Tables found: " . implode(', ', $tables);
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>