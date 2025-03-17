<?php

include 'config.php';
require_once 'core.php';

// Ensure session is started only once
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection parameters
$host = "localhost";
$dbname = "book_store_site";
$user = "postgres";
$password = "Ashmit@1203*";

// Establish database connection
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Database connection failed: " . pg_last_error());
}

// Detect if inside 'admin' folder
$isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$basePath = $isAdmin ? '../' : '';

if (isset($_POST['submit'])) {
    
    $name = pg_escape_string($conn, $_POST['name']);
    $email = pg_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Secure hashing
    $cpass = $_POST['cpassword'];  // No need to hash for comparison
    $user_type = $_POST['user_type'];
    
    // Check if the user already exists
    $result = pg_query_params(
        $conn,
        "SELECT * FROM users WHERE email = $1",
        [$email]
        );
    
    if (pg_num_rows($result) > 0) {
        $message[] = 'User already exists!';
    } else {
        // Check if passwords match
        if (!password_verify($cpass, $pass)) {
            $message[] = 'Confirm password does not match!';
        } else {
            // Insert the new user into the database
            $query = "INSERT INTO users (name, email, password, user_type) VALUES ($1, $2, $3, $4)";
            $insert_result = pg_query_params($conn, $query, [$name, $email, $pass, $user_type]);
            
            if ($insert_result) {
                $message[] = 'Registered successfully!';
                header('location:login.php');
                exit;  // Prevent further execution
            } else {
                $message[] = 'Registration failed. Please try again!';
            }
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
   <title>Register</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- CSS & JS Linking (✅ Corrected Placement) -->
   <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
   <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
</head>
<body>

<?php
// Displaying error/success messages
if (!empty($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<div class="form-container">
   <form action="" method="post">
      <h3>Register Now</h3>
      <input type="text" name="name" placeholder="Enter your name" required class="box">
      <input type="email" name="email" placeholder="Enter your email" required class="box">
      <input type="password" name="password" placeholder="Enter your password" required class="box">
      <input type="password" name="cpassword" placeholder="Confirm your password" required class="box">
      <select name="user_type" class="box">
         <option value="user">User</option>
         <option value="admin">Admin</option>
      </select>
      <input type="submit" name="submit" value="Register Now" class="btn">
      <p>Already have an account? <a href="login.php">Login Now</a></p>
   </form>
</div>

</body>
</html>
