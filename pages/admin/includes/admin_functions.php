<?php
// Admin Helper Functions
// Common functions used across admin modules

// Function to check admin privileges
function isAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
}

// Function to log admin actions
function logAdminAction($action, $details = '') {
    // Implementation for logging admin actions
}

// Function to get system statistics
function getSystemStats() {
    // Implementation for system statistics
}

// Function to validate admin permissions
function requireAdminAccess() {
    if (!isAdmin()) {
        header('Location: ../../login/');
        exit();
    }
}

?>
