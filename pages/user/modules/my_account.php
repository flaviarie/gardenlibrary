<?php
session_start();
$page_title = 'My Account';

include_once '../includes/user_header.php';
include_once '../includes/user_functions.php';
include_once '../../../includes/db.php';

// Check if user is logged in
requireUserAccess();

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        try {
            switch ($_POST['action']) {
                case 'update_profile':
                    $username = sanitizeInput($_POST['username']);
                    $email = sanitizeInput($_POST['email']);
                    $phone = sanitizeInput($_POST['phone'] ?? '');
                    $address = sanitizeInput($_POST['address'] ?? '');
                    
                    // Validate inputs
                    if (empty($username) || empty($email)) {
                        $error = 'Username and email are required.';
                    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $error = 'Please enter a valid email address.';
                    } else {
                        // Check if username or email already exists for other users
                        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
                        $stmt->execute([$username, $email, $user_id]);
                        
                        if ($stmt->fetch()) {
                            $error = 'Username or email already exists for another user.';
                        } else {
                            // Update profile
                            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, phone = ?, address = ?, updated_at = NOW() WHERE user_id = ?");
                            $stmt->execute([$username, $email, $phone, $address, $user_id]);
                            $message = 'Profile updated successfully!';
                        }
                    }
                    break;
                    
                case 'change_password':
                    $current_password = $_POST['current_password'] ?? '';
                    $new_password = $_POST['new_password'] ?? '';
                    $confirm_password = $_POST['confirm_password'] ?? '';
                    
                    // Validate passwords
                    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                        $error = 'All password fields are required.';
                    } elseif ($new_password !== $confirm_password) {
                        $error = 'New password and confirmation do not match.';
                    } elseif (strlen($new_password) < 8) {
                        $error = 'New password must be at least 8 characters long.';
                    } else {
                        // Verify current password
                        $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
                        $stmt->execute([$user_id]);
                        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if (!password_verify($current_password, $user_data['password'])) {
                            $error = 'Current password is incorrect.';
                        } else {
                            // Update password
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE user_id = ?");
                            $stmt->execute([$hashed_password, $user_id]);
                            $message = 'Password changed successfully!';
                        }
                    }
                    break;
            }
        } catch (PDOException $e) {
            error_log("Account update error: " . $e->getMessage());
            $error = 'Failed to update account. Please try again.';
        }
    }
}

// Get user information
$user_info = [];
$account_stats = [];
try {
    // Get user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get account statistics
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_borrowings FROM borrowings WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_borrowings = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as active_borrowings FROM borrowings WHERE user_id = ? AND return_date IS NULL");
    $stmt->execute([$user_id]);
    $active_borrowings = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_reservations FROM reservations WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_reservations = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as active_reservations FROM reservations WHERE user_id = ? AND status = 'pending'");
    $stmt->execute([$user_id]);
    $active_reservations = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_reviews FROM reviews WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $total_reviews = $stmt->fetchColumn();
    
    // Calculate total fines
    $stmt = $pdo->prepare("
        SELECT SUM(CASE 
            WHEN b.due_date < NOW() THEN DATEDIFF(NOW(), b.due_date) * 10
            ELSE 0 
        END) as total_fines
        FROM borrowings b
        WHERE b.user_id = ? AND b.return_date IS NULL
    ");
    $stmt->execute([$user_id]);
    $total_fines = $stmt->fetchColumn() ?? 0;
    
    $account_stats = [
        'total_borrowings' => $total_borrowings,
        'active_borrowings' => $active_borrowings,
        'total_reservations' => $total_reservations,
        'active_reservations' => $active_reservations,
        'total_reviews' => $total_reviews,
        'total_fines' => $total_fines
    ];
    
} catch (PDOException $e) {
    error_log("Get user info error: " . $e->getMessage());
    $error = 'Failed to load account information.';
}
?>

<!-- Page Content -->
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-3xl p-6 sm:p-8 text-white">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <i class="fas fa-user text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold mb-1">My Account</h1>
                    <p class="text-teal-100 text-sm sm:text-base">Manage your profile and account settings</p>
                </div>
            </div>
            <div class="mt-4 lg:mt-0">
                <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-4 py-2">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-calendar text-sm"></i>
                        <span class="text-sm font-medium">Member since <?php echo formatDate($user_info['created_at'] ?? ''); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <?php if ($message): ?>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 alert-auto-hide">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <p class="text-green-800"><?php echo htmlspecialchars($message); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 alert-auto-hide">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-600 mr-3"></i>
                <p class="text-red-800"><?php echo htmlspecialchars($error); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Account Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Borrowings</p>
                    <p class="text-2xl font-bold text-blue-600"><?php echo $account_stats['total_borrowings']; ?></p>
                </div>
                <i class="fas fa-book text-blue-400 text-2xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Borrowings</p>
                    <p class="text-2xl font-bold text-green-600"><?php echo $account_stats['active_borrowings']; ?></p>
                </div>
                <i class="fas fa-book-open text-green-400 text-2xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Total Reservations</p>
                    <p class="text-2xl font-bold text-orange-600"><?php echo $account_stats['total_reservations']; ?></p>
                </div>
                <i class="fas fa-calendar-alt text-orange-400 text-2xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Active Reservations</p>
                    <p class="text-2xl font-bold text-purple-600"><?php echo $account_stats['active_reservations']; ?></p>
                </div>
                <i class="fas fa-bookmark text-purple-400 text-2xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Reviews Written</p>
                    <p class="text-2xl font-bold text-indigo-600"><?php echo $account_stats['total_reviews']; ?></p>
                </div>
                <i class="fas fa-star text-indigo-400 text-2xl"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1">Outstanding Fines</p>
                    <p class="text-2xl font-bold <?php echo $account_stats['total_fines'] > 0 ? 'text-red-600' : 'text-green-600'; ?>">
                        ₱<?php echo number_format($account_stats['total_fines'], 2); ?>
                    </p>
                </div>
                <i class="fas fa-money-bill-wave <?php echo $account_stats['total_fines'] > 0 ? 'text-red-400' : 'text-green-400'; ?> text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Profile Information -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-teal-500 to-cyan-500 p-6">
            <h2 class="text-xl font-bold text-white mb-2">Profile Information</h2>
            <p class="text-teal-100 text-sm">Update your personal information</p>
        </div>
        
        <div class="p-6">
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                        <input type="text" name="username" value="<?php echo htmlspecialchars($user_info['username'] ?? ''); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" 
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" 
                               required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user_info['phone'] ?? ''); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" 
                               placeholder="Enter your phone number">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <input type="text" value="<?php echo htmlspecialchars(ucfirst($user_info['role'] ?? 'User')); ?>" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-500" 
                               readonly>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" 
                              placeholder="Enter your address"><?php echo htmlspecialchars($user_info['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-teal-600 to-cyan-600 text-white rounded-lg hover:from-teal-700 hover:to-cyan-700 transition-all duration-300 font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-red-500 to-pink-500 p-6">
            <h2 class="text-xl font-bold text-white mb-2">Change Password</h2>
            <p class="text-red-100 text-sm">Update your account password for security</p>
        </div>
        
        <div class="p-6">
            <form method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="change_password">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password *</label>
                    <input type="password" name="current_password" 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                           required>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password *</label>
                        <input type="password" name="new_password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                               minlength="8" required>
                        <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters long</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password *</label>
                        <input type="password" name="confirm_password" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" 
                               minlength="8" required>
                        <p class="text-xs text-gray-500 mt-1">Must match the new password</p>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-300 font-medium">
                        <i class="fas fa-key mr-2"></i>
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Status -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-600 to-gray-700 p-6">
            <h2 class="text-xl font-bold text-white mb-2">Account Status</h2>
            <p class="text-gray-100 text-sm">Your account information and status</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Account Status:</span>
                        <span class="px-3 py-1 <?php echo $account_stats['total_fines'] > 0 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?> rounded-full text-sm font-medium">
                            <?php echo $account_stats['total_fines'] > 0 ? 'Suspended (Fines)' : 'Active'; ?>
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">User ID:</span>
                        <span class="text-sm text-gray-600">#<?php echo str_pad($user_info['user_id'], 6, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Member Since:</span>
                        <span class="text-sm text-gray-600"><?php echo formatDate($user_info['created_at']); ?></span>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Books Borrowed:</span>
                        <span class="text-sm text-gray-600"><?php echo $account_stats['total_borrowings']; ?> books</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Can Borrow More:</span>
                        <span class="px-3 py-1 <?php echo canBorrowMore($user_id) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> rounded-full text-sm font-medium">
                            <?php echo canBorrowMore($user_id) ? 'Yes' : 'No'; ?>
                        </span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Last Updated:</span>
                        <span class="text-sm text-gray-600"><?php echo formatDate($user_info['updated_at'] ?? $user_info['created_at']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

