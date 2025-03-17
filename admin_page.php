<?php
include 'config.php';
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

// Define BASE_URL only if not defined
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Internship_Project_Main/');
}

// Detect if inside 'admin' folder
$currentDir = dirname(__FILE__);
$docRoot = $_SERVER['DOCUMENT_ROOT'];
$basePath = str_replace($docRoot, '', $currentDir) . '/';

$isAdmin = strpos($basePath, '/admin/') !== false;

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

    <!-- Font Awesome CDN Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom Admin CSS & JS File Link -->
    <link rel="stylesheet" href="<?= BASE_URL . 'css/' . ($isAdmin ? 'admin_style.css' : 'style.css') ?>">
    <script src="<?= BASE_URL . 'js/' . ($isAdmin ? 'admin_script.js' : 'script.js') ?>"></script>
</head>

<body>

    <!-- Include Admin Header -->
    <?php include 'admin_header.php'; ?>

    <!-- Admin Dashboard Section Starts -->
    <section class="dashboard">

        <h1 class="title">Dashboard</h1>

        <div class="box-container">

            <!-- Total Pending Payments -->
            <div class="box">
                <?php
                $select_pending = pg_query($conn, "SELECT SUM(total_price) AS total FROM orders WHERE payment_status = 'pending'") or die('Query failed');
                $row = pg_fetch_assoc($select_pending);
                $total_pendings = $row['total'] ?? 0;
                ?>
                <h3>$<?= number_format($total_pendings, 2); ?>/-</h3>
                <p>Total Pendings</p>
            </div>

            <!-- Total Completed Payments -->
            <div class="box">
                <?php
                $select_completed = pg_query($conn, "SELECT SUM(total_price) AS total FROM orders WHERE payment_status = 'completed'") or die('Query failed');
                $row = pg_fetch_assoc($select_completed);
                $total_completed = $row['total'] ?? 0;
                ?>
                <h3>$<?= number_format($total_completed, 2); ?>/-</h3>
                <p>Completed Payments</p>
            </div>

            <!-- Total Orders Placed -->
            <div class="box">
                <?php
                $select_orders = pg_query($conn, "SELECT COUNT(*) AS count FROM orders") or die('Query failed');
                $row = pg_fetch_assoc($select_orders);
                ?>
                <h3><?= $row['count']; ?></h3>
                <p>Orders Placed</p>
            </div>

            <!-- Total Products Added -->
            <div class="box">
                <?php
                $select_products = pg_query($conn, "SELECT COUNT(*) AS count FROM products") or die('Query failed');
                $row = pg_fetch_assoc($select_products);
                ?>
                <h3><?= $row['count']; ?></h3>
                <p>Products Added</p>
            </div>

            <!-- Total Normal Users -->
            <div class="box">
                <?php
                $select_users = pg_query($conn, "SELECT COUNT(*) AS count FROM users WHERE user_type = 'user'") or die('Query failed');
                $row = pg_fetch_assoc($select_users);
                ?>
                <h3><?= $row['count']; ?></h3>
                <p>Normal Users</p>
            </div>

            <!-- Total Admin Users -->
            <div class="box">
                <?php
                $select_admins = pg_query($conn, "SELECT COUNT(*) AS count FROM users WHERE user_type = 'admin'") or die('Query failed');
                $row = pg_fetch_assoc($select_admins);
                ?>
                <h3><?= $row['count']; ?></h3>
                <p>Admin Users</p>
            </div>

            <!-- Total Accounts -->
            <div class="box">
                <?php
                $select_account = pg_query($conn, "SELECT COUNT(*) AS count FROM users") or die('Query failed');
                $row = pg_fetch_assoc($select_account);
                ?>
                <h3><?= $row['count']; ?></h3>
                <p>Total Accounts</p>
            </div>

            <!-- Total New Messages -->
            <div class="box">
                <?php
                $select_messages = pg_query($conn, "SELECT COUNT(*) AS count FROM message") or die('Query failed');
                $row = pg_fetch_assoc($select_messages);
                ?>
                <h3><?= $row['count']; ?></h3>
                <p>New Messages</p>
            </div>

        </div>

    </section>
    <!-- Admin Dashboard Section Ends -->

</body>

</html>
