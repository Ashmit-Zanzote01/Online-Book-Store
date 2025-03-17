<?php

include 'config.php';
require_once 'core.php';

// Database connection parameters
$host = "localhost";
$dbname = "book_store_site"; // Your actual database name
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

if (isset($_POST['submit'])) {
    
    $email = pg_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // Raw password input
    
    // ✅ Get user details based on email only
    $query = "SELECT * FROM users WHERE email = '$email'";
    $select_users = pg_query($conn, $query) or die('Query failed: ' . pg_last_error($conn));
    
    if (pg_num_rows($select_users) > 0) {
        
        $row = pg_fetch_assoc($select_users);
        $hashed_password = $row['password']; // Stored bcrypt hashed password
        
        // ✅ Verify password using password_verify()
        if (password_verify($password, $hashed_password)) {
            
            if ($row['user_type'] == 'admin') {
                $_SESSION['admin_name'] = $row['name'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_id'] = $row['id'];
                header('location:admin_page.php');
                exit;
            } elseif ($row['user_type'] == 'user') {
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_id'] = $row['id'];
                header('location:home.php');
                exit;
            }
        } else {
            $message[] = 'Incorrect email or password!';
        }
        
    } else {
        $message[] = 'Incorrect email or password!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS link -->
    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>

</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>';
    }
}
?>

<div class="form-container">

    <form action="" method="post">
        <h3>Login Now</h3>
        <input type="email" name="email" placeholder="Enter your email" required class="box" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        <input type="password" name="password" placeholder="Enter your password" required class="box">
        <input type="submit" name="submit" value="Login Now" class="btn">
        <p>Forgot Password? <a href="forgot_password.php">Reset Here</a></p>
        <p>Don't have an account? <a href="register.php">Register Now</a></p>
    </form>

</div>

</body>
</html>
