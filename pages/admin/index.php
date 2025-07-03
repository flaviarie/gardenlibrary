<?php
// Set page title
$page_title = 'Admin Dashboard';

// Include header and functions first
include_once 'includes/admin_header.php';
include_once 'includes/admin_functions.php';
include_once '../../includes/db_connection.php';

// Check admin access
requireAdminAccess();

// Get system statistics
$stats = getSystemStats($pdo);

// Extract variables for backward compatibility
$total_users = $stats['total_users'];
$total_librarians = $stats['total_librarians'];

// Get additional dashboard data
try {
    // Today's new registrations
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()");
    $today_registrations = $stmt->fetchColumn();
    
    // Overdue books
    $stmt = $pdo->query("SELECT COUNT(*) FROM borrowings WHERE return_date IS NULL AND due_date < CURDATE()");
    $overdue_books = $stmt->fetchColumn();
    
    // Recent user registrations for activity feed
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
    
    // Recent borrowing activity
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

<!-- Main Content -->
<div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Welcome Header -->
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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
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

            <!-- Total Librarians -->
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

            <!-- Total Books -->
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

            <!-- Active Borrowings -->
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

        <!-- Quick Actions -->
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

        <!-- Activity Feed -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent User Registrations -->
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

            <!-- Recent Borrowing Activity -->
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

<script>
// Enhanced JavaScript for improved responsiveness and loading
document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin dashboard loaded');
    
    // Enhanced mobile touch interactions
    function enhanceMobileExperience() {
        // Add touch feedback for interactive elements
        const buttons = document.querySelectorAll('button, .cursor-pointer, a[class*="hover:"]');
        buttons.forEach(button => {
            // Add minimum touch target size for mobile
            if (window.innerWidth <= 768) {
                const rect = button.getBoundingClientRect();
                if (rect.height < 44) {
                    button.style.minHeight = '44px';
                    button.style.display = 'flex';
                    button.style.alignItems = 'center';
                    button.style.justifyContent = 'center';
                }
            }
            
            // Add touch feedback
            button.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            }, { passive: true });
            
            button.addEventListener('touchend', function() {
                this.style.transform = '';
            }, { passive: true });
        });
        
        // Enhanced table overflow handling
        const tableContainer = document.querySelector('.overflow-x-auto');
        if (tableContainer && window.innerWidth >= 640) {
            let isScrolling = false;
            
            tableContainer.addEventListener('scroll', function() {
                if (!isScrolling) {
                    isScrolling = true;
                    this.style.boxShadow = 'inset -10px 0 10px -10px rgba(0,0,0,0.1)';
                    
                    setTimeout(() => {
                        if (this.scrollLeft === 0) {
                            this.style.boxShadow = '';
                        }
                        isScrolling = false;
                    }, 150);
                }
            }, { passive: true });
        }
    }
    
    // Responsive layout adjustments
    function handleResponsiveLayout() {
        const viewport = window.innerWidth;
        const systemOverview = document.querySelector('.bg-white.rounded-2xl');
        
        if (viewport <= 640) {
            // Mobile optimizations
            systemOverview?.classList.add('mx-2');
            
            // Ensure proper spacing for mobile cards
            const mobileCards = document.querySelectorAll('.block.sm\\:hidden .bg-white');
            mobileCards.forEach(card => {
                card.style.touchAction = 'manipulation';
            });
        } else {
            // Desktop optimizations
            systemOverview?.classList.remove('mx-2');
        }
    }
    
    // Debounced resize handler
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            handleResponsiveLayout();
            enhanceMobileExperience();
        }, 150);
    }, { passive: true });
    
    // Initial setup
    handleResponsiveLayout();
    enhanceMobileExperience();
    
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
    
    // Add loading states for dynamic content
    const addUserButton = document.querySelector('button[class*="bg-white"][class*="text-red-700"]');
    if (addUserButton) {
        addUserButton.addEventListener('click', function() {
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>';
            this.disabled = true;
            
            // Simulate loading (replace with actual functionality)
            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.disabled = false;
            }, 2000);
        });
    }
    
    // Enhanced accessibility
    document.querySelectorAll('button, a[role="button"]').forEach(element => {
        if (!element.getAttribute('aria-label') && !element.textContent.trim()) {
            const icon = element.querySelector('i[class*="fa-"]');
            if (icon) {
                const iconClass = Array.from(icon.classList).find(cls => cls.startsWith('fa-'));
                if (iconClass) {
                    element.setAttribute('aria-label', iconClass.replace('fa-', '').replace('-', ' '));
                }
            }
        }
    });
});

// Handle resource loading errors with improved UX
window.addEventListener('error', function(e) {
    if (e.target.tagName === 'LINK' || e.target.tagName === 'SCRIPT') {
        console.error('Failed to load resource:', e.target.src || e.target.href);
        
        // Create a more user-friendly notification
        let notification = document.querySelector('.resource-error-notification');
        if (!notification) {
            notification = document.createElement('div');
            notification.className = 'resource-error-notification fixed top-4 right-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-lg z-50 max-w-sm';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <div class="flex-1">
                        <strong class="block">Resource Loading Issue</strong>
                        <span class="text-sm">Some resources failed to load, but the page should still work.</span>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-yellow-700 hover:text-yellow-900 text-xl leading-none">&times;</button>
                </div>
            `;
            document.body.appendChild(notification);
            
            // Auto-remove after 8 seconds
            setTimeout(function() {
                if (notification && notification.parentElement) {
                    notification.remove();
                }
            }, 8000);
        }
    }
}, true);

// Add CSS for responsive notifications
const style = document.createElement('style');
style.textContent = `
    @media (max-width: 640px) {
        .resource-error-notification {
            position: fixed !important;
            top: 1rem !important;
            left: 1rem !important;
            right: 1rem !important;
            max-width: none !important;
        }
    }
    
    /* Improve touch targets on mobile */
    @media (max-width: 768px) {
        button, a[class*="hover:"], .cursor-pointer {
            min-height: 44px !important;
            min-width: 44px !important;
        }
        
        /* Prevent text selection on interactive elements */
        button, .cursor-pointer {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Improve scroll behavior */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }
    }
    
    /* Enhanced hover states for desktop */
    @media (min-width: 1024px) {
        .hover\\:scale-105:hover {
            transform: scale(1.05) !important;
        }
    }
`;
document.head.appendChild(style);
</script>



