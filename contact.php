<?php
// Include the database connection file
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

// Check if the user is logged in
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('location:login.php');
    exit;
}

$message = []; // Initialize the message array

// Handle the form submission
if (isset($_POST['send'])) {
    // Sanitize and escape the form inputs
    $name = pg_escape_string($conn, $_POST['name']);
    $email = pg_escape_string($conn, $_POST['email']);
    $number = $_POST['number'];
    $msg = pg_escape_string($conn, $_POST['message']);
    
    // Check if the message already exists in the database
    $select_message = pg_query_params($conn, "SELECT * FROM message WHERE name = $1 AND email = $2 AND number = $3 AND message = $4", [$name, $email, $number, $msg]);
    
    // If the message already exists, notify the user
    if (pg_num_rows($select_message) > 0) {
        $message[] = 'Message has already been sent!';
    } else {
        // Insert the new message into the database
        pg_query_params($conn, "INSERT INTO message (user_id, name, email, number, message) VALUES ($1, $2, $3, $4, $5)", [$user_id, $name, $email, $number, $msg]);
        $message[] = 'Message sent successfully!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    
    <!-- Custom JS file link -->
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
    <h3>Contact Us</h3>
    <p> <a href="home.php">Home</a> / Contact </p>
</div>

<section class="contact">
    <?php
    // Display messages
    if (!empty($message)) {
        foreach ($message as $msg) {
            echo '<p class="message">' . htmlspecialchars($msg) . '</p>';
        }
    }
    ?>
    <form action="" method="post">
        <h3>Say something!</h3>
        <input type="text" name="name" required placeholder="Enter your name" class="box">
        <input type="email" name="email" required placeholder="Enter your email" class="box">
        <input type="number" name="number" required placeholder="Enter your number" class="box">
        <textarea name="message" class="box" placeholder="Enter your message" cols="30" rows="10"></textarea>
        <input type="submit" value="Send Message" name="send" class="btn">
    </form>
</section>

<?php include 'footer.php'; ?>

</body>
</html>
