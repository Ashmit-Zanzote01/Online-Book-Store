<?php
include 'config.php';
require_once 'core.php';

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection parameters
$host = "localhost";
$dbname = "book_store_site"; // Replace with actual database name
$user = "postgres";
$password = "Ashmit@1203*";

// Detect if inside 'admin' folder
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';


// Establishing the database connection
global $conn;
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit;
}

// Ensure session messages exist
if (!isset($_SESSION['message'])) {
    $_SESSION['message'] = [];
}

// Order update logic
if (isset($_POST['update_order'])) {
    $order_update_id = $_POST['order_id'];
    $update_payment = $_POST['update_payment'];
    
    // Use correct syntax for PostgreSQL (No backticks)
    $query = "UPDATE orders SET payment_status = $1 WHERE id = $2";
    pg_query_params($conn, $query, array($update_payment, $order_update_id)) or die('Query failed');
    
    // Store message in session
    $_SESSION['message'][] = 'Payment status has been updated!';
    
    // Redirect to refresh page and show message
    header('location:admin_orders.php');
    exit;
}

// Order delete logic
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    
    $query = "DELETE FROM orders WHERE id = $1";
    pg_query_params($conn, $query, array($delete_id)) or die('Query failed');
    
    $_SESSION['message'][] = 'Order deleted successfully!';
    
    header('location:admin_orders.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom admin CSS file link -->
<!--    <link rel="stylesheet" href="../admin_style.css"> -->

<!-- CSS & JS Linking (✅ Corrected Placement) -->
    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
    
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- Display Messages -->
<?php
if (!empty($_SESSION['message'])) {
    foreach ($_SESSION['message'] as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>';
    }
    unset($_SESSION['message']); // Clear messages after displaying
}
?>

<section class="orders">
   <h1 class="title">Placed Orders</h1>

   <div class="box-container">
      <?php
      // Fetch orders using correct PostgreSQL syntax
      $select_orders = pg_query($conn, "SELECT * FROM orders") or die('Query failed');
      if (pg_num_rows($select_orders) > 0) {
         while ($fetch_orders = pg_fetch_assoc($select_orders)) {
      ?>
      <div class="box">
         <p> User ID: <span><?php echo htmlspecialchars($fetch_orders['user_id']); ?></span> </p>
         <p> Placed on: <span><?php echo htmlspecialchars($fetch_orders['placed_on']); ?></span> </p>
         <p> Name: <span><?php echo htmlspecialchars($fetch_orders['name']); ?></span> </p>
         <p> Number: <span><?php echo htmlspecialchars($fetch_orders['number']); ?></span> </p>
         <p> Email: <span><?php echo htmlspecialchars($fetch_orders['email']); ?></span> </p>
         <p> Address: <span><?php echo htmlspecialchars($fetch_orders['address']); ?></span> </p>
         <p> Total Products: <span><?php echo htmlspecialchars($fetch_orders['total_products']); ?></span> </p>
         <p> Total Price: <span>$<?php echo htmlspecialchars($fetch_orders['total_price']); ?>/-</span> </p>
         <p> Payment Method: <span><?php echo htmlspecialchars($fetch_orders['method']); ?></span> </p>
         <form action="" method="post">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($fetch_orders['id']); ?>">
            <select name="update_payment">
               <option value="" selected disabled><?php echo htmlspecialchars($fetch_orders['payment_status']); ?></option>
               <option value="pending">Pending</option>
               <option value="completed">Completed</option>
            </select>
            <input type="submit" value="Update" name="update_order" class="option-btn">
            <a href="admin_orders.php?delete=<?php echo htmlspecialchars($fetch_orders['id']); ?>" onclick="return confirm('Delete this order?');" class="delete-btn">Delete</a>
         </form>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty">No orders placed yet!</p>';
      }
      ?>
   </div>
</section>

<!-- Custom admin JS file link -->
<script src="../admin_script.js"></script>

</body>
</html>
