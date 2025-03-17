<?php

include 'config.php';
include 'core.php';

// Database connection parameters
$host = "localhost";
$dbname = "book_store_site";
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

// Array to store messages
$message = [];

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_FILES['image']['name'];
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'uploaded_img/' . $image;
    
    // Check for duplicate name
    $select_product_name = pg_query_params($conn, "SELECT name FROM products WHERE name = $1", array($name));
    
    if (pg_num_rows($select_product_name) > 0) {
        $message[] = 'Product name already exists!';
    } else {
        if ($image_size > 2000000) {
            $message[] = 'Image size is too large!';
        } else {
            // Get the next available unique ID
            $new_id = 1;
            while (true) {
                $check_id = pg_query_params($conn, "SELECT id FROM products WHERE id = $1", array($new_id));
                if (pg_num_rows($check_id) == 0) {
                    break; // Found a unique ID
                }
                $new_id++; // Try the next ID
            }
            
            // Insert the new product with unique ID
            $add_product_query = pg_query_params($conn, "INSERT INTO products (id, name, price, image) VALUES ($1, $2, $3, $4)", array($new_id, $name, $price, $image));
            
            if ($add_product_query) {
                move_uploaded_file($image_tmp_name, $image_folder);
                $message[] = 'Product added successfully!';
            } else {
                $message[] = 'Failed to add product!';
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    
    $delete_image_query = pg_query_params($conn, "SELECT image FROM products WHERE id = $1", array($delete_id));
    $fetch_delete_image = pg_fetch_assoc($delete_image_query);
    if ($fetch_delete_image) {
        $image_path = 'uploaded_img/' . $fetch_delete_image['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        } else {
            $image_path = 'images/' . $fetch_delete_image['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }
    
    pg_query_params($conn, "DELETE FROM products WHERE id = $1", array($delete_id));
    
    header('location:admin_products.php');
    exit;
}

if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = $_POST['update_name'];
    $update_price = $_POST['update_price'];
    
    pg_query_params($conn, "UPDATE products SET name = $1, price = $2 WHERE id = $3", array($update_name, $update_price, $update_p_id));
    
    $update_image = $_FILES['update_image']['name'];
    $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
    $update_image_size = $_FILES['update_image']['size'];
    $update_folder = 'uploaded_img/' . $update_image;
    $update_old_image = $_POST['update_old_image'];
    
    if (!empty($update_image)) {
        if ($update_image_size > 2000000) {
            $message[] = 'Image file size is too large!';
        } else {
            pg_query_params($conn, "UPDATE products SET image = $1 WHERE id = $2", array($update_image, $update_p_id));
            move_uploaded_file($update_image_tmp_name, $update_folder);
            
            // Check if old image exists in uploaded_img
            $old_image_path = 'uploaded_img/' . $update_old_image;
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            } else {
                // Check if old image exists in images folder
                $old_image_path = 'images/' . $update_old_image;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
        }
    }
    
    header('location:admin_products.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>

    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Custom admin CSS file link -->
    <link rel="stylesheet" href="<?= $basePath ?>css/<?= $isAdmin ? 'admin_style.css' : 'style.css' ?>">
    <script src="<?= $basePath ?>js/<?= $isAdmin ? 'admin_script.js' : 'script.js' ?>"></script>
</head>
<body>

<?php include 'admin_header.php'; ?>

<!-- Display messages -->
<?php
if (!empty($message)) {
    foreach ($message as $msg) {
        echo '<div class="message">' . htmlspecialchars($msg) . '</div>';
    }
}
?>

<!-- Product CRUD section -->
<section class="add-products">
    <h1 class="title">Shop Products</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <h3>Add Product</h3>
        <input type="text" name="name" class="box" placeholder="Enter product name" required>
        <input type="number" min="0" name="price" class="box" placeholder="Enter product price" required>
        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
        <input type="submit" value="Add Product" name="add_product" class="btn">
    </form>
</section>

<!-- Show products -->
<section class="show-products">
    <div class="box-container">
        <?php
        $select_products = pg_query($conn, "SELECT * FROM products");
        if (pg_num_rows($select_products) > 0) {
            while ($fetch_products = pg_fetch_assoc($select_products)) {
                // Check if image exists in uploaded_img folder
                $image_path = 'uploaded_img/' . $fetch_products['image'];
                if (!file_exists($image_path)) {
                    // If not found in uploaded_img, check in images folder
                    $image_path = 'images/' . $fetch_products['image'];
                    if (!file_exists($image_path)) {
                        // If not found in either folder, use a placeholder
                        $image_path = 'images/placeholder.png';
                    }
                }
                ?>
                <div class="box">
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="">
                    <div class="name"><?php echo htmlspecialchars($fetch_products['name']); ?></div>
                    <div class="price">$<?php echo htmlspecialchars($fetch_products['price']); ?>/-</div>
                    <a href="admin_products.php?update=<?php echo $fetch_products['id']; ?>" class="option-btn">Update</a>
                    <a href="admin_products.php?delete=<?php echo $fetch_products['id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">No products added yet!</p>';
        }
        ?>
    </div>
</section>

<?php
// Code for update product popup
if (isset($_GET['update'])) {
    $update_id = $_GET['update'];
    $update_query = pg_query_params($conn, "SELECT * FROM products WHERE id = $1", array($update_id));
    if (pg_num_rows($update_query) > 0) {
        $fetch_update = pg_fetch_assoc($update_query);
        ?>
        <section class="edit-product-form">
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                <img src="<?php 
                    $img_path = 'uploaded_img/' . $fetch_update['image'];
                    if (!file_exists($img_path)) {
                        $img_path = 'images/' . $fetch_update['image'];
                    }
                    echo htmlspecialchars($img_path); 
                ?>" alt="">
                <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Enter product name">
                <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="Enter product price">
                <input type="file" class="box" name="update_image" accept="image/jpg, image/jpeg, image/png">
                <input type="submit" value="Update" name="update_product" class="btn">
                <input type="reset" value="Cancel" id="close-update" class="option-btn">
            </form>
        </section>
        <?php
    }
}
?>

<!-- <script src="../admin_script.js"></script> -->
</body>
</html>