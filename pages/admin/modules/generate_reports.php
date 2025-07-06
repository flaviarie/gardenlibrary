<?php
$page_title = 'Generate Reports';

// AJAX handler
if (isset($_POST['action'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    include_once '../includes/admin_functions.php';
    include_once '../../../includes/db.php';
    
    // Access check
    if (!isAdmin()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'generate_report':
                error_log("Generate report request: " . print_r($_POST, true));
                
                $date_from = !empty($_POST['date_from']) ? $_POST['date_from'] : null;
                $date_to = !empty($_POST['date_to']) ? $_POST['date_to'] : null;
                
                $result = generateReports(
                    $pdo,
                    $_POST['report_type'],
                    $date_from,
                    $date_to
                );
                
                error_log("Generate report result: " . print_r($result, true));
                echo json_encode($result);
                exit;
                
            case 'export_report':
                $report_type = $_POST['report_type'];
                $date_from = !empty($_POST['date_from']) ? $_POST['date_from'] : null;
                $date_to = !empty($_POST['date_to']) ? $_POST['date_to'] : null;
                
                $result = generateReports($pdo, $report_type, $date_from, $date_to);
                
                if ($result['success']) {
                    header('Content-Type: application/csv');
                    header('Content-Disposition: attachment; filename="' . $report_type . '_report_' . date('Y-m-d') . '.csv"');
                    
                    $output = fopen('php://output', 'w');
                    
                    // CSV headers by type
                    switch ($report_type) {
                        case 'user_registrations':
                            fputcsv($output, ['Date', 'New Registrations']);
                            break;
                        case 'borrowing_activity':
                            fputcsv($output, ['Date', 'Books Borrowed']);
                            break;
                        case 'popular_books':
                            fputcsv($output, ['Book Title', 'Author', 'Borrow Count']);
                            break;
                    }
                    
                    // Add data rows
                    foreach ($result['data'] as $row) {
                        fputcsv($output, $row);
                    }
                    
                    fclose($output);
                    exit;
                } else {
                    echo json_encode($result);
                    exit;
                }
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
                exit;
        }
    } catch (Exception $e) {
        error_log("Exception in generate_reports.php: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}

// Include header and functions for the main page
include_once '../includes/admin_header.php';
include_once '../includes/admin_functions.php';
include_once '../../../includes/db.php';

// Check admin access for the main page
requireAdminAccess();

// Get system statistics for dashboard
try {
    $stats = getSystemStats($pdo);
    
    // Get additional report-specific stats
    $stmt = $pdo->query("SELECT COUNT(*) as total_reports FROM borrowings");
    $borrowing_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT COUNT(*) as overdue_books FROM borrowings WHERE return_date IS NULL AND due_date < CURDATE()");
    $overdue_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query("SELECT COUNT(DISTINCT category) as categories FROM books WHERE is_deleted = FALSE");
    $category_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $stats = [
        'total_users' => 0,
        'total_books' => 0,
        'active_borrowings' => 0,
        'total_librarians' => 0
    ];
    $borrowing_stats = ['total_reports' => 0];
    $overdue_stats = ['overdue_books' => 0];
    $category_stats = ['categories' => 0];
}
?>

<!-- Main Content -->
<div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Generate Reports</h1>
                    <p class="text-gray-600">Generate and export comprehensive system reports</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Last updated: <?php echo date('M j, Y g:i A'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-blue-600"><?php echo number_format($stats['total_users']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center gap-2 text-sm text-green-600">
                        <i class="fas fa-arrow-up"></i>
                        <span>Active users in system</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Books</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo number_format($stats['total_books']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-layer-group"></i>
                        <span><?php echo $category_stats['categories']; ?> categories</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Borrowings</p>
                        <p class="text-3xl font-bold text-purple-600"><?php echo number_format($stats['active_borrowings']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book-open text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center gap-2 text-sm text-orange-600">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?php echo $overdue_stats['overdue_books']; ?> overdue</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                        <p class="text-3xl font-bold text-red-600"><?php echo number_format($borrowing_stats['total_reports']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-red-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-chart-line"></i>
                        <span>All time activity</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Generation Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Report Generator</h2>
            
            <form id="reportForm" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Report Type</label>
                        <select id="reportType" name="report_type" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select report type</option>
                            <option value="user_registrations">User Registrations</option>
                            <option value="borrowing_activity">Borrowing Activity</option>
                            <option value="popular_books">Popular Books</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">From Date</label>
                        <input type="date" id="dateFrom" name="date_from"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">To Date</label>
                        <input type="date" id="dateTo" name="date_to"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-chart-bar"></i>
                        Generate Report
                    </button>
                    <button type="button" onclick="exportReport()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-2">
                        <i class="fas fa-download"></i>
                        Export CSV
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Results Section -->
        <div id="reportResults" class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 hidden">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Report Results</h2>
                <button onclick="clearResults()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div id="reportContent">
                <!-- Report content will be loaded here -->
            </div>
        </div>

        <!-- Quick Reports Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Quick Reports</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-plus text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">New Users This Month</h3>
                            <p class="text-sm text-gray-600">User registration trends</p>
                        </div>
                    </div>
                    <button onclick="generateQuickReport('user_registrations', 'month')" 
                            class="w-full bg-blue-50 hover:bg-blue-100 text-blue-600 py-2 px-4 rounded-lg font-medium transition-all duration-200">
                        Generate
                    </button>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-book-open text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Borrowing Activity</h3>
                            <p class="text-sm text-gray-600">Last 30 days activity</p>
                        </div>
                    </div>
                    <button onclick="generateQuickReport('borrowing_activity', 'month')" 
                            class="w-full bg-green-50 hover:bg-green-100 text-green-600 py-2 px-4 rounded-lg font-medium transition-all duration-200">
                        Generate
                    </button>
                </div>

                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-all duration-300">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-star text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Popular Books</h3>
                            <p class="text-sm text-gray-600">Most borrowed books</p>
                        </div>
                    </div>
                    <button onclick="generateQuickReport('popular_books', 'all')" 
                            class="w-full bg-purple-50 hover:bg-purple-100 text-purple-600 py-2 px-4 rounded-lg font-medium transition-all duration-200">
                        Generate
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-8 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-700">Generating report...</p>
    </div>
</div>

<!-- Alert Messages -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<!-- External Scripts -->
<script src="../assets/js/reports-management.js"></script>

</body>
</html>

