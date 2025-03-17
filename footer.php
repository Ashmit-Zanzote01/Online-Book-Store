<?php 

require_once 'core.php';

// Detect if inside 'admin' folder
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';

?>

<head>
	
	<!-- CSS & JS Linking (✅ Corrected Placement) -->
    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>

</head>
<section class="footer">

   <div class="box-container">

      <div class="box">
         <h3>Quick Links</h3>
         <a href="home.php">Home</a>
         <a href="about.php">About</a>
         <a href="shop.php">Shop</a>
         <a href="contact.php">Contact</a>
      </div>

      <div class="box">
         <h3>Extra Links</h3>
         <a href="login.php">Login</a>
         <a href="register.php">Register</a>
         <a href="cart.php">Cart</a>
         <a href="orders.php">Orders</a>
      </div>

      <div class="box">
         <h3>Contact Info</h3>
         <p><i class="fas fa-phone"></i> +91-9637659999</p>
<!--          <p><i class="fas fa-phone"></i> +111-222-3333</p> -->
         <p><i class="fas fa-envelope"></i> thearcaneink@gmail.com</p>
         <p><i class="fas fa-map-marker-alt"></i> Nagpur, Maharashtra, India - 440030</p>
      </div>

      <div class="box">
         <h3>Follow Us</h3>
         <a href="#"> <i class="fab fa-facebook-f"></i> Facebook </a>
         <a href="#"> <i class="fab fa-twitter"></i> Twitter </a>
         <a href="#"> <i class="fab fa-instagram"></i> Instagram </a>
         <a href="#"> <i class="fab fa-linkedin"></i> LinkedIn </a>
      </div>

   </div>

   <p class="credit"> &copy; <?php echo date('Y'); ?> All rights reserved by <span>The Arcane Ink</span></p>

</section>
