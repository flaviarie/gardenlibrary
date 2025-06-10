<?php
// Define the base path for all includes and assets
$base_path = '';  // Empty for root level

// Include the header
include('includes/header.php');
?>

<main>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <section class="welcome-section">
                    <div class="caption">                        
                        <h1>Your<span class="highlight_heading"> Smart</span> Campus Library System</h1>
                        <p>A modern and intelligent library system</p>
                        <p>designed to connect students and librarians </p>
                        <p>through seamless, efficient, and accessible </p>
                        <p>digital experiences.</p>
                        <div class="hero-cta">
                            <a href="<?php echo $base_path; ?>pages/signup/index.php" class="hero-button signup-btn">Sign Up</a>
                            <a href="<?php echo $base_path; ?>pages/login/index.php" class="hero-button login-btn">Login</a>
                        </div>
                    </div>
                    <div class="image-container">
                        <img src="assets/img/Hero.png" alt="Library Welcome Image">
                    </div>
                </section>
            </div>
        </div>        <div class="row">
            <div class="col-8 center-col">
                <section class="lend-section">
                    <h3>Want to lend a book?</h3>
                    <p>Click below to view our lending services.</p>
                    <a href="pages/bookborrow/index.php" class="lend-button">Lend a Book</a>
                </section>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <section id="features" class="features-section">
                    <h2>Our Features</h2>
                    <div class="features-container">
                        <div class="feature-card">
                            <img src="assets/img/books.png" alt="Book Catalog">
                            <h3>Extensive Book Catalog</h3>
                            <p>Browse through our diverse collection of books</p>
                        </div>
                        <div class="feature-card">
                            <img src="assets/img/trending-topic.png" alt="Easy Borrowing">
                            <h3>Easy Borrowing</h3>
                            <p>Simple process to borrow and return books</p>
                        </div>
                        <div class="feature-card">
                            <img src="assets/img/alarm-clock.png" alt="Timely Reminders">
                            <h3>Timely Reminders</h3>
                            <p>Get notifications about due dates and reservations</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

<?php
// Include the footer
include('includes/footer.php');
?>
