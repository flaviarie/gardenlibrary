<?php
// Set the page title 
$page_title = 'Manage Books';

include_once '../includes/librarian_header.php';
include_once '../../../includes/db_connection.php'; // Use PDO connection

// --- CATEGORY VALIDATION ---
function is_valid_category($category) {
    $valid_categories = ['FIC', 'SCI', 'HIS', 'TEC', 'PHI', 'BIO', 'ART', 'REF', 'KID', 'OTH']; // Add more as needed
    return in_array(strtoupper($category), $valid_categories);
}

// --- BOOK ID GENERATION ---
function generate_book_id($title, $publish_date, $category, $added_date, $pdo) {
    // 1. First 2 letters from the Book Title (letters only, uppercase)
    $title_prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $title), 0, 2));
    // 2. Month (published, 3-letter uppercase)
    $publish_month = strtoupper(date('M', strtotime($publish_date)));
    // 3. Day (added to the system, 2 digits)
    $added_day = str_pad(date('d', strtotime($added_date)), 2, '0', STR_PAD_LEFT);
    // 4. Year (published, 4 digits)
    $publish_year = date('Y', strtotime($publish_date));
    // 5. Category (3-letter code, uppercase)
    $category = strtoupper($category);
    // 6. Count of books in the library (not deleted)
    $stmt = $pdo->query("SELECT COUNT(*)+1 FROM books WHERE is_deleted = 0");
    $count = $stmt->fetchColumn();
    $formatted_count = str_pad($count, 5, '0', STR_PAD_LEFT);
    // 7. Format: THFEB102022-FIC00001
    return $title_prefix . $publish_month . $added_day . $publish_year . '-' . $category . $formatted_count;
}

// --- MAINTAIN MINIMUM BOOKS ---
function ensure_minimum_books($pdo, $min = 50) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM books WHERE is_deleted = 0");
    $count = $stmt->fetchColumn();
    return $count >= $min;
}

// --- ARCHIVE BOOK ---
function archive_book($book_id, $pdo) {
    $stmt = $pdo->prepare("UPDATE books SET status = 'archived' WHERE book_id = ?");
    return $stmt->execute([$book_id]);
}

// --- UPLOAD BOOK COVER ---
function upload_book_cover($file, $book_title) {
    $upload_dir = '../assets/img/';
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Check if file was uploaded without errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error.'];
    }
        
    // Check file size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File too large. Maximum size is 5MB.'];
    }
    
    // Check file type
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, and SVG are allowed.'];
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safe_title = preg_replace('/[^a-zA-Z0-9_-]/', '_', $book_title);
    $filename = 'book_' . $safe_title . '_' . time() . '.' . $file_extension;
    $target_path = $upload_dir . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Failed to save uploaded file.'];
    }
}

// --- ADD BOOK (with validation) ---
function add_book($title, $author, $publish_date, $category, $added_date, $pdo, $cover_file = null) {
    if (!is_valid_category($category)) {
        return ['success' => false, 'message' => 'Invalid category.'];
    }
    
    $book_id = generate_book_id($title, $publish_date, $category, $added_date, $pdo);
    
    // Handle book cover - now required
    if (!$cover_file || $cover_file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'Book cover is required.'];
    }
    
    $upload_result = upload_book_cover($cover_file, $title);
    if (!$upload_result['success']) {
        return $upload_result; // Return error if upload fails
    }
    
    $book_cover = $upload_result['filename'];
    
    $stmt = $pdo->prepare("INSERT INTO books (book_id, title, author, publish_date, category, book_cover, added_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'available')");
    $success = $stmt->execute([$book_id, $title, $author, $publish_date, strtoupper($category), $book_cover, $added_date]);
    return ['success' => $success, 'book_id' => $book_id];
}

// --- GET BOOK COVER ---
function get_book_cover($title, $author) {
    $cover_map = [
        'A Brief History of Time' => 'brief_history_time.svg',
        '1984' => '1984.svg',
        'Pride and Prejudice' => 'pride_prejudice.svg',
        'The Great Gatsby' => 'great_gatsby.svg',
        'The Catcher in the Rye' => 'catcher_rye.svg',
        'Lord of the Flies' => 'lord_flies.svg',
        'The Hobbit' => 'hobbit.svg',
        'Harry Potter and the Philosopher\'s Stone' => 'harry_potter.svg',
        'Cosmos' => 'cosmos.svg',
        'To Kill a Mockingbird' => 'mockingbird.svg',
        'The Origin of Species' => 'origin_species.svg',
        'The Selfish Gene' => 'selfish_gene.svg',
        'Silent Spring' => 'silent_spring.svg',
        'Sapiens: A Brief History of Humankind' => 'sapiens.svg',
        'The Guns of August' => 'guns_august.svg',
        'A People\'s History of the United States' => 'people_history.svg',
        'The Diary of a Young Girl' => 'diary_young_girl.svg',
        'Clean Code' => 'clean_code.svg',
        'The Pragmatic Programmer' => 'pragmatic_programmer.svg',
        'Design Patterns' => 'design_patterns.svg',
        'Introduction to Algorithms' => 'algorithms.svg',
        'The Republic' => 'republic.svg',
        'Meditations' => 'meditations.svg',
        'The Art of War' => 'art_of_war.svg',
        'Anthony langgam' => 'anthony_langgam.svg',
        'buhay ng Aso' => 'buhay_aso.svg'
    ];
    
    return isset($cover_map[$title]) ? $cover_map[$title] : 'default_book_cover.svg';
}

// --- HANDLE FORM SUBMISSIONS ---
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_book'])) {
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $publish_date = $_POST['publish_date'];
        $category = $_POST['category'];
        $added_date = date('Y-m-d');
        $cover_file = isset($_FILES['book_cover']) ? $_FILES['book_cover'] : null;
        
        // Validate all required fields
        if (empty($title) || empty($author) || empty($publish_date) || empty($category)) {
            $message = 'All fields (Title, Author, Publish Date, and Category) are required.';
            $message_type = 'error';
        } 
        // Validate publish date is not in the future
        elseif (strtotime($publish_date) > time()) {
            $message = 'Publish date cannot be in the future.';
            $message_type = 'error';
        }
        // Validate category
        elseif (!is_valid_category($category)) {
            $message = 'Please select a valid category.';
            $message_type = 'error';
        }
        // Validate book cover is provided
        elseif (!$cover_file || $cover_file['error'] === UPLOAD_ERR_NO_FILE) {
            $message = 'Book cover is required. Please upload an image file.';
            $message_type = 'error';
        }
        // Validate title length
        elseif (strlen($title) < 2 || strlen($title) > 255) {
            $message = 'Title must be between 2 and 255 characters.';
            $message_type = 'error';
        }
        // Validate author length
        elseif (strlen($author) < 2 || strlen($author) > 255) {
            $message = 'Author name must be between 2 and 255 characters.';
            $message_type = 'error';
        }
        else {
            $result = add_book($title, $author, $publish_date, $category, $added_date, $pdo, $cover_file);
            if ($result['success']) {
                $message = 'Book added successfully! Book ID: ' . $result['book_id'];
                $message_type = 'success';
            } else {
                $message = $result['message'] ?? 'Failed to add book.';
                $message_type = 'error';
            }
        }
    }
    
    if (isset($_POST['update_book'])) {
        $book_id = $_POST['book_id'];
        $title = trim($_POST['title']);
        $author = trim($_POST['author']);
        $publish_date = $_POST['publish_date'];
        $category = $_POST['category'];
        $cover_file = isset($_FILES['edit_book_cover']) ? $_FILES['edit_book_cover'] : null;
        
        // Validate all required fields
        if (empty($title) || empty($author) || empty($publish_date) || empty($category)) {
            $message = 'All fields (Title, Author, Publish Date, and Category) are required.';
            $message_type = 'error';
        } 
        // Validate publish date is not in the future
        elseif (strtotime($publish_date) > time()) {
            $message = 'Publish date cannot be in the future.';
            $message_type = 'error';
        }
        // Validate category
        elseif (!is_valid_category($category)) {
            $message = 'Please select a valid category.';
            $message_type = 'error';
        }
        // Validate title length
        elseif (strlen($title) < 2 || strlen($title) > 255) {
            $message = 'Title must be between 2 and 255 characters.';
            $message_type = 'error';
        }
        // Validate author length
        elseif (strlen($author) < 2 || strlen($author) > 255) {
            $message = 'Author name must be between 2 and 255 characters.';
            $message_type = 'error';
        }
        else {
            // Handle book cover for update
            $book_cover = null; // Will be set based on conditions
            
            if ($cover_file && $cover_file['error'] === UPLOAD_ERR_OK) {
                // New cover uploaded
                $upload_result = upload_book_cover($cover_file, $title);
                if ($upload_result['success']) {
                    $book_cover = $upload_result['filename'];
                } else {
                    $message = $upload_result['message'];
                    $message_type = 'error';
                }
            } else {
                // No new cover uploaded, keep existing cover
                $stmt = $pdo->prepare("SELECT book_cover FROM books WHERE book_id = ?");
                $stmt->execute([$book_id]);
                $current_book = $stmt->fetch(PDO::FETCH_ASSOC);
                $book_cover = $current_book['book_cover'];
            }
            
            if ($message_type !== 'error') {
                $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, publish_date = ?, category = ?, book_cover = ? WHERE book_id = ?");
                $success = $stmt->execute([$title, $author, $publish_date, strtoupper($category), $book_cover, $book_id]);
                
                if ($success) {
                    $message = 'Book updated successfully!';
                    $message_type = 'success';
                } else {
                    $message = 'Failed to update book.';
                    $message_type = 'error';
                }
            }
        }
    }
    
    if (isset($_POST['delete_book'])) {
        $book_id = $_POST['book_id'];
        $stmt = $pdo->prepare("UPDATE books SET is_deleted = 1 WHERE book_id = ?");
        $success = $stmt->execute([$book_id]);
        
        if ($success) {
            $message = 'Book deleted successfully!';
            $message_type = 'success';
        } else {
            $message = 'Failed to delete book.';
            $message_type = 'error';
        }
    }
}

// --- FETCH BOOKS ---
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';

$where_conditions = ['is_deleted = 0'];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(title LIKE ? OR author LIKE ? OR book_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category_filter)) {
    $where_conditions[] = "category = ?";
    $params[] = $category_filter;
}

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

$where_clause = implode(' AND ', $where_conditions);
$stmt = $pdo->prepare("SELECT * FROM books WHERE $where_clause ORDER BY added_date DESC");
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Manage Books</h2>
        
        <!-- Add Book Form -->
        <div class="mb-8 p-6 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-semibold mb-4">Add New Book</h3>
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    All fields marked with <span class="text-red-500">*</span> are required. Please ensure you have all book information and a cover image before submitting.
                </p>
            </div>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Author <span class="text-red-500">*</span></label>
                    <input type="text" name="author" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Publish Date <span class="text-red-500">*</span></label>
                    <input type="date" name="publish_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                    <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Category</option>
                        <option value="FIC">Fiction (FIC)</option>
                        <option value="SCI">Science (SCI)</option>
                        <option value="HIS">History (HIS)</option>
                        <option value="TEC">Technology (TEC)</option>
                        <option value="PHI">Philosophy (PHI)</option>
                        <option value="BIO">Biography (BIO)</option>
                        <option value="ART">Art (ART)</option>
                        <option value="REF">Reference (REF)</option>
                        <option value="KID">Kids (KID)</option>
                        <option value="OTH">Other (OTH)</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Book Cover <span class="text-red-500">*</span></label>
                    <input type="file" name="book_cover" accept="image/*,.svg" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Upload a cover image (JPEG, PNG, GIF, SVG). Max size: 5MB. This field is required.</p>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" name="add_book" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Add Book
                    </button>
                </div>
            </form>
        </div>

        <!-- Search and Filter -->
        <div class="mb-6">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-64">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search books..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select name="category" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        <option value="FIC" <?php echo $category_filter === 'FIC' ? 'selected' : ''; ?>>Fiction</option>
                        <option value="SCI" <?php echo $category_filter === 'SCI' ? 'selected' : ''; ?>>Science</option>
                        <option value="HIS" <?php echo $category_filter === 'HIS' ? 'selected' : ''; ?>>History</option>
                        <option value="TEC" <?php echo $category_filter === 'TEC' ? 'selected' : ''; ?>>Technology</option>
                        <option value="PHI" <?php echo $category_filter === 'PHI' ? 'selected' : ''; ?>>Philosophy</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="available" <?php echo $status_filter === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="borrowed" <?php echo $status_filter === 'borrowed' ? 'selected' : ''; ?>>Borrowed</option>
                        <option value="reserved" <?php echo $status_filter === 'reserved' ? 'selected' : ''; ?>>Reserved</option>
                        <option value="archived" <?php echo $status_filter === 'archived' ? 'selected' : ''; ?>>Archived</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    Filter
                </button>
            </form>
        </div>

        <!-- Books Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($books as $book): ?>
                <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Book Cover -->
                    <div class="h-48 bg-gray-100 flex items-center justify-center">
                        <img src="../assets/img/<?php echo htmlspecialchars($book['book_cover']); ?>" 
                             alt="<?php echo htmlspecialchars($book['title']); ?>" 
                             class="h-full w-auto object-contain"
                             onerror="this.src='../assets/img/default_book_cover.svg'">
                    </div>
                    
                    <!-- Book Info -->
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2 text-gray-800 line-clamp-2"><?php echo htmlspecialchars($book['title']); ?></h3>
                        <p class="text-gray-600 text-sm mb-2">by <?php echo htmlspecialchars($book['author']); ?></p>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded"><?php echo htmlspecialchars($book['category']); ?></span>
                            <span class="text-xs px-2 py-1 rounded <?php 
                                echo $book['status'] === 'available' ? 'bg-green-100 text-green-800' : 
                                    ($book['status'] === 'borrowed' ? 'bg-red-100 text-red-800' : 
                                    ($book['status'] === 'reserved' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'));
                            ?>">
                                <?php echo ucfirst($book['status']); ?>
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">ID: <?php echo htmlspecialchars($book['book_id']); ?></p>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-2">
                            <button onclick="editBook('<?php echo htmlspecialchars($book['book_id']); ?>', '<?php echo htmlspecialchars($book['title']); ?>', '<?php echo htmlspecialchars($book['author']); ?>', '<?php echo $book['publish_date']; ?>', '<?php echo $book['category']; ?>')" 
                                    class="flex-1 px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                Edit
                            </button>
                            <button onclick="deleteBook('<?php echo htmlspecialchars($book['book_id']); ?>', '<?php echo htmlspecialchars($book['title']); ?>')" 
                                    class="flex-1 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($books)): ?>
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">No books found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Book Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Edit Book</h3>
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="edit_book_id" name="book_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_title" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Author <span class="text-red-500">*</span></label>
                        <input type="text" id="edit_author" name="author" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Publish Date <span class="text-red-500">*</span></label>
                        <input type="date" id="edit_publish_date" name="publish_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select id="edit_category" name="category" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="FIC">Fiction (FIC)</option>
                            <option value="SCI">Science (SCI)</option>
                            <option value="HIS">History (HIS)</option>
                            <option value="TEC">Technology (TEC)</option>
                            <option value="PHI">Philosophy (PHI)</option>
                            <option value="BIO">Biography (BIO)</option>
                            <option value="ART">Art (ART)</option>
                            <option value="REF">Reference (REF)</option>
                            <option value="KID">Kids (KID)</option>
                            <option value="OTH">Other (OTH)</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Book Cover (Optional for Updates)</label>
                        <input type="file" name="edit_book_cover" accept="image/*,.svg" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Upload a new cover to replace the current one. Leave empty to keep existing cover.</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" name="update_book" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update Book
                        </button>
                        <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editBook(bookId, title, author, publishDate, category) {
    document.getElementById('edit_book_id').value = bookId;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_author').value = author;
    document.getElementById('edit_publish_date').value = publishDate;
    document.getElementById('edit_category').value = category;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function deleteBook(bookId, title) {
    if (confirm('Are you sure you want to delete "' + title + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="book_id" value="' + bookId + '"><input type="hidden" name="delete_book" value="1">';
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Add form validation
    const forms = document.querySelectorAll('form[method="POST"]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const title = form.querySelector('input[name="title"]');
            const author = form.querySelector('input[name="author"]');
            const publishDate = form.querySelector('input[name="publish_date"]');
            const category = form.querySelector('select[name="category"]');
            const bookCover = form.querySelector('input[name="book_cover"]');
            
            let isValid = true;
            let errorMessage = '';
            
            // Validate title
            if (title && (title.value.trim().length < 2 || title.value.trim().length > 255)) {
                isValid = false;
                errorMessage += 'Title must be between 2 and 255 characters.\n';
            }
            
            // Validate author
            if (author && (author.value.trim().length < 2 || author.value.trim().length > 255)) {
                isValid = false;
                errorMessage += 'Author name must be between 2 and 255 characters.\n';
            }
            
            // Validate publish date
            if (publishDate && publishDate.value) {
                const selectedDate = new Date(publishDate.value);
                const today = new Date();
                if (selectedDate > today) {
                    isValid = false;
                    errorMessage += 'Publish date cannot be in the future.\n';
                }
            }
            
            // Validate category
            if (category && !category.value) {
                isValid = false;
                errorMessage += 'Please select a category.\n';
            }
            
            // Validate book cover for add form (not edit form)
            if (bookCover && bookCover.name === 'book_cover' && !bookCover.files.length) {
                isValid = false;
                errorMessage += 'Please upload a book cover image.\n';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fix the following errors:\n\n' + errorMessage);
                return false;
            }
        });
    });
});
</script>

<?php
include_once '../includes/librarian_footer.php';
?>

