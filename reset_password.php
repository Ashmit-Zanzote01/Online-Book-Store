<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php'; // Database connection
require_once 'core.php';


if (!isset($_SESSION['email']) || !isset($_SESSION['otp'])) {
    echo "Unauthorized access!";
    exit;
}

if (isset($_POST['verify_otp'])) {
    $user_otp = trim($_POST['otp']);
    
    if ($user_otp === (string) $_SESSION['otp']) {
        $_SESSION['otp_verified'] = true;
        echo "OTP verified successfully! You can now reset your password.";
    } else {
        echo "Invalid OTP! Please try again.";
    }
}

if (isset($_POST['reset_password'])) {
    if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        echo "Please verify OTP first!";
        exit;
    }
    
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
    $email = $_SESSION['email'];
    
    // Update password securely using prepared statements
    $updateQuery = "UPDATE users SET password = $1 WHERE email = $2";
    $result = pg_query_params($conn, $updateQuery, [$new_password, $email]);
    
    if ($result) {
        echo "Password reset successfully!";
        session_destroy(); // Clear session after reset
    } else {
        echo "Failed to update password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP & Reset Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <?php if (!isset($_SESSION['otp_verified'])) { ?>
            <form action="" method="post">
                <h3>Enter OTP</h3>
                <input type="text" name="otp" placeholder="Enter OTP" required class="box">
                <input type="submit" name="verify_otp" value="Verify OTP" class="btn">
            </form>
        <?php } else { ?>
            <form action="" method="post">
                <h3>Reset Password</h3>
                <input type="password" name="new_password" placeholder="Enter new password" required class="box">
                <input type="submit" name="reset_password" value="Reset Password" class="btn">
            </form>
        <?php } ?>
    </div>
</body>
</html>
