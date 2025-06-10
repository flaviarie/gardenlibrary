<?php
// Define the base path for all includes and assets
$base_path = '../';  // One level up from pages folder

// Include the header
include($base_path . 'includes/header.php');
?>

<main>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <section class="about-section">
                    <h2>About Garden Library</h2>
                    <p>Garden Library is a modern campus library system designed to make book management and borrowing seamless and efficient for both students and librarians.</p>
                    
                    <div class="about-content">
                        <div class="about-image">
                            <img src="<?php echo $base_path; ?>assets/img/library.jpg" alt="Library Image">
                        </div>
                        
                        <div class="about-text">
                            <h3>Our Mission</h3>
                            <p>To provide an accessible, user-friendly platform that connects students with the resources they need for academic success.</p>
                            
                            <h3>Our Vision</h3>
                            <p>To transform the traditional library experience through technology, making knowledge more accessible to everyone on campus.</p>
                            
                            <h3>Contact Us</h3>
                            <p>Email: info@gardenlibrary.edu<br>
                            Phone: (123) 456-7890<br>
                            Address: University Campus, Building 4, Room 101</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

<?php
// Include the footer
include($base_path . 'includes/footer.php');
?>
