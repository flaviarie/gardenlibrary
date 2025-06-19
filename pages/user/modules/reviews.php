<?php
// User Module: Book Reviews and Ratings
// Allows users to rate and review books they've read

session_start();
require_once '../../../includes/db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login/');
    exit();
}

// Book review and rating functions will be implemented here

?>
