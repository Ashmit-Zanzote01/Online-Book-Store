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

// Detect if inside 'admin' folder
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    pg_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_users.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>users</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
<!--    <link rel="stylesheet" href="../admin_style.css"> -->

<!-- CSS & JS Linking (✅ Corrected Placement) -->
    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">

   <h1 class="title"> user accounts </h1>

   <div class="box-container">
      <?php
         $select_users = pg_query($conn, "SELECT * FROM users") or die('query failed');
         if (pg_num_rows($select_users) > 0) {
            while ($fetch_users = pg_fetch_assoc($select_users)) {
      ?>
      <div class="box">
         <p> user id : <span><?php echo $fetch_users['id']; ?></span> </p>
         <p> username : <span><?php echo $fetch_users['name']; ?></span> </p>
         <p> email : <span><?php echo $fetch_users['email']; ?></span> </p>
         <p> user type : <span style="color:<?php echo $fetch_users['user_type'] == 'admin' ? 'var(--orange)' : ''; ?>"><?php echo $fetch_users['user_type']; ?></span> </p>
         <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>" onclick="return confirm('delete this user?');" class="delete-btn">delete user</a>
      </div>
      <?php
            }
         } else {
            echo '<p class="empty">No users found!</p>';
         }
      ?>
   </div>

</section>

<!-- custom admin js file link  -->
<script src="../admin_script.js"></script>

</body>
</html>
