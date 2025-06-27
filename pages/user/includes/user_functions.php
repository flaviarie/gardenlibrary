<?php
// User helpers
// Core user functions

// Check login status
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get user data
function getUserInfo($user_id) {
    // Gets user profile
    // Example assumes a PDO connection $pdo is available globally
    global $pdo;
    $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Calculate late fees
function calculateFine($due_date) {
    // Fee math logic
    // $due_date should be in 'Y-m-d' format
    $today = new DateTime();
    $due = new DateTime($due_date);
    $interval = $today->diff($due);
    $days_late = (int)$interval->format('%r%a');
    $fine_per_day = 1.00; // $1 per day late

    if ($days_late < 0) {
        return abs($days_late) * $fine_per_day;
    }
    return 0;
}

// Check book limit
function canBorrowMore($user_id) {
    // Borrowing limit check
    // Example assumes a PDO connection $pdo is available globally
    global $pdo;
    $max_books = 5;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM borrowed_books WHERE user_id = ? AND returned_at IS NULL");
    $stmt->execute([$user_id]);
    $borrowed = $stmt->fetchColumn();
    return $borrowed < $max_books;
}

// Force login check
function requireUserAccess() {
    if (!isLoggedIn()) {
        header('Location: ../../login/');
        exit();
    }
}

?>