<?php
require_once __DIR__ . '/../../config/database.php';

// Get external_id from GET parameter
$externalId = $_GET['external_id'] ?? null;

if ($externalId) {
    $pdo = Database::connect();
    $stmt = $pdo->prepare('UPDATE orders SET payment_status = ? WHERE external_id = ?');
    $stmt->execute(['paid', $externalId]);
    echo "<div class='max-w-xl mx-auto mt-20 text-center text-green-500'>Payment successful! Your reservation is confirmed.</div>";
} else {
    echo "<div class='max-w-xl mx-auto mt-20 text-center text-red-500'>Payment success, but order not found.</div>";
}

// You can add logic here to fetch the latest order/receipt for the user if needed
session_start();

// Placeholder for receipt file or download link
$defaultReceipt = '/public/sample_receipt.pdf'; // Change this to actual receipt logic if available
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
        <h1 class="text-3xl font-bold text-green-600 mb-4">Payment Successful!</h1>
        <p class="text-lg mb-6">Thank you for your payment. Your reservation is confirmed.</p>
        <a href="<?php echo $defaultReceipt; ?>" download class="inline-block bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg transition">Download Receipt</a>
        <div class="mt-6">
            <a href="/dashboard" class="text-green-700 underline">Back to Dashboard</a>
        </div>
    </div>
</body>
</html> 