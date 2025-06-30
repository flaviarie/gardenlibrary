<?php
// Set the page title 
$page_title = 'My Account';

include_once '../includes/user_header.php';
include_once '../../../includes/db.php';

// --- Account Functions ---
// View personal information (by user_id)
function get_user_info($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT user_id, username, email, role, created_at FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Edit personal information (name/username, email)
function update_user_info($user_id, $username, $email) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
    $stmt->bind_param('ssi', $username, $email, $user_id);
    return $stmt->execute();
}

// Change password
function change_user_password($user_id, $new_password) {
    global $conn;
    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param('si', $hashed, $user_id);
    return $stmt->execute();
}

// View account status (active, suspended due to fines, etc.)
function get_account_status($user_id) {
    global $conn;
    // Check for unpaid fines
    $stmt = $conn->prepare("SELECT COUNT(*) as unpaid FROM fines f JOIN borrowings b ON f.borrowing_id = b.borrowing_id WHERE b.user_id = ? AND f.status = 'unpaid'");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row && $row['unpaid'] > 0) {
        return 'suspended (unpaid fines)';
    }
    return 'active';
}
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">My Account</h2>

        <!-- Add your account management content here -->
        <p class="text-gray-600">This is the account management page. Add your account management functionality here.</p>
    </div>
</div>

<?php
include_once '../includes/user_footer.php';
?>
