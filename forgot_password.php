<?php

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

include 'config.php';
require_once 'core.php';
include 'config_email.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Reset session if user cancels or refreshes
if (isset($_GET['cancel'])) {
    unset($_SESSION['otp_sent']);
    unset($_SESSION['otp_verified']);
    unset($_SESSION['reset_email']);
    unset($_SESSION['otp']);
    header("Location: forgot_password.php");
    exit;
}

$error = '';
$success = '';

// Step 1: Handle Email Submission (Send OTP)
if (isset($_POST['send_otp'])) {
    $email = pg_escape_string($conn, $_POST['email']);
    
    // Check if email exists
    $query = "SELECT * FROM users WHERE email = $1";
    $result = pg_query_params($conn, $query, [$email]);
    
    if (pg_num_rows($result) > 0) {
        // Generate and store OTP (valid for 10 minutes)
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['reset_email'] = $email;
        $_SESSION['otp_expiry'] = time() + 600; // 10-minute expiry
        
        // Send OTP via Email
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'thearcaneink@gmail.com';
            $mail->Password = 'mvan meqa geqe lnmt';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            $mail->setFrom('thearcaneink@gmail.com', 'Book Store');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Your OTP for Password Reset";
            $mail->Body = "Your OTP is: <b>$otp</b> (valid for 10 minutes)";
            
            if ($mail->send()) {
                $success = "OTP sent to $email!";
                $_SESSION['otp_sent'] = true;
            } else {
                $error = "Failed to send OTP.";
            }
        } catch (Exception $e) {
            $error = "Mailer Error: " . $e->getMessage();
        }
    } else {
        $error = "Email not found!";
    }
}

// Step 2: Handle OTP Verification
if (isset($_POST['verify_otp'])) {
    // Check OTP expiry
    if (time() > $_SESSION['otp_expiry']) {
        $error = "OTP has expired!";
        unset($_SESSION['otp_sent']);
    } else {
        $entered_otp = $_POST['otp'];
        if ($entered_otp == $_SESSION['otp']) {
            $_SESSION['otp_verified'] = true;
            $success = "OTP verified! Set new password.";
        } else {
            $error = "Invalid OTP!";
        }
    }
}

// Step 3: Handle Password Reset
if (isset($_POST['reset_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $email = $_SESSION['reset_email'];
    
    $query = "UPDATE users SET password = $1 WHERE email = $2";
    $result = pg_query_params($conn, $query, [$new_password, $email]);
    
    if ($result) {
        session_destroy();
        $success = "Password updated! <a href='login.php'>Login Now</a>";
    } else {
        $error = "Failed to update password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .reset-link {
            margin-top: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <!-- Error/Success Messages -->
        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>

        <!-- Step 1: Email Input -->
        <?php if (!isset($_SESSION['otp_sent'])): ?>
            <form method="POST">
                <h3>Reset Password</h3>
                <input type="email" name="email" placeholder="Enter your email" required class="box" 
                    value="<?= isset($_SESSION['reset_email']) ? htmlspecialchars($_SESSION['reset_email']) : '' ?>">
                <input type="submit" name="send_otp" value="Send OTP" class="btn">
                <p class="reset-link">Remembered password? <a href="login.php">Login</a></p>
            </form>

        <!-- Step 2: OTP Verification -->
        <?php elseif (isset($_SESSION['otp_sent']) && !isset($_SESSION['otp_verified'])): ?>
            <form method="POST">
                <h3>Enter OTP</h3>
                <input type="text" name="otp" placeholder="6-digit OTP" required class="box" maxlength="6">
                <input type="submit" name="verify_otp" value="Verify OTP" class="btn">
                <p class="reset-link"><a href="?cancel">Cancel & Start Over</a></p>
            </form>

        <!-- Step 3: New Password -->
        <?php elseif (isset($_SESSION['otp_verified'])): ?>
            <form method="POST">
                <h3>New Password</h3>
                <input type="password" name="new_password" placeholder="Enter new password" required class="box" minlength="6">
                <input type="submit" name="reset_password" value="Reset Password" class="btn">
                <p class="reset-link"><a href="?cancel">Cancel</a></p>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>