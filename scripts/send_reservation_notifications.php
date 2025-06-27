<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Helpers/EmailHelper.php';

use App\Helpers\EmailHelper;

$db = \Database::connect();

// Get today's reservations
$today = date('Y-m-d');
$sql = "SELECT o.*, u.email, u.username, s.package_name FROM orders o JOIN users u ON o.user_id = u.id JOIN services s ON o.service_id = s.id WHERE o.reservation_date = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$today]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all admin emails
$adminEmails = [];
$adminStmt = $db->query("SELECT email FROM admins");
while ($row = $adminStmt->fetch(PDO::FETCH_ASSOC)) {
    $adminEmails[] = $row['email'];
}

foreach ($reservations as $reservation) {
    $to = $reservation['email'];
    $subject = "Reservation Reminder: " . $reservation['package_name'];
    $message = "<h2>Hi " . htmlspecialchars($reservation['username']) . ",</h2>"
        . "<p>This is a reminder for your reservation today (" . htmlspecialchars($reservation['reservation_date']) . ") for the service: <strong>" . htmlspecialchars($reservation['package_name']) . "</strong>.</p>"
        . "<p>Thank you for choosing us!</p>";
    EmailHelper::sendMail($to, $subject, $message);

    // Notify all admins
    $adminSubject = "[ADMIN ALERT] Reservation Event Today: " . $reservation['package_name'];
    $adminMessage = "<h2>Admin Notification</h2>"
        . "<p>There is a reservation event today for <strong>" . htmlspecialchars($reservation['username']) . "</strong> (" . htmlspecialchars($reservation['email']) . ") for the service: <strong>" . htmlspecialchars($reservation['package_name']) . "</strong> (Reservation Date: " . htmlspecialchars($reservation['reservation_date']) . ").</p>";
    foreach ($adminEmails as $adminEmail) {
        EmailHelper::sendMail($adminEmail, $adminSubject, $adminMessage);
    }
} 