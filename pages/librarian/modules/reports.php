<?php
$page_title = 'Reports';

include_once '../includes/librarian_header.php';
include_once '../../../includes/db.php';

// Report generation functions
function get_borrowed_books_report($pdo) {
    $stmt = $pdo->query("SELECT b.book_id, b.title, b.author, b.book_cover, u.username, u.email, br.borrow_date, br.due_date, br.return_date FROM borrowings br JOIN books b ON br.book_id = b.book_id JOIN users u ON br.user_id = u.user_id WHERE br.return_date IS NULL ORDER BY br.borrow_date DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_overdue_items_report($pdo) {
    $stmt = $pdo->query("SELECT b.book_id, b.title, b.author, b.book_cover, u.username, u.email, br.borrow_date, br.due_date, DATEDIFF(CURDATE(), br.due_date) as days_overdue FROM borrowings br JOIN books b ON br.book_id = b.book_id JOIN users u ON br.user_id = u.user_id WHERE br.return_date IS NULL AND br.due_date < CURDATE() ORDER BY br.due_date ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_fines_collected_report($pdo) {
    $stmt = $pdo->query("SELECT SUM(amount) as total_collected, COUNT(*) as total_fines FROM fines WHERE status = 'paid'");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_unpaid_fines_report($pdo) {
    $stmt = $pdo->query("SELECT f.fine_id, f.amount, f.created_at, b.book_id, b.title, u.username, u.email FROM fines f JOIN borrowings br ON f.borrowing_id = br.borrowing_id JOIN books b ON br.book_id = b.book_id JOIN users u ON br.user_id = u.user_id WHERE f.status = 'unpaid' ORDER BY f.created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_inventory_status_report($pdo) {
    $stmt = $pdo->query("SELECT category, COUNT(*) as total_books, SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_books, SUM(CASE WHEN status = 'borrowed' THEN 1 ELSE 0 END) as borrowed_books, SUM(CASE WHEN status = 'archived' THEN 1 ELSE 0 END) as archived_books, SUM(CASE WHEN status = 'reserved' THEN 1 ELSE 0 END) as reserved_books FROM books WHERE is_deleted = 0 GROUP BY category ORDER BY category");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_popular_books_report($pdo) {
    $stmt = $pdo->query("SELECT b.book_id, b.title, b.author, b.book_cover, COUNT(br.borrowing_id) as borrow_count FROM books b LEFT JOIN borrowings br ON b.book_id = br.book_id WHERE b.is_deleted = 0 GROUP BY b.book_id ORDER BY borrow_count DESC LIMIT 10");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_recent_activity_report($pdo) {
    $stmt = $pdo->query("SELECT b.book_id, b.title, b.author, b.book_cover, u.username, br.borrow_date, br.return_date FROM borrowings br JOIN books b ON br.book_id = b.book_id JOIN users u ON br.user_id = u.user_id ORDER BY br.borrow_date DESC LIMIT 20");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Generate all reports
$borrowed_books = get_borrowed_books_report($pdo);
$overdue_items = get_overdue_items_report($pdo);
$fines_collected = get_fines_collected_report($pdo);
$unpaid_fines = get_unpaid_fines_report($pdo);
$inventory_status = get_inventory_status_report($pdo);
$popular_books = get_popular_books_report($pdo);
$recent_activity = get_recent_activity_report($pdo);

$total_unpaid_fines = array_sum(array_column($unpaid_fines, 'amount'));
?>

<!-- Page Content Goes Here -->
<div class="container mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Library Reports</h2>
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Active Borrowings</h3>
                <p class="text-3xl font-bold text-blue-600"><?php echo count($borrowed_books); ?></p>
            </div>
            <div class="bg-red-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Overdue Items</h3>
                <p class="text-3xl font-bold text-red-600"><?php echo count($overdue_items); ?></p>
            </div>
            <div class="bg-green-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-green-800 mb-2">Fines Collected</h3>
                <p class="text-3xl font-bold text-green-600">₱<?php echo number_format($fines_collected['total_collected'] ?? 0, 2); ?></p>
            </div>
            <div class="bg-yellow-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-yellow-800 mb-2">Unpaid Fines</h3>
                <p class="text-3xl font-bold text-yellow-600">₱<?php echo number_format($total_unpaid_fines, 2); ?></p>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="mb-6">
            <nav class="flex space-x-8">
                <button class="report-tab active px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600" data-tab="borrowed">
                    Active Borrowings
                </button>
                <button class="report-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="overdue">
                    Overdue Items
                </button>
                <button class="report-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="fines">
                    Unpaid Fines
                </button>
                <button class="report-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="inventory">
                    Inventory Status
                </button>
                <button class="report-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="popular">
                    Popular Books
                </button>
                <button class="report-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="recent">
                    Recent Activity
                </button>
            </nav>
        </div>

        <!-- Active Borrowings Tab -->
        <div id="borrowed-tab" class="report-content">
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
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($borrowed_books as $book): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <img src="../assets/img/<?php echo htmlspecialchars($book['book_cover']); ?>" 
                                             alt="<?php echo htmlspecialchars($book['title']); ?>" 
                                             class="w-10 h-12 object-cover rounded mr-3"
                                             onerror="this.src='../assets/img/default_book_cover.svg'">
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($book['title']); ?></div>
                                            <div class="text-sm text-gray-500">by <?php echo htmlspecialchars($book['author']); ?></div>
                                            <div class="text-xs text-gray-400">ID: <?php echo htmlspecialchars($book['book_id']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium"><?php echo htmlspecialchars($book['username']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($book['email']); ?></div>
                                </td>
                                <td class="px-4 py-2"><?php echo date('M j, Y', strtotime($book['borrow_date'])); ?></td>
                                <td class="px-4 py-2"><?php echo date('M j, Y', strtotime($book['due_date'])); ?></td>
                                <td class="px-4 py-2">
                                    <?php if (strtotime($book['due_date']) < strtotime(date('Y-m-d'))): ?>
                                        <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">Overdue</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Active</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Overdue Items Tab -->
        <div id="overdue-tab" class="report-content hidden">
            <h3 class="text-lg font-semibold mb-4">Overdue Items</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Book</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">User</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Due Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Days Overdue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($overdue_items as $item): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <img src="../assets/img/<?php echo htmlspecialchars($item['book_cover']); ?>" 
                                             alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                             class="w-10 h-12 object-cover rounded mr-3"
                                             onerror="this.src='../assets/img/default_book_cover.svg'">
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($item['title']); ?></div>
                                            <div class="text-sm text-gray-500">by <?php echo htmlspecialchars($item['author']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium"><?php echo htmlspecialchars($item['username']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($item['email']); ?></div>
                                </td>
                                <td class="px-4 py-2 text-red-600"><?php echo date('M j, Y', strtotime($item['due_date'])); ?></td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">
                                        <?php echo $item['days_overdue']; ?> days
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Unpaid Fines Tab -->
        <div id="fines-tab" class="report-content hidden">
            <h3 class="text-lg font-semibold mb-4">Unpaid Fines</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">User</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Book</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Fine Amount</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Created Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($unpaid_fines as $fine): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div class="font-medium"><?php echo htmlspecialchars($fine['username']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($fine['email']); ?></div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium"><?php echo htmlspecialchars($fine['title']); ?></div>
                                    <div class="text-xs text-gray-400">ID: <?php echo htmlspecialchars($fine['book_id']); ?></div>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="font-medium text-red-600">₱<?php echo number_format($fine['amount'], 2); ?></span>
                                </td>
                                <td class="px-4 py-2"><?php echo date('M j, Y', strtotime($fine['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Inventory Status Tab -->
        <div id="inventory-tab" class="report-content hidden">
            <h3 class="text-lg font-semibold mb-4">Inventory Status by Category</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($inventory_status as $category): ?>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($category['category']); ?></h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Total:</span>
                                <span class="font-medium"><?php echo $category['total_books']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Available:</span>
                                <span class="font-medium text-green-600"><?php echo $category['available_books']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Borrowed:</span>
                                <span class="font-medium text-blue-600"><?php echo $category['borrowed_books']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Reserved:</span>
                                <span class="font-medium text-yellow-600"><?php echo $category['reserved_books']; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Archived:</span>
                                <span class="font-medium text-gray-600"><?php echo $category['archived_books']; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Popular Books Tab -->
        <div id="popular-tab" class="report-content hidden">
            <h3 class="text-lg font-semibold mb-4">Most Popular Books</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($popular_books as $book): ?>
                    <div class="bg-gray-50 p-4 rounded-lg flex items-center">
                        <img src="../assets/img/<?php echo htmlspecialchars($book['book_cover']); ?>" 
                             alt="<?php echo htmlspecialchars($book['title']); ?>" 
                             class="w-16 h-20 object-cover rounded mr-4"
                             onerror="this.src='../assets/img/default_book_cover.svg'">
                        <div>
                            <h4 class="font-semibold"><?php echo htmlspecialchars($book['title']); ?></h4>
                            <p class="text-sm text-gray-600">by <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="text-sm text-blue-600 font-medium"><?php echo $book['borrow_count']; ?> borrows</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Activity Tab -->
        <div id="recent-tab" class="report-content hidden">
            <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Book</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">User</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Borrow Date</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Return Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($recent_activity as $activity): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <div class="flex items-center">
                                        <img src="../assets/img/<?php echo htmlspecialchars($activity['book_cover']); ?>" 
                                             alt="<?php echo htmlspecialchars($activity['title']); ?>" 
                                             class="w-10 h-12 object-cover rounded mr-3"
                                             onerror="this.src='../assets/img/default_book_cover.svg'">
                                        <div>
                                            <div class="font-medium"><?php echo htmlspecialchars($activity['title']); ?></div>
                                            <div class="text-sm text-gray-500">by <?php echo htmlspecialchars($activity['author']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <div class="font-medium"><?php echo htmlspecialchars($activity['username']); ?></div>
                                </td>
                                <td class="px-4 py-2"><?php echo date('M j, Y', strtotime($activity['borrow_date'])); ?></td>
                                <td class="px-4 py-2">
                                    <?php if ($activity['return_date']): ?>
                                        <span class="text-green-600"><?php echo date('M j, Y', strtotime($activity['return_date'])); ?></span>
                                    <?php else: ?>
                                        <span class="text-yellow-600">Not returned</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="../assets/css/reports.css">
<script src="../assets/js/reports.js"></script>

<?php
include_once '../includes/librarian_footer.php';
?>
