<?php
include 'config.php';
require_once 'core.php';
include 'config_email.php'; // Ensure email config is included

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$dbname = "book_store_site";
$user = "postgres";
$password = "Ashmit@1203*";

$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('location:login.php');
    exit;
}

$message = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_btn'])) {
    $name = pg_escape_string($conn, $_POST['name']);
    $number = pg_escape_string($conn, $_POST['number']);
    $email = pg_escape_string($conn, $_POST['email']);
    $method = pg_escape_string($conn, $_POST['method']);
    $address = "Flat No. {$_POST['flat']}, {$_POST['street']}, {$_POST['city']}, {$_POST['state']}, {$_POST['country']} - {$_POST['pin_code']}";
    $placed_on = date('Y-m-d');
    
    $cart_total = 0;
    $cart_products = [];
    
    $cart_query = pg_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");
    if (pg_num_rows($cart_query) > 0) {
        while ($cart_item = pg_fetch_assoc($cart_query)) {
            $cart_products[] = "{$cart_item['name']} ({$cart_item['quantity']})";
            $cart_total += $cart_item['price'] * $cart_item['quantity'];
        }
    }
    
    if ($cart_total == 0) {
        $message[] = "Your cart is empty!";
    } else {
        $total_products = implode(', ', $cart_products);
        
        $order_query = pg_query_params($conn, "INSERT INTO orders (user_id, name, number, email, method, address, total_products, total_price, placed_on) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)",
            [$user_id, $name, $number, $email, $method, $address, $total_products, $cart_total, $placed_on]);
        
        if ($order_query) {
            pg_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");
            
            // Get the newly created order ID
            $order_id_query = pg_query($conn, "SELECT id FROM orders WHERE user_id = '$user_id' ORDER BY placed_on DESC LIMIT 1");
            $order_id_row = pg_fetch_assoc($order_id_query);
            $order_id = $order_id_row['id'];
            
            // Build products table HTML
            $products_table = '';
            $cart_query = pg_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");
            while ($cart_item = pg_fetch_assoc($cart_query)) {
                $products_table .= '
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #f8f9fa;">
                        '.$cart_item['name'].' (Qty: '.$cart_item['quantity'].')
                    </td>
                    <td style="text-align: right; padding: 8px; border-bottom: 1px solid #f8f9fa;">
                        $'.number_format($cart_item['price'] * $cart_item['quantity'], 2).'
                    </td>
                </tr>';
            }
            
            // Prepare order details
            $order_details = [
                'order_id' => $order_id,
                'products_table' => $products_table,
                'total_price' => number_format($cart_total, 2),
                'payment_method' => $method,
                'payment_status' => 'pending',
                'delivery_address' => $address
            ];
            
            if (sendOrderConfirmationEmail($email, $order_details)) {
                $message[] = "Order placed successfully! Confirmation email sent.";
            } else {
                $message[] = "Order placed successfully! Email confirmation failed to send.";
            }
        } else {
            $message[] = "Order failed. Please try again!";
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
    <title>Checkout</title>

    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
</head>
<body>

<?php include 'header.php'; ?>

<div class="heading">
    <h3>Checkout</h3>
    <p><a href="home.php">Home</a> / Checkout</p>
</div>

<?php if (!empty($message)) : ?>
    <div class="message-box">
        <?php foreach ($message as $msg) : ?>
            <p><?php echo htmlspecialchars($msg); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<section class="display-order">
    <?php  
    $grand_total = 0;
    $select_cart = pg_query($conn, "SELECT * FROM cart WHERE user_id = '$user_id'");
    if (pg_num_rows($select_cart) > 0) {
        while ($fetch_cart = pg_fetch_assoc($select_cart)) {
            $total_price = $fetch_cart['price'] * $fetch_cart['quantity'];
            $grand_total += $total_price;
    ?>
    <p><?php echo htmlspecialchars($fetch_cart['name']); ?> <span>(<?php echo '$' . htmlspecialchars($fetch_cart['price']) . ' x ' . htmlspecialchars($fetch_cart['quantity']); ?>)</span></p>
    <?php
        }
    } else {
        echo '<p class="empty">Your cart is empty</p>';
    }
    ?>
    <div class="grand-total">Grand Total: <span>$<?php echo $grand_total; ?>/-</span></div>
</section>

<section class="checkout">
    <form action="" method="post">
        <h3>Place Your Order</h3>
        <div class="flex">
            <div class="inputBox">
                <span>Your Name:</span>
                <input type="text" name="name" required placeholder="Enter your name">
            </div>
            <div class="inputBox">
                <span>Your Number:</span>
                <input type="number" name="number" required placeholder="Enter your number">
            </div>
            <div class="inputBox">
                <span>Your Email:</span>
                <input type="email" name="email" required placeholder="Enter your email">
            </div>
            <div class="inputBox">
                <span>Payment Method:</span>
                <select name="method">
                    <option value="cash on delivery">Cash on Delivery</option>
                    <option value="credit card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="paytm">Paytm</option>
                </select>
            </div>
            <div class="inputBox">
                <span>Address Line 1:</span>
                <input type="text" name="flat" required placeholder="Flat No.">
            </div>
            <div class="inputBox">
                <span>Street:</span>
                <input type="text" name="street" required placeholder="Street Name">
            </div>
            <div class="inputBox">
                <span>City:</span>
                <input type="text" name="city" required placeholder="City">
            </div>
            <div class="inputBox">
                <span>State:</span>
                <input type="text" name="state" required placeholder="State">
            </div>
            <div class="inputBox">
                <span>Country:</span>
                <input type="text" name="country" required placeholder="Country">
            </div>
            <div class="inputBox">
                <span>Pin Code:</span>
                <input type="number" name="pin_code" required placeholder="Pin Code">
            </div>
        </div>
        <input type="submit" value="Order Now" class="btn" name="order_btn">
    </form>
</section>

<?php include 'footer.php'; ?>

<script src="../script.js"></script>

</body>
</html>