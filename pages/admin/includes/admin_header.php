<?php
// Start session before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default page title if not provided
if (!isset($page_title)) {
    $page_title = 'Dashboard';
}

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// Determine the correct base path for assets and navigation
$base_path = '';
$assets_path = '';

if ($current_dir == 'modules') {
    $base_path = '../';
    $assets_path = '../../../';
} elseif ($current_dir == 'admin') {
    $base_path = '';
    $assets_path = '../../';
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Garden Library</title>    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Heroicons -->
    <script src="https://unpkg.com/heroicons@2.0.18/24/outline/index.js"></script>
    <link rel = icon href="<?php echo $assets_path; ?>assets/img/LogoCat.png" type="image/png">


    
    <!-- Fallback for FontAwesome if CDN fails -->
    <script>
        // Check if FontAwesome loaded
        setTimeout(function() {
            if (!document.querySelector('.fa-solid, .fas')) {
                console.log('FontAwesome not loaded, applying fallbacks...');
                // Add fallback icons using Unicode characters
                const style = document.createElement('style');
                style.innerHTML = `
                    .fas.fa-book::before { content: "📚"; }
                    .fas.fa-check-circle::before { content: "✅"; }
                    .fas.fa-exclamation-triangle::before { content: "⚠️"; }
                    .fas.fa-bookmark::before { content: "🔖"; }
                    .fas.fa-plus::before { content: "➕"; }
                    .fas.fa-arrow-right::before { content: "→"; }
                    .fas.fa-hand-point-right::before { content: "👉"; }
                    .fas.fa-undo::before { content: "↩️"; }
                    .fas.fa-chart-pie::before { content: "📊"; }
                    .fas.fa-exchange-alt::before { content: "🔄"; }
                    .fas.fa-users::before { content: "👥"; }
                    .fas.fa-chart-bar::before { content: "📈"; }
                    .fas.fa-calendar-alt::before { content: "📅"; }
                    .fas.fa-cog::before { content: "⚙️"; }                    
                    .fas.fa-user::before { content: "👤"; }
                    .fas.fa-sign-out-alt::before { content: "🚪"; }
                    .fas.fa-database::before { content: "🗄️"; }
                    .fas.fa-shield-alt::before { content: "🛡️"; }
                    .fas.fa-tools::before { content: "🔧"; }
                `;
                document.head.appendChild(style);
            }
        }, 2000);
    </script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#53933D',
                        secondary: '#E1AA74', 
                        light: '#F5FBF3',
                        dark: '#333333'
                    },
                    fontFamily: {
                        'raleway': ['Raleway', 'sans-serif'],
                        'roboto': ['Roboto', 'sans-serif'],
                        'roboto-slab': ['Roboto Slab', 'serif']
                    }
                }
            }
        }
    </script>    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&family=Roboto+Slab:wght@100..900&family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $assets_path; ?>assets/css/style.css">      <!-- Admin specific styles and scripts -->
    <?php if ($current_dir == 'admin' || $current_dir == 'modules'): ?>
        <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
        <script src="<?php echo $base_path; ?>assets/js/script.js"></script>
    <?php endif; ?>

</head>
<body class="bg-gray-50 font-raleway text-gray-800">    <!-- Loading Overlay -->
    <div id="page-loading" class="page-loading">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <p class="mt-4 text-gray-600">Loading dashboard...</p>
        </div>
    </div>
    
    <script>
        // Hide loading overlay once page is ready
        window.addEventListener('load', function() {
            setTimeout(function() {
                const loading = document.getElementById('page-loading');
                if (loading) {
                    loading.classList.add('hidden');
                    setTimeout(function() {
                        loading.remove();
                    }, 500);
                }
            }, 500);
        });

        // Mobile sidebar functionality
        function openSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('mobile-menu-overlay');
            const hamburger = document.getElementById('hamburger-btn');
            
            sidebar.classList.remove('sidebar-hidden');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
            if (hamburger) hamburger.classList.add('hamburger-active');
            document.body.classList.add('overflow-hidden');
        }

        function closeSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('mobile-menu-overlay');
            const hamburger = document.getElementById('hamburger-btn');
            
            sidebar.classList.remove('translate-x-0');
            sidebar.classList.add('sidebar-hidden');
            overlay.classList.add('hidden');
            if (hamburger) hamburger.classList.remove('hamburger-active');
            document.body.classList.remove('overflow-hidden');
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Hamburger menu button
            const hamburgerBtn = document.getElementById('hamburger-btn');
            if (hamburgerBtn) {
                hamburgerBtn.addEventListener('click', openSidebar);
            }

            // Close sidebar button
            const closeSidebarBtn = document.getElementById('close-sidebar');
            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', closeSidebar);
            }

            // Overlay click to close
            const overlay = document.getElementById('mobile-menu-overlay');
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // Navigation links close sidebar
            const navLinks = document.querySelectorAll('#mobile-sidebar nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    setTimeout(closeSidebar, 100);
                });
            });

            // Logout button (only in sidebar)
            const mobileLogoutBtn = document.getElementById('mobile-logout-btn');
            if (mobileLogoutBtn) {
                mobileLogoutBtn.addEventListener('click', confirmLogout);
            }

            // Escape key to close sidebar
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                }
            });
        });

        // Logout confirmation function
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '<?php echo $base_path; ?>../../includes/logout.php';
            }
        }
    </script>

    <style>
        /* Simple hamburger menu animation */
        .hamburger-menu {
            width: 24px;
            height: 18px;
            position: relative;
            cursor: pointer;
        }
        
        .hamburger-line {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background: currentColor;
            border-radius: 1px;
            opacity: 1;
            left: 0;
            transform: rotate(0deg);
            transition: all 0.25s ease-in-out;
        }
        
        .hamburger-line:nth-child(1) {
            top: 0px;
        }
        
        .hamburger-line:nth-child(2) {
            top: 8px;
        }
        
        .hamburger-line:nth-child(3) {
            top: 16px;
        }
        
        .hamburger-active .hamburger-line:nth-child(1) {
            top: 8px;
            transform: rotate(135deg);
        }
        
        .hamburger-active .hamburger-line:nth-child(2) {
            opacity: 0;
            left: -60px;
        }
        
        .hamburger-active .hamburger-line:nth-child(3) {
            top: 8px;
            transform: rotate(-135deg);
        }

        /* Prevent sidebar flash on load */
        .sidebar-hidden {
            transform: translateX(-100%);
        }
    </style>

      <div class="min-h-screen bg-gray-50">
        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>
        
        <!-- Mobile Sidebar -->
        <div id="mobile-sidebar" class="fixed top-0 left-0 w-80 h-full bg-white shadow-xl sidebar-hidden transition-transform duration-300 ease-in-out z-50">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between p-6 bg-gradient-to-r from-red-500 to-pink-600 text-white">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <img src="<?php echo $assets_path; ?>assets/img/LogoCat.png" alt="Purring Pages Logo" class="w-8 h-8 object-contain">
                    </div>
                    <div>
                        <h2 class="font-bold text-lg">Purring Pages</h2>
                        <p class="text-sm text-red-100">Admin Portal</p>
                    </div>
                </div>
                <button id="close-sidebar" class="p-2 hover:bg-white hover:bg-opacity-20 rounded-lg transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="p-4 space-y-2">
                <!-- Admin Dashboard -->
                <a href="<?php echo $base_path; ?>index.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-red-500 hover:text-white transition-colors duration-200 <?php echo ($current_page == 'index' && $current_dir == 'admin') ? 'bg-red-500 text-white' : ''; ?>">
                    <i class="fas fa-chart-pie w-5"></i>
                    <span class="font-medium">Admin Dashboard</span>
                </a>
                
                <!-- Manage Users -->
                <a href="<?php echo $base_path; ?>modules/manage_users.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-red-500 hover:text-white transition-colors duration-200 <?php echo ($current_page == 'manage_users') ? 'bg-red-500 text-white' : ''; ?>">
                    <i class="fas fa-users w-5"></i>
                    <span class="font-medium">Manage Users</span>
                </a>

                <!-- Manage Librarians -->
                <a href="<?php echo $base_path; ?>modules/manage_librarians.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-red-500 hover:text-white transition-colors duration-200 <?php echo ($current_page == 'manage_librarians') ? 'bg-red-500 text-white' : ''; ?>">
                    <i class="fas fa-user-tie w-5"></i>
                    <span class="font-medium">Manage Librarians</span>
                </a>

                <!-- Generate Reports -->
                <a href="<?php echo $base_path; ?>modules/generate_reports.php" class="flex items-center space-x-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-red-500 hover:text-white transition-colors duration-200 <?php echo ($current_page == 'generate_reports') ? 'bg-red-500 text-white' : ''; ?>">
                    <i class="fas fa-chart-bar w-5"></i>
                    <span class="font-medium">Generate Reports</span>
                </a>
            </nav>

            <!-- User Section -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrator'; ?></p>
                        <p class="text-sm text-gray-500">Admin</p>
                    </div>
                </div>
                <button id="mobile-logout-btn" class="w-full flex items-center justify-center space-x-2 py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="w-full">
            <!-- Top Header with Hamburger -->
            <header class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-10 py-4 sm:py-6 flex justify-between items-center shadow-lg sticky top-0 z-30">
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <!-- Hamburger Menu Button -->
                    <button id="hamburger-btn" class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <div class="hamburger-menu">
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                            <span class="hamburger-line"></span>
                        </div>
                    </button>
                    
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg p-1">                        
                        <img src="<?php echo $assets_path; ?>assets/img/LogoBook.png" alt="Purring Pages Logo" class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
                    </div>
                    <h1 class="text-lg sm:text-2xl lg:text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent"><?php echo htmlspecialchars($page_title); ?></h1>
                </div>
                
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Welcome Message -->
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-600 text-sm">Welcome,</span>
                        <div class="px-3 py-1 bg-gradient-to-r from-red-100 to-pink-100 text-red-800 font-semibold rounded-full border border-red-200">
                            <i class="fas fa-shield-alt text-xs mr-1"></i>
                            <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrator'; ?>
                        </div>
                    </div>
                </div>            
            </header>            
            
            <!-- Dashboard Content -->
            <div class="flex-1 p-4 sm:p-6 lg:p-10 bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 min-h-screen">