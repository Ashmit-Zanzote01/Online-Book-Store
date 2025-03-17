<?php
include 'config.php';
require_once 'core.php';

$host = "localhost";
$dbname = "book_store_site";
$user = "postgres";
$password = "Ashmit@1203*";

global $conn;
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_id = $_SESSION['user_id'] ?? null;

// Detect if inside 'admin' folder
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

$message = [];

if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $cart_quantity = (int)$_POST['cart_quantity'];
    pg_query_params($conn, "UPDATE cart SET quantity = $1 WHERE id = $2", [$cart_quantity, $cart_id]);
    $message[] = 'Cart quantity updated!';
}

if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    pg_query_params($conn, "DELETE FROM cart WHERE id = $1", [$delete_id]);
    header('location:cart.php');
    exit;
}

if (isset($_GET['delete_all'])) {
    pg_query_params($conn, "DELETE FROM cart WHERE user_id = $1", [$user_id]);
    header('location:cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>Shopping Cart</h3>
    <p><a href="home.php">Home</a> / Cart</p>
</div>

<?php if (!empty($message)): ?>
    <div class="message-container">
        <?php foreach ($message as $msg): ?>
            <div class="message"><?= htmlspecialchars($msg) ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<section class="shopping-cart">
    <h1 class="title">Products Added</h1>
    <div class="box-container">
        <?php
        $grand_total = 0;
        $cart_query = pg_query_params($conn, "SELECT * FROM cart WHERE user_id = $1", [$user_id]);
        
        if (pg_num_rows($cart_query) > 0) {
            while ($item = pg_fetch_assoc($cart_query)) {
                // Sanitize and prepare image data
                $image_name = trim($item['image']);
                $safe_image_name = htmlspecialchars($image_name);
                $encoded_image = rawurlencode($image_name);
                
                // Filesystem paths (raw)
                $uploaded_path = "uploaded_img/{$image_name}";
                $images_path = "images/{$image_name}";
                
                // URL paths (encoded)
                $image_url = file_exists($uploaded_path) 
                    ? "uploaded_img/{$encoded_image}"
                    : (file_exists($images_path)
                        ? "images/{$encoded_image}"
                        : "images/placeholder.jpg");

                // Calculate totals
                $sub_total = $item['price'] * $item['quantity'];
                $grand_total += $sub_total;
        ?>
        <div class="box">
            <a href="cart.php?delete=<?= $item['id'] ?>" class="fas fa-times" 
               onclick="return confirm('Delete this item?')"></a>
            
            <img src="<?= $image_url ?>" 
                 alt="<?= htmlspecialchars($item['name']) ?>"
                 onerror="this.onerror=null;this.src='images/placeholder.jpg'">
            
            <div class="name"><?= htmlspecialchars($item['name']) ?></div>
            <div class="price">$<?= number_format($item['price'], 2) ?>/-</div>
            
            <form method="post">
                <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                <input type="number" name="cart_quantity" 
                       min="1" value="<?= $item['quantity'] ?>"
                       class="quantity-input">
                <button type="submit" name="update_cart" class="option-btn">
                    Update
                </button>
            </form>
            
            <div class="sub-total">
                Subtotal: <span>$<?= number_format($sub_total, 2) ?>/-</span>
            </div>
        </div>
        <?php }
        } else {
            echo '<p class="empty">Your cart is empty.</p>';
        } ?>
    </div>

    <div class="cart-actions">
        <?php if ($grand_total > 0): ?>
            <a href="cart.php?delete_all" class="delete-btn" 
               onclick="return confirm('Clear entire cart?')">
                Delete All
            </a>
        <?php endif; ?>
    </div>

    <div class="cart-summary">
        <div class="grand-total">
            Grand Total: $<?= number_format($grand_total, 2) ?>/-
        </div>
        <div class="action-buttons">
            <a href="shop.php" class="option-btn">Continue Shopping</a>
            <?php if ($grand_total > 0): ?>
                <a href="checkout.php" class="btn">Proceed to Checkout</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>

</body>
</html>