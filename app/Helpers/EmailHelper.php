<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailHelper {
    public static function sendOrderSuccess($to, $orderDetails) {
        $subject = "Your Reservation is Confirmed!";
        $message = "<h2>Reservation Successful</h2>"
            . "<p>Hi, your order for package <b>" . htmlspecialchars($orderDetails['package_name']) . "</b> is successful.</p>"
            . "<p>Reservation Date: <b>" . htmlspecialchars($orderDetails['reservation_date']) . "</b></p>"
            . "<p>Amount Paid: <b>₱" . number_format($orderDetails['amount'], 2) . "</b></p>"
            . "<p>Thank you for your reservation!</p>";
        return self::sendMail($to, $subject, $message);
    }

    public static function sendMail($to, $subject, $message, $headers = '') {
        $mail = new PHPMailer(true);
        try {
            // Gmail SMTP server configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hperformanceexhaust@gmail.com';
            $mail->Password = 'wolv wvyy chhl rvvm';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('hperformanceexhaust@gmail.com', 'Reservation System');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Optionally log the error: $e->getMessage()
            return false;
        }
    }
} 