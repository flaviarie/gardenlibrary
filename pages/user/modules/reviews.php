<?php
// Page name
$page_title = 'My Reviews';

include_once '../includes/user_header.php';
include_once '../../../includes/db.php';

// --- Review Functions ---
// Submit a review and rating for a book
function submit_review($user_id, $book_id, $rating, $review_text) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, book_id, rating, review_text, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param('iiss', $user_id, $book_id, $rating, $review_text);
    return $stmt->execute();
}

// Read reviews for a book (from all users)
function get_book_reviews($book_id) {
    global $conn;
    $sql = "SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.book_id = ? ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Edit a personal review
function edit_review($review_id, $user_id, $rating, $review_text) {
    global $conn;
    $stmt = $conn->prepare("UPDATE reviews SET rating = ?, review_text = ?, updated_at = NOW() WHERE review_id = ? AND user_id = ?");
    $stmt->bind_param('isii', $rating, $review_text, $review_id, $user_id);
    return $stmt->execute();
}

// Delete a personal review
function delete_review($review_id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM reviews WHERE review_id = ? AND user_id = ?");
    $stmt->bind_param('ii', $review_id, $user_id);
    return $stmt->execute();
}

// Get popular/recommended books (by average rating, min 3 reviews)
function get_popular_books($limit = 5) {
    global $conn;
    $sql = "SELECT b.*, AVG(r.rating) as avg_rating, COUNT(r.review_id) as review_count
            FROM books b
            JOIN reviews r ON b.book_id = r.book_id
            GROUP BY b.book_id
            HAVING review_count >= 3
            ORDER BY avg_rating DESC, review_count DESC
            LIMIT ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">My Reviews</h2>

        <!-- Add your review management content here -->
        <p class="text-gray-600">This is the review management page. Add your review management functionality here.</p>
    </div>
</div>

<?php
include_once '../includes/user_footer.php';
?>