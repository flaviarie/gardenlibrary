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

// Calculate late fees
function calculateFine($due_date) {
    // Fee math logic
}

// Check book limit
function canBorrowMore($user_id) {
    // Borrowing limit check
}

// Force login check
function requireUserAccess() {
    if (!isLoggedIn()) {
        header('Location: ../../login/');
        exit();
    }
}

?>
