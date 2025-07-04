<?php
$page_title = 'Reservations';

include_once '../includes/librarian_header.php';
include_once '../../../includes/db_connection.php';

// Create reservations table if needed
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS reservations (
        reservation_id INT AUTO_INCREMENT PRIMARY KEY,
        book_id VARCHAR(25) NOT NULL,
        user_id INT NOT NULL,
        status ENUM('pending', 'notified', 'fulfilled', 'cancelled') DEFAULT 'pending',
        reserved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        notified_at TIMESTAMP NULL,
        expires_at TIMESTAMP NULL,
        FOREIGN KEY (book_id) REFERENCES books(book_id),
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )");
} catch (Exception $e) {
    // Table might already exist
}

$message = '';
$message_type = '';

// Book reservation functions
function reserve_book($book_id, $user_id, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE book_id = ? AND is_deleted = 0");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$book) {
        return ['success' => false, 'message' => 'Book not found.'];
    }
    
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE book_id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$book_id, $user_id]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'User already has a pending reservation for this book.'];
    }
    
    $stmt = $pdo->prepare("INSERT INTO reservations (book_id, user_id, status, reserved_at) VALUES (?, ?, 'pending', NOW())");
    $success = $stmt->execute([$book_id, $user_id]);
    
    if ($success && $book['status'] === 'available') {
        $pdo->prepare("UPDATE books SET status = 'reserved' WHERE book_id = ?")->execute([$book_id]);
    }
    
    return ['success' => $success];
}

function fulfill_reservation($reservation_id, $pdo) {
    $stmt = $pdo->prepare("UPDATE reservations SET status = 'fulfilled' WHERE reservation_id = ?");
    $success = $stmt->execute([$reservation_id]);
    
    if ($success) {
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE reservation_id = ?");
        $stmt->execute([$reservation_id]);
        $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($reservation) {
            $pdo->prepare("UPDATE books SET status = 'borrowed' WHERE book_id = ?")->execute([$reservation['book_id']]);
            
            $borrow_date = date('Y-m-d');
            $due_date = date('Y-m-d', strtotime('+7 days'));
            $pdo->prepare("INSERT INTO borrowings (book_id, user_id, borrow_date, due_date) VALUES (?, ?, ?, ?)")
                ->execute([$reservation['book_id'], $reservation['user_id'], $borrow_date, $due_date]);
        }
    }
    
    return ['success' => $success];
}

function cancel_reservation($reservation_id, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE reservation_id = ?");
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ?");
    $success = $stmt->execute([$reservation_id]);
    
    if ($success && $reservation) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE book_id = ? AND status = 'pending'");
        $stmt->execute([$reservation['book_id']]);
        $pending_count = $stmt->fetchColumn();
        
        if ($pending_count == 0) {
            $pdo->prepare("UPDATE books SET status = 'available' WHERE book_id = ?")->execute([$reservation['book_id']]);
        }
    }
    
    return ['success' => $success];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reserve_book'])) {
        $book_id = trim($_POST['book_id']);
        $user_id = intval($_POST['user_id']);
        
        if (empty($book_id) || empty($user_id)) {
            $message = 'All fields are required.';
            $message_type = 'error';
        } else {
            $result = reserve_book($book_id, $user_id, $pdo);
            if ($result['success']) {
                $message = 'Book reserved successfully!';
                $message_type = 'success';
            } else {
                $message = $result['message'] ?? 'Failed to reserve book.';
                $message_type = 'error';
            }
        }
    }
    
    if (isset($_POST['fulfill_reservation'])) {
        $reservation_id = intval($_POST['reservation_id']);
        $result = fulfill_reservation($reservation_id, $pdo);
        if ($result['success']) {
            $message = 'Reservation fulfilled successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to fulfill reservation.';
            $message_type = 'error';
        }
    }
    
    if (isset($_POST['cancel_reservation'])) {
        $reservation_id = intval($_POST['reservation_id']);
        $result = cancel_reservation($reservation_id, $pdo);
        if ($result['success']) {
            $message = 'Reservation cancelled successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to cancel reservation.';
            $message_type = 'error';
        }
    }
}

// Fetch data for display
$stmt = $pdo->prepare("SELECT user_id, username, email FROM users WHERE role = 'student' ORDER BY username");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT book_id, title, author, status, book_cover FROM books WHERE is_deleted = 0 ORDER BY title");
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT r.reservation_id, r.book_id, r.user_id, r.status, r.reserved_at,
           b.title, b.author, b.book_cover, u.username, u.email
    FROM reservations r
    JOIN books b ON r.book_id = b.book_id
    JOIN users u ON r.user_id = u.user_id
    WHERE r.status = 'pending'
    ORDER BY r.reserved_at ASC
");
$stmt->execute();
$pending_reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT r.reservation_id, r.book_id, r.user_id, r.status, r.reserved_at,
           b.title, b.author, b.book_cover, u.username, u.email
    FROM reservations r
    JOIN books b ON r.book_id = b.book_id
    JOIN users u ON r.user_id = u.user_id
    WHERE r.status IN ('fulfilled', 'cancelled')
    ORDER BY r.reserved_at DESC
    LIMIT 20
");
$stmt->execute();
$reservation_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto px-4">
    <!-- Alert Messages -->
    <?php if (!empty($message)): ?>
        <div class="mb-4 p-4 rounded-lg <?php echo $message_type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Reservations</h2>
        
        <!-- Reserve Book Form -->
        <div class="mb-8 p-6 bg-purple-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-purple-800">Reserve Book</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                    <select name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Select a user...</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['user_id']; ?>">
                                <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Book</label>
                    <select name="book_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">Select a book...</option>
                        <?php foreach ($books as $book): ?>
                            <option value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                <?php echo htmlspecialchars($book['title']); ?> by <?php echo htmlspecialchars($book['author']); ?> 
                                (<?php echo ucfirst($book['status']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" name="reserve_book" class="w-full px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        Reserve Book
                    </button>
                </div>
            </form>
        </div>

        <!-- Pending Reservations -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Pending Reservations</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Book</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">User</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Reserved Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($pending_reservations as $reservation): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <img src="../assets/img/<?php echo htmlspecialchars($reservation['book_cover']); ?>" 
                                             alt="<?php echo htmlspecialchars($reservation['title']); ?>" 
                                             class="w-10 h-12 object-cover rounded mr-3"
                                             onerror="this.src='../assets/img/default_book_cover.svg'">
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($reservation['title']); ?></div>
                                            <div class="text-sm text-gray-500">by <?php echo htmlspecialchars($reservation['author']); ?></div>
                                            <div class="text-xs text-gray-400">ID: <?php echo htmlspecialchars($reservation['book_id']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium"><?php echo htmlspecialchars($reservation['username']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($reservation['email']); ?></div>
                                </td>
                                <td class="px-4 py-2"><?php echo date('M j, Y g:i A', strtotime($reservation['reserved_at'])); ?></td>
                                <td class="px-4 py-2">
                                    <div class="flex gap-2">
                                        <button onclick="fulfillReservation(<?php echo $reservation['reservation_id']; ?>, '<?php echo htmlspecialchars($reservation['title']); ?>')" 
                                                class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                            Fulfill
                                        </button>
                                        <button onclick="cancelReservation(<?php echo $reservation['reservation_id']; ?>, '<?php echo htmlspecialchars($reservation['title']); ?>')" 
                                                class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                            Cancel
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if (empty($pending_reservations)): ?>
                    <div class="text-center py-8">
                        <p class="text-gray-500">No pending reservations found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Reservation History -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Reservation History</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Book</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">User</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Reserved Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($reservation_history as $reservation): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <img src="../assets/img/<?php echo htmlspecialchars($reservation['book_cover']); ?>" 
                                             alt="<?php echo htmlspecialchars($reservation['title']); ?>" 
                                             class="w-10 h-12 object-cover rounded mr-3"
                                             onerror="this.src='../assets/img/default_book_cover.svg'">
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($reservation['title']); ?></div>
                                            <div class="text-sm text-gray-500">by <?php echo htmlspecialchars($reservation['author']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium"><?php echo htmlspecialchars($reservation['username']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($reservation['email']); ?></div>
                                </td>
                                <td class="px-4 py-2"><?php echo date('M j, Y g:i A', strtotime($reservation['reserved_at'])); ?></td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs font-medium rounded <?php 
                                        echo $reservation['status'] === 'fulfilled' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                    ?>">
                                        <?php echo ucfirst($reservation['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if (empty($reservation_history)): ?>
                    <div class="text-center py-8">
                        <p class="text-gray-500">No reservation history found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/reservations.js"></script>

<?php
include_once '../includes/librarian_footer.php';
?>
