<?php
session_start();
require_once('../../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Please fill in all fields.";
        header("Location: index.php");
        exit();
    }
    
    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT user_id, username, password, role, email FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            
            // Redirect based on role and username
            if ($user['role'] === 'admin') {
                // Check if it's the super admin or a librarian
                if ($user['username'] === 'admin') {
                    header("Location: ../admin/index.php");
                } else {
                    header("Location: ../librarian/index.php");
                }
            } elseif ($user['role'] === 'student') {
                header("Location: ../user/index.php");
            } else {
                header("Location: ../user/index.php");
            }
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid username or password.";
            header("Location: index.php");
            exit();
        }
        
    } catch (PDOException $e) {
        $_SESSION['login_error'] = "Database error: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
