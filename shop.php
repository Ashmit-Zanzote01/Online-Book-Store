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

// Start session only if not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Detect if inside 'admin' folder
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['add_to_cart'])) {
    $product_name = pg_escape_string($conn, $_POST['product_name']);
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];
    
    // Check if product is already in cart
    $check_cart = pg_query_params($conn, "SELECT * FROM cart WHERE name = $1 AND user_id = $2", [$product_name, $user_id]);
    
    if (pg_num_rows($check_cart) > 0) {
        $message[] = 'Product already in cart!';
    } else {
        // Add product to cart
        pg_query_params($conn, "INSERT INTO cart (user_id, name, price, quantity, image) VALUES ($1, $2, $3, $4, $5)", [$user_id, $product_name, $product_price, $product_quantity, $product_image]);
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
   <title>Shop</title>

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- CSS & JS -->
   <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
   <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Our Shop</h3>
   <p><a href="home.php">Home</a> / Shop</p>
</div>

<section class="products">
   <h1 class="title">Latest Products</h1>

   <div class="box-container">
      <?php  
         // Fetch products from the database
         $select_products = pg_query($conn, "SELECT * FROM products") or die('Query failed');
         if (pg_num_rows($select_products) > 0) {
            while ($fetch_products = pg_fetch_assoc($select_products)) {
                // Ensure BASE_URL is correctly defined in config.php
                $image_path = BASE_URL . "images/";
                
                if (!empty($fetch_products['image'])) {
                    $image_path .= htmlspecialchars($fetch_products['image']);
                } else {
                    $image_path .= "default.jpg"; // Use a placeholder image if missing
                }
      ?>
      <form action="" method="post" class="box">
         <img class="image" src="<?= $image_path ?>" alt="<?= htmlspecialchars($fetch_products['name']) ?>">
         <div class="name"><?= htmlspecialchars($fetch_products['name']) ?></div>
         <div class="price">$<?= htmlspecialchars($fetch_products['price']) ?>/-</div>
         <input type="number" min="1" name="product_quantity" value="1" class="qty">
         <input type="hidden" name="product_name" value="<?= htmlspecialchars($fetch_products['name']) ?>">
         <input type="hidden" name="product_price" value="<?= htmlspecialchars($fetch_products['price']) ?>">
         <input type="hidden" name="product_image" value="<?= htmlspecialchars($fetch_products['image']) ?>">
         <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
      </form>
      <?php
            }
         } else {
            echo '<p class="empty">No products available!</p>';
         }
      ?>
   </div>

</section>

<?php include 'footer.php'; ?>

</body>
</html>
