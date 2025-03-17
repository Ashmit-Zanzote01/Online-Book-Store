<?php
include 'config.php'; // Ensure database connection is included
require_once 'core.php';

// Database connection parameters
$host = "localhost";
$dbname = "book_store_site"; // Replace with your actual database name
$user = "postgres";
$password = "Ashmit@1203*";

// Establishing the database connection
global $conn;
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit;
}

// Define BASE_URL only if not defined
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/Internship_Project_Main/'); // Adjust this if needed
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- CSS Linking -->
    <link rel="stylesheet" href="<?= BASE_URL . 'css/admin_style.css' ?>">
</head>
<body>

<?php
// Display session messages (if any)
if (!empty($_SESSION['message'])) {
    foreach ($_SESSION['message'] as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>';
    }
    unset($_SESSION['message']); // Clear messages after displaying
}
?>

<header class="header">
   <div class="flex">
      <a href="<?= BASE_URL ?>admin_page.php" class="logo">Admin<span>Panel</span></a>

      <nav class="navbar">
         <a href="<?= BASE_URL ?>admin_page.php">Home</a>
         <a href="<?= BASE_URL ?>admin_products.php">Products</a>
         <a href="<?= BASE_URL ?>admin_orders.php">Orders</a>
         <a href="<?= BASE_URL ?>admin_users.php">Users</a>
         <a href="<?= BASE_URL ?>admin_contacts.php">Messages</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="account-box">
         <p>Username: <span><?= isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'N/A'; ?></span></p>
         <p>Email: <span><?= isset($_SESSION['admin_email']) ? htmlspecialchars($_SESSION['admin_email']) : 'N/A'; ?></span></p>
         <a href="<?= BASE_URL ?>logout.php" class="delete-btn">Logout</a>
         <div>New <a href="<?= BASE_URL ?>login.php">Login</a> | <a href="<?= BASE_URL ?>register.php">Register</a></div>
      </div>
   </div>
</header>

<!-- Ensure JS is loaded after HTML -->
<script src="<?= BASE_URL . 'js/admin_script.js' ?>"></script>
<script src="<?= BASE_URL . 'js/script.js' ?>"></script>

</body>
</html>
