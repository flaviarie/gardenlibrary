<?php
// includes/session_check.php
// Call this at the top of every protected page (after session_start)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set session timeout (in seconds)
$timeout_duration = 180; // 3 minutes

// If user is not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . (isset($site_url) ? $site_url : '../') . 'pages/login/index.php');
    exit();
}

// Check for session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Last request was more than 3 minutes ago
    session_unset();
    session_destroy();
    header('Location: ' . (isset($site_url) ? $site_url : '../') . 'pages/login/index.php?timeout=1');
    exit();
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time();

// Prevent browser caching after logout
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Pragma: no-cache');
