<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = 'Dashboard';
include_once 'includes/librarian_header.php';

// Include database connection
include_once '../../includes/db_connection.php';

// Add debugging information
$debug_mode = false; // Set to true for debugging

if ($debug_mode) {
    echo "<!-- Debug: Page loaded at " . date('Y-m-d H:i:s') . " -->";
}

// Get statistics from database
try {
    // Total Books
    $stmt = $pdo->query("SELECT COUNT(*) FROM books WHERE is_deleted = FALSE");
    $total_books = $stmt->fetchColumn();
    
    // Books Borrowed (currently checked out)
    $stmt = $pdo->query("SELECT COUNT(*) FROM borrowings WHERE return_date IS NULL");
    $books_borrowed = $stmt->fetchColumn();
      // Overdue Books
    $stmt = $pdo->query("SELECT COUNT(*) FROM borrowings WHERE return_date IS NULL AND due_date < CURDATE()");
    $overdue_books = $stmt->fetchColumn();
    
    // Reserved Books
    $stmt = $pdo->query("SELECT COUNT(*) FROM books WHERE status = 'reserved' AND is_deleted = FALSE");
    $reserved_books = $stmt->fetchColumn();// Get recent books for the table (limit to 5 for dashboard)
    $stmt = $pdo->prepare("
        SELECT b.*, 
               CASE 
                   WHEN br.return_date IS NULL AND br.due_date < CURDATE() THEN 'overdue'
                   WHEN b.status = 'borrowed' THEN 'borrowed'
                   ELSE b.status
               END as display_status,
               u.username as borrowed_by
        FROM books b 
        LEFT JOIN borrowings br ON b.book_id = br.book_id AND br.return_date IS NULL
        LEFT JOIN users u ON br.user_id = u.user_id
        WHERE b.is_deleted = FALSE 
        ORDER BY b.added_date DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $total_books = 0;
    $books_borrowed = 0;
    $overdue_books = 0;
    $reserved_books = 0;
    $recent_books = [];
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!-- Statistics Cards -->
<div class="grid grid-cols-4 gap-8 mb-12">
    <!-- Total Books -->
    <a href="modules/manage_books.php" class="block">
        <div class="bg-gradient-to-br from-red-50 to-red-100 p-8 rounded-2xl shadow-lg border border-red-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600 mb-2 uppercase tracking-wide">Total Books</p>
                    <p class="text-4xl font-bold text-red-800 group-hover:text-red-900"><?php echo number_format($total_books); ?></p>
                </div>            
                <div class="w-16 h-16 bg-red-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-red-600 transition-colors duration-300">
                    <i class="fas fa-book text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-red-600">
                <i class="fas fa-arrow-right text-xs mr-2"></i>
                <span class="text-xs font-medium">View all books</span>
            </div>
        </div>
    </a>    
    <!-- Books Borrowed -->
    <a href="modules/issue_returns.php" class="block">
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-2xl shadow-lg border border-green-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 mb-2 uppercase tracking-wide">Books Borrowed</p>
                    <p class="text-4xl font-bold text-green-800 group-hover:text-green-900"><?php echo number_format($books_borrowed); ?></p>
                </div>
                <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-green-600 transition-colors duration-300">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-green-600">
                <i class="fas fa-arrow-right text-xs mr-2"></i>
                <span class="text-xs font-medium">Active borrowings</span>
            </div>
        </div>
    </a>    
    <!-- Overdue Books -->
    <a href="modules/reports.php" class="block">
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-8 rounded-2xl shadow-lg border border-yellow-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-600 mb-2 uppercase tracking-wide">Overdue Books</p>
                    <p class="text-4xl font-bold text-yellow-800 group-hover:text-yellow-900"><?php echo number_format($overdue_books); ?></p>
                </div>            
                <div class="w-16 h-16 bg-yellow-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-yellow-600 transition-colors duration-300">
                    <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-yellow-600">
                <i class="fas fa-arrow-right text-xs mr-2"></i>
                <span class="text-xs font-medium">Needs attention</span>
            </div>
        </div>
    </a>    
    <!-- Reserved Books -->
    <a href="modules/reservation.php" class="block">
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-8 rounded-2xl shadow-lg border border-purple-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600 mb-2 uppercase tracking-wide">Reserved Books</p>
                    <p class="text-4xl font-bold text-purple-800 group-hover:text-purple-900"><?php echo number_format($reserved_books); ?></p>
                </div>            
                <div class="w-16 h-16 bg-purple-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-purple-600 transition-colors duration-300">
                    <i class="fas fa-bookmark text-white text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-purple-600">
                <i class="fas fa-arrow-right text-xs mr-2"></i>
                <span class="text-xs font-medium">View reservations</span>
            </div>
        </div>
    </a>
</div>

<!-- Manage Books Section -->
<div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden font-raleway">
    <div class="bg-gradient-to-r from-green-600 to-green-700 p-8">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-white mb-2 font-raleway">Manage Books</h2>
                <p class="text-green-100 font-raleway">Track and manage your library collection</p>
            </div>            
            <button class="bg-white text-green-700 px-6 py-3 rounded-xl hover:bg-green-50 hover:scale-105 transform transition-all duration-300 font-semibold shadow-lg flex items-center space-x-2 group font-raleway">
                <i class="fas fa-plus text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                <span>Add New Book</span>
            </button>
        </div>
    </div>
      <div class="p-8">
        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full">                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Book ID</th>
                        <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Title</th>
                        <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Author</th>
                        <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Category</th>
                        <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Status</th>
                        <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Borrowed By</th>
                        <th class="px-8 py-4 text-left text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($recent_books)): ?>
                        <?php foreach ($recent_books as $book): ?>                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300 group">
                                <!-- Clean Book ID Display -->
                                <td class="px-8 py-6 text-xs font-mono text-gray-600 group-hover:text-blue-800 font-raleway">
                                    <span class="bg-gray-100 px-3 py-2 rounded-lg border border-gray-200 font-raleway">
                                        <?php echo htmlspecialchars($book['book_id']); ?>
                                    </span>
                                </td>
                                </td>
                                <td class="px-8 py-6 text-sm font-medium text-gray-900 group-hover:text-blue-900 font-raleway"><?php echo htmlspecialchars($book['title']); ?></td>
                                <td class="px-8 py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway"><?php echo htmlspecialchars($book['author']); ?></td>
                                <td class="px-8 py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-md font-raleway"><?php echo htmlspecialchars($book['category']); ?></span>
                                </td>
                                <td class="px-8 py-6">
                                    <?php 
                                    $status = $book['display_status'];
                                    $status_classes = [
                                        'available' => 'bg-gradient-to-r from-green-100 to-green-200 text-green-800',
                                        'borrowed' => 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800',
                                        'overdue' => 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 animate-pulse'
                                    ];
                                    $class = $status_classes[$status] ?? 'bg-gray-100 text-gray-800';
                                    ?>                                    <span class="px-4 py-2 <?php echo $class; ?> text-xs font-bold rounded-full shadow-sm font-raleway">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway">
                                    <?php echo $book['borrowed_by'] ? htmlspecialchars($book['borrowed_by']) : '-'; ?>
                                </td>
                                <td class="px-8 py-6">                                    <?php if ($status === 'available'): ?>
                                        <button class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-blue-700 hover:scale-105 transform transition-all duration-300 text-sm font-medium shadow-md flex items-center space-x-2 font-raleway">
                                            <i class="fas fa-hand-point-right text-xs"></i>
                                            <span>Issue</span>
                                        </button>
                                    <?php elseif ($status === 'borrowed'): ?>
                                        <button class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-4 py-2 rounded-lg hover:from-orange-600 hover:to-orange-700 hover:scale-105 transform transition-all duration-300 text-sm font-medium shadow-md flex items-center space-x-2 font-raleway">
                                            <i class="fas fa-undo text-xs"></i>
                                            <span>Return</span>
                                        </button>
                                    <?php elseif ($status === 'overdue'): ?>
                                        <button class="bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-lg hover:from-red-600 hover:to-red-700 hover:scale-105 transform transition-all duration-300 text-sm font-medium shadow-md flex items-center space-x-2">
                                            <i class="fas fa-exclamation-triangle text-xs"></i>
                                            <span>Overdue</span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-8 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center space-y-2">
                                    <i class="fas fa-book text-4xl text-gray-300"></i>
                                    <p class="text-lg font-medium">No books found</p>
                                    <p class="text-sm">Add some books to get started</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>            </table>
        </div>
        
        <!-- View All Books Link -->
        <div class="mt-6 text-center">
            <a href="./modules/manage_books.php" class="inline-flex items-center space-x-2 text-green-600 hover:text-green-700 font-medium hover:underline">
                <span>View All Books</span>
                <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>    
    </div>
</div>

<script>
// Additional JavaScript to ensure proper loading
document.addEventListener('DOMContentLoaded', function() {
    console.log('Librarian dashboard loaded');
    
    // Verify FontAwesome is working
    setTimeout(function() {
        const icons = document.querySelectorAll('i[class*="fa-"]');
        console.log('Found ' + icons.length + ' FontAwesome icons');
        
        // Check if any icon is not displaying properly
        let missingIcons = 0;
        icons.forEach(function(icon) {
            const computedStyle = window.getComputedStyle(icon, ':before');
            const content = computedStyle.getPropertyValue('content');
            if (!content || content === 'none' || content === '""') {
                missingIcons++;
            }
        });
        
        if (missingIcons > 0) {
            console.warn(missingIcons + ' icons not displaying properly');
            // Apply additional fallbacks if needed
        } else {
            console.log('All icons loaded successfully');
        }
    }, 1000);
    
    // Verify fonts are loaded
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(function() {
            console.log('Fonts loaded successfully');
        });
    }
});

// Handle resource loading errors
window.addEventListener('error', function(e) {
    if (e.target.tagName === 'LINK' || e.target.tagName === 'SCRIPT') {
        console.error('Failed to load resource:', e.target.src || e.target.href);
          // Show user-friendly message
        const notification = document.createElement('div');
        notification.className = 'resource-error-notification';
        notification.innerHTML = `
            <strong>Resource Loading Issue:</strong><br>
            Some stylesheets or scripts failed to load. The page should still be functional.
            <button onclick="this.parentElement.remove()" class="error-notification-close">×</button>
        `;
        document.body.appendChild(notification);
        
        // Auto-remove after 10 seconds
        setTimeout(function() {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 10000);
    }
});
</script>

<?php
include_once 'includes/librarian_footer.php';
?>

