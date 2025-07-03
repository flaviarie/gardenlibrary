<?php
// Show errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the page title 
$page_title = 'Manage Librarians';

// Handle AJAX requests first, before any output
if (isset($_POST['action'])) {
    // Start session and include necessary files for AJAX
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    include_once '../includes/admin_functions.php';
    include_once '../../../includes/db_connection.php';
    
    // Check admin access for AJAX
    if (!isAdmin()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
    
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'promote_to_librarian':
            $result = updateUser(
                $pdo,
                $_POST['user_id'],
                $_POST['username'],
                $_POST['email'],
                'librarian',
                '' // full_name not used
            );
            echo json_encode($result);
            exit;
            
        case 'demote_librarian':
            $result = updateUser(
                $pdo,
                $_POST['user_id'],
                $_POST['username'],
                $_POST['email'],
                'user',
                '' // full_name not used
            );
            echo json_encode($result);
            exit;
            
        case 'create_librarian':
            $result = createUser(
                $pdo,
                $_POST['username'],
                $_POST['email'],
                $_POST['password'],
                'librarian',
                '' // full_name not used
            );
            echo json_encode($result);
            exit;
            
        case 'get_librarian_stats':
            $stmt = $pdo->prepare("
                SELECT u.user_id, u.username, u.full_name,
                       COUNT(DISTINCT b.borrowing_id) as total_issues,
                       COUNT(DISTINCT CASE WHEN b.return_date IS NOT NULL THEN b.borrowing_id END) as total_returns,
                       COUNT(DISTINCT CASE WHEN b.return_date IS NULL THEN b.borrowing_id END) as active_issues
                FROM users u
                LEFT JOIN borrowings b ON u.user_id = b.librarian_id
                WHERE u.user_id = ? AND u.role = 'librarian'
                GROUP BY u.user_id
            ");
            $stmt->execute([$_POST['user_id']]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($stats);
            exit;
    }
}

// Include header and functions for the main page
include_once '../includes/admin_header.php';
include_once '../includes/admin_functions.php';
include_once '../../../includes/db_connection.php';

// Check admin access for the main page
requireAdminAccess();

// Get parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$per_page = 10;

// Get librarians data
$librarians_data = getAllUsers($pdo, $page, $per_page, $search, 'librarian');
$librarians = $librarians_data['users'];
$total_librarians = $librarians_data['total'];
$total_pages = $librarians_data['pages'];
$current_page = $librarians_data['current_page'];

// Get regular users for promotion
$users_data = getAllUsers($pdo, 1, 50, '', 'user');
$regular_users = $users_data['users'];

// Get librarian statistics
try {
    $stmt = $pdo->query("
        SELECT 
            COUNT(DISTINCT u.user_id) as total_librarians,
            COUNT(DISTINCT b.borrowing_id) as total_issues_handled,
            COUNT(DISTINCT CASE WHEN b.return_date IS NOT NULL THEN b.borrowing_id END) as total_returns_processed,
            COUNT(DISTINCT CASE WHEN DATE(b.borrow_date) = CURDATE() THEN b.borrowing_id END) as today_issues
        FROM users u
        LEFT JOIN borrowings b ON u.user_id = b.librarian_id
        WHERE u.role = 'librarian' AND u.is_deleted = FALSE
    ");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $stats = [
        'total_librarians' => 0,
        'total_issues_handled' => 0,
        'total_returns_processed' => 0,
        'today_issues' => 0
    ];
}
?>

<!-- Main Content -->
<div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Librarians</h1>
                    <p class="text-gray-600">Manage librarian staff and their performance</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus"></i>
                        Add Librarian
                    </button>
                    <button onclick="openPromoteModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-xl">
                        <i class="fas fa-user-plus"></i>
                        Promote User
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Librarians</p>
                        <p class="text-3xl font-bold text-blue-600"><?php echo number_format($stats['total_librarians']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-tie text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Issues Handled</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo number_format($stats['total_issues_handled']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Returns Processed</p>
                        <p class="text-3xl font-bold text-purple-600"><?php echo number_format($stats['total_returns_processed']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-undo text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Today's Issues</p>
                        <p class="text-3xl font-bold text-red-600"><?php echo number_format($stats['today_issues']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Search librarians..." 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button onclick="applyFilters()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-search"></i>
                    Search
                </button>
            </div>
        </div>

        <!-- Librarians Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Librarians List</h2>
                <p class="text-gray-600 mt-1">Total: <?php echo $total_librarians; ?> librarians</p>
            </div>

            <?php if (!empty($librarians)): ?>
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">ID</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Username</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Performance</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Joined</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($librarians as $librarian): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">
                                        #<?php echo htmlspecialchars($librarian['user_id']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($librarian['username']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo htmlspecialchars($librarian['email']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="text-green-600 font-semibold"><?php echo $librarian['active_borrowings']; ?></span>
                                            <span class="text-gray-500">active</span>
                                            <button onclick="viewStats(<?php echo $librarian['user_id']; ?>)" 
                                                    class="text-blue-600 hover:text-blue-800 text-xs">
                                                <i class="fas fa-chart-bar"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo date('M j, Y', strtotime($librarian['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <button onclick="viewStats(<?php echo $librarian['user_id']; ?>)" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200">
                                                <i class="fas fa-chart-line"></i>
                                            </button>
                                            <button onclick="demoteLibrarian(<?php echo $librarian['user_id']; ?>, '<?php echo htmlspecialchars($librarian['username']); ?>', '<?php echo htmlspecialchars($librarian['email']); ?>')" 
                                                    class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="lg:hidden p-4 space-y-4">
                    <?php foreach ($librarians as $librarian): ?>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($librarian['username']); ?></h3>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($librarian['email']); ?></p>
                                </div>
                                <span class="text-xs font-mono text-gray-500">#<?php echo $librarian['user_id']; ?></span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500">Active Books</p>
                                    <p class="text-sm font-semibold text-green-600"><?php echo $librarian['active_borrowings']; ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Joined</p>
                                    <p class="text-sm font-semibold text-gray-900"><?php echo date('M j, Y', strtotime($librarian['created_at'])); ?></p>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex gap-2">
                                    <button onclick="viewStats(<?php echo $librarian['user_id']; ?>)" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200">
                                        <i class="fas fa-chart-line"></i> Stats
                                    </button>
                                    <button onclick="demoteLibrarian(<?php echo $librarian['user_id']; ?>, '<?php echo htmlspecialchars($librarian['username']); ?>', '<?php echo htmlspecialchars($librarian['email']); ?>')" 
                                            class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200">
                                        <i class="fas fa-user-minus"></i> Demote
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-user-tie text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No librarians found</h3>
                    <p class="text-gray-500 mb-6">Add librarians to manage your library system</p>
                    <div class="flex justify-center gap-3">
                        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300">
                            Add Librarian
                        </button>
                        <button onclick="openPromoteModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300">
                            Promote User
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="flex justify-center mt-8">
                <div class="flex gap-2">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>" 
                           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                           class="px-4 py-2 <?php echo $i === $current_page ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'; ?> rounded-lg transition-all duration-200">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>" 
                           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Librarian Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Add New Librarian</h2>
            <button onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700 text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="createForm" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" id="createUsername" name="username" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" id="createEmail" name="email" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" id="createPassword" name="password" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeCreateModal()" 
                        class="flex-1 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-all duration-200">
                    Create Librarian
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Promote User Modal -->
<div id="promoteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Promote User to Librarian</h2>
            <button onclick="closePromoteModal()" class="text-gray-500 hover:text-gray-700 text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="promoteForm" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Select User</label>
                <select id="promoteUser" name="user_id" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Select a user to promote</option>
                    <?php foreach ($regular_users as $user): ?>
                        <option value="<?php echo $user['user_id']; ?>" 
                                data-username="<?php echo htmlspecialchars($user['username']); ?>"
                                data-email="<?php echo htmlspecialchars($user['email']); ?>">
                            <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    This will promote the selected user to a librarian role with library management permissions.
                </p>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closePromoteModal()" 
                        class="flex-1 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-all duration-200">
                    Promote User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats Modal -->
<div id="statsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Librarian Performance</h2>
            <button onclick="closeStatsModal()" class="text-gray-500 hover:text-gray-700 text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="statsContent" class="space-y-4">
            <div class="animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
    document.getElementById('createForm').reset();
}

function openPromoteModal() {
    document.getElementById('promoteModal').classList.remove('hidden');
}

function closePromoteModal() {
    document.getElementById('promoteModal').classList.add('hidden');
    document.getElementById('promoteForm').reset();
}

function closeStatsModal() {
    document.getElementById('statsModal').classList.add('hidden');
}

function demoteLibrarian(userId, username, email) {
    if (confirm(`Are you sure you want to demote ${username} from librarian to user?`)) {
        const formData = new FormData();
        formData.append('action', 'demote_librarian');
        formData.append('user_id', userId);
        formData.append('username', username);
        formData.append('email', email);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showAlert(result.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert(result.message, 'error');
            }
        })
        .catch(error => {
            showAlert('Error demoting librarian', 'error');
        });
    }
}

function viewStats(userId) {
    document.getElementById('statsModal').classList.remove('hidden');
    
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_librarian_stats&user_id=${userId}`
    })
    .then(response => response.json())
    .then(stats => {
        document.getElementById('statsContent').innerHTML = `
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-600 font-semibold">Total Issues</p>
                    <p class="text-2xl font-bold text-blue-800">${stats.total_issues || 0}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-green-600 font-semibold">Total Returns</p>
                    <p class="text-2xl font-bold text-green-800">${stats.total_returns || 0}</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg">
                    <p class="text-sm text-orange-600 font-semibold">Active Issues</p>
                    <p class="text-2xl font-bold text-orange-800">${stats.active_issues || 0}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <p class="text-sm text-purple-600 font-semibold">Success Rate</p>
                    <p class="text-2xl font-bold text-purple-800">${stats.total_issues > 0 ? Math.round((stats.total_returns / stats.total_issues) * 100) : 0}%</p>
                </div>
            </div>
            <div class="mt-6">
                <h3 class="font-semibold text-gray-900 mb-2">Performance Summary</h3>
                <p class="text-sm text-gray-600">${stats.username} has processed ${stats.total_issues || 0} book issues and ${stats.total_returns || 0} returns.</p>
            </div>
        `;
    })
    .catch(error => {
        document.getElementById('statsContent').innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <p class="text-red-600">Error loading statistics</p>
            </div>
        `;
    });
}

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const url = new URL(window.location.href);
    url.searchParams.set('search', search);
    url.searchParams.set('page', '1');
    window.location.href = url.toString();
}

function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
    
    alertDiv.innerHTML = `
        <div class="flex items-center text-white ${bgColor} px-4 py-3 rounded-lg">
            <i class="fas fa-${icon} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.classList.remove('translate-x-full');
    }, 100);
    
    setTimeout(() => {
        alertDiv.classList.add('translate-x-full');
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

// Handle create form submission
document.getElementById('createForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    formData.append('action', 'create_librarian');
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showAlert(result.message, 'success');
            closeCreateModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(result.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error creating librarian', 'error');
    });
});

// Handle promote form submission
document.getElementById('promoteForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const selectedOption = document.getElementById('promoteUser').selectedOptions[0];
    const formData = new FormData();
    formData.append('action', 'promote_to_librarian');
    formData.append('user_id', selectedOption.value);
    formData.append('username', selectedOption.dataset.username);
    formData.append('email', selectedOption.dataset.email);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showAlert(result.message, 'success');
            closePromoteModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(result.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error promoting user', 'error');
    });
});

// Search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});
</script>

