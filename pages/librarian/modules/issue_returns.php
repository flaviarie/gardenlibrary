<?php
$page_title = 'Issue & Returns';

include_once '../includes/librarian_header.php';
include_once '../../../includes/db.php';

// Message handling
$message = '';
$message_type = '';

// Process book borrowing
function borrow_book($book_id, $user_id, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM borrowings WHERE user_id = ? AND return_date IS NULL");
    $stmt->execute([$user_id]);
    $active_borrows = $stmt->fetchColumn();
    if ($active_borrows >= 2) {
        return ['success' => false, 'message' => 'Borrowing limit reached (2 books).'];
    }
    
    $stmt = $pdo->prepare("SELECT status FROM books WHERE book_id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$book || $book['status'] !== 'available') {
        return ['success' => false, 'message' => 'Book is not available for borrowing.'];
    }
    
    $borrow_date = date('Y-m-d');
    $due_date = date('Y-m-d', strtotime('+7 days'));
    $stmt = $pdo->prepare("INSERT INTO borrowings (book_id, user_id, borrow_date, due_date) VALUES (?, ?, ?, ?)");
    $success = $stmt->execute([$book_id, $user_id, $borrow_date, $due_date]);
    if ($success) {
        $pdo->prepare("UPDATE books SET status = 'borrowed' WHERE book_id = ?")->execute([$book_id]);
    }
    return ['success' => $success];
}

// Process book returns
function return_book($book_id, $user_id, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM borrowings WHERE book_id = ? AND user_id = ? AND return_date IS NULL");
    $stmt->execute([$book_id, $user_id]);
    $borrowing = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$borrowing) {
        return ['success' => false, 'message' => 'No active borrowing found.'];
    }
    
    $return_date = date('Y-m-d');
    $stmt = $pdo->prepare("UPDATE borrowings SET return_date = ? WHERE borrowing_id = ?");
    $success = $stmt->execute([$return_date, $borrowing['borrowing_id']]);
    if ($success) {
        $pdo->prepare("UPDATE books SET status = 'available' WHERE book_id = ?")->execute([$book_id]);
        $due_date = $borrowing['due_date'];
        if ($return_date > $due_date) {
            $days_overdue = (strtotime($return_date) - strtotime($due_date)) / (60*60*24);
            $fine = ceil($days_overdue) * 10.00;
            $pdo->prepare("INSERT INTO fines (borrowing_id, amount) VALUES (?, ?)")->execute([$borrowing['borrowing_id'], $fine]);
        }
    }
    return ['success' => $success];
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['issue_book'])) {
        $book_id = trim($_POST['book_id']);
        $user_id = intval($_POST['user_id']);
        
        if (empty($book_id) || empty($user_id)) {
            $message = 'All fields are required.';
            $message_type = 'error';
        } else {
            $result = borrow_book($book_id, $user_id, $pdo);
            if ($result['success']) {
                $message = 'Book issued successfully!';
                $message_type = 'success';
            } else {
                $message = $result['message'] ?? 'Failed to issue book.';
                $message_type = 'error';
            }
        }
    }
    
    if (isset($_POST['return_book'])) {
        $book_id = trim($_POST['book_id']);
        $user_id = intval($_POST['user_id']);
        
        if (empty($book_id) || empty($user_id)) {
            $message = 'All fields are required.';
            $message_type = 'error';
        } else {
            $result = return_book($book_id, $user_id, $pdo);
            if ($result['success']) {
                $message = 'Book returned successfully!';
                $message_type = 'success';
            } else {
                $message = $result['message'] ?? 'Failed to return book.';
                $message_type = 'error';
            }
        }
    }
}

// Fetch data for display
$stmt = $pdo->prepare("SELECT user_id, username, email FROM users WHERE role = 'student' ORDER BY username");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT book_id, title, author, category, book_cover FROM books WHERE status = 'available' AND is_deleted = 0 ORDER BY title");
$stmt->execute();
$available_books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT b.borrowing_id, b.book_id, b.user_id, b.borrow_date, b.due_date, 
           books.title, books.author, books.book_cover, u.username, u.email
    FROM borrowings b
    JOIN books ON b.book_id = books.book_id
    JOIN users u ON b.user_id = u.user_id
    WHERE b.return_date IS NULL
    ORDER BY b.borrow_date DESC
");
$stmt->execute();
$active_borrowings = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Issue & Returns</h2>
        
        <!-- Issue Book Form -->
        <div class="mb-8 p-6 bg-green-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-green-800">Issue Book</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                    <select name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
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
                    <select name="book_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="">Select a book...</option>
                        <?php foreach ($available_books as $book): ?>
                            <option value="<?php echo htmlspecialchars($book['book_id']); ?>">
                                <?php echo htmlspecialchars($book['title']); ?> by <?php echo htmlspecialchars($book['author']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" name="issue_book" class="w-full px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Issue Book
                    </button>
                </div>
            </form>
        </div>

        <!-- Return Book Form -->
        <div class="mb-8 p-6 bg-blue-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4 text-blue-800">Return Book</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select User</label>
                    <select name="user_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select a user...</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['user_id']; ?>">
                                <?php echo htmlspecialchars($user['username']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Book ID</label>
                    <input type="text" name="book_id" required placeholder="Enter book ID..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" name="return_book" class="w-full px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Return Book
                    </button>
                </div>
            </form>
        </div>

        <!-- Active Borrowings -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Active Borrowings</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Book</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">User</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Borrow Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Due Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($active_borrowings as $borrowing): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <img src="../assets/img/<?php echo htmlspecialchars($borrowing['book_cover']); ?>" 
                                             alt="<?php echo htmlspecialchars($borrowing['title']); ?>" 
                                             class="w-10 h-12 object-cover rounded mr-3"
                                             onerror="this.src='../assets/img/default_book_cover.svg'">
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($borrowing['title']); ?></div>
                                            <div class="text-sm text-gray-500">by <?php echo htmlspecialchars($borrowing['author']); ?></div>
                                            <div class="text-xs text-gray-400">ID: <?php echo htmlspecialchars($borrowing['book_id']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium"><?php echo htmlspecialchars($borrowing['username']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($borrowing['email']); ?></div>
                                </td>
                                <td class="px-4 py-2"><?php echo date('M j, Y', strtotime($borrowing['borrow_date'])); ?></td>
                                <td class="px-4 py-2">
                                    <?php 
                                    $due_date = strtotime($borrowing['due_date']);
                                    $today = strtotime(date('Y-m-d'));
                                    $is_overdue = $today > $due_date;
                                    ?>
                                    <span class="<?php echo $is_overdue ? 'text-red-600 font-medium' : 'text-gray-900'; ?>">
                                        <?php echo date('M j, Y', $due_date); ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2">
                                    <?php if ($is_overdue): ?>
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Overdue</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-2">
                                    <button onclick="returnBook('<?php echo htmlspecialchars($borrowing['book_id']); ?>', '<?php echo $borrowing['user_id']; ?>', '<?php echo htmlspecialchars($borrowing['title']); ?>')" 
                                            class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                        Return
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if (empty($active_borrowings)): ?>
                    <div class="text-center py-8">
                        <p class="text-gray-500">No active borrowings found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/issue-returns.js"></script>

<?php
include_once '../includes/librarian_footer.php';
?>
