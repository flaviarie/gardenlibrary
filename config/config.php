<?php


// Detect whether we're on a development or production server
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    // Local development environment
    $site_url = '/TB21/Final Project/4/gardenlibrary/'; // For URLs in the browser
    $base_path = $_SERVER['DOCUMENT_ROOT'] . $site_url; // Absolute file system path for includes
    
    // Define a relative path for includes from subdirectories
    $include_path = dirname(dirname(__FILE__)) . '/'; // This gets the directory of the current file (config.php)
} else {
    // Production environment
    // Update these values when you deploy to your production server
    $site_url = '/'; // This would be the URL path on your production server, e.g. '/gardenlibrary/'
    $base_path = $_SERVER['DOCUMENT_ROOT'] . $site_url; // Absolute file system path
    $include_path = dirname(__FILE__) . '/'; // This gets the directory of the current file
}

// Database connection settings
// Define your database settings here
$db_host = 'localhost';
$db_user = 'root'; // Change in production
$db_pass = ''; // Change in production
$db_name = 'garden_library';

// Other global settings
$site_name = 'Garden Library';
$admin_email = 'admin@gardenlibrary.com';
?>
