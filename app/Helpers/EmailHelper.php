<?php
namespace App\Helpers;

class EmailHelper {
    public static function sendMail($to, $subject, $message, $headers = '') {
        // Basic headers
        if (empty($headers)) {
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: noreply@yourdomain.com' . "\r\n";
        }
        return mail($to, $subject, $message, $headers);
    }
} 