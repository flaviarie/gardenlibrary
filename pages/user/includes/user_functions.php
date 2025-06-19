<?php
// User Helper Functions
// Common functions used across user modules

// Function to check user login status
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to get user information
function getUserInfo($user_id) {
    // Implementation for getting user information
}

// Function to calculate fine amount
function calculateFine($due_date) {
    // Implementation for fine calculation
}

// Function to check borrowing limits
function canBorrowMore($user_id) {
    // Implementation for borrowing limit check
}

// Function to validate user access
function requireUserAccess() {
    if (!isLoggedIn()) {
        header('Location: ../../login/');
        exit();
    }
}

?>
