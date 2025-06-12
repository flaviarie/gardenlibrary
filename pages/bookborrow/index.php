<?php
// Include the configuration file
require_once('../../config.php');

// Include the header and database
include($include_path . 'includes/header.php');
include($include_path . 'includes/db.php');

// Borrow request
if (isset($_GET['borrow'])) {
    $bookId = intval($_GET['borrow']);
    // Only borrow if the book is available
    $check = $conn->prepare("SELECT available FROM books WHERE id = ?");
    $check->bind_param("i", $bookId);
    $check->execute();
    $check->bind_result($available);
    $check->fetch();
    $check->close();

    if ($available) {
        $stmt = $conn->prepare("UPDATE books SET available = FALSE WHERE id = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $stmt->close();
    }
    
    // Redirect back to the bookborrow page
    header("Location: index.php");
    exit();
}
?>

<main>
    <div class="container">
        <div class="row">
            <div class="col-12 lend-page">
                <h2>Available Books</h2>
                <p>Browse our collection and borrow books for free.</p>
                
                <div class="book-table">
                    <?php
                    // Get all books
                    $result = $conn->query("SELECT * FROM books");
                    while ($book = $result->fetch_assoc()) {
                    ?>
                    <div class="book-card">
                        <img src="<?php echo $base_path; ?>assets/img/book-cover.jpg" alt="<?= htmlspecialchars($book['title']) ?>">
                        <h3><?= htmlspecialchars($book['title']) ?></h3>
                        <a href="index.php?borrow=<?= $book['id'] ?>" class="borrow-button"><?= $book['available'] ? 'Borrow' : 'Not Available' ?></a>
                    </div>
                    <?php } ?>
                </div>
                
                <a href="../../index.php" class="home-link">Back to Home</a>
            </div>
        </div>
    </div>
</main>

<?php
// Include the footer
include($include_path . 'includes/footer.php');
?>
