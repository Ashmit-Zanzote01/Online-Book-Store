<?php

include 'config.php';
require_once 'core.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Prevent multiple inclusions
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

// Database connection parameters
$host = "localhost";
$dbname = "book_store_site"; // Replace with your actual database name
$user = "postgres";
$password = "Ashmit@1203*";

// Establishing the database connection
global $conn;
$conn = pg_connect("host=$host dbname=$dbname user=$user password=$password");

function sendOTP($toEmail, $otp) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'thearcaneink@gmail.com';  // Your Gmail
        $mail->Password = 'mvan meqa geqe lnmt';     // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Email settings
        $mail->setFrom('thearcaneink@gmail.com', 'Your Website');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = "Password Reset OTP";
        
        // OTP Message
        $mail->Body = "Your OTP for password reset is: <b>$otp</b>";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// New function to send order confirmation email
function sendOrderConfirmationEmail($toEmail, $order_details) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'thearcaneink@gmail.com';
        $mail->Password = 'mvan meqa geqe lnmt';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('thearcaneink@gmail.com', 'The Arcane Ink');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation #{$order_details['order_id']}";
        
        // HTML email template
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="text-align: center; padding: 20px; background-color: #f8f9fa;">
                <h2 style="color: #2d3436;">Order Confirmation</h2>
                <p>Order ID: #'.$order_details['order_id'].'</p>
            </div>
                    
            <div style="padding: 30px; background-color: #ffffff;">
                <h3 style="color: #2d3436; border-bottom: 2px solid #f8f9fa; padding-bottom: 10px;">Order Details</h3>
                    
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr>
                        <th style="text-align: left; padding: 8px; background-color: #f8f9fa;">Product</th>
                        <th style="text-align: right; padding: 8px; background-color: #f8f9fa;">Total</th>
                    </tr>
                    '.$order_details['products_table'].'
                    <tr>
                        <td style="text-align: right; padding: 8px; font-weight: bold;">Grand Total</td>
                        <td style="text-align: right; padding: 8px; font-weight: bold;">$'.$order_details['total_price'].'</td>
                    </tr>
                </table>
                            
                <div style="margin-top: 30px;">
                    <h4 style="color: #2d3436;">Payment Information</h4>
                    <p>Payment Method: '.$order_details['payment_method'].'</p>
                    <p>Payment Status: <span style="color: '.($order_details['payment_status'] == 'completed' ? '#2ecc71' : '#e74c3c').';">
                        '.$order_details['payment_status'].'
                    </span></p>
                </div>
                            
                <div style="margin-top: 30px;">
                    <h4 style="color: #2d3436;">Delivery Address</h4>
                    <p>'.$order_details['delivery_address'].'</p>
                </div>
                        
                <p style="margin-top: 30px; color: #636e72;">
                    Thank you for shopping with us!<br>
                    The Arcane Ink Team
                </p>
            </div>
        </div>';
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Order Confirmation Email Error: " . $mail->ErrorInfo);
        return false;
    }
}

?>
