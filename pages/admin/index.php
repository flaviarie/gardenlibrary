<?php
$page_title = 'Admin Dashboard';

// Core includes
include_once 'includes/admin_header.php';
include_once 'includes/admin_functions.php';
include_once '../../includes/db_connection.php';

// Verify access
requireAdminAccess();

// System stats
$stats = getSystemStats($pdo);
$total_users = $stats['total_users'];
$total_librarians = $stats['total_librarians'];

// Dashboard data
try {
    // Today registrations
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()");
    $today_registrations = $stmt->fetchColumn();
    
    // Overdue count
    $stmt = $pdo->query("SELECT COUNT(*) FROM borrowings WHERE return_date IS NULL AND due_date < CURDATE()");
    $overdue_books = $stmt->fetchColumn();
    
    // Recent users
    $stmt = $pdo->prepare("
        SELECT u.*, 
               DATE_FORMAT(u.created_at, '%M %d, %Y at %h:%i %p') as formatted_date,
               0 as is_deleted
        FROM users u 
        ORDER BY u.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent borrowings
    $stmt = $pdo->prepare("
        SELECT b.*, u.username, bk.title,
               DATE_FORMAT(b.borrow_date, '%M %d, %Y') as formatted_date
        FROM borrowings b
        JOIN users u ON b.user_id = u.user_id
        JOIN books bk ON b.book_id = bk.book_id
        ORDER BY b.borrow_date DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $today_registrations = 0;
    $overdue_books = 0;
    $recent_users = [];
    $recent_borrowings = [];
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!-- Main Dashboard -->
<div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Admin Dashboard</h1>
                    <p class="text-gray-600">Welcome to the Garden Library Admin Panel</p>
                    <?php if (isset($_SESSION['username'])): ?>
                        <p class="text-sm text-blue-600 mt-1">Logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?php echo date('l, F j, Y'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Users Count -->
            <a href="modules/manage_users.php" class="block group">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300 transform group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-3xl font-bold text-blue-600"><?php echo number_format($stats['total_users']); ?></p>
                            <p class="text-xs text-green-600 mt-1">
                                <i class="fas fa-plus mr-1"></i>
                                <?php echo $today_registrations; ?> today
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Librarians Count -->
            <a href="modules/manage_librarians.php" class="block group">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-all duration-300 transform group-hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Librarians</p>
                            <p class="text-3xl font-bold text-green-600"><?php echo number_format($stats['total_librarians']); ?></p>
                            <p class="text-xs text-gray-500 mt-1">Staff members</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-user-tie text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Books Count -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Books</p>
                        <p class="text-3xl font-bold text-purple-600"><?php echo number_format($stats['total_books']); ?></p>
                        <p class="text-xs text-gray-500 mt-1">In collection</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Borrowings Count -->
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Borrowings</p>
                        <p class="text-3xl font-bold text-red-600"><?php echo number_format($stats['active_borrowings']); ?></p>
                        <p class="text-xs text-orange-600 mt-1">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <?php echo $overdue_books; ?> overdue
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Menu -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="modules/manage_users.php" class="flex items-center gap-3 p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-plus text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Manage Users</p>
                        <p class="text-sm text-gray-600">Add, edit, or remove users</p>
                    </div>
                </a>

                <a href="modules/manage_librarians.php" class="flex items-center gap-3 p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors duration-200">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tie text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Manage Librarians</p>
                        <p class="text-sm text-gray-600">Promote users to librarians</p>
                    </div>
                </a>

                <a href="modules/generate_reports.php" class="flex items-center gap-3 p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors duration-200">
                    <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Generate Reports</p>
                        <p class="text-sm text-gray-600">View system analytics</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Activity Feeds -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- User Registrations -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Recent User Registrations</h2>
                    <a href="modules/manage_users.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <?php if (!empty($recent_users)): ?>
                    <div class="space-y-4">
                        <?php foreach ($recent_users as $user): ?>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($user['username']); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo $user['formatted_date']; ?></p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    <?php echo $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 
                                             ($user['role'] === 'librarian' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800'); ?>">
                                    <?php 
                                    if ($user['role'] === 'admin') {
                                        echo ($user['username'] === 'admin') ? 'Administrator' : 'Librarian';
                                    } else {
                                        echo ucfirst($user['role']);
                                    }
                                    ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No recent registrations</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Borrowing Activity -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Borrowing Activity</h2>
                    <span class="text-sm text-gray-500">Last 5 activities</span>
                </div>

                <?php if (!empty($recent_borrowings)): ?>
                    <div class="space-y-4">
                        <?php foreach ($recent_borrowings as $borrowing): ?>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-book text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($borrowing['title']); ?></p>
                                    <p class="text-sm text-gray-600">Borrowed by <?php echo htmlspecialchars($borrowing['username']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo $borrowing['formatted_date']; ?></p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                    Active
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-book-open text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No recent borrowing activity</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- External Scripts -->
<link rel="stylesheet" href="assets/css/dashboard-core.css">
<script src="assets/js/dashboard-core.js"></script>



