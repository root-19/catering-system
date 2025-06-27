<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../app/models/User.php';

// Set your Xendit API key directly here
$xenditApiKey = '';

use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

Configuration::setXenditKey($xenditApiKey);

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

$externalId = 'invoice-' . time() . '-' . uniqid();
$params = [
    'external_id' => $externalId,
    'payer_email' => $userEmail,
    'description' => 'Reservation for ' . $packageName,
    'amount' => (float)$amount,
    'success_redirect_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/payment_success?external_id=' . $externalId,
    'failure_redirect_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/payment_failed',
    'currency' => 'PHP',
    'payment_methods' => ['GCASH'],
];

try {
    $apiInstance = new InvoiceApi();
    $createInvoiceRequest = new CreateInvoiceRequest($params);
    $invoice = $apiInstance->createInvoice($createInvoiceRequest);
    if (!isset($invoice['invoice_url']) && method_exists($invoice, 'getInvoiceUrl')) {
        $invoiceUrl = $invoice->getInvoiceUrl();
    } else if (isset($invoice['invoice_url'])) {
        $invoiceUrl = $invoice['invoice_url'];
    } else {
        throw new Exception('Failed to get payment URL.');
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
    header('Location: ' . $invoiceUrl);
    exit;
} catch (\Exception $e) {
    echo '<pre>Xendit API Error: ' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
} 