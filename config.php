<?php
// Database connection parameters
$host = "localhost";
$dbname = "book_store_site"; // Replace with your actual database name
$user = "postgres";
$password = "Ashmit@1203*";

require_once 'core.php';

// ✅ Prevent multiple session_start() calls
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Prevent multiple BASE_URL definitions
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/Internship_Project_Main/');
}

// Establishing the database connection
global $conn;
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

// Error handling for connection failure
if (!$conn) {
    error_log("Connection failed: " . pg_last_error());
    die("Database connection failed. Please try again later.");
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
