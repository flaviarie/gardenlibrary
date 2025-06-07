<?php
// Define the base path for all includes and assets
$base_path = '../../';

// Include the header
include($base_path . 'includes/header.php');
?>

<main>
    <div class="container">
        <div class="auth-container">
            <h2>Login to Your Account</h2>
            <form action="login_process.php" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="Login">
            </form>
            <p>Don't have an account? <a href="../signup/index.php">Sign up here</a></p>
            <a href="../../index.php" class="home-link">Back to Home</a>
        </div>
    </div>
</main>

<?php
// Include the footer
include($base_path . 'includes/footer.php');
?>
