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

// Detect if inside 'admin' folder
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit; // Ensure no further code is executed after redirection
}

if (isset($_POST['add_to_cart'])) {
    $product_name = pg_escape_string($conn, $_POST['product_name']);
    $product_price = pg_escape_string($conn, $_POST['product_price']);
    $product_image = pg_escape_string($conn, $_POST['product_image']);
    $product_quantity = pg_escape_string($conn, $_POST['product_quantity']);
    
    // Use parameterized queries to prevent SQL injection
    $check_cart_numbers = pg_query_params(
        $conn,
        "SELECT * FROM cart WHERE name = $1 AND user_id = $2",
        array($product_name, $user_id)
        );
    
    if (pg_num_rows($check_cart_numbers) > 0) {
        $message[] = 'Product already added to cart!';
    } else {
        // Use parameterized query for inserting data
        $insert_result = pg_query_params(
            $conn,
            "INSERT INTO cart (user_id, name, price, quantity, image) VALUES ($1, $2, $3, $4, $5)",
            array($user_id, $product_name, $product_price, $product_quantity, $product_image)
            );
        
        if ($insert_result) {
            $message[] = 'Product added to cart!';
        } else {
            $message[] = 'Failed to add product to cart!';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Search Page</title>

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
   <h3>Search Page</h3>
   <p><a href="home.php">Home</a> / Search</p>
</div>

<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search" placeholder="Search products..." class="box" required>
      <input type="submit" name="submit" value="Search" class="btn">
   </form>
</section>

<section class="products" style="padding-top: 0;">

   <div class="box-container">
      <?php
         if (isset($_POST['submit'])) {
            $search_item = pg_escape_string($conn, $_POST['search']);
            $select_products = pg_query_params(
               $conn, 
               "SELECT * FROM products WHERE name LIKE $1", 
               array('%' . $search_item . '%')
            );
            
            if (pg_num_rows($select_products) > 0) {
               while ($fetch_product = pg_fetch_assoc($select_products)) {
      ?>
      <form action="" method="post" class="box">
         <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="" class="image">
         <div class="name"><?php echo $fetch_product['name']; ?></div>
         <div class="price">$<?php echo $fetch_product['price']; ?>/-</div>
         <input type="number" class="qty" name="product_quantity" min="1" value="1">
         <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
         <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
         <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
         <input type="submit" class="btn" value="Add to Cart" name="add_to_cart">
      </form>
      <?php
               }
            } else {
               echo '<p class="empty">No result found!</p>';
            }
         } else {
            echo '<p class="empty">Search something!</p>';
         }
      ?>
   </div>

</section>

<?php include 'footer.php'; ?>

<!-- Custom JS File Link -->
<script src="../script.js"></script>

</body>
</html>
