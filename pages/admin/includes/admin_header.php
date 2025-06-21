<?php
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

        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-menu-overlay');
            const closeSidebar = document.getElementById('close-sidebar');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            }

            function closeSidebarMenu() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', toggleSidebar);
            }

            if (closeSidebar) {
                closeSidebar.addEventListener('click', closeSidebarMenu);
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebarMenu);
            }

            // Close sidebar when clicking navigation links on mobile
            const navLinks = sidebar.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024) {
                        closeSidebarMenu();
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeSidebarMenu();
                }
            });
        });

        // Logout confirmation function
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../login/index.php';
            }
        }
    </script>
      <div class="flex min-h-screen">
        <!-- Mobile Menu Overlay -->
        <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>
        
        <!-- Sidebar Navigation -->
        <aside id="sidebar" class="bg-gradient-to-b from-white to-gray-50 w-72 min-h-screen border-r border-gray-200 shadow-xl fixed lg:static transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50">
            <div class="p-4 sm:p-6">                 <!-- Logo Section with Close Button -->
                <div class="flex items-center justify-between mb-8 lg:mb-10 p-3 sm:p-4 bg-gradient-to-r from-green-100 to-blue-100 rounded-2xl">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-xl flex items-center justify-center shadow-lg p-1">
                            <img src="<?php echo $assets_path; ?>assets/img/LogoCat.png" alt="eLibrary Logo" class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
                        </div>
                        <span class="font-bold text-lg sm:text-xl text-gray-800">eLibrary</span>
                    </div>
                    <!-- Mobile Close Button -->
                    <button id="close-sidebar" class="lg:hidden p-2 text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>                
                <!-- Navigation Links -->
                <nav class="space-y-2 sm:space-y-3">
                    <a href="<?php echo $base_path; ?>index.php" class="flex items-center space-x-3 sm:space-x-4 py-3 sm:py-4 px-4 sm:px-6 text-gray-700 <?php echo ($current_page == 'index' && $current_dir == 'admin') ? 'bg-gradient-to-r from-blue-100 to-indigo-100 border-blue-200 text-blue-800' : 'hover:bg-gradient-to-r hover:from-blue-100 hover:to-indigo-100 border-transparent hover:border-blue-200'; ?> rounded-xl transition-all duration-300 group hover:shadow-md border">
                        <i class="fas fa-chart-pie <?php echo ($current_page == 'index' && $current_dir == 'admin') ? 'text-blue-800' : 'text-blue-600'; ?> text-base sm:text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium group-hover:text-blue-800 text-sm sm:text-base">Admin Dashboard</span>
                    </a>
                    <a href="<?php echo $base_path; ?>modules/manage_users.php" class="flex items-center space-x-3 sm:space-x-4 py-3 sm:py-4 px-4 sm:px-6 text-gray-700 <?php echo ($current_page == 'manage_users') ? 'bg-gradient-to-r from-green-100 to-emerald-100 border-green-200 text-green-800' : 'hover:bg-gradient-to-r hover:from-green-100 hover:to-emerald-100 border-transparent hover:border-green-200'; ?> rounded-xl transition-all duration-300 group hover:shadow-md border">
                        <i class="fas fa-users <?php echo ($current_page == 'manage_users') ? 'text-green-800' : 'text-green-600'; ?> text-base sm:text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium group-hover:text-green-800 text-sm sm:text-base">Manage Users</span>
                    </a>
                    <a href="<?php echo $base_path; ?>modules/database_management.php" class="flex items-center space-x-3 sm:space-x-4 py-3 sm:py-4 px-4 sm:px-6 text-gray-700 <?php echo ($current_page == 'database_management') ? 'bg-gradient-to-r from-purple-100 to-violet-100 border-purple-200 text-purple-800' : 'hover:bg-gradient-to-r hover:from-purple-100 hover:to-violet-100 border-transparent hover:border-purple-200'; ?> rounded-xl transition-all duration-300 group hover:shadow-md border">
                        <i class="fas fa-database <?php echo ($current_page == 'database_management') ? 'text-purple-800' : 'text-purple-600'; ?> text-base sm:text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium group-hover:text-purple-800 text-sm sm:text-base">Database Management</span>
                    </a>
                    <a href="<?php echo $base_path; ?>modules/reports.php" class="flex items-center space-x-3 sm:space-x-4 py-3 sm:py-4 px-4 sm:px-6 text-gray-700 <?php echo ($current_page == 'reports') ? 'bg-gradient-to-r from-orange-100 to-amber-100 border-orange-200 text-orange-800' : 'hover:bg-gradient-to-r hover:from-orange-100 hover:to-amber-100 border-transparent hover:border-orange-200'; ?> rounded-xl transition-all duration-300 group hover:shadow-md border">
                        <i class="fas fa-chart-bar <?php echo ($current_page == 'reports') ? 'text-orange-800' : 'text-orange-600'; ?> text-base sm:text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium group-hover:text-orange-800 text-sm sm:text-base">System Reports</span>
                    </a>
                    <a href="<?php echo $base_path; ?>modules/system_settings.php" class="flex items-center space-x-3 sm:space-x-4 py-3 sm:py-4 px-4 sm:px-6 text-gray-700 <?php echo ($current_page == 'system_settings') ? 'bg-gradient-to-r from-teal-100 to-cyan-100 border-teal-200 text-teal-800' : 'hover:bg-gradient-to-r hover:from-teal-100 hover:to-cyan-100 border-transparent hover:border-teal-200'; ?> rounded-xl transition-all duration-300 group hover:shadow-md border">
                        <i class="fas fa-cog <?php echo ($current_page == 'system_settings') ? 'text-teal-800' : 'text-teal-600'; ?> text-base sm:text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium group-hover:text-teal-800 text-sm sm:text-base">System Settings</span>
                    </a>

                </nav>
            </div>
        </aside>        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col lg:ml-0">            
            <!-- Top Header -->            
             <header class="bg-white border-b border-gray-200 px-4 sm:px-6 lg:px-10 py-4 sm:py-6 flex justify-between items-center shadow-lg">
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="lg:hidden p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white rounded-2xl flex items-center justify-center shadow-lg p-1">                        
                        <img src="<?php echo $assets_path; ?>assets/img/LogoBook.png" alt="eLibrary Logo" class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
                    </div>
                    <h1 class="text-lg sm:text-2xl lg:text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent"><?php echo htmlspecialchars($page_title); ?></h1>
                </div>
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <div class="hidden sm:block text-gray-600 text-sm">Logged in as</div>                    <div class="px-2 sm:px-4 py-1 sm:py-2 bg-gradient-to-r from-red-100 to-pink-100 text-red-800 font-medium rounded-full border border-red-200 flex items-center space-x-1 sm:space-x-2">
                        <i class="fas fa-shield-alt text-xs sm:text-sm"></i>
                        <span class="text-xs sm:text-sm">Administrator</span>
                    </div>
                    <!-- Quick Logout Button -->
                    <button onclick="confirmLogout()" class="px-2 sm:px-3 py-1 sm:py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 hover:text-red-700 transition-all duration-300 flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm font-medium">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </div>            
            </header>            
            <!-- Dashboard Content -->
            <div class="flex-1 p-4 sm:p-6 lg:p-10 bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 min-h-screen">