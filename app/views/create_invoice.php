<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/models/User.php';

// Xendit API key (public key is for client-side, but for server-to-server use secret key; for demo, we use public as placeholder)
$xenditApiKey = '';

if (!isset($_SESSION['user_id'])) {
    die('You must be logged in to reserve.');
}

// Get POST data
$userId = $_SESSION['user_id'];
$serviceId = $_POST['service_id'] ?? null;
$packageName = $_POST['package_name'] ?? '';
$reservationDate = $_POST['reservation_date'] ?? '';
$notes = $_POST['notes'] ?? '';
$amount = $_POST['amount'] ?? 0;

// get email
$userModel = new \root_dev\Models\User();
$user = $userModel->find($userId);
$userEmail = $user['email'] ?? 'noemail@example.com';

if (!$serviceId || !$reservationDate || !$packageName || !$amount) {
    die('Missing required fields.');
}

// create Xendit invoice
$externalId = 'invoice-' . time() . '-' . uniqid();
$data = [
    'external_id' => $externalId,
    'payer_email' => $userEmail,
    'description' => 'Reservation for ' . $packageName,
    'amount' => (float)$amount,
    'success_redirect_url' => 'http://' . $_SERVER['HTTP_HOST'] . 'payment_success.php',
    'failure_redirect_url' => 'http://' . $_SERVER['HTTP_HOST'] . 'payment_failed.php',
    'currency' => 'PHP',
    'payment_methods' => ['GCASH'],
];

$ch = curl_init('https://api.xendit.co/v2/invoices');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Basic ' . base64_encode($xenditApiKey . ':')
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 && $httpCode !== 201) {
    echo '<pre>API Response: ' . htmlspecialchars($response) . "\nHTTP Code: $httpCode</pre>";
    die('Failed to create invoice.');
}

$invoice = json_decode($response, true);
if (!isset($invoice['invoice_url'])) {
    die('Failed to get payment URL.');
}

// Save pending order in database
$pdo = Database::connect();
$stmt = $pdo->prepare('INSERT INTO orders (user_id, service_id, package_name, reservation_date, notes, amount, payment_status, external_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())');
$stmt->execute([
    $userId,
    $serviceId,
    $packageName,
    $reservationDate,
    $notes,
    $amount,
    'pending',
    $externalId
]);

// Redirect to Xendit payment page
header('Location: ' . $invoice['invoice_url']);
exit; 