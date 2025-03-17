<?php

include 'config.php';
require_once 'core.php';

// Database connection parameters
$host = "localhost";
$dbname = "book_store_site";
$user = "postgres";
$password = "Ashmit@1203*";

// Establish database connection
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
    header('Location: login.php');
    exit;
}

// Detect if inside 'admin' folder
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Your Orders</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS File Link -->
<!--    <link rel="stylesheet" href="../style.css"> -->

<!-- CSS & JS Linking (✅ Corrected Placement) -->
    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Your Orders</h3>
   <p><a href="home.php">Home</a> / Orders</p>
</div>

<section class="placed-orders">
   <h1 class="title">Placed Orders</h1>

   <div class="box-container">
      <?php
         // Fetch user's orders from the database
         $order_query = pg_query($conn, "SELECT * FROM  orders WHERE user_id = '$user_id'") or die('Query failed');
         
         if(pg_num_rows($order_query) > 0){
            while($fetch_orders = pg_fetch_assoc($order_query)){
      ?>
      <div class="box">
         <p>Placed on: <span><?php echo $fetch_orders['placed_on']; ?></span></p>
         <p>Name: <span><?php echo $fetch_orders['name']; ?></span></p>
         <p>Number: <span><?php echo $fetch_orders['number']; ?></span></p>
         <p>Email: <span><?php echo $fetch_orders['email']; ?></span></p>
         <p>Address: <span><?php echo $fetch_orders['address']; ?></span></p>
         <p>Payment Method: <span><?php echo $fetch_orders['method']; ?></span></p>
         <p>Your Orders: <span><?php echo $fetch_orders['total_products']; ?></span></p>
         <p>Total Price: <span>$<?php echo $fetch_orders['total_price']; ?>/-</span></p>
         <p>Payment Status: 
            <span style="color:<?php echo ($fetch_orders['payment_status'] == 'pending') ? 'red' : 'green'; ?>;">
               <?php echo $fetch_orders['payment_status']; ?>
            </span>
         </p>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No orders placed yet!</p>';
         }
      ?>
   </div>
</section>

<?php include 'footer.php'; ?>

<!-- Custom JS File Link -->
<script src="../script.js"></script>

</body>
</html>
