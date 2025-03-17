<?php
// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
include_once 'config.php';

// Ensure session variables are set
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = null;
}

// ================== NEW PATH MANAGEMENT SYSTEM ==================
// Define absolute paths
define('ROOT_PATH', realpath(dirname(__FILE__)));
define('CSS_PATH', ROOT_PATH . '/css');
define('JS_PATH', ROOT_PATH . '/js');
define('IMG_PATH', ROOT_PATH . '/images');

// Dynamic base path detection
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : './';

// Define BASE_URL for absolute links
define('BASE_URL', $protocol . '://' . $host . dirname($_SERVER['PHP_SELF']) . '/');

// Auto-versioning function for assets
function asset_version($file_type, $file_name) {
    $map = [
        'css' => CSS_PATH,
        'js' => JS_PATH
    ];
    
    if (!array_key_exists($file_type, $map)) {
        return time(); // Fallback timestamp
    }
    
    $full_path = $map[$file_type] . '/' . $file_name;
    return file_exists($full_path) ? filemtime($full_path) : time();
}

// ================== EXISTING AUTOLOADER ==================
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        include_once $file;
    }
});




    ?>