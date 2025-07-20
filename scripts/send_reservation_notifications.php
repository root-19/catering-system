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

// Set the single admin email
$adminEmail = 'wasieacuna@gmail.com';

if (count($reservations) > 0) {
    // Build a summary message
    $adminSubject = "[ADMIN ALERT] Reservation Events Today (" . $today . ")";
    $adminMessage = "<h2>Admin Notification</h2>";
    $adminMessage .= "<p>The following reservation events are scheduled for today:</p><ul>";
    foreach ($reservations as $reservation) {
        $adminMessage .= "<li><strong>User:</strong> " . htmlspecialchars($reservation['username']) .
                         " (" . htmlspecialchars($reservation['email']) . ")<br>" .
                         "<strong>Service:</strong> " . htmlspecialchars($reservation['package_name']) . "</li>";
    }
    $adminMessage .= "</ul>";

    EmailHelper::sendMail($adminEmail, $adminSubject, $adminMessage);
} 