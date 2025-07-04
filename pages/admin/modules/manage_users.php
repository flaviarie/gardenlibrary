<?php
// Show errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the page title 
$page_title = 'Manage Users';

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
        case 'create_user':
            $result = createUser(
                $pdo,
                $_POST['username'],
                $_POST['email'],
                $_POST['password'],
                $_POST['role']
            );
            echo json_encode($result);
            exit;
            
        case 'update_user':
            $result = updateUser(
                $pdo,
                $_POST['user_id'],
                $_POST['username'],
                $_POST['email'],
                $_POST['role'],
                '', // full_name not used
                $_POST['password']
            );
            echo json_encode($result);
            exit;
            
        case 'delete_user':
            $result = deleteUser($pdo, $_POST['user_id']);
            echo json_encode($result);
            exit;
            
        case 'suspend_user':
            $result = suspendUser($pdo, $_POST['user_id']);
            echo json_encode($result);
            exit;
            
        case 'activate_user':
            $result = activateUser($pdo, $_POST['user_id']);
            echo json_encode($result);
            exit;
            
        case 'get_user':
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$_POST['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($user);
            exit;
    }
}

// Include header and functions for regular page load
include_once '../includes/admin_header.php';
include_once '../includes/admin_functions.php';
include_once '../../../includes/db_connection.php';

// Check admin access
requireAdminAccess();

// Get parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$per_page = 10;

// Get users data
$users_data = getAllUsers($pdo, $page, $per_page, $search, $role_filter, $status_filter);
$users = $users_data['users'];
$total_users = $users_data['total'];
$total_pages = $users_data['pages'];
$current_page = $users_data['current_page'];
?>

<!-- Main Content -->
<div class="min-h-screen bg-gray-50 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Manage Users</h1>
                    <p class="text-gray-600">Manage system users and their permissions</p>
                </div>
                <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus"></i>
                    Add New User
                </button>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Search users..." 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="lg:w-48">
                    <select id="roleFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Roles</option>
                        <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="librarian" <?php echo $role_filter === 'librarian' ? 'selected' : ''; ?>>Librarian</option>
                        <option value="user" <?php echo $role_filter === 'user' ? 'selected' : ''; ?>>User</option>
                    </select>
                </div>
                <div class="lg:w-48">
                    <select id="statusFilter" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="active" <?php echo isset($_GET['status']) && $_GET['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="suspended" <?php echo isset($_GET['status']) && $_GET['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                    </select>
                </div>
                <button onclick="applyFilters()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-search"></i>
                    Search
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Users List</h2>
                <p class="text-gray-600 mt-1">Total: <?php echo $total_users; ?> users</p>
            </div>

            <?php if (!empty($users)): ?>
                <!-- Desktop Table -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-blue-600 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">ID</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Username</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Active Borrowings</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Created</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($users as $user): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">
                                        #<?php echo htmlspecialchars($user['user_id']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                            <?php echo $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php 
                                            if ($user['role'] === 'admin') {
                                                echo ($user['username'] === 'admin') ? 'Administrator' : 'Librarian';
                                            } else {
                                                echo 'Student';
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                        $status = $user['effective_status'];
                                        $statusClass = $status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                        $statusIcon = $status === 'active' ? 'check-circle' : 'times-circle';
                                        $suspensionReason = $user['suspension_reason'];
                                        ?>
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full <?php echo $statusClass; ?>"
                                              <?php if ($status === 'suspended' && $suspensionReason): ?>
                                              title="<?php echo htmlspecialchars($suspensionReason); ?>"
                                              <?php endif; ?>>
                                            <i class="fas fa-<?php echo $statusIcon; ?> mr-1"></i>
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                        <?php if ($status === 'suspended' && $suspensionReason): ?>
                                            <div class="text-xs text-gray-500 mt-1 max-w-32 truncate">
                                                <?php echo htmlspecialchars($suspensionReason); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo $user['active_borrowings']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <button onclick="editUser(<?php echo $user['user_id']; ?>)" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200"
                                                    title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <?php if ($user['username'] !== 'admin'): ?>
                                                <?php 
                                                $manualStatus = isset($user['status']) ? $user['status'] : 'active';
                                                $effectiveStatus = $user['effective_status'];
                                                $isAutoSuspended = ($effectiveStatus === 'suspended' && $manualStatus === 'active');
                                                ?>
                                                
                                                <?php if ($manualStatus === 'active' && !$isAutoSuspended): ?>
                                                    <button onclick="suspendUser(<?php echo $user['user_id']; ?>)" 
                                                            class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200"
                                                            title="Manually Suspend User">
                                                        <i class="fas fa-user-slash"></i>
                                                    </button>
                                                <?php elseif ($manualStatus === 'suspended'): ?>
                                                    <button onclick="activateUser(<?php echo $user['user_id']; ?>)" 
                                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200"
                                                            title="Remove Manual Suspension">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                <?php elseif ($isAutoSuspended): ?>
                                                    <button class="bg-gray-400 text-white px-3 py-1 rounded text-xs font-semibold cursor-not-allowed"
                                                            title="User automatically suspended: <?php echo htmlspecialchars($user['suspension_reason']); ?>"
                                                            disabled>
                                                        <i class="fas fa-exclamation-triangle"></i>
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <button onclick="deleteUser(<?php echo $user['user_id']; ?>)" 
                                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200"
                                                        title="Delete User">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="lg:hidden p-4 space-y-4">
                    <?php foreach ($users as $user): ?>
                        <?php 
                        $status = $user['effective_status'];
                        $statusClass = $status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                        $statusIcon = $status === 'active' ? 'check-circle' : 'times-circle';
                        $suspensionReason = $user['suspension_reason'];
                        $manualStatus = isset($user['status']) ? $user['status'] : 'active';
                        $isAutoSuspended = ($status === 'suspended' && $manualStatus === 'active');
                        ?>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($user['username']); ?></h3>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                <span class="text-xs font-mono text-gray-500">#<?php echo $user['user_id']; ?></span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500">Role</p>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        <?php echo $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php 
                                        if ($user['role'] === 'admin') {
                                            echo ($user['username'] === 'admin') ? 'Administrator' : 'Librarian';
                                        } else {
                                            echo 'Student';
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Status</p>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusClass; ?>"
                                          <?php if ($status === 'suspended' && $suspensionReason): ?>
                                          title="<?php echo htmlspecialchars($suspensionReason); ?>"
                                          <?php endif; ?>>
                                        <i class="fas fa-<?php echo $statusIcon; ?> mr-1"></i>
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                    <?php if ($status === 'suspended' && $suspensionReason): ?>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <?php echo htmlspecialchars($suspensionReason); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <p class="text-xs text-gray-500">Active Borrowings</p>
                                <p class="text-sm font-semibold text-gray-900"><?php echo $user['active_borrowings']; ?></p>
                            </div>
                            <div class="flex justify-between items-center">
                                <p class="text-xs text-gray-500">Created: <?php echo date('M j, Y', strtotime($user['created_at'])); ?></p>
                                <div class="flex gap-2">
                                    <button onclick="editUser(<?php echo $user['user_id']; ?>)" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200"
                                            title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <?php if ($user['username'] !== 'admin'): ?>
                                        <?php if ($manualStatus === 'active' && !$isAutoSuspended): ?>
                                            <button onclick="suspendUser(<?php echo $user['user_id']; ?>)" 
                                                    class="bg-orange-600 hover:bg-orange-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200"
                                                    title="Manually Suspend User">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        <?php elseif ($manualStatus === 'suspended'): ?>
                                            <button onclick="activateUser(<?php echo $user['user_id']; ?>)" 
                                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200"
                                                    title="Remove Manual Suspension">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        <?php elseif ($isAutoSuspended): ?>
                                            <button class="bg-gray-400 text-white px-3 py-1 rounded text-xs font-semibold cursor-not-allowed"
                                                    title="User automatically suspended: <?php echo htmlspecialchars($suspensionReason); ?>"
                                                    disabled>
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button onclick="deleteUser(<?php echo $user['user_id']; ?>)" 
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-semibold transition-all duration-200"
                                                title="Delete User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-12 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-users text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No users found</h3>
                    <p class="text-gray-500 mb-6">Try adjusting your search criteria or add new users</p>
                    <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-all duration-300">
                        Add First User
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="flex justify-center mt-8">
                <div class="flex gap-2">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?php echo $current_page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                           class="px-4 py-2 <?php echo $i === $current_page ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700'; ?> rounded-lg transition-all duration-200">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?php echo $current_page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-all duration-200">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create/Edit User Modal -->
<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-6">
            <h2 id="modalTitle" class="text-xl font-semibold text-gray-900">Add New User</h2>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="userForm" class="space-y-4">
            <input type="hidden" id="userId" name="user_id">
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                <input type="text" id="username" name="username" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" id="email" name="email" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                <select id="role" name="role" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="user">User</option>
                    <option value="librarian">Librarian</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                <input type="password" id="password" name="password" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Leave empty to keep current password (for editing)</p>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()" 
                        class="flex-1 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-all duration-200">
                    Save User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Alert Messages -->
<div id="alertContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
let editingUserId = null;

function openCreateModal() {
    editingUserId = null;
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('userModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
    editingUserId = null;
}

function editUser(userId) {
    editingUserId = userId;
    document.getElementById('modalTitle').textContent = 'Edit User';
    
    // Fetch user data
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=get_user&user_id=${userId}`
    })
    .then(response => response.json())
    .then(user => {
        document.getElementById('userId').value = user.user_id;
        document.getElementById('username').value = user.username;
        document.getElementById('email').value = user.email;
        document.getElementById('role').value = user.role;
        document.getElementById('password').value = '';
        
        document.getElementById('userModal').classList.remove('hidden');
    })
    .catch(error => {
        showAlert('Error loading user data', 'error');
    });
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_user&user_id=${userId}`
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
            showAlert('Error deleting user', 'error');
        });
    }
}

function suspendUser(userId) {
    if (confirm('Are you sure you want to suspend this user account?')) {
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=suspend_user&user_id=${userId}`
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
            showAlert('Error suspending user', 'error');
        });
    }
}

function activateUser(userId) {
    if (confirm('Are you sure you want to activate this user account?')) {
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=activate_user&user_id=${userId}`
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
            showAlert('Error activating user', 'error');
        });
    }
}

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const role = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;
    const url = new URL(window.location.href);
    url.searchParams.set('search', search);
    url.searchParams.set('role', role);
    url.searchParams.set('status', status);
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

// Handle form submission
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const action = editingUserId ? 'update_user' : 'create_user';
    formData.append('action', action);
    
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showAlert(result.message, 'success');
            closeModal();
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(result.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Error saving user', 'error');
    });
});

// Search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});
</script>
