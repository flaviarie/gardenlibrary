<?php
// Page name
$page_title = 'Browse Books';

include_once '../includes/user_header.php';
include_once '../../../includes/db.php';

// --- Book Functions ---
// Search books by title, author, category, or publication date
function search_books($query, $field = 'title') {
    global $conn;
    $allowed_fields = ['title', 'author', 'category', 'publish_date'];
    if (!in_array($field, $allowed_fields)) $field = 'title';
    $stmt = $conn->prepare("SELECT * FROM books WHERE $field LIKE ? AND is_deleted = 0");
    $like = "%$query%";
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get book details by book_id
function get_book_details($book_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ? AND is_deleted = 0");
    $stmt->bind_param('s', $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Check if a book is available or archived
function get_book_status($book_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT status FROM books WHERE book_id = ? AND is_deleted = 0");
    $stmt->bind_param('s', $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['status'] : null;
}

// Reserve an available book for a user
function reserve_book($book_id, $user_id) {
    global $conn;
    $status = get_book_status($book_id);
    if ($status !== 'available') {
        return 'Book is not available for reservation.';
    }
    $stmt = $conn->prepare("INSERT INTO reservations (book_id, user_id, reserved_at) VALUES (?, ?, NOW())");
    $stmt->bind_param('si', $book_id, $user_id);
    if ($stmt->execute()) {
        return true;
    } else {
        return 'Failed to reserve book.';
    }
}
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Browse Books</h2>

        <!-- Add your book browsing content here -->
        <p class="text-gray-600">This is the book browsing page. Add your book browsing functionality here.</p>
    </div>
</div>

<?php
include_once '../includes/user_footer.php';
?>