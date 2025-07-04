<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to DB first (before any output)
include_once '../../../includes/db_connection.php';
include_once '../includes/user_functions.php';

// Check if user is logged in (this might redirect, so do it early)
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header('Location: ../../login/index.php');
    exit();
}

// Get current user
$user_id = $_SESSION['user_id'];

// Handle return confirmation BEFORE any output
if (isset($_POST['confirm_return']) && isset($_POST['borrowing_id'])) {
    $borrowing_id = (int)$_POST['borrowing_id'];
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get borrowing details
        $stmt = $pdo->prepare("
            SELECT br.book_id, br.due_date, b.title 
            FROM borrowings br 
            JOIN books b ON br.book_id = b.book_id 
            WHERE br.borrowing_id = ? AND br.user_id = ? AND br.return_date IS NULL
        ");
        $stmt->execute([$borrowing_id, $user_id]);
        $borrowing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$borrowing) {
            throw new Exception("Borrowing record not found or already returned.");
        }
        
        // Update borrowing record with return date
        $return_date = date('Y-m-d');
        $stmt = $pdo->prepare("UPDATE borrowings SET return_date = ? WHERE borrowing_id = ?");
        $result1 = $stmt->execute([$return_date, $borrowing_id]);
        
        // Update book status to available
        $stmt = $pdo->prepare("UPDATE books SET status = 'available' WHERE book_id = ?");
        $result2 = $stmt->execute([$borrowing['book_id']]);
        
        // Calculate fine if overdue
        $fine_amount = 0;
        $today = new DateTime();
        $due_date = new DateTime($borrowing['due_date']);
        
        if ($today > $due_date) {
            $days_overdue = $today->diff($due_date)->days;
            $fine_per_day = 5.00; // ₱5 per day
            $fine_amount = $days_overdue * $fine_per_day;
            
            // Create fine record
            $stmt = $pdo->prepare("INSERT INTO fines (borrowing_id, amount, status) VALUES (?, ?, 'unpaid')");
            $stmt->execute([$borrowing_id, $fine_amount]);
        }
        
        if ($result1 && $result2) {
            $pdo->commit();
            
            if ($fine_amount > 0) {
                $_SESSION['success_message'] = "Book '{$borrowing['title']}' returned successfully! However, you have a fine of ₱" . number_format($fine_amount, 2) . " for returning it " . $days_overdue . " day(s) late.";
            } else {
                $_SESSION['success_message'] = "Book '{$borrowing['title']}' returned successfully!";
            }
        } else {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Failed to return book. Please try again.";
        }
        
    } catch (Exception $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        $_SESSION['error_message'] = "Error returning book: " . $e->getMessage();
    }
    
    // Redirect to clear POST data
    header("Location: my_borrowings.php");
    exit();
}

// Now include header after all possible redirects
$page_title = 'My Borrowings';
include_once '../includes/user_header.php';

// Initialize variables
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$sort_by = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'borrow_date';
$sort_order = isset($_GET['order']) ? sanitizeInput($_GET['order']) : 'DESC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Handle messages from session
$success_message = '';
$error_message = '';

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Build query for borrowings
$where_conditions = ["br.user_id = ?"];
$params = [$user_id];

if (!empty($status_filter)) {
    if ($status_filter === 'current') {
        $where_conditions[] = "br.return_date IS NULL";
    } elseif ($status_filter === 'returned') {
        $where_conditions[] = "br.return_date IS NOT NULL";
    } elseif ($status_filter === 'overdue') {
        $where_conditions[] = "br.return_date IS NULL AND br.due_date < CURDATE()";
    }
}

$where_clause = implode(' AND ', $where_conditions);

// Valid sort columns
$valid_sort_columns = ['borrow_date', 'due_date', 'return_date', 'title'];
if (!in_array($sort_by, $valid_sort_columns)) {
    $sort_by = 'borrow_date';
}

$sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

// Get total count for pagination
$count_sql = "SELECT COUNT(*) FROM borrowings br WHERE $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_borrowings = $count_stmt->fetchColumn();
$total_pages = ceil($total_borrowings / $per_page);

// Get borrowings with pagination
$sql = "SELECT br.borrowing_id, br.book_id, br.borrow_date, br.due_date, br.return_date,
               b.title, b.author, b.category, b.book_cover,
               CASE 
                   WHEN br.return_date IS NULL AND br.due_date < CURDATE() THEN 'overdue'
                   WHEN br.return_date IS NULL THEN 'current'
                   ELSE 'returned'
               END as status,
               CASE 
                   WHEN br.return_date IS NULL AND br.due_date < CURDATE() THEN DATEDIFF(CURDATE(), br.due_date)
                   ELSE 0
               END as days_overdue,
               f.amount as fine_amount,
               f.status as fine_status
        FROM borrowings br
        JOIN books b ON br.book_id = b.book_id
        LEFT JOIN fines f ON br.borrowing_id = f.borrowing_id AND f.status = 'unpaid'
        WHERE $where_clause
        ORDER BY $sort_by $sort_order
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get summary statistics
$stats_sql = "SELECT 
    COUNT(CASE WHEN return_date IS NULL THEN 1 END) as current_borrowings,
    COUNT(CASE WHEN return_date IS NOT NULL THEN 1 END) as returned_books,
    COUNT(CASE WHEN return_date IS NULL AND due_date < CURDATE() THEN 1 END) as overdue_books,
    COALESCE(SUM(CASE WHEN f.status = 'unpaid' THEN f.amount ELSE 0 END), 0) as unpaid_fines
FROM borrowings br
LEFT JOIN fines f ON br.borrowing_id = f.borrowing_id
WHERE br.user_id = ?";

$stats_stmt = $pdo->prepare($stats_sql);
$stats_stmt->execute([$user_id]);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Category names mapping
$category_names = [
    'FIC' => 'Fiction',
    'SCI' => 'Science',
    'HIS' => 'History',
    'TEC' => 'Technology',
    'PHI' => 'Philosophy',
    'BIO' => 'Biography',
    'ROM' => 'Romance',
    'MYS' => 'Mystery',
    'THR' => 'Thriller',
    'FAN' => 'Fantasy'
];
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">My Borrowings</h1>
                    <p class="text-gray-600">Track your borrowed books, due dates, and return history</p>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600"><?php echo $stats['current_borrowings']; ?></div>
                        <div class="text-sm text-gray-500">Current</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600"><?php echo $stats['returned_books']; ?></div>
                        <div class="text-sm text-gray-500">Returned</div>
                    </div>
                    <?php if ($stats['overdue_books'] > 0): ?>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600"><?php echo $stats['overdue_books']; ?></div>
                            <div class="text-sm text-gray-500">Overdue</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if (!empty($success_message) && trim($success_message) !== ''): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success_message; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message) && trim($error_message) !== ''): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error_message; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Outstanding Fines Alert -->
        <?php if ($stats['unpaid_fines'] > 0): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg mb-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>You have <strong>₱<?php echo number_format($stats['unpaid_fines'], 2); ?></strong> in unpaid fines. Please visit the library to settle your account.</span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Filter and Sort Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Borrowings</option>
                            <option value="current" <?php echo $status_filter === 'current' ? 'selected' : ''; ?>>Current Borrowings</option>
                            <option value="returned" <?php echo $status_filter === 'returned' ? 'selected' : ''; ?>>Returned Books</option>
                            <option value="overdue" <?php echo $status_filter === 'overdue' ? 'selected' : ''; ?>>Overdue Books</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="borrow_date" <?php echo $sort_by === 'borrow_date' ? 'selected' : ''; ?>>Borrow Date</option>
                            <option value="due_date" <?php echo $sort_by === 'due_date' ? 'selected' : ''; ?>>Due Date</option>
                            <option value="return_date" <?php echo $sort_by === 'return_date' ? 'selected' : ''; ?>>Return Date</option>
                            <option value="title" <?php echo $sort_by === 'title' ? 'selected' : ''; ?>>Book Title</option>
                        </select>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                        <select name="order" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="DESC" <?php echo $sort_order === 'DESC' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="ASC" <?php echo $sort_order === 'ASC' ? 'selected' : ''; ?>>Oldest First</option>
                        </select>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <a href="my_borrowings.php" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Borrowings List -->
        <?php if (empty($borrowings)): ?>
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-600 mb-2">No Borrowings Found</h3>
                <p class="text-gray-500 mb-6">You haven't borrowed any books yet or no borrowings match your filters.</p>
                <a href="browse_books.php" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-search mr-2"></i>Browse Books
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrow Date</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fine</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($borrowings as $borrowing): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-12 h-16 bg-gradient-to-br from-blue-100 to-green-100 rounded-lg flex items-center justify-center mr-4">
                                                <img src="../../librarian/assets/img/<?php echo htmlspecialchars($borrowing['book_cover'] ?? 'default_book_cover.svg'); ?>" 
                                                     alt="<?php echo htmlspecialchars($borrowing['title']); ?>"
                                                     class="w-full h-full object-cover rounded-lg"
                                                     onerror="this.src='../../librarian/assets/img/default_book_cover.svg'">
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($borrowing['title']); ?></div>
                                                <div class="text-sm text-gray-500">by <?php echo htmlspecialchars($borrowing['author']); ?></div>
                                                <div class="text-xs text-gray-400">
                                                    <?php echo isset($category_names[$borrowing['category']]) ? $category_names[$borrowing['category']] : $borrowing['category']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo formatDate($borrowing['borrow_date']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo formatDate($borrowing['due_date']); ?>
                                        <?php if ($borrowing['status'] === 'overdue'): ?>
                                            <div class="text-xs text-red-600 mt-1">
                                                <?php echo $borrowing['days_overdue']; ?> day(s) overdue
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php if ($borrowing['status'] === 'current'): ?>
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">Current</span>
                                        <?php elseif ($borrowing['status'] === 'returned'): ?>
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Returned</span>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <?php echo formatDate($borrowing['return_date']); ?>
                                            </div>
                                        <?php elseif ($borrowing['status'] === 'overdue'): ?>
                                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">Overdue</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <?php if ($borrowing['fine_amount'] > 0): ?>
                                            <div class="text-red-600 font-medium">₱<?php echo number_format($borrowing['fine_amount'], 2); ?></div>
                                            <div class="text-xs text-gray-500">
                                                <?php echo $borrowing['fine_status'] === 'paid' ? 'Paid' : 'Unpaid'; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400">No fine</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <?php if ($borrowing['status'] === 'current' || $borrowing['status'] === 'overdue'): ?>
                                            <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to return \'<?php echo addslashes($borrowing['title']); ?>\'?');">
                                                <input type="hidden" name="borrowing_id" value="<?php echo $borrowing['borrowing_id']; ?>">
                                                <button type="submit" name="confirm_return" 
                                                        class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-xs">
                                                    <i class="fas fa-check mr-1"></i>Return Book
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <?php echo (($page - 1) * $per_page) + 1; ?> to <?php echo min($page * $per_page, $total_borrowings); ?> of <?php echo $total_borrowings; ?> results
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            <?php if ($page > 1): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <!-- Page Numbers -->
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                   class="px-3 py-2 <?php echo $i === $page ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?> rounded-lg transition-colors duration-200">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <!-- Next Button -->
                            <?php if ($page < $total_pages): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Custom Styles -->
<style>
/* Hover effects for table rows */
.hover\:bg-gray-50:hover {
    background-color: #f9fafb;
}

/* Animation for status badges */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.animate-pulse {
    animation: pulse 2s infinite;
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .table-responsive th,
    .table-responsive td {
        padding: 0.5rem;
    }
}
</style>

<script>
// Auto-hide alert messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100, .bg-yellow-100');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease-out';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    });
}, 7000);
</script>

<?php include_once '../includes/user_footer.php'; ?>

