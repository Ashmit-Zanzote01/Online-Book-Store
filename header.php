<?php
include 'config.php'; // Database connection only
require_once 'core.php';

// Start session if not started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Detect if inside 'admin' folder
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';

// Retrieve user session details
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['user_name'] ?? 'Guest';
$user_email = $_SESSION['user_email'] ?? 'Not Available';

// Retrieve cart item count
$cart_rows_number = 0;
if ($user_id) {
    $select_cart_number = pg_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");
    $cart_rows_number = pg_num_rows($select_cart_number);
}

// Display messages if available
if (!empty($_SESSION['message'])) {
    foreach ($_SESSION['message'] as $msg) {
        echo '<div class="message">
                <span>' . htmlspecialchars($msg) . '</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
              </div>';
    }
    unset($_SESSION['message']); // Clear messages after displaying
}
?>

<head>
    <!-- ✅ CSS & JS Linking (Corrected Placement) -->
    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
</head>

<header class="header">

   <div class="header-1">
      <div class="flex">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <p>
            <?php if ($user_id): ?>
                Welcome, <?php echo htmlspecialchars($user_name); ?> | <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a> | <a href="register.php">Register</a>
            <?php endif; ?>
         </p>
      </div>
   </div>

   <div class="header-2">
      <div class="flex">
         <a href="home.php" class="logo">The Arcane Ink</a>

         <nav class="navbar">
            <a href="home.php">Home</a>
            <a href="about.php">About</a>
            <a href="shop.php">Shop</a>
            <a href="contact.php">Contact</a>
            <a href="orders.php">Orders</a>
         </nav>

         <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <a href="search_page.php" class="fas fa-search"></a>
            <div id="user-btn" class="fas fa-user"></div>
            <a href="cart.php"> 
                <i class="fas fa-shopping-cart"></i> 
                <span>(<?php echo $cart_rows_number; ?>)</span> 
            </a>
         </div>

         <div class="user-box">
            <p>Username: <span><?php echo htmlspecialchars($user_name); ?></span></p>
            <p>Email: <span><?php echo htmlspecialchars($user_email); ?></span></p>
            <?php if ($user_id): ?>
                <a href="logout.php" class="delete-btn">Logout</a>
            <?php endif; ?>
         </div>
      </div>
   </div>

</header>
