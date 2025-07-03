<?php
// Page name
$page_title = 'My Borrowings';

include_once '../includes/user_header.php';
include_once '../../../includes/db.php';

// --- Borrowing Functions ---
// View list of currently borrowed books for a user
function get_current_borrowings($user_id) {
    global $conn;
    $sql = "SELECT b.*, bk.title, bk.author, bk.category, bk.status AS book_status
            FROM borrowings b
            JOIN books bk ON b.book_id = bk.book_id
            WHERE b.user_id = ? AND b.return_date IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// See due dates and return status for a borrowing
function get_borrowing_details($borrowing_id) {
    global $conn;
    $sql = "SELECT b.*, bk.title, bk.author, bk.category, bk.status AS book_status
            FROM borrowings b
            JOIN books bk ON b.book_id = bk.book_id
            WHERE b.borrowing_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $borrowing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Calculate fines (₱10.00/day/book if overdue)
function calculate_fine($due_date, $return_date = null) {
    $today = new DateTime();
    $due = new DateTime($due_date);
    $end = $return_date ? new DateTime($return_date) : $today;
    if ($end > $due) {
        $interval = $due->diff($end);
        $days_overdue = $interval->days;
        return $days_overdue * 10.00;
    }
    return 0.00;
}

// Request return (mark as requested, actual return handled by librarian)
function request_return($borrowing_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE borrowings SET return_requested = 1 WHERE borrowing_id = ?");
    $stmt->bind_param('i', $borrowing_id);
    return $stmt->execute();
}

// Request renewal (if allowed, e.g., not overdue)
function request_renewal($borrowing_id) {
    global $conn;
    // Check if overdue
    $details = get_borrowing_details($borrowing_id);
    $due = new DateTime($details['due_date']);
    $today = new DateTime();
    if ($today > $due) {
        return 'Cannot renew overdue book.';
    }
    $stmt = $conn->prepare("UPDATE borrowings SET renewal_requested = 1 WHERE borrowing_id = ?");
    $stmt->bind_param('i', $borrowing_id);
    return $stmt->execute();
}
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">My Borrowings</h2>

        <!-- Add your borrowing management content here -->
        <p class="text-gray-600">This is the borrowing management page. Add your borrowing management functionality here.</p>
    </div>
</div>

<?php
include_once '../includes/user_footer.php';
?>