<?php
// Set the page title 
$page_title = 'Issue & Returns';

include_once '../includes/librarian_header.php';
include_once '../../../includes/db_connection.php'; // Use PDO connection
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Issue & Returns</h2>
        
        <!-- Add your issue & returns content here -->
        <p class="text-gray-600">This is the issue & returns page. Add your book issuing and returning functionality here.</p>
    </div>
</div>

<?php
// --- BORROW BOOK ---
function borrow_book($book_id, $user_id, $pdo) {
    // Check if user has reached borrowing limit (2 books)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND return_date IS NULL");
    $stmt->execute([$user_id]);
    $active_borrows = $stmt->fetchColumn();
    if ($active_borrows >= 2) {
        return ['success' => false, 'message' => 'Borrowing limit reached (2 books).'];
    }
    // Check if book is available
    $stmt = $pdo->prepare("SELECT status FROM books WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$book || $book['status'] !== 'available') {
        return ['success' => false, 'message' => 'Book is not available for borrowing.'];
    }
    // Borrow book (7 days)
    $borrow_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+7 days'));
    $stmt = $pdo->prepare("INSERT INTO borrowings (book_id, user_id, borrow_date, due_date) VALUES (?, ?, ?, ?)");
    $success = $stmt->execute([$book_id, $user_id, $borrow_date, $due_date]);
    if ($success) {
        $pdo->prepare("UPDATE books SET status = 'borrowed' WHERE book_id = ?")->execute([$book_id]);
    }
    return ['success' => $success];
}

// --- RETURN BOOK ---
function return_book($book_id, $user_id, $pdo) {
    // Find active borrowing
    $stmt = $pdo->prepare("SELECT * FROM borrowings WHERE book_id = ? AND user_id = ? AND return_date IS NULL");
    $stmt->execute([$book_id, $user_id]);
    $borrowing = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$borrowing) {
        return ['success' => false, 'message' => 'No active borrowing found.'];
    }
    $return_date = date('Y-m-d');
    // Update borrowing record
    $stmt = $pdo->prepare("UPDATE borrowings SET return_date = ? WHERE borrowing_id = ?");
    $success = $stmt->execute([$return_date, $borrowing['borrowing_id']]);
    if ($success) {
        $pdo->prepare("UPDATE books SET status = 'available' WHERE book_id = ?")->execute([$book_id]);
        // Check for overdue and apply fine
        $due_date = $borrowing['due_date'];
        if ($return_date > $due_date) {
            $days_overdue = (strtotime($return_date) - strtotime($due_date)) / (60*60*24);
            $fine = ceil($days_overdue) * 10.00;
            $pdo->prepare("INSERT INTO fines (borrowing_id, amount) VALUES (?, ?)")->execute([$borrowing['borrowing_id'], $fine]);
        }
    }
    return ['success' => $success];
}

include_once '../includes/librarian_footer.php';
?>
