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

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle add to cart functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_name = pg_escape_string($conn, $_POST['product_name']);
    $product_price = pg_escape_string($conn, $_POST['product_price']);
    $product_image = pg_escape_string($conn, $_POST['product_image']);
    $product_quantity = (int)$_POST['product_quantity'];
    
    $check_cart_query = "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'";
    $check_cart_result = pg_query($conn, $check_cart_query) or die('Query failed');
    
    if (pg_num_rows($check_cart_result) > 0) {
        $message[] = 'Already added to cart!';
    } else {
        $insert_cart_query = "INSERT INTO cart (user_id, name, price, quantity, image)
                              VALUES ('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')";
        pg_query($conn, $insert_cart_query) or die('Query failed');
        $message[] = 'Product added to cart!';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>

   <!-- Font Awesome CDN -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS -->
   <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
   <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
</head>
<body>

<?php include 'header.php'; ?>

<section class="home">
   <div class="content">
      <h3>Hand-Picked Books to Your Door</h3>
      <p>Explore our collection of carefully selected books delivered straight to your home.</p>
      <a href="about.php" class="white-btn">Discover More</a>
   </div>
</section>

<section class="products">
   <h1 class="title">Latest Products</h1>

   <div class="box-container">
      <?php  
         $select_products_query = "SELECT * FROM products ORDER BY id DESC LIMIT 6";
         $select_products_result = pg_query($conn, $select_products_query) or die('Query failed');

         if (pg_num_rows($select_products_result) > 0) {
            while ($fetch_products = pg_fetch_assoc($select_products_result)) {
                $image_path = BASE_URL . "images/";
                
                if (!empty($fetch_products['image'])) {
                    $image_path .= htmlspecialchars($fetch_products['image']);
                } else {
                    $image_path .= "default.jpg"; // Use a default image if missing
                }
      ?>
     <form action="" method="post" class="box">
      <img class="image" src="<?= $image_path ?>" alt="<?= htmlspecialchars($fetch_products['name']); ?>">
      <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
      <div class="price">$<?= htmlspecialchars($fetch_products['price']); ?>/-</div>
      <input type="number" min="1" name="product_quantity" value="1" class="qty">
      <input type="hidden" name="product_name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
      <input type="hidden" name="product_price" value="<?= htmlspecialchars($fetch_products['price']); ?>">
      <input type="hidden" name="product_image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
      <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
     </form>
      <?php
         }
      } else {
         echo '<p class="empty">No products added yet!</p>';
      }
      ?>
   </div>

   <div class="load-more" style="margin-top: 2rem; text-align:center">
      <a href="shop.php" class="option-btn">Load More</a>
   </div>

</section>

<section class="about">
   <div class="flex">
      <div class="image">
         <img src="<?= BASE_URL ?>images/about-img.jpg" alt="About Us">
      </div>

      <div class="content">
         <h3>About Us</h3>
         <p>Learn more about our journey and mission in bringing quality books to readers worldwide.</p>
         <a href="about.php" class="btn">Read More</a>
      </div>
   </div>
</section>

<section class="home-contact">
   <div class="content">
      <h3>Have any questions?</h3>
      <p>Feel free to reach out to us anytime.</p>
      <a href="contact.php" class="option-btn">Contact Us</a>
   </div>
</section>

<?php include 'footer.php'; ?>

</body>
</html>
