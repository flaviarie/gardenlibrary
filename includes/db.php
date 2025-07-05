<?php
// Environment detection and database connection
// This file will automatically choose the right database connection based on environment

// Detect if we're in production or development
$is_production = false;

// Check if we're on a production server (you can customize these checks)
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    // Add your production domain(s) here
    $production_domains = ['your-domain.com', 'www.your-domain.com', 'infinityfree.com'];
    
    foreach ($production_domains as $domain) {
        if (strpos($host, $domain) !== false) {
            $is_production = true;
            break;
        }
    }
}

// You can also check for environment variables
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
    $is_production = true;
}

// Load the appropriate connection file
if ($is_production) {
    require_once __DIR__ . '/db_connection_production.php';
} else {
    require_once __DIR__ . '/db_connection.php';
}
?>
