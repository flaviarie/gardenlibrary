<?php
/**
 * Test script to verify book cover functionality
 * This script can be run to test if all book covers are accessible
 */

// Include database connection
include_once 'includes/db_connection.php';

// Test function to check if a book cover exists
function test_book_cover($cover_name, $img_path) {
    $full_path = $img_path . $cover_name;
    if (file_exists($full_path)) {
        return "✓ {$cover_name} exists";
    } else {
        return "✗ {$cover_name} NOT FOUND";
    }
}

// Image path
$img_path = 'pages/librarian/assets/img/';

echo "<h2>Book Cover Test Results</h2>\n";
echo "<pre>\n";

// Test default cover
echo test_book_cover('default_book_cover.svg', $img_path) . "\n";

// Test all predefined covers
$covers = [
    'brief_history_time.svg',
    '1984.svg',
    'pride_prejudice.svg',
    'great_gatsby.svg',
    'catcher_rye.svg',
    'lord_flies.svg',
    'hobbit.svg',
    'harry_potter.svg',
    'cosmos.svg',
    'mockingbird.svg',
    'origin_species.svg',
    'selfish_gene.svg',
    'silent_spring.svg',
    'sapiens.svg',
    'guns_august.svg',
    'people_history.svg',
    'diary_young_girl.svg',
    'clean_code.svg',
    'pragmatic_programmer.svg',
    'design_patterns.svg',
    'algorithms.svg',
    'republic.svg',
    'meditations.svg',
    'art_of_war.svg',
    'anthony_langgam.svg',
    'buhay_aso.svg'
];

foreach ($covers as $cover) {
    echo test_book_cover($cover, $img_path) . "\n";
}

echo "\nTotal covers tested: " . (count($covers) + 1) . "\n";

// Test database connection and book covers
try {
    $stmt = $pdo->query("SELECT book_id, title, book_cover FROM books WHERE is_deleted = 0 LIMIT 5");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nDatabase Book Cover Test (First 5 books):\n";
    foreach ($books as $book) {
        $cover_status = file_exists($img_path . $book['book_cover']) ? "✓" : "✗";
        echo "{$cover_status} {$book['title']} -> {$book['book_cover']}\n";
    }
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "</pre>\n";
?>
