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

// Handle form processing BEFORE any output
// Handle borrow book request
if (isset($_POST['action']) && $_POST['action'] === 'borrow' && isset($_POST['book_id'])) {
    $book_id = sanitizeInput($_POST['book_id']);
    
    error_log("Attempting to borrow book: $book_id for user: $user_id");
    
    // Check if account is suspended first
    if (isAccountSuspended($user_id)) {
        $_SESSION['error_message'] = "Your account is suspended. You cannot borrow books at this time. " . getSuspensionReason($user_id);
    }
    // Check if user can borrow more books
    elseif (!canBorrowMore($user_id)) {
        $_SESSION['error_message'] = "You have reached the maximum borrowing limit of 5 books.";
    } else {
        try {
            // Start transaction
            $pdo->beginTransaction();
            
            // Check if book is available
            $stmt = $pdo->prepare("SELECT book_id, status, title FROM books WHERE book_id = ? AND is_deleted = FALSE");
            $stmt->execute([$book_id]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$book) {
                $_SESSION['error_message'] = "Book not found.";
            } elseif ($book['status'] !== 'available') {
                $_SESSION['error_message'] = "This book is not available for borrowing. Current status: " . $book['status'];
            } else {
                // Create borrowing record
                $borrow_date = date('Y-m-d');
                $due_date = date('Y-m-d', strtotime('+14 days')); // 2 weeks borrowing period
                
                $stmt = $pdo->prepare("INSERT INTO borrowings (book_id, user_id, borrow_date, due_date) VALUES (?, ?, ?, ?)");
                $result1 = $stmt->execute([$book_id, $user_id, $borrow_date, $due_date]);
                
                // Update book status
                $stmt = $pdo->prepare("UPDATE books SET status = 'borrowed' WHERE book_id = ?");
                $result2 = $stmt->execute([$book_id]);
                
                if ($result1 && $result2) {
                    $pdo->commit();
                    error_log("Book borrowed successfully: $book_id by user $user_id");
                    
                    // Redirect to prevent form resubmission
                    header("Location: browse_books.php?success=borrowed&book=" . urlencode($book_id) . "&title=" . urlencode($book['title']));
                    exit();
                } else {
                    $pdo->rollBack();
                    $_SESSION['error_message'] = "Failed to complete borrowing transaction.";
                }
            }
        } catch (Exception $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            $_SESSION['error_message'] = "Error borrowing book: " . $e->getMessage();
            error_log("Exception borrowing book $book_id: " . $e->getMessage());
        }
    }
    
    // Redirect to clear POST data
    header("Location: browse_books.php");
    exit();
}

// Handle reserve book request
if (isset($_POST['action']) && $_POST['action'] === 'reserve' && isset($_POST['book_id'])) {
    $book_id = sanitizeInput($_POST['book_id']);
    
    error_log("Attempting to reserve book: $book_id for user: $user_id");
    
    // Check if account is suspended first
    if (isAccountSuspended($user_id)) {
        $_SESSION['error_message'] = "Your account is suspended. You cannot reserve books at this time. " . getSuspensionReason($user_id);
    } else {
        try {
            // Check if book exists and get its current status
            $stmt = $pdo->prepare("SELECT book_id, status, title FROM books WHERE book_id = ? AND is_deleted = FALSE");
            $stmt->execute([$book_id]);
            $book = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$book) {
                $_SESSION['error_message'] = "Book not found.";
            } elseif ($book['status'] === 'available') {
                $_SESSION['error_message'] = "This book is available for immediate borrowing. Please borrow it instead of reserving.";
            } else {
                // Check if user already has a pending reservation for this book
                $stmt = $pdo->prepare("SELECT reservation_id FROM reservations WHERE book_id = ? AND user_id = ? AND status = 'pending'");
                $stmt->execute([$book_id, $user_id]);
                if ($stmt->fetch()) {
                    $_SESSION['error_message'] = "You already have a pending reservation for this book.";
                } else {
                    // Add to reservation queue
                    $stmt = $pdo->prepare("INSERT INTO reservations (book_id, user_id, status, reserved_at) VALUES (?, ?, 'pending', NOW())");
                    $success = $stmt->execute([$book_id, $user_id]);
                    
                    if ($success) {
                        // If book is borrowed, mark it as reserved (to indicate there are reservations)
                        if ($book['status'] === 'borrowed') {
                            $update_stmt = $pdo->prepare("UPDATE books SET status = 'reserved' WHERE book_id = ?");
                            $update_stmt->execute([$book_id]);
                        }
                        
                        error_log("Book reserved successfully: $book_id by user $user_id");
                        
                        // Redirect to prevent form resubmission
                        header("Location: browse_books.php?success=reserved&book=" . urlencode($book_id) . "&title=" . urlencode($book['title']));
                        exit();
                    } else {
                        $_SESSION['error_message'] = "Failed to reserve book. Please try again.";
                    }
                }
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error reserving book: " . $e->getMessage();
            error_log("Exception reserving book $book_id: " . $e->getMessage());
        }
    }
    
    // Redirect to clear POST data
    header("Location: browse_books.php");
    exit();
}

// Now include header after all possible redirects
$page_title = 'Browse Books';
include_once '../includes/user_header.php';

// Check account suspension status
$is_suspended = isAccountSuspended($user_id);
$suspension_reason = $is_suspended ? getSuspensionReason($user_id) : null;

// Initialize variables
$search_query = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$sort_by = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'title';
$sort_order = isset($_GET['order']) ? sanitizeInput($_GET['order']) : 'ASC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Handle success messages from redirects
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'borrowed' && isset($_GET['book'])) {
        $book_title = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : 'the book';
        $success_message = "'{$book_title}' borrowed successfully! <a href='my_borrowings.php' class='underline font-semibold'>View in My Borrowings</a>";
    } elseif ($_GET['success'] === 'reserved' && isset($_GET['book'])) {
        $book_title = isset($_GET['title']) ? htmlspecialchars($_GET['title']) : 'the book';
        $success_message = "'{$book_title}' reserved successfully! <a href='reservations.php' class='underline font-semibold'>View in Reservations</a>";
    }
}

// Get messages from session (from form processing)
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Initialize variables
$search_query = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$sort_by = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'title';
$sort_order = isset($_GET['order']) ? sanitizeInput($_GET['order']) : 'ASC';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// Build search query
$where_conditions = ["is_deleted = FALSE"];
$params = [];

if (!empty($search_query)) {
    $where_conditions[] = "(title LIKE ? OR author LIKE ?)";
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
}

if (!empty($category_filter)) {
    $where_conditions[] = "category = ?";
    $params[] = $category_filter;
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

$where_clause = implode(' AND ', $where_conditions);

// Valid sort columns
$valid_sort_columns = ['title', 'author', 'publish_date', 'added_date', 'category'];
if (!in_array($sort_by, $valid_sort_columns)) {
    $sort_by = 'title';
}

$sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

// Get books with pagination - exclude books that user has already borrowed (but not reserved ones)
$sql = "SELECT b.book_id, b.title, b.author, b.publish_date, b.category, b.book_cover, b.added_date, b.status,
               CASE WHEN br.book_id IS NOT NULL THEN 1 ELSE 0 END as user_borrowed,
               CASE WHEN r.book_id IS NOT NULL THEN 1 ELSE 0 END as user_reserved
        FROM books b
        LEFT JOIN borrowings br ON b.book_id = br.book_id AND br.user_id = ? AND br.return_date IS NULL
        LEFT JOIN reservations r ON b.book_id = r.book_id AND r.user_id = ? AND r.status = 'pending'
        WHERE $where_clause 
        ORDER BY $sort_by $sort_order 
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
$params_with_user = array_merge([$user_id, $user_id], $params);
$stmt->execute($params_with_user);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) FROM books WHERE $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_books = $count_stmt->fetchColumn();
$total_pages = ceil($total_books / $per_page);

// Get all categories for filter dropdown
$categories_stmt = $pdo->query("SELECT DISTINCT category FROM books WHERE is_deleted = FALSE ORDER BY category");
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Browse Books</h1>
                    <p class="text-gray-600">Discover and borrow books from our library collection</p>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600"><?php echo count($books); ?></div>
                        <div class="text-sm text-gray-500">Books Found</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <?php if (!empty($success_message) && trim($success_message) !== ''): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 alert-message">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success_message; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message) && trim($error_message) !== ''): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 alert-message">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error_message; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php // Display account suspension notification ?>
        <?php if ($is_suspended): ?>
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-lg shadow-md">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-ban text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-red-800 mb-2">Account Suspended</h3>
                        <p class="text-sm text-red-700 mb-3">
                            Your account has been temporarily suspended. You cannot borrow or reserve books at this time.
                        </p>
                        <div class="bg-red-100 p-3 rounded-md">
                            <p class="text-sm text-red-800"><strong>Reason:</strong> <?php echo htmlspecialchars($suspension_reason); ?></p>
                        </div>
                        <div class="mt-3">
                            <p class="text-sm text-red-700">
                                Please contact the library administration to resolve this issue or return any overdue books.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search Books</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" 
                               placeholder="Search by title or author..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category; ?>" <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                                    <?php echo isset($category_names[$category]) ? $category_names[$category] : $category; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Books</option>
                            <option value="available" <?php echo $status_filter === 'available' ? 'selected' : ''; ?>>Available</option>
                            <option value="borrowed" <?php echo $status_filter === 'borrowed' ? 'selected' : ''; ?>>Borrowed</option>
                            <option value="reserved" <?php echo $status_filter === 'reserved' ? 'selected' : ''; ?>>Reserved</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    <a href="browse_books.php" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Books Grid -->
        <?php if (empty($books)): ?>
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-semibold text-gray-600 mb-2">No Books Found</h3>
                <p class="text-gray-500 mb-6">Try adjusting your search criteria or browse all books.</p>
                <a href="browse_books.php" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-list mr-2"></i>View All Books
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                <?php foreach ($books as $book): ?>
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Book Cover -->
                        <div class="aspect-w-3 aspect-h-4 bg-gradient-to-br from-blue-100 to-green-100 p-4">
                            <?php if ($book['book_cover'] && $book['book_cover'] !== 'default_book_cover.svg'): ?>
                                <img src="../../../../assets/img/book_covers/<?php echo htmlspecialchars($book['book_cover']); ?>" 
                                     alt="<?php echo htmlspecialchars($book['title']); ?>"
                                     class="w-full h-48 object-cover rounded-lg">
                            <?php else: ?>
                                <div class="w-full h-48 flex items-center justify-center bg-gray-200 rounded-lg">
                                    <i class="fas fa-book text-4xl text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="p-6">
                            <!-- Book Info -->
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                    <?php echo htmlspecialchars($book['title']); ?>
                                </h3>
                                <p class="text-sm text-gray-600 mb-1">by <?php echo htmlspecialchars($book['author']); ?></p>
                                <p class="text-xs text-gray-500">
                                    <?php echo isset($category_names[$book['category']]) ? $category_names[$book['category']] : $book['category']; ?>
                                </p>
                            </div>

                            <!-- Status Badge -->
                            <div class="mb-4">
                                <?php if ($book['status'] === 'available'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Available</span>
                                <?php elseif ($book['status'] === 'borrowed'): ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">Borrowed</span>
                                <?php elseif ($book['status'] === 'reserved'): ?>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full">Reserved</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex flex-col gap-2">
                                <?php if ($is_suspended): ?>
                                    <!-- Account suspended -->
                                    <button disabled class="w-full px-4 py-2 bg-red-200 text-red-600 rounded-lg cursor-not-allowed" 
                                            title="Account suspended - <?php echo htmlspecialchars($suspension_reason); ?>">
                                        <i class="fas fa-ban mr-2"></i>Account Suspended
                                    </button>
                                <?php elseif ($book['user_borrowed']): ?>
                                    <!-- User already borrowed this book -->
                                    <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-check mr-2"></i>Already Borrowed
                                    </button>
                                <?php elseif ($book['user_reserved']): ?>
                                    <!-- User already reserved this book -->
                                    <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                                        <i class="fas fa-clock mr-2"></i>Already Reserved
                                    </button>
                                <?php elseif ($book['status'] === 'available'): ?>
                                    <!-- Available for borrowing -->
                                    <form method="POST" class="w-full borrow-form">
                                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                        <input type="hidden" name="action" value="borrow">
                                        <button type="submit" 
                                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 borrow-btn">
                                            <i class="fas fa-download mr-2"></i>Borrow Book
                                        </button>
                                    </form>
                                <?php elseif ($book['status'] === 'borrowed' || $book['status'] === 'reserved'): ?>
                                    <!-- Available for reservation -->
                                    <form method="POST" class="w-full reserve-form">
                                        <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                        <input type="hidden" name="action" value="reserve">
                                        <button type="submit" 
                                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 reserve-btn">
                                            <i class="fas fa-clock mr-2"></i>Reserve Book
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button disabled class="w-full px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                                        Not Available
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <?php echo (($page - 1) * $per_page) + 1; ?> to <?php echo min($page * $per_page, $total_books); ?> of <?php echo $total_books; ?> results
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
/* Line clamp for book titles */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Hover effects for book cards */
.hover\:shadow-xl:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Button loading states */
.btn-loading {
    position: relative;
    color: transparent !important;
}

.btn-loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid #ffffff;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Alert animation */
.alert-message {
    animation: slideInDown 0.5s ease-out;
}

@keyframes slideInDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>

<script>
// Handle form submissions with confirmation and loading states
document.addEventListener('DOMContentLoaded', function() {
    // Handle borrow forms
    document.querySelectorAll('.borrow-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('.borrow-btn');
            const bookTitle = form.closest('.bg-white').querySelector('h3').textContent.trim();
            
            if (!confirm(`Are you sure you want to borrow "${bookTitle}"?\n\nThis book will be due in 14 days.`)) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            button.disabled = true;
            button.classList.add('btn-loading');
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        });
    });

    // Handle reserve forms
    document.querySelectorAll('.reserve-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('.reserve-btn');
            const bookTitle = form.closest('.bg-white').querySelector('h3').textContent.trim();
            
            if (!confirm(`Are you sure you want to reserve "${bookTitle}"?\n\nYou will be notified when it becomes available.`)) {
                e.preventDefault();
                return false;
            }
            
            // Show loading state
            button.disabled = true;
            button.classList.add('btn-loading');
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        });
    });
});

// Auto-hide alert messages
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-message');
    alerts.forEach(alert => {
        alert.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(() => alert.remove(), 500);
    });
}, 5000);
</script>



