<?php
require_once __DIR__ . '/../../config/database.php';

// Get external_id from GET parameter
$externalId = $_GET['external_id'] ?? null;

$orderFound = false;
$receiptPath = null;

if ($externalId) {
    $pdo = Database::connect();
    // Check if order exists
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE external_id = ?');
    $stmt->execute([$externalId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($order) {
        // Update payment status
        $updateStmt = $pdo->prepare('UPDATE orders SET payment_status = ? WHERE external_id = ?');
        $updateStmt->execute(['paid', $externalId]);
        $orderFound = true;
        // Send email notification if not already sent
        if (empty($order['email_notified']) || $order['email_notified'] == 0) {
            require_once __DIR__ . '/../../app/models/User.php';
            require_once __DIR__ . '/../../app/Helpers/EmailHelper.php';
            $userModel = new \root_dev\Models\User();
            $user = $userModel->find($order['user_id']);
            if ($user && !empty($user['email'])) {
                \App\Helpers\EmailHelper::sendOrderSuccess($user['email'], $order);
                // Mark as notified
                $pdo->prepare('UPDATE orders SET email_notified = 1 WHERE id = ?')->execute([$order['id']]);
            }
        }
        // Example: set receipt path (customize as needed)
        $receiptDir = __DIR__ . '/../../public/receipts/';
        if (!is_dir($receiptDir)) {
            mkdir($receiptDir, 0777, true);
        }
        $receiptPath = $receiptDir . 'receipt_' . $externalId . '.pdf';
        if (!file_exists($receiptPath)) {
            // Fetch username
            require_once __DIR__ . '/../../app/models/User.php';
            $userModel = new \root_dev\Models\User();
            $user = $userModel->find($order['user_id']);
            $username = $user['username'] ?? 'Unknown';
            // Generate PDF receipt using FPDF
            require_once __DIR__ . '/../../vendor/autoload.php';
            $pdf = new \FPDF();
            $pdf->AddPage();
            // Add logo
            $logoPath = __DIR__ . '/../../resources/image/cater.jpg';
            if (file_exists($logoPath)) {
                $pdf->Image($logoPath, 10, 10, 30, 30); // x, y, width, height
                $pdf->Ln(25);
            }
            // Business name
            $pdf->SetFont('Arial', 'B', 18);
            $pdf->Cell(0, 10, 'Your Catering Business', 0, 1, 'C');
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 8, 'Official Receipt', 0, 1, 'C');
            $pdf->Ln(5);
            // Receipt details
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Order ID: ' . $order['id'], 0, 1);
            $pdf->Cell(0, 10, 'External ID: ' . $order['external_id'], 0, 1);
            $pdf->Cell(0, 10, 'Username: ' . $username, 0, 1);
            $pdf->Cell(0, 10, 'Service ID: ' . $order['service_id'], 0, 1);
            $pdf->Cell(0, 10, 'Package: ' . $order['package_name'], 0, 1);
            $pdf->Cell(0, 10, 'Reservation Date: ' . $order['reservation_date'], 0, 1);
            $pdf->Cell(0, 10, 'Amount: PHP ' . number_format($order['amount'], 2), 0, 1);
            $pdf->Cell(0, 10, 'Payment Status: ' . $order['payment_status'], 0, 1);
            $pdf->Cell(0, 10, 'Payment Method: GCash', 0, 1);
            $pdf->Cell(0, 10, 'Created At: ' . $order['created_at'], 0, 1);
            $pdf->Ln(10);
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->Cell(0, 10, 'Thank you for your reservation!', 0, 1, 'C');
            $pdf->Output('F', $receiptPath);
        }
        // For download link (web path)
        if (file_exists($receiptPath)) {
            $receiptPath = '/public/receipts/receipt_' . $externalId . '.pdf';
        } else {
            $receiptPath = null;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; }
    </style>
</head>
<body class="bg-green-50 min-h-screen flex flex-col items-center justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-lg text-center">
        <?php if ($orderFound): ?>
            <h1 class="text-3xl font-bold text-green-600 mb-4">Payment Successful!</h1>
            <p class="text-lg mb-6">Thank you for your payment. Your reservation is confirmed.</p>
            <?php if ($receiptPath): ?>
                <a href="<?php echo $receiptPath; ?>" download class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg transition">Download Receipt</a>
            <?php else: ?>
                <p class="text-gray-500 mb-4">Receipt not available yet.</p>
            <?php endif; ?>
        <?php else: ?>
            <h1 class="text-2xl font-bold text-red-500 mb-4">Order Not Found</h1>
            <p class="text-lg mb-6">Payment was received, but we could not find your order. Please contact support.</p>
        <?php endif; ?>
        <div class="mt-6">
            <a href="/dashboard" class="text-green-700 underline">Back to Dashboard</a>
        </div>
    </div>
</body>
</html> 