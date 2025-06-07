<?php
// Define the base path for all includes and assets
$base_path = '../../';

// Include the header
include($base_path . 'includes/header.php');
?>

<main>
    <div class="container">
        <div class="auth-container">
            <h2>Create an Account</h2>
            <form action="signup_process.php" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <input type="submit" value="Sign Up">
            </form>
            <p>Already have an account? <a href="../login/index.php">Login here</a></p>
            <a href="../../index.php" class="home-link">Back to Home</a>
        </div>
    </div>
</main>

<?php
// Include the footer
include($base_path . 'includes/footer.php');
?>
