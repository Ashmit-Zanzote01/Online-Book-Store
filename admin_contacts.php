<?php
include 'config.php';
require_once 'core.php';

// Database connection parameters
$host = "localhost";
$dbname = "book_store_site"; // Replace with your actual database name
$user = "postgres";
$password = "Ashmit@1203*";

// Establishing the database connection
global $conn;
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    // Using parameterized queries to prevent SQL injection
    pg_prepare($conn, "delete_message", "DELETE FROM message WHERE id = $1");
    $stmt_execute = pg_execute($conn, "delete_message", array($delete_id));
    
    if ($stmt_execute) {
        header('location:admin_contacts.php');
    } else {
        echo 'Error deleting message.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Messages</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
<!--    <link rel="stylesheet" href="../admin_style.css"> -->

<!-- Detect if the script is inside 'admin' folder -->

	<?php $isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;?>

<link rel="stylesheet" href="<?= BASE_URL ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
<script src="<?= BASE_URL ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="messages">

   <h1 class="title">Messages</h1>

   <div class="box-container">
   <?php
      $select_message = pg_query($conn, "SELECT * FROM message") or die('Query failed');
      if (pg_num_rows($select_message) > 0) {
         while ($fetch_message = pg_fetch_assoc($select_message)) {
   ?>
   <div class="box">
      <p>User ID: <span><?php echo $fetch_message['user_id']; ?></span></p>
      <p>Name: <span><?php echo $fetch_message['name']; ?></span></p>
      <p>Number: <span><?php echo $fetch_message['number']; ?></span></p>
      <p>Email: <span><?php echo $fetch_message['email']; ?></span></p>
      <p>Message: <span><?php echo $fetch_message['message']; ?></span></p>
      <a href="admin_contacts.php?delete=<?php echo $fetch_message['id']; ?>" onclick="return confirm('Delete this message?');" class="delete-btn">Delete Message</a>
   </div>
   <?php
         }
      } else {
         echo '<p class="empty">You have no messages!</p>';
      }
   ?>
   </div>

</section>

<!-- custom admin js file link -->
<!-- <script src="../admin_script.js"></script> -->

</body>
</html>
