<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = 'Admin Dashboard';
include_once 'includes/admin_header.php';

// Include database connection
include_once '../../includes/db_connection.php';

// Add debugging information
$debug_mode = false; // Set to true for debugging

if ($debug_mode) {
    echo "<!-- Debug: Admin page loaded at " . date('Y-m-d H:i:s') . " -->";
}

// Get admin statistics from database
try {
    // Total Users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_deleted = FALSE");
    $total_users = $stmt->fetchColumn();
    
    // Total Librarians
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'librarian' AND is_deleted = FALSE");
    $total_librarians = $stmt->fetchColumn();
    
    // Total Active Borrowings
    $stmt = $pdo->query("SELECT COUNT(*) FROM borrowings WHERE return_date IS NULL");
    $active_borrowings = $stmt->fetchColumn();
    
    // Total Books in System
    $stmt = $pdo->query("SELECT COUNT(*) FROM books WHERE is_deleted = FALSE");
    $total_books = $stmt->fetchColumn();
    
    // Get recent system activities (recent user registrations)
    $stmt = $pdo->prepare("
        SELECT u.*, 
               DATE_FORMAT(u.created_at, '%M %d, %Y at %h:%i %p') as formatted_date
        FROM users u 
        WHERE u.is_deleted = FALSE 
        ORDER BY u.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $total_users = 0;
    $total_librarians = 0;
    $active_borrowings = 0;
    $total_books = 0;
    $recent_users = [];
    $error_message = "Database error: " . $e->getMessage();
}
?>

<!-- Admin Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8 mb-8 lg:mb-12">
    <!-- Total Users -->
    <a href="./modules/manage_users.php" class="block">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow-lg border border-blue-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-blue-600 mb-2 uppercase tracking-wide">Total Users</p>
                    <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-blue-800 group-hover:text-blue-900"><?php echo number_format($total_users); ?></p>
                </div>            
                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-blue-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-blue-600 transition-colors duration-300">
                    <i class="fas fa-users text-white text-lg sm:text-xl lg:text-2xl"></i>
                </div>
            </div>
            <div class="mt-3 lg:mt-4 flex items-center text-blue-600">
                <i class="fas fa-arrow-right text-xs mr-2"></i>
                <span class="text-xs font-medium">Manage users</span>
            </div>
        </div>
    </a>
    
    <!-- Total Librarians -->
    <a href="modules/manage_users.php?role=librarian" class="block">
        <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow-lg border border-green-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-green-600 mb-2 uppercase tracking-wide">Librarians</p>
                    <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-green-800 group-hover:text-green-900"><?php echo number_format($total_librarians); ?></p>
                </div>
                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-green-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-green-600 transition-colors duration-300">
                    <i class="fas fa-user-tie text-white text-lg sm:text-xl lg:text-2xl"></i>
                </div>
            </div>
            <div class="mt-3 lg:mt-4 flex items-center text-green-600">
                <i class="fas fa-arrow-right text-xs mr-2"></i>
                <span class="text-xs font-medium">Staff management</span>
            </div>
        </div>
    </a>
    
    <!-- Active Borrowings -->
    <a href="modules/reports.php" class="block">
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow-lg border border-yellow-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-yellow-600 mb-2 uppercase tracking-wide">Active Loans</p>
                    <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-yellow-800 group-hover:text-yellow-900"><?php echo number_format($active_borrowings); ?></p>
                </div>            
                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-yellow-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-yellow-600 transition-colors duration-300">
                    <i class="fas fa-exchange-alt text-white text-lg sm:text-xl lg:text-2xl"></i>
                </div>
            </div>
            <div class="mt-3 lg:mt-4 flex items-center text-yellow-600">
                <i class="fas fa-arrow-right text-xs mr-2"></i>
                <span class="text-xs font-medium">View reports</span>
            </div>
        </div>
    </a>
    
    <!-- Total Books -->
    <a href="modules/database_management.php" class="block">
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 sm:p-6 lg:p-8 rounded-2xl shadow-lg border border-purple-200 hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-purple-600 mb-2 uppercase tracking-wide">Total Books</p>
                    <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-purple-800 group-hover:text-purple-900"><?php echo number_format($total_books); ?></p>
                </div>            
                <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-purple-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:bg-purple-600 transition-colors duration-300">
                    <i class="fas fa-database text-white text-lg sm:text-xl lg:text-2xl"></i>
                </div>
            </div>
            <div class="mt-3 lg:mt-4 flex items-center text-purple-600">
                <i class="fas fa-arrow-right text-xs mr-2"></i>
                <span class="text-xs font-medium">Database overview</span>
            </div>
        </div>
    </a>
</div>

<!-- System Overview Section -->
<div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden font-raleway">
    <div class="bg-gradient-to-r from-red-600 to-red-700 p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-white mb-2 font-raleway">System Overview</h2>
                <p class="text-red-100 font-raleway">Monitor and manage system users and activities</p>
            </div>            
            <button class="bg-white text-red-700 px-4 sm:px-6 py-2 sm:py-3 rounded-xl hover:bg-red-50 hover:scale-105 transform transition-all duration-300 font-semibold shadow-lg flex items-center justify-center space-x-2 group font-raleway w-full sm:w-auto">
                <i class="fas fa-plus text-base sm:text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                <span>Add New User</span>
            </button>
        </div>
    </div>
    
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Table Container with proper mobile responsiveness -->
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <!-- Mobile Cards View (visible on small screens) -->
            <div class="block sm:hidden space-y-4">
                <?php if (!empty($recent_users)): ?>
                    <?php foreach ($recent_users as $user): ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 mb-1"><?php echo htmlspecialchars($user['username']); ?></h3>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                <span class="bg-gray-100 px-2 py-1 rounded text-xs font-mono text-gray-600">
                                    ID: <?php echo htmlspecialchars($user['user_id']); ?>
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Role:</span>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-md">
                                        <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Status:</span>
                                    <?php 
                                    $status_class = $user['is_deleted'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                                    $status_text = $user['is_deleted'] ? 'Inactive' : 'Active';
                                    ?>
                                    <span class="px-3 py-1 <?php echo $status_class; ?> text-xs font-bold rounded-full">
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Joined:</span>
                                    <span class="text-sm text-gray-700"><?php echo htmlspecialchars($user['formatted_date']); ?></span>
                                </div>
                                <div class="pt-2 border-t border-gray-100">
                                    <button class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-lg hover:from-blue-600 hover:to-blue-700 text-sm font-medium shadow-md flex items-center justify-center space-x-2">
                                        <i class="fas fa-edit text-xs"></i>
                                        <span>Manage User</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                        <p class="text-lg font-medium text-gray-500">No users found</p>
                        <p class="text-sm text-gray-400">Start by adding some users</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Desktop Table View (hidden on small screens) -->
            <table class="w-full hidden sm:table">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">User ID</th>
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Username</th>
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway hidden md:table-cell">Email</th>
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway hidden lg:table-cell">Role</th>
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Status</th>
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway hidden lg:table-cell">Joined Date</th>
                        <th class="px-4 lg:px-8 py-4 text-left text-xs sm:text-sm font-bold text-gray-700 uppercase tracking-wider font-raleway">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($recent_users)): ?>
                        <?php foreach ($recent_users as $user): ?>
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300 group">
                                <td class="px-4 lg:px-8 py-4 sm:py-6 text-xs font-mono text-gray-600 group-hover:text-blue-800 font-raleway">
                                    <span class="bg-gray-100 px-2 sm:px-3 py-1 sm:py-2 rounded-lg border border-gray-200 font-raleway text-xs">
                                        <?php echo htmlspecialchars($user['user_id']); ?>
                                    </span>
                                </td>
                                <td class="px-4 lg:px-8 py-4 sm:py-6 text-sm font-medium text-gray-900 group-hover:text-blue-900 font-raleway">
                                    <div class="md:hidden text-xs text-gray-500 mb-1"><?php echo htmlspecialchars($user['email']); ?></div>
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </td>
                                <td class="px-4 lg:px-8 py-4 sm:py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway hidden md:table-cell"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="px-4 lg:px-8 py-4 sm:py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway hidden lg:table-cell">
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-md font-raleway"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span>
                                </td>
                                <td class="px-4 lg:px-8 py-4 sm:py-6">
                                    <?php 
                                    $status_class = $user['is_deleted'] ? 'bg-gradient-to-r from-red-100 to-red-200 text-red-800' : 'bg-gradient-to-r from-green-100 to-green-200 text-green-800';
                                    $status_text = $user['is_deleted'] ? 'Inactive' : 'Active';
                                    ?>
                                    <span class="px-2 sm:px-4 py-1 sm:py-2 <?php echo $status_class; ?> text-xs font-bold rounded-full shadow-sm font-raleway">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td class="px-4 lg:px-8 py-4 sm:py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway hidden lg:table-cell">
                                    <?php echo htmlspecialchars($user['formatted_date']); ?>
                                </td>
                                <td class="px-4 lg:px-8 py-4 sm:py-6">
                                    <button class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-2 sm:px-4 py-1 sm:py-2 rounded-lg hover:from-blue-600 hover:to-blue-700 hover:scale-105 transform transition-all duration-300 text-xs sm:text-sm font-medium shadow-md flex items-center space-x-1 sm:space-x-2 font-raleway">
                                        <i class="fas fa-edit text-xs"></i>
                                        <span class="hidden sm:inline">Manage</span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-4 lg:px-8 py-8 sm:py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center space-y-2">
                                    <i class="fas fa-users text-3xl sm:text-4xl text-gray-300"></i>
                                    <p class="text-base sm:text-lg font-medium">No users found</p>
                                    <p class="text-sm">Start by adding some users</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- View All Users Link -->
        <div class="mt-4 sm:mt-6 text-center">
            <a href="./modules/manage_users.php" class="inline-flex items-center space-x-2 text-red-600 hover:text-red-700 font-medium hover:underline">
                <span>View All Users</span>
                <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>    
    </div>
</div>

<script>
// Additional JavaScript to ensure proper loading
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin dashboard loaded');
    
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
include_once 'includes/admin_footer.php';
?>

