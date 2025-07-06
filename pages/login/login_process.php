<?php
// Debugging: Show errors (remove on production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "Please fill in all fields.";
        header("Location: index.php");
        exit();
    }

    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT user_id, username, password, role, email FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && isset($user['password']) && password_verify($password, $user['password'])) {
            // Login successful
            session_regenerate_id(true); // Prevent session fixation
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;

            // Redirect based on role and username
            if ($user['role'] === 'admin') {
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
    } catch (Exception $e) {
        $_SESSION['login_error'] = "Database error: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
