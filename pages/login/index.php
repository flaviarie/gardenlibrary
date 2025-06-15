<?php
// Include the configuration file
require_once('../../config/config.php');


?>

<!-- Add login-specific CSS -->
<link rel="stylesheet" href="<?php echo $site_url; ?>pages/login/assets/css/style.css?v=<?php echo time(); ?>">

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
            <a href="<?php echo $site_url; ?>index.php" class="home-link">Back to Home</a>
        </div>
    </div>
</main>

