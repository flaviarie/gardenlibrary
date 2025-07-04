<?php
// User helper functions - Revised and secured
// Core user functions with proper error handling and security

// Check login status
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Get user data with proper error handling
function getUserInfo($user_id) {
    try {
        global $pdo;
        if (!$pdo) {
            throw new Exception("Database connection not available");
        }
        
        $stmt = $pdo->prepare("SELECT user_id, username, email, role, created_at FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("getUserInfo error: " . $e->getMessage());
        return false;
    }
}

// Calculate late fees with proper validation
function calculateFine($due_date) {
    try {
        if (empty($due_date)) {
            return 0;
        }
        
        $today = new DateTime();
        $due = new DateTime($due_date);
        $interval = $today->diff($due);
        $days_late = (int)$interval->format('%r%a');
        $fine_per_day = 10.00; // ₱10 per day late

        if ($days_late < 0) {
            return abs($days_late) * $fine_per_day;
        }
        return 0;
    } catch (Exception $e) {
        error_log("calculateFine error: " . $e->getMessage());
        return 0;
    }
}

// Check book borrowing limit
function canBorrowMore($user_id) {
    try {
        global $pdo;
        if (!$pdo) {
            return false;
        }
        
        $max_books = 5;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND return_date IS NULL");
        $stmt->execute([$user_id]);
        $borrowed = $stmt->fetchColumn();
        return $borrowed < $max_books;
    } catch (Exception $e) {
        error_log("canBorrowMore error: " . $e->getMessage());
        return false;
    }
}

// Check if user account is suspended
function isAccountSuspended($user_id) {
    try {
        global $pdo;
        if (!$pdo) {
            return false;
        }
        
        // Check if user has a status column (for backward compatibility)
        $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'status'");
        $stmt->execute();
        $statusColumn = $stmt->fetch();
        
        if ($statusColumn) {
            // Check if account is suspended
            $stmt = $pdo->prepare("SELECT status FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $status = $stmt->fetchColumn();
            return $status === 'suspended';
        }
        
        // If no status column, check for overdue books and excessive fines as suspension criteria
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM borrowings 
            WHERE user_id = ? AND return_date IS NULL 
            AND due_date < CURDATE() - INTERVAL 30 DAY
        ");
        $stmt->execute([$user_id]);
        $overdueBooks = $stmt->fetchColumn();
        
        // Consider account suspended if user has books overdue for more than 30 days
        return $overdueBooks > 0;
        
    } catch (Exception $e) {
        error_log("isAccountSuspended error: " . $e->getMessage());
        return false;
    }
}

// Get suspension reason
function getSuspensionReason($user_id) {
    try {
        global $pdo;
        if (!$pdo) {
            return 'Database connection error';
        }
        
        // Check if user has a suspension_reason column
        $stmt = $pdo->prepare("SHOW COLUMNS FROM users LIKE 'suspension_reason'");
        $stmt->execute();
        $reasonColumn = $stmt->fetch();
        
        if ($reasonColumn) {
            $stmt = $pdo->prepare("SELECT suspension_reason FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            return $stmt->fetchColumn() ?: 'No reason specified';
        }
        
        // Default reason based on overdue books
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM borrowings 
            WHERE user_id = ? AND return_date IS NULL 
            AND due_date < CURDATE() - INTERVAL 30 DAY
        ");
        $stmt->execute([$user_id]);
        $overdueBooks = $stmt->fetchColumn();
        
        if ($overdueBooks > 0) {
            return "Account suspended due to books overdue for more than 30 days";
        }
        
        return 'Account restrictions in place';
        
    } catch (Exception $e) {
        error_log("getSuspensionReason error: " . $e->getMessage());
        return 'Unable to determine suspension reason';
    }
}

// Check if user can perform borrowing actions
function canPerformBorrowingActions($user_id) {
    return !isAccountSuspended($user_id);
}

// Force login check with proper redirect
function requireUserAccess() {
    if (!isLoggedIn()) {
        header('Location: ../../login/index.php');
        exit();
    }
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Format date for display
function formatDate($date) {
    try {
        if (empty($date)) {
            return 'N/A';
        }
        return date('M d, Y', strtotime($date));
    } catch (Exception $e) {
        return 'N/A';
    }
}

// Check if book is overdue
function isOverdue($due_date) {
    try {
        if (empty($due_date)) {
            return false;
        }
        return new DateTime() > new DateTime($due_date);
    } catch (Exception $e) {
        return false;
    }
}

?>