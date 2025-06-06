<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up - Garden Library</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    body {
        position: relative;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        overflow-x: hidden;
    }

    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('images/background.jpg') no-repeat center center fixed;
        background-size: cover;
        filter: blur(8px); /* Adjust blur level */
        z-index: -1; /* Push behind the content */
    }
</style>
<body>
    <div class="auth-container">
        <h2>Sign Up</h2>
        <form action="signup.php" method="post">
            <input type="text" name="username" placeholder="Choose a Username" required><br>
            <input type="email" name="email" placeholder="Email Address" required><br>
            <input type="password" name="password" placeholder="Create Password" required><br>
            <input type="submit" value="Sign Up">
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
        <a class="home-link" href="index.php">← Back to Home</a>
    </div>
</body>
</html>

<?php
// insert php code na lang here
?>
