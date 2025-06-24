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
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8 mb-8 lg:mb-12">
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
    

</div>

<!-- System Overview Section -->
<div class="bg-white rounded-2xl sm:rounded-3xl shadow-xl sm:shadow-2xl border border-gray-100 overflow-hidden font-raleway mx-2 sm:mx-0">
    <div class="bg-gradient-to-r from-red-600 to-red-700 p-3 sm:p-6 lg:p-8">
        <div class="flex flex-col space-y-3 sm:space-y-4 lg:flex-row lg:justify-between lg:items-center lg:space-y-0">
            <div class="text-center sm:text-left">
                <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-white mb-1 sm:mb-2 font-raleway">System Overview</h2>
                <p class="text-xs sm:text-sm text-red-100 font-raleway">Monitor and manage system users and activities</p>
            </div>            
            <button class="bg-white text-red-700 px-3 sm:px-4 lg:px-6 py-2 sm:py-2 lg:py-3 rounded-lg sm:rounded-xl hover:bg-red-50 hover:scale-105 transform transition-all duration-300 font-semibold shadow-lg flex items-center justify-center space-x-2 group font-raleway w-full sm:w-full lg:w-auto text-sm sm:text-base min-h-[44px] sm:min-h-[48px]">
                <i class="fas fa-plus text-sm sm:text-base lg:text-lg group-hover:rotate-90 transition-transform duration-300"></i>
                <span class="text-sm sm:text-base">Add New User</span>
            </button>
        </div>
    </div>    
    <div class="p-3 sm:p-6 lg:p-8">
        <!-- Table Container with enhanced mobile responsiveness -->
        <div class="overflow-hidden rounded-lg sm:rounded-xl border border-gray-200">
            <!-- Mobile Cards View (visible on small screens) -->
            <div class="block sm:hidden space-y-3">
                <?php if (!empty($recent_users)): ?>
                    <?php foreach ($recent_users as $user): ?>
                        <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 mb-1 truncate text-sm"><?php echo htmlspecialchars($user['username']); ?></h3>
                                    <p class="text-xs text-gray-600 truncate"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                <span class="bg-gray-100 px-2 py-1 rounded text-xs font-mono text-gray-600 ml-2 flex-shrink-0">
                                    #<?php echo htmlspecialchars($user['user_id']); ?>
                                </span>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Role:</span>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                        <?php echo ucfirst(htmlspecialchars($user['role'])); ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Status:</span>
                                    <?php 
                                    $status_class = $user['is_deleted'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                                    $status_text = $user['is_deleted'] ? 'Inactive' : 'Active';
                                    ?>
                                    <span class="px-2 py-1 <?php echo $status_class; ?> text-xs font-bold rounded-full">
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">Joined:</span>
                                    <span class="text-xs text-gray-700 text-right flex-1 ml-2 truncate"><?php echo htmlspecialchars($user['formatted_date']); ?></span>
                                </div>
                                <div class="pt-2 border-t border-gray-100">
                                    <button class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white px-3 py-2 rounded-lg hover:from-blue-600 hover:to-blue-700 text-xs font-medium shadow-md flex items-center justify-center space-x-2 min-h-[40px] transition-all duration-200">
                                        <i class="fas fa-edit text-xs"></i>
                                        <span>Manage User</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center py-6 px-4">
                        <i class="fas fa-users text-3xl text-gray-300 mb-3"></i>
                        <p class="text-base font-medium text-gray-500 mb-1">No users found</p>
                        <p class="text-xs text-gray-400">Start by adding some users</p>
                    </div>
                <?php endif; ?>
            </div>            <!-- Desktop Table View (hidden on small screens) -->
            <div class="hidden sm:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider font-raleway">User ID</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider font-raleway">Username</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider font-raleway hidden md:table-cell">Email</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider font-raleway hidden lg:table-cell">Role</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider font-raleway">Status</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider font-raleway hidden lg:table-cell">Joined Date</th>
                            <th class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider font-raleway">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (!empty($recent_users)): ?>
                            <?php foreach ($recent_users as $user): ?>
                                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-300 group">
                                    <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 lg:py-6 text-xs font-mono text-gray-600 group-hover:text-blue-800 font-raleway">
                                        <span class="bg-gray-100 px-2 py-1 rounded-lg border border-gray-200 font-raleway text-xs">
                                            <?php echo htmlspecialchars($user['user_id']); ?>
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 lg:py-6 text-sm font-medium text-gray-900 group-hover:text-blue-900 font-raleway">
                                        <div class="md:hidden text-xs text-gray-500 mb-1 truncate"><?php echo htmlspecialchars($user['email']); ?></div>
                                        <div class="truncate"><?php echo htmlspecialchars($user['username']); ?></div>
                                    </td>
                                    <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 lg:py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway hidden md:table-cell">
                                        <div class="truncate max-w-[200px]"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </td>
                                    <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 lg:py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway hidden lg:table-cell">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded font-raleway"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></span>
                                    </td>
                                    <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 lg:py-6">
                                        <?php 
                                        $status_class = $user['is_deleted'] ? 'bg-gradient-to-r from-red-100 to-red-200 text-red-800' : 'bg-gradient-to-r from-green-100 to-green-200 text-green-800';
                                        $status_text = $user['is_deleted'] ? 'Inactive' : 'Active';
                                        ?>
                                        <span class="px-2 sm:px-3 py-1 <?php echo $status_class; ?> text-xs font-bold rounded-full shadow-sm font-raleway whitespace-nowrap">
                                            <?php echo $status_text; ?>
                                        </span>
                                    </td>
                                    <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 lg:py-6 text-sm text-gray-600 group-hover:text-blue-800 font-raleway hidden lg:table-cell">
                                        <div class="truncate max-w-[150px]"><?php echo htmlspecialchars($user['formatted_date']); ?></div>
                                    </td>
                                    <td class="px-3 sm:px-4 lg:px-8 py-3 sm:py-4 lg:py-6">
                                        <button class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-2 sm:px-3 lg:px-4 py-1 sm:py-2 rounded-lg hover:from-blue-600 hover:to-blue-700 hover:scale-105 transform transition-all duration-300 text-xs font-medium shadow-md flex items-center space-x-1 sm:space-x-2 font-raleway min-h-[32px] sm:min-h-[36px]">
                                            <i class="fas fa-edit text-xs"></i>
                                            <span class="hidden sm:inline text-xs sm:text-sm">Manage</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-3 sm:px-4 lg:px-8 py-6 sm:py-8 lg:py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center space-y-2">
                                        <i class="fas fa-users text-2xl sm:text-3xl lg:text-4xl text-gray-300"></i>
                                        <p class="text-sm sm:text-base lg:text-lg font-medium">No users found</p>
                                        <p class="text-xs sm:text-sm">Start by adding some users</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- View All Users Link -->
        <div class="mt-3 sm:mt-4 lg:mt-6 text-center">
            <a href="./modules/manage_users.php" class="inline-flex items-center space-x-2 text-red-600 hover:text-red-700 font-medium hover:underline transition-colors duration-200 text-sm sm:text-base min-h-[44px] px-3 py-2 rounded-lg hover:bg-red-50">
                <span>View All Users</span>
                <i class="fas fa-arrow-right text-xs sm:text-sm"></i>
            </a>
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

<?php
include_once 'includes/admin_footer.php';
?>

