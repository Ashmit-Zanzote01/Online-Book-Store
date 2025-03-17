<?php
include './config.php';
require_once 'core.php';

// Database connection parameters
$host = "localhost";
$dbname = "book_store_site"; // Replace with your actual database name
$user = "postgres";
$password = "Ashmit@1203*";

// Establishing the database connection
global $conn;
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

// Anonymous function to execute queries safely
$executeQuery = function ($query) use ($conn) {
    global $conn; // <-- Ensures the function correctly accesses the global $conn variable
    return pg_query($conn, $query);
};

// Add to cart logic
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    
    // Check if the product is already in the cart
    $check_cart_query = "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = '$product_id'";
    $check_cart_result = $executeQuery($check_cart_query);
    
    if (pg_num_rows($check_cart_result) > 0) {
        // Product is already in the cart, update the quantity
        $update_cart_query = "UPDATE cart SET quantity = quantity + '$quantity' WHERE user_id = '$user_id' AND product_id = '$product_id'";
        $executeQuery($update_cart_query);
    } else {
        // Product is not in the cart, insert it
        $insert_cart_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', '$product_id', '$quantity')";
        $executeQuery($insert_cart_query);
    }
}

// Fetch the cart items
$cart_query = "SELECT * FROM cart INNER JOIN products ON cart.product_id = products.product_id WHERE cart.user_id = '$user_id'";
$cart_result = $executeQuery($cart_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
<!-- Detect if the script is inside 'admin' folder -->

	<?php $isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;?>

<link rel="stylesheet" href="<?= BASE_URL ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
<script src="<?= BASE_URL ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>

</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>Shopping Cart</h3>
    <p><a href="home.php">Home</a> / Cart</p>
</div>

<section class="cart">
    <div class="cart-items">
        <?php
        if (pg_num_rows($cart_result) > 0) {
            while ($cart_item = pg_fetch_assoc($cart_result)) { ?>
                <div class="cart-item">
                    <div class="cart-item-info">
                        <img src="images/<?php echo $cart_item['image']; ?>" alt="">
                        <h3><?php echo $cart_item['product_name']; ?></h3>
                        <p>Price: $<?php echo $cart_item['price']; ?></p>
                        <p>Quantity: <?php echo $cart_item['quantity']; ?></p>
                    </div>
                    <div class="cart-item-remove">
                        <a href="remove_cart_item.php?cart_id=<?php echo $cart_item['cart_id']; ?>" class="btn">Remove</a>
                    </div>
                </div>
            <?php }
        } else {
            echo "<p>Your cart is empty.</p>";
        }
        ?>
    </div>

    <div class="cart-total">
        <h3>Total: $<?php
            $total_query = "SELECT SUM(products.price * cart.quantity) AS total_price FROM cart INNER JOIN products ON cart.product_id = products.product_id WHERE cart.user_id = '$user_id'";
            $total_result = $executeQuery($total_query);
            $total_row = pg_fetch_assoc($total_result);
            echo $total_row['total_price'];
        ?></h3>
    </div>

    <div class="checkout">
        <a href="checkout.php" class="btn">Proceed to Checkout</a>
    </div>
</section>

<?php include 'footer.php'; ?>


</body>
</html>
