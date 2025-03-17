<?php
include 'config.php';
require_once 'core.php';

// Database connection parameters
$host = "localhost";
$dbname = "book_store_site";
$user = "postgres";
$password = "Ashmit@1203*";

// Establishing the database connection
global $conn;
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

if (!$conn) {
    die("Database connection failed!");
}

// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch reviews from the database
$query = "SELECT * FROM reviews LIMIT $1";
$result = pg_query_params($conn, $query, [5]);

$reviews = ($result) ? pg_fetch_all($result) : [];

// Author names array
$author_names = [
    1 => 'J.K Rowling',
    2 => 'John Sandford',
    3 => 'Rick Riordan',
    4 => 'Victoria Aveyard',
    5 => 'Raymond Beckham',
    6 => 'Andy Graham'
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>About Us</title>

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Detect if the script is inside 'admin' folder -->
   <?php $isAdmin = strpos($_SERVER['PHP_SELF'], '/admin/') !== false; ?>

   <link rel="stylesheet" href="<?= BASE_URL ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
   <script src="<?= BASE_URL ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>About Us</h3>
   <p> <a href="home.php">Home</a> / About </p>
</div>

<section class="about">
   <div class="flex">
      <div class="image">
         <img src="images/about-img.jpg" alt="About Us">
      </div>
      <div class="content">
           <h3>Why Choose Us?</h3>
           <p>At <strong>The Arcane Ink</strong>, we bring together a diverse collection of books from renowned authors across all genres. Whether you're a fan of gripping thrillers, timeless classics, thought-provoking non-fiction, or immersive fantasy, we have something for every reader.</p>
           <p>We take pride in offering a seamless shopping experience, carefully curated selections, and a passion for literature. With a commitment to quality and customer satisfaction, *The Arcane Ink* is your go-to destination for discovering new stories and timeless treasures.</p>
           <a href="contact.php" class="btn">Contact Us</a>
       </div>

   </div>
</section>

<section class="reviews">
   <h1 class="title">Client's Reviews</h1>
   <div class="box-container">
      <?php
      if ($reviews) {
         foreach ($reviews as $review) {
            // Image handling with multiple formats
            $imagePath = 'images/default.png';
            if (!empty($review['image'])) {
                $baseName = pathinfo($review['image'], PATHINFO_FILENAME);
                $extensions = ['webp', 'png', 'jpg', 'jpeg']; // Added jpeg
                foreach ($extensions as $ext) {
                    $file = "images/{$baseName}.{$ext}";
                    if (file_exists($file)) {
                        $imagePath = $file;
                        break;
                    }
                }
            }
            
            echo "
            <div class='box'>
               <img src='$imagePath' alt='Review' style='width:100px; height:100px; object-fit:cover;'>
               <p>" . htmlspecialchars($review['comment']) . "</p>
               <div class='stars'>
                  <i class='fas fa-star'></i>
                  <i class='fas fa-star'></i>
                  <i class='fas fa-star'></i>
                  <i class='fas fa-star'></i>
                  <i class='fas fa-star-half-alt'></i>
               </div>
               <h3>" . htmlspecialchars($review['author']) . "</h3>
            </div>";
         }
      } else {
         echo "<p>No reviews available.</p>";
      }
      ?>
   </div>
</section>

<section class="authors">
   <h1 class="title">Great Authors</h1>
   <div class="box-container">
      <?php 
      for ($i = 1; $i <= 6; $i++) {
         // Image handling with multiple formats
         $imageSrc = 'images/default.png';
         $extensions = ['webp', 'png', 'jpg', 'jpeg']; // Added jpeg
         foreach ($extensions as $ext) {
             $file = "images/author-$i.$ext";
             if (file_exists($file)) {
                 $imageSrc = $file;
                 break;
             }
         }
         
         echo "
         <div class='box'>
            <img src='$imageSrc' alt='{$author_names[$i]}'>
            <div class='share'>
               <a href='#' class='fab fa-facebook-f'></a>
               <a href='#' class='fab fa-twitter'></a>
               <a href='#' class='fab fa-instagram'></a>
               <a href='#' class='fab fa-linkedin'></a>
            </div>
            <h3>{$author_names[$i]}</h3>
         </div>";
      }
      ?>
   </div>
</section>

<?php include 'footer.php'; ?>

</body>
</html>