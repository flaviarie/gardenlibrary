<?php
// Set the page title 
$page_title = 'Reports';

include_once '../includes/librarian_header.php';
include_once '../../../includes/db_connection.php'; // Use PDO connection

// --- REPORT: Borrowed Books ---
function get_borrowed_books_report($pdo) {
    $stmt = $pdo->query("SELECT b.book_id, b.title, u.username, br.borrow_date, br.due_date, br.return_date FROM borrowings br JOIN books b ON br.book_id = b.book_id JOIN users u ON br.user_id = u.user_id WHERE br.return_date IS NULL");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- REPORT: Overdue Items ---
function get_overdue_items_report($pdo) {
    $stmt = $pdo->query("SELECT b.book_id, b.title, u.username, br.borrow_date, br.due_date FROM borrowings br JOIN books b ON br.book_id = b.book_id JOIN users u ON br.user_id = u.user_id WHERE br.return_date IS NULL AND br.due_date < CURDATE()");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- REPORT: Fines Collected ---
function get_fines_collected_report($pdo) {
    $stmt = $pdo->query("SELECT SUM(amount) as total_collected FROM fines WHERE status = 'paid'");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- REPORT: Inventory Status ---
function get_inventory_status_report($pdo) {
    $stmt = $pdo->query("SELECT category, COUNT(*) as total_books, SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_books, SUM(CASE WHEN status = 'borrowed' THEN 1 ELSE 0 END) as borrowed_books, SUM(CASE WHEN status = 'archived' THEN 1 ELSE 0 END) as archived_books FROM books WHERE is_deleted = 0 GROUP BY category");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- REPORT: Student Borrowing History ---
function get_student_borrowing_history($user_id, $pdo) {
    $stmt = $pdo->prepare("SELECT b.book_id, b.title, br.borrow_date, br.due_date, br.return_date FROM borrowings br JOIN books b ON br.book_id = b.book_id WHERE br.user_id = ? ORDER BY br.borrow_date DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// --- REPORT: Book Popularity ---
function get_book_popularity_report($pdo) {
    $stmt = $pdo->query("SELECT b.book_id, b.title, COUNT(br.borrowing_id) as times_borrowed FROM books b LEFT JOIN borrowings br ON b.book_id = br.book_id GROUP BY b.book_id, b.title ORDER BY times_borrowed DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Reports</h2>
        
        <!-- Add your reports content here -->
        <p class="text-gray-600">This is the reports page. Add your reporting functionality here.</p>
    </div>
</div>

<?php
include_once '../includes/librarian_footer.php';
?>
