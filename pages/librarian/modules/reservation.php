<?php
// Set the page title 
$page_title = 'Reservations';

include_once '../includes/librarian_header.php';
include_once '../../../includes/db_connection.php'; // Use PDO connection

// --- RESERVE BOOK ---
function reserve_book($book_id, $user_id, $pdo) {
    // Check if book is available or already reserved by this user
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE book_id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$book_id, $user_id]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'You have already reserved this book.'];
    }
    // Add to reservation queue
    $stmt = $pdo->prepare("INSERT INTO reservations (book_id, user_id, status, reserved_at) VALUES (?, ?, 'pending', NOW())");
    $success = $stmt->execute([$book_id, $user_id]);
    return ['success' => $success];
}

// --- MANAGE RESERVATION QUEUE & NOTIFY ---
function process_reservation_queue($book_id, $pdo) {
    // Find the next user in the queue
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE book_id = ? AND status = 'pending' ORDER BY reserved_at ASC LIMIT 1");
    $stmt->execute([$book_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($reservation) {
        // Notify user (for demo, just return user_id)
        // In production, send email or notification here
        // Mark as notified
        $update = $pdo->prepare("UPDATE reservations SET status = 'notified', notified_at = NOW() WHERE id = ?");
        $update->execute([$reservation['id']]);
        return ['notified_user_id' => $reservation['user_id']];
    }
    return null;
}
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Reservations</h2>
        
        <!-- Add your reservations content here -->
        <p class="text-gray-600">This is the reservations page. Add your reservation management functionality here.</p>
    </div>
</div>

<?php
include_once '../includes/librarian_footer.php';
?>
