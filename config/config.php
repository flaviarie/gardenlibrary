
<?php
//Auto detect base path
$script_path = str_replace('\\', '/',dirname($_SERVER['SCRIPT_NAME'] ?? ''));

// --- LOCAL DEV OVERRIDE ---
// If a file named .localdev exists in the project root, always use local settings
$force_local = false;
$localdev_file = dirname(__DIR__) . '/.localdev';
if (file_exists($localdev_file)) {
    $force_local = true;
}
if ($force_local) { echo '<!-- LOCALDEV DETECTED -->'; }

// Check if we're on production hosting (InfinityFree, AwardSpace, etc.)
$is_production = false;
if (!$force_local && isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    if (strpos($host, 'infinityfree') !== false || 
        strpos($host, '.infinityfreeapp.com') !== false ||
        strpos($host, 'awardspace') !== false ||
        strpos($host, '.awardspace.net') !== false ||
        strpos($host, '.awardspace.info') !== false ||
        strpos($host, 'netlify') !== false ||
        strpos($host, 'vercel') !== false ||
        strpos($host, 'github.io') !== false ||
        (!strpos($host, 'localhost') && !strpos($host, '127.0.0.1'))) {
        $is_production = true;
    }
}

if ($is_production) {
    // For production hosting, try to detect the correct path
    $path_parts = explode('/', trim($script_path, '/'));
    $project_index = array_search('gardenlibrary', $path_parts);
    if ($project_index !== false) {
        $site_url = '/' . implode('/', array_slice($path_parts, 0, $project_index + 1)) . '/';
    } else {
        // If gardenlibrary is not found in path, use root
        $site_url = '/';
    }
} else {
    // For local development - use file path calculation
    $current_file_path = str_replace('\\', '/', __FILE__);
    $htdocs_pos = strpos($current_file_path, '/htdocs/');
    if ($htdocs_pos !== false) {
        $relative_path = substr($current_file_path, $htdocs_pos + 8); // +8 for '/htdocs/'
        $path_to_project = str_replace('/config/config.php', '', $relative_path);
        $site_url = '/' . $path_to_project . '/';
    } else {
        // Fallback - try traditional method
        $path_parts = explode('/', trim($script_path, '/'));
        $project_index = array_search('gardenlibrary', $path_parts);
        if ($project_index !== false){
            $site_url = '/' . implode('/', array_slice($path_parts, 0, $project_index + 1)) . '/';
        } else {
            // Final fallback
            $site_url = '/blanca/Final Project -Library System/8/gardenlibrary/';
        }
    }
}


$base_path = $_SERVER['DOCUMENT_ROOT'] . $site_url;
$include_path = dirname(dirname(__FILE__)) . '/';

function asset_url($path) {
    global $site_url;
    return $site_url . ltrim($path, '/');
}

if ($is_production) {
    $host = 'fdb1033.awardspace.net'; // awardspace MySQL hostname
    $dbname = '4656167_purringpage'; // awardspace DB name
    $username = '4656167_purringpage'; // awardspace DB user
    $password = 'I4lIH61y2Vglh%N:'; // awardspace DB password
    $port = 3306; // Default MySQL port
} else {
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'purringpage';
    $port = 3306;
}

// Other global settings
$site_name = 'Garden Library';
$admin_email = 'admin@gardenlibrary.com';

// Create MySQLi connection (procedural style)
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Log error instead of displaying it in production
    error_log("PDO connection failed: " . $e->getMessage());
    die("Database connection failed. Please contact administrator.");
}
?>
