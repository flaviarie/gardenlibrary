<?php
session_start();
// Show errors on test
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = 'User Dashboard';
include_once 'includes/user_header.php';
include_once 'includes/user_functions.php';

// Check if user is logged in
requireUserAccess();

// Connect to DB
include_once '../../includes/db_connection.php';

// Debug only when needed
$debug_mode = false; // toggle for tests

if ($debug_mode) {
    echo "<!-- Debug: User page loaded at " . date('Y-m-d H:i:s') . " -->";
    echo "<!-- Debug: User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . " -->";
}

// Get active user
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($debug_mode) {
    echo "<!-- Debug: Final User ID: " . $user_id . " -->";
}

// Check if account is suspended
$is_suspended = isAccountSuspended($user_id);
$suspension_reason = $is_suspended ? getSuspensionReason($user_id) : null;

try {
    // Count all books
    $stmt = $pdo->query("SELECT COUNT(*) FROM books WHERE is_deleted = FALSE");
    $total_books = $stmt->fetchColumn();
    
    // User's active loans
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND return_date IS NULL");
    $stmt->execute([$user_id]);
    $my_borrowings = $stmt->fetchColumn();
    
    // Active book holds - check if reservations table exists first
    $my_reservations = 0;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE user_id = ? AND status = 'pending'");
        $stmt->execute([$user_id]);
        $my_reservations = $stmt->fetchColumn();
    } catch (PDOException $e) {
        // Reservations table doesn't exist, set to 0
        $my_reservations = 0;
    }
    
    // User review count - check if reviews table exists first
    $my_reviews = 0;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $my_reviews = $stmt->fetchColumn();
    } catch (PDOException $e) {
        // Reviews table doesn't exist, set to 0
        $my_reviews = 0;
    }
    
    // Last 5 checkouts
    $stmt = $pdo->prepare("
        SELECT b.title, br.borrow_date, br.due_date, br.return_date
        FROM borrowings br
        JOIN books b ON br.book_id = b.book_id
        WHERE br.user_id = ?
        ORDER BY br.borrow_date DESC
        LIMIT 5
    ");
    $stmt->execute([$user_id]);
    $recent_borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Reset if DB fails
    $total_books = 0;
    $my_borrowings = 0;
    $my_reservations = 0;
    $my_reviews = 0;
    $recent_borrowings = [];
    $error_message = "Database error: " . $e->getMessage();
    
    // Log the error for debugging
    error_log("Dashboard DB Error: " . $e->getMessage());
}
?>

<?php // Display any error messages ?>
<?php if (isset($error_message)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span><?php echo htmlspecialchars($error_message); ?></span>
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

<?php // --- DASHBOARD UI STARTS HERE --- ?>
<?php // --- STATS SECTION --- ?>

<!-- User Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 mb-8 lg:mb-12">
    <!-- Total Books -->
    <?php if ($is_suspended): ?>
        <div class="block opacity-60 cursor-not-allowed">
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-2 uppercase tracking-wide">Books Available</p>
                        <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800"><?php echo number_format($total_books); ?></p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-gray-400 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-book text-white text-lg sm:text-xl lg:text-2xl"></i>
                    </div>
                </div>
                <div class="mt-3 lg:mt-4 flex items-center text-gray-500">
                    <i class="fas fa-ban text-xs mr-2"></i>
                    <span class="text-xs font-medium">Access suspended</span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <a href="modules/browse_books.php" class="block">
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow-lg border border-green-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-green-600 mb-2 uppercase tracking-wide">Books Available</p>
                        <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-green-800 group-hover:text-green-900"><?php echo number_format($total_books); ?></p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-green-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-green-600 transition-colors duration-300">
                        <i class="fas fa-book text-white text-lg sm:text-xl lg:text-2xl"></i>
                    </div>
                </div>
                <div class="mt-3 lg:mt-4 flex items-center text-green-600">
                    <i class="fas fa-arrow-right text-xs mr-2"></i>
                    <span class="text-xs font-medium">Browse books</span>
                </div>
            </div>
        </a>
    <?php endif; ?>
    
    <!-- My Borrowings -->
    <a href="modules/my_borrowings.php" class="block">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow-lg border border-blue-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-blue-600 mb-2 uppercase tracking-wide">My Borrowings</p>
                    <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-blue-800 group-hover:text-blue-900"><?php echo number_format($my_borrowings); ?></p>
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-blue-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-blue-600 transition-colors duration-300">
                    <i class="fas fa-exchange-alt text-white text-lg sm:text-xl lg:text-2xl"></i>
                </div>
            </div>
            <div class="mt-3 lg:mt-4 flex items-center text-blue-600">
                <i class="fas fa-arrow-right text-xs mr-2"></i>
                <span class="text-xs font-medium">View borrowings</span>
            </div>
        </div>
    </a>
    
    <!-- My Reservations -->
    <?php if ($is_suspended): ?>
        <div class="block opacity-60 cursor-not-allowed">
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow-lg border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-2 uppercase tracking-wide">My Reservations</p>
                        <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800"><?php echo number_format($my_reservations); ?></p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-gray-400 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-calendar-alt text-white text-lg sm:text-xl lg:text-2xl"></i>
                    </div>
                </div>
                <div class="mt-3 lg:mt-4 flex items-center text-gray-500">
                    <i class="fas fa-ban text-xs mr-2"></i>
                    <span class="text-xs font-medium">Access suspended</span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <a href="modules/reservations.php" class="block">
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow-lg border border-orange-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-orange-600 mb-2 uppercase tracking-wide">My Reservations</p>
                        <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-orange-800 group-hover:text-orange-900"><?php echo number_format($my_reservations); ?></p>
                    </div>
                    <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-orange-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-orange-600 transition-colors duration-300">
                        <i class="fas fa-calendar-alt text-white text-lg sm:text-xl lg:text-2xl"></i>
                    </div>
                </div>
                <div class="mt-3 lg:mt-4 flex items-center text-orange-600">
                    <i class="fas fa-arrow-right text-xs mr-2"></i>
                    <span class="text-xs font-medium">View reservations</span>
                </div>
            </div>
        </a>
    <?php endif; ?>
    
    <!-- My Reviews -->
    <a href="modules/reviews.php" class="block">
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow-lg border border-indigo-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-indigo-600 mb-2 uppercase tracking-wide">My Reviews</p>
                    <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-indigo-800 group-hover:text-indigo-900"><?php echo number_format($my_reviews); ?></p>
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-indigo-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-indigo-600 transition-colors duration-300">
                    <i class="fas fa-star text-white text-lg sm:text-xl lg:text-2xl"></i>
                </div>
            </div>
            <div class="mt-3 lg:mt-4 flex items-center text-indigo-600">
                <i class="fas fa-arrow-right text-xs mr-2"></i>
                <span class="text-xs font-medium">View reviews</span>
            </div>
        </div>
    </a>
</div>

<?php // --- BORROWING HISTORY TABLE --- ?>
<!-- Recent Borrowings Section -->
<div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden font-raleway">
    <div class="bg-gradient-to-r from-green-700 to-green-600 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-white mb-2 font-raleway">Recent Borrowings</h2>
                <p class="text-blue-100 font-raleway">Your latest borrowed books and due dates</p>
            </div>            
        </div>
    </div>
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Title</th>
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Borrowed On</th>
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Due Date</th>
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($recent_borrowings)): ?>
                        <?php foreach ($recent_borrowings as $borrow): ?>
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300 group">
                                <td class="px-4 lg:px-8 py-4 sm:py-6 text-sm font-medium text-gray-900 group-hover:text-blue-900 font-raleway">
                                    <?php echo htmlspecialchars($borrow['title']); ?>
                                </td>
                                <td class="px-4 lg:px-8 py-4 sm:py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway">
                                    <?php echo htmlspecialchars(date('M d, Y', strtotime($borrow['borrow_date']))); ?>
                                </td>
                                <td class="px-4 lg:px-8 py-4 sm:py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway">
                                    <?php echo htmlspecialchars(date('M d, Y', strtotime($borrow['due_date']))); ?>
                                </td>
                                <td class="px-4 lg:px-8 py-4 sm:py-6">
                                    <?php 
                                    if ($borrow['return_date']) {
                                        echo '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Returned</span>';
                                    } else {
                                        echo '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">Borrowed</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-4 lg:px-8 py-8 sm:py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center space-y-2">
                                    <i class="fas fa-book text-3xl sm:text-4xl text-gray-300"></i>
                                    <p class="text-base sm:text-lg font-medium">No recent borrowings</p>
                                    <p class="text-sm">Start by borrowing a book</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- View All Borrowings Link -->
        <div class="mt-4 sm:mt-6 text-center">
            <a href="modules/my_borrowings.php" class="inline-flex items-center space-x-2 text-blue-600 hover:text-blue-700 font-medium hover:underline">
                <span>View All Borrowings</span>
                <i class="fas fa-arrow-right text-sm"></i>
            </a>        </div>    
    </div>
</div>

<?php // --- END OF DASHBOARD CONTENT --- ?>



