<?php
// Set the page title 
$page_title = 'Manage Books';

include_once '../includes/librarian_header.php';
include_once '../../../includes/db_connection.php'; // Use PDO connection

// --- CATEGORY VALIDATION ---
function is_valid_category($category) {
    $valid_categories = ['FIC', 'SCI', 'HIS', 'BIO', 'ART', 'REF', 'KID', 'OTH']; // Add more as needed
    return in_array(strtoupper($category), $valid_categories);
}

// --- BOOK ID GENERATION ---
function generate_book_id($title, $publish_date, $category, $added_date, $pdo) {
    // 1. First 2 letters from the Book Title (letters only, uppercase)
    $title_prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $title), 0, 2));
    // 2. Month (published, 3-letter uppercase)
    $publish_month = strtoupper(date('M', strtotime($publish_date)));
    // 3. Day (added to the system, 2 digits)
    $added_day = str_pad(date('d', strtotime($added_date)), 2, '0', STR_PAD_LEFT);
    // 4. Year (published, 4 digits)
    $publish_year = date('Y', strtotime($publish_date));
    // 5. Category (3-letter code, uppercase)
    $category = strtoupper($category);
    // 6. Count of books in the library (not deleted)
    $stmt = $pdo->query("SELECT COUNT(*)+1 FROM books WHERE is_deleted = 0");
    $count = $stmt->fetchColumn();
    $formatted_count = str_pad($count, 5, '0', STR_PAD_LEFT);
    // 7. Format: THFEB102022-FIC00001
    return $title_prefix . $publish_month . $added_day . $publish_year . '-' . $category . $formatted_count;
}

// --- MAINTAIN MINIMUM BOOKS ---
function ensure_minimum_books($pdo, $min = 50) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM books WHERE is_deleted = 0");
    $count = $stmt->fetchColumn();
    return $count >= $min;
}

// --- ARCHIVE BOOK ---
function archive_book($book_id, $pdo) {
    $stmt = $pdo->prepare("UPDATE books SET status = 'archived' WHERE book_id = ?");
    return $stmt->execute([$book_id]);
}

// --- ADD BOOK (with validation) ---
function add_book($title, $author, $publish_date, $category, $added_date, $pdo) {
    if (!is_valid_category($category)) {
        return ['success' => false, 'message' => 'Invalid category.'];
    }
    $book_id = generate_book_id($title, $publish_date, $category, $added_date, $pdo);
    $stmt = $pdo->prepare("INSERT INTO books (book_id, title, author, publish_date, category, added_date, status) VALUES (?, ?, ?, ?, ?, ?, 'available')");
    $success = $stmt->execute([$book_id, $title, $author, $publish_date, strtoupper($category), $added_date]);
    return ['success' => $success, 'book_id' => $book_id];
}
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Manage Books</h2>
        
        <!-- Add your manage books content here -->
        <p class="text-gray-600">This is the manage books page. Add your book management functionality here.</p>
    </div>
</div>

<?php
include_once '../includes/librarian_footer.php';
?>

