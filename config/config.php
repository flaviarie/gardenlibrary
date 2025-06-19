<?php


//Auto detect base path
$script_path = str_replace('\\', '/',dirname($_SERVER['SCRIPT_NAME']));

$path_parts = explode('/', trim($script_path, '/'));
$project_index = array_search('gardenlibrary', $path_parts);
if ($project_index !== false){
    $project_path = '/' . implode('/', array_slice($path_parts, 0, $project_index + 1)) . '/';
} else {
    $project_path = '/TB21/Final Project/garden-library/';
}


$site_url = $project_path;
$base_path = $_SERVER['DOCUMENT_ROOT'] . $site_url;
$include_path = dirname(dirname(__FILE__)) . '/';

function asset_url($path) {
    global $site_url;
    return $site_url . ltrim($path, '/');
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
