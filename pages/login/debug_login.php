<?php
// Debug login process
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Login Debug Test</h2>";

// Test database connection
try {
    include_once '../../includes/db_connection.php';
    echo "<p>✅ Database connection successful</p>";
    
    // Test users table
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $user_count = $stmt->fetchColumn();
    echo "<p>✅ Found {$user_count} users in database</p>";
    
    // Show sample users
    $stmt = $pdo->query("SELECT user_id, username, role FROM users LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<h3>Sample Users:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Username</th><th>Role</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['user_id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test file paths
echo "<h3>File Path Tests:</h3>";
$files_to_check = [
    '../../includes/db_connection.php',
    '../admin/index.php',
    '../user/index.php',
    'login_process.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p>✅ {$file} exists</p>";
    } else {
        echo "<p>❌ {$file} NOT FOUND</p>";
    }
}

// Test URLs
echo "<h3>URL Tests:</h3>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script name: " . $_SERVER['SCRIPT_NAME'] . "</p>";

// Simulate login redirect paths
echo "<h3>Login Redirect Test:</h3>";
$admin_path = "../admin/index.php";
$user_path = "../user/index.php";

echo "<p>Admin redirect path: {$admin_path}</p>";
echo "<p>User redirect path: {$user_path}</p>";

// Check if those paths resolve correctly
$full_admin_path = realpath(__DIR__ . '/' . $admin_path);
$full_user_path = realpath(__DIR__ . '/' . $user_path);

echo "<p>Full admin path: " . ($full_admin_path ?: "NOT FOUND") . "</p>";
echo "<p>Full user path: " . ($full_user_path ?: "NOT FOUND") . "</p>";

?>

<form method="post" action="login_process.php" style="margin-top: 20px; padding: 20px; border: 1px solid #ccc;">
    <h3>Test Login Form</h3>
    <p>Username: <input type="text" name="username" value="admin" required></p>
    <p>Password: <input type="password" name="password" value="" required></p>
    <p><button type="submit">Test Login</button></p>
    <p><small>Default admin password should be 'password' or check the database</small></p>
</form>
