<?php
session_start();
require_once('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['signup_error'] = "Please fill in all fields.";
        header("Location: index.php");
        exit();
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['signup_error'] = "Passwords do not match.";
        header("Location: index.php");
        exit();
    }
    
    if (strlen($password) < 6) {
        $_SESSION['signup_error'] = "Password must be at least 6 characters long.";
        header("Location: index.php");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['signup_error'] = "Please enter a valid email address.";
        header("Location: index.php");
        exit();
    }
    
    try {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $_SESSION['signup_error'] = "Username already exists. Please choose a different username.";
            header("Location: index.php");
            exit();
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['signup_error'] = "Email already exists. Please use a different email address.";
            header("Location: index.php");
            exit();
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user (default role is 'student')
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, email) VALUES (?, ?, 'student', ?)");
        $stmt->execute([$username, $hashed_password, $email]);
        
        $_SESSION['signup_success'] = "Account created successfully! You can now log in.";
        header("Location: ../login/index.php");
        exit();
        
    } catch (PDOException $e) {
        $_SESSION['signup_error'] = "Database error: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
