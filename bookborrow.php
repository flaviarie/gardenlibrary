<?php
include('header.php');
include('db.php');

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

    header("Location: bookborrow.php");
    exit;
}

// Fetch books from DB
$result = $conn->query("SELECT * FROM books");
?>

<main>
    <section class="lend-page">
        <h2>Borrow a Book</h2>
        <p>Select a book to borrow. Unavailable books are disabled.</p>

        <div class="book-table">
            <?php while ($book = $result->fetch_assoc()): ?>
                <div class="book-card">
                    <img src="<?= htmlspecialchars($book['image']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <?php if ($book['available']): ?>
                        <a href="bookborrow.php?borrow=<?= $book['id'] ?>" class="borrow-button">Borrow</a>
                    <?php else: ?>
                        <button class="borrow-button" disabled>Unavailable</button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <a href="index.php" class="home-link">← Back to Home</a>
    </section>
</main>

<?php include('footer.php'); ?>
