<?php
// Set the page title 
$page_title = 'My Reservations';

include_once '../includes/user_header.php';
include_once '../../../includes/db.php';

// --- Reservation Functions ---
// Reserve a book that is currently borrowed by others
function reserve_book_for_user($book_id, $user_id) {
    global $conn;
    // Only allow reservation if book is borrowed
    $stmt = $conn->prepare("SELECT status FROM books WHERE book_id = ?");
    $stmt->bind_param('s', $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row || $row['status'] != 'borrowed') {
        return 'Book is not currently borrowed.';
    }
    // Insert reservation
    $stmt2 = $conn->prepare("INSERT INTO reservations (book_id, user_id, reserved_at, status) VALUES (?, ?, NOW(), 'pending')");
    $stmt2->bind_param('si', $book_id, $user_id);
    return $stmt2->execute();
}

// View reservation status for a user
function get_user_reservations($user_id) {
    global $conn;
    $sql = "SELECT r.*, b.title, b.author, b.category FROM reservations r JOIN books b ON r.book_id = b.book_id WHERE r.user_id = ? ORDER BY r.reserved_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Cancel a reservation
function cancel_reservation($reservation_id, $user_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE reservations SET status = 'canceled' WHERE reservation_id = ? AND user_id = ?");
    $stmt->bind_param('ii', $reservation_id, $user_id);
    return $stmt->execute();
}

// Modify a reservation (change to another book)
function modify_reservation($reservation_id, $user_id, $new_book_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE reservations SET book_id = ?, modified_at = NOW() WHERE reservation_id = ? AND user_id = ? AND status = 'pending'");
    $stmt->bind_param('sii', $new_book_id, $reservation_id, $user_id);
    return $stmt->execute();
}

// Receive notifications when reserved books become available (fetch ready notifications)
function get_available_notifications($user_id) {
    global $conn;
    $sql = "SELECT r.*, b.title, b.author FROM reservations r JOIN books b ON r.book_id = b.book_id WHERE r.user_id = ? AND r.status = 'confirmed' AND r.notified = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">My Reservations</h2>

        <!-- Add your reservation management content here -->
        <p class="text-gray-600">This is the reservation management page. Add your reservation management functionality here.</p>
    </div>
</div>

<?php
include_once '../includes/user_footer.php';
?>