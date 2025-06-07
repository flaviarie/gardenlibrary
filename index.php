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
                        <h2>Welcome to the Garden Library</h2>                        <p>Discover, borrow, and manage books with ease. Your knowledge journey starts here.</p>
                    </div>
                    <div class="image-container">
                        <img src="assets/img/library.jpg" alt="Library Welcome Image">
                    </div>
                </section>
            </div>
        </div>

        <div class="row">
            <div class="col-8 center-col">                <section class="lend-section">
                    <h3>Want to lend a book?</h3>
                    <p>Click below to view our lending services.</p>
                    <a href="pages/bookborrow/index.php" class="lend-button">Lend a Book</a>
                </section>
            </div>
        </div>
    </div>
</main>

<?php
// Include the footer
include('includes/footer.php');
?>
