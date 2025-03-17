<?php

include 'config.php';
require_once 'core.php';

// Detect if inside 'admin' folder
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';

session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login page after logout
header('Location: login.php');
exit;

?>

<head>

<!-- CSS & JS Linking (✅ Corrected Placement) -->
    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
    
</head>
