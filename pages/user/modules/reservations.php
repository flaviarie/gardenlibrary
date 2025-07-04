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

// Handle reservation cancellation BEFORE any output
if (isset($_POST['cancel_reservation']) && isset($_POST['reservation_id'])) {
    $reservation_id = (int)$_POST['reservation_id'];
    
    try {
        // Start transaction
        $pdo->beginTransaction();
        
        // Get reservation details
        $stmt = $pdo->prepare("
            SELECT r.book_id, b.title 
            FROM reservations r 
            JOIN books b ON r.book_id = b.book_id 
            WHERE r.reservation_id = ? AND r.user_id = ? AND r.status = 'pending'
        ");
        $stmt->execute([$reservation_id, $user_id]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) {
            throw new Exception("Reservation not found or cannot be cancelled.");
        }
        
        // Update reservation status to cancelled
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
        $result1 = $stmt->execute([$reservation_id]);
        
        // Check if there are other pending reservations for this book
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE book_id = ? AND status = 'pending'");
        $stmt->execute([$reservation['book_id']]);
        $remaining_reservations = $stmt->fetchColumn();
        
        // If no more reservations and book is currently marked as reserved, check if we should change status
        if ($remaining_reservations == 0) {
            $stmt = $pdo->prepare("SELECT status FROM books WHERE book_id = ?");
            $stmt->execute([$reservation['book_id']]);
            $book_status = $stmt->fetchColumn();
            
            // If book is marked as reserved but has no pending reservations, change it to borrowed
            if ($book_status === 'reserved') {
                $stmt = $pdo->prepare("UPDATE books SET status = 'borrowed' WHERE book_id = ?");
                $result2 = $stmt->execute([$reservation['book_id']]);
            }
        }
        
        if ($result1) {
            $pdo->commit();
            $_SESSION['success_message'] = "Reservation for '{$reservation['title']}' has been cancelled successfully.";
        } else {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Failed to cancel reservation. Please try again.";
        }
        
    } catch (Exception $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        $_SESSION['error_message'] = "Error cancelling reservation: " . $e->getMessage();
    }
    
    // Redirect to clear POST data
    header("Location: reservations.php");
    exit();
}

// Now include header after all possible redirects
$page_title = 'My Reservations';
include_once '../includes/user_header.php';

// Initialize variables
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
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

// Build query for reservations
$where_conditions = ["r.user_id = ?"];
$params = [$user_id];

if (!empty($status_filter)) {
    $where_conditions[] = "r.status = ?";
    $params[] = $status_filter;
}

$where_clause = implode(' AND ', $where_conditions);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) FROM reservations r WHERE $where_clause";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_reservations = $count_stmt->fetchColumn();
$total_pages = ceil($total_reservations / $per_page);

// Get reservations with pagination
$sql = "SELECT r.reservation_id, r.book_id, r.status, r.reserved_at, r.notified_at, r.expires_at,
               b.title, b.author, b.category, b.book_cover, b.status as book_status
        FROM reservations r
        JOIN books b ON r.book_id = b.book_id
        WHERE $where_clause
        ORDER BY r.reserved_at DESC
        LIMIT $per_page OFFSET $offset";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get summary statistics
$stats_sql = "SELECT 
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_reservations,
    COUNT(CASE WHEN status = 'fulfilled' THEN 1 END) as fulfilled_reservations,
    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_reservations,
    COUNT(CASE WHEN status = 'notified' THEN 1 END) as notified_reservations
FROM reservations r
WHERE r.user_id = ?";

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

// Status badge colors
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'notified':
            return 'bg-blue-100 text-blue-800';
        case 'fulfilled':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}
?>

<!-- Page Content -->
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">My Reservations</h1>
                    <p class="text-gray-600">Manage your book reservations and track their status</p>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600"><?php echo $stats['pending_reservations']; ?></div>
                        <div class="text-sm text-gray-500">Pending</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600"><?php echo $stats['fulfilled_reservations']; ?></div>
                        <div class="text-sm text-gray-500">Fulfilled</div>
                    </div>
                    <?php if ($stats['cancelled_reservations'] > 0): ?>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600"><?php echo $stats['cancelled_reservations']; ?></div>
                            <div class="text-sm text-gray-500">Cancelled</div>
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

        <!-- Filter Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Reservations</option>
                            <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="notified" <?php echo $status_filter === 'notified' ? 'selected' : ''; ?>>Notified</option>
                            <option value="fulfilled" <?php echo $status_filter === 'fulfilled' ? 'selected' : ''; ?>>Fulfilled</option>
                            <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        Apply Filters
                    </button>
                    <a href="reservations.php" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Reservations List -->
        <?php if (empty($reservations)): ?>
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div class="text-6xl text-gray-300 mb-4">📅</div>
                <h3 class="text-2xl font-semibold text-gray-600 mb-2">No Reservations Found</h3>
                <p class="text-gray-500 mb-6">You haven't made any reservations yet or no reservations match your filters.</p>
                <a href="browse_books.php" class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    Browse Books
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reserved Date</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($reservations as $reservation): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-12 h-16 bg-gradient-to-br from-blue-100 to-green-100 rounded-lg flex items-center justify-center mr-4">
                                                <img src="../../librarian/assets/img/<?php echo htmlspecialchars($reservation['book_cover'] ?? 'default_book_cover.svg'); ?>" 
                                                     alt="<?php echo htmlspecialchars($reservation['title']); ?>"
                                                     class="w-full h-full object-cover rounded-lg"
                                                     onerror="this.src='../../librarian/assets/img/default_book_cover.svg'">
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($reservation['title']); ?></div>
                                                <div class="text-sm text-gray-500">by <?php echo htmlspecialchars($reservation['author']); ?></div>
                                                <div class="text-xs text-gray-400">
                                                    <?php echo isset($category_names[$reservation['category']]) ? $category_names[$reservation['category']] : $reservation['category']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <?php echo formatDate($reservation['reserved_at']); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo getStatusBadgeClass($reservation['status']); ?>">
                                            <?php echo ucfirst($reservation['status']); ?>
                                        </span>
                                        <?php if ($reservation['status'] === 'notified' && $reservation['expires_at']): ?>
                                            <div class="text-xs text-gray-500 mt-1">
                                                Expires: <?php echo formatDate($reservation['expires_at']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            <?php echo $reservation['book_status'] === 'available' ? 'bg-green-100 text-green-800' : 
                                                     ($reservation['book_status'] === 'borrowed' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                            <?php echo ucfirst($reservation['book_status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <?php if ($reservation['status'] === 'pending'): ?>
                                            <form method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to cancel reservation for \'<?php echo addslashes($reservation['title']); ?>\'?');">
                                                <input type="hidden" name="reservation_id" value="<?php echo $reservation['reservation_id']; ?>">
                                                <button type="submit" name="cancel_reservation" 
                                                        class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-xs">
                                                    Cancel
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
                            Showing <?php echo (($page - 1) * $per_page) + 1; ?> to <?php echo min($page * $per_page, $total_reservations); ?> of <?php echo $total_reservations; ?> results
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <!-- Previous Button -->
                            <?php if ($page > 1): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                                    Previous
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
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Help Section -->
        
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
    const alerts = document.querySelectorAll('.bg-green-100.border-green-400, .bg-red-100.border-red-400, .bg-yellow-100.border-yellow-400');
    alerts.forEach(alert => {
        // Only hide actual alert messages, not status badges
        if (alert.classList.contains('border-green-400') || alert.classList.contains('border-red-400') || alert.classList.contains('border-yellow-400')) {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    });
}, 7000);
</script>
    });
}, 7000);
</script>

