<?php
// Admin Helper Functions
// Common functions used across admin modules

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check admin privileges (super admin only)
function isAdmin() {
    return isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin' && $_SESSION['username'] === 'admin';
}

// Function to log admin actions
function logAdminAction($pdo, $action, $details = '') {
    try {
        // Only log if we're not in the middle of an AJAX response
        if (!headers_sent()) {
            $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, details, timestamp) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_SESSION['user_id'], $action, $details]);
        }
    } catch (PDOException $e) {
        error_log("Failed to log admin action: " . $e->getMessage());
    }
}

// Function to get system statistics
function getSystemStats($pdo) {
    try {
        $stats = [];
        
        // Total users (students)
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student'");
        $stats['total_users'] = $stmt->fetchColumn();
        
        // Total librarians (admin role users who are not the main admin)
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND username != 'admin'");
        $stats['total_librarians'] = $stmt->fetchColumn();
        
        // Total books
        $stmt = $pdo->query("SELECT COUNT(*) FROM books WHERE is_deleted = FALSE");
        $stats['total_books'] = $stmt->fetchColumn();
        
        // Active borrowings
        $stmt = $pdo->query("SELECT COUNT(*) FROM borrowings WHERE return_date IS NULL");
        $stats['active_borrowings'] = $stmt->fetchColumn();
        
        // Overdue books
        $stmt = $pdo->query("SELECT COUNT(*) FROM borrowings WHERE return_date IS NULL AND due_date < CURDATE()");
        $stats['overdue_books'] = $stmt->fetchColumn();
        
        // Today's registrations
        $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()");
        $stats['today_registrations'] = $stmt->fetchColumn();
        
        return $stats;
    } catch (PDOException $e) {
        error_log("Error getting system stats: " . $e->getMessage());
        return [
            'total_users' => 0,
            'total_librarians' => 0,
            'total_books' => 0,
            'active_borrowings' => 0,
            'overdue_books' => 0,
            'today_registrations' => 0
        ];
    }
}

// Function to validate admin permissions
function requireAdminAccess() {
    if (!isAdmin()) {
        // Handle AJAX requests differently
        if (isset($_POST['action']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            echo json_encode(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
            exit();
        } else {
            // For regular page requests, redirect to login
            if (!headers_sent()) {
                header('Location: ../../login/');
            } else {
                echo '<script>window.location.href = "../../login/";</script>';
            }
            exit();
        }
    }
}

// Function to get all users with pagination
function getAllUsers($pdo, $page = 1, $per_page = 10, $search = '', $role_filter = '', $status_filter = '') {
    $offset = ($page - 1) * $per_page;
    
    $where_conditions = ["1=1"];
    $params = [];
    
    if (!empty($search)) {
        $where_conditions[] = "(u.username LIKE ? OR u.email LIKE ?)";
        $search_param = "%{$search}%";
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    if (!empty($role_filter)) {
        if ($role_filter == 'librarian') {
            // Map librarian to admin role (excluding main admin)
            $where_conditions[] = "u.role = 'admin' AND u.username != 'admin'";
        } else if ($role_filter == 'user') {
            // Map user to student role
            $where_conditions[] = "u.role = 'student'";
        } else {
            $where_conditions[] = "u.role = ?";
            $params[] = $role_filter;
        }
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) FROM users u WHERE {$where_clause}";
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_users = $stmt->fetchColumn();
    
    // Get users with comprehensive status calculation
    $sql = "SELECT u.*, 
                   DATE_FORMAT(u.created_at, '%M %d, %Y at %h:%i %p') as formatted_date,
                   (SELECT COUNT(*) FROM borrowings b WHERE b.user_id = u.user_id AND b.return_date IS NULL) as active_borrowings,
                   (SELECT COUNT(*) FROM borrowings b WHERE b.user_id = u.user_id AND b.return_date IS NULL AND b.due_date < CURDATE() - INTERVAL 30 DAY) as severely_overdue,
                   (SELECT COALESCE(SUM(f.amount), 0) FROM borrowings b LEFT JOIN fines f ON b.borrowing_id = f.borrowing_id WHERE b.user_id = u.user_id AND f.status = 'unpaid') as unpaid_fines
            FROM users u 
            WHERE {$where_clause}
            ORDER BY u.created_at DESC 
            LIMIT " . (int)$per_page . " OFFSET " . (int)$offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate effective status for each user
    foreach ($users as &$user) {
        $user['effective_status'] = calculateUserEffectiveStatus($user);
        $user['suspension_reason'] = calculateSuspensionReason($user);
    }
    
    // Apply status filter after calculating effective status
    if (!empty($status_filter)) {
        $users = array_filter($users, function($user) use ($status_filter) {
            return $user['effective_status'] === $status_filter;
        });
        $total_users = count($users);
    }
    
    return [
        'users' => $users,
        'total' => $total_users,
        'pages' => ceil($total_users / $per_page),
        'current_page' => $page
    ];
}

// Calculate the effective status of a user considering all factors
function calculateUserEffectiveStatus($user) {
    // Check if manually suspended first
    if (isset($user['status']) && $user['status'] === 'suspended') {
        return 'suspended';
    }
    
    // Check for automatic suspension conditions
    if ($user['severely_overdue'] > 0) {
        return 'suspended'; // Automatically suspended due to severely overdue books
    }
    
    if ($user['unpaid_fines'] > 50) { // Suspend if fines exceed ₱50
        return 'suspended'; // Automatically suspended due to excessive fines
    }
    
    return 'active';
}

// Calculate suspension reason
function calculateSuspensionReason($user) {
    if (isset($user['status']) && $user['status'] === 'suspended') {
        return 'Manually suspended by administrator';
    }
    
    if ($user['severely_overdue'] > 0) {
        return 'Books overdue for more than 30 days';
    }
    
    if ($user['unpaid_fines'] > 50) {
        return 'Unpaid fines exceed ₱50.00';
    }
    
    return '';
}

// Function to create new user
function createUser($pdo, $username, $email, $password, $role = 'user', $full_name = '') {
    try {
        // Map roles to database schema
        $db_role = ($role == 'user') ? 'student' : (($role == 'librarian') ? 'admin' : $role);
        
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user with default active status
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())");
        $stmt->execute([$username, $email, $hashed_password, $db_role]);
        
        return ['success' => true, 'message' => 'User created successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error creating user: ' . $e->getMessage()];
    }
}

// Function to update user
function updateUser($pdo, $user_id, $username, $email, $role, $full_name = '', $password = '') {
    try {
        // Map roles to database schema
        $db_role = ($role == 'user') ? 'student' : (($role == 'librarian') ? 'admin' : $role);
        
        // Check if username or email already exists for other users
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
        $stmt->execute([$username, $email, $user_id]);
        
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Username or email already exists'];
        }
        
        // Update user
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ? WHERE user_id = ?");
            $stmt->execute([$username, $email, $hashed_password, $db_role, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?");
            $stmt->execute([$username, $email, $db_role, $user_id]);
        }
        
        return ['success' => true, 'message' => 'User updated successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()];
    }
}

// Function to suspend user account
function suspendUser($pdo, $user_id) {
    try {
        // Cannot suspend the main admin
        $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $user['username'] === 'admin') {
            return ['success' => false, 'message' => 'Cannot suspend the main administrator account'];
        }
        
        // Update user status to suspended
        $stmt = $pdo->prepare("UPDATE users SET status = 'suspended' WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Log the action
        logAdminAction($pdo, 'User Suspended', "User ID: {$user_id}");
        
        return ['success' => true, 'message' => 'User account suspended successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error suspending user: ' . $e->getMessage()];
    }
}

// Function to activate user account
function activateUser($pdo, $user_id) {
    try {
        // Update user status to active
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Log the action
        logAdminAction($pdo, 'User Activated', "User ID: {$user_id}");
        
        return ['success' => true, 'message' => 'User account activated successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error activating user: ' . $e->getMessage()];
    }
}

// Function to delete user (hard delete since no is_deleted field)
function deleteUser($pdo, $user_id) {
    try {
        // Cannot delete the main admin
        $stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && $user['username'] === 'admin') {
            return ['success' => false, 'message' => 'Cannot delete the main administrator account'];
        }
        
        // Check if user has active borrowings
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND return_date IS NULL");
        $stmt->execute([$user_id]);
        
        if ($stmt->fetchColumn() > 0) {
            return ['success' => false, 'message' => 'Cannot delete user with active borrowings'];
        }
        
        // Delete user
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Log the action
        logAdminAction($pdo, 'User Deleted', "User ID: {$user_id}");
        
        return ['success' => true, 'message' => 'User deleted successfully'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()];
    }
}

// Function to generate reports
function generateReports($pdo, $report_type, $date_from = null, $date_to = null) {
    try {
        switch ($report_type) {
            case 'user_registrations':
                $sql = "SELECT DATE(created_at) as date, COUNT(*) as count 
                        FROM users 
                        WHERE 1=1"; // Remove is_deleted condition
                if ($date_from) $sql .= " AND DATE(created_at) >= '$date_from'";
                if ($date_to) $sql .= " AND DATE(created_at) <= '$date_to'";
                $sql .= " GROUP BY DATE(created_at) ORDER BY date DESC";
                break;
                
            case 'borrowing_activity':
                $sql = "SELECT DATE(borrow_date) as date, COUNT(*) as count 
                        FROM borrowings";
                if ($date_from) $sql .= " WHERE DATE(borrow_date) >= '$date_from'";
                if ($date_to) {
                    $sql .= $date_from ? " AND" : " WHERE";
                    $sql .= " DATE(borrow_date) <= '$date_to'";
                }
                $sql .= " GROUP BY DATE(borrow_date) ORDER BY date DESC";
                break;
                
            case 'popular_books':
                $sql = "SELECT b.title, b.author, COUNT(br.borrowing_id) as borrow_count 
                        FROM books b 
                        LEFT JOIN borrowings br ON b.book_id = br.book_id 
                        WHERE b.is_deleted = FALSE";
                if ($date_from) $sql .= " AND DATE(br.borrow_date) >= '$date_from'";
                if ($date_to) $sql .= " AND DATE(br.borrow_date) <= '$date_to'";
                $sql .= " GROUP BY b.book_id ORDER BY borrow_count DESC LIMIT 10";
                break;
                
            default:
                return ['success' => false, 'message' => 'Invalid report type'];
        }
        
        $stmt = $pdo->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return ['success' => true, 'data' => $data];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Error generating report: ' . $e->getMessage()];
    }
}

// Function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    
    return $bytes;
}

?>
