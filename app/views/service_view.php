<?php
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../config/database.php';


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="max-w-xl mx-auto mt-20 text-center text-red-500">Invalid service ID.</div>';
    exit;
}

$serviceId = (int)$_GET['id'];
$pdo = Database::connect();
$stmt = $pdo->prepare('SELECT * FROM services WHERE id = ?');
$stmt->execute([$serviceId]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    echo '<div class="max-w-xl mx-auto mt-20 text-center text-red-500">Service not found.</div>';
    exit;
}

// Reservation form handling
$reservationSuccess = false;
$reservationError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    if (!isset($_SESSION['user_id'])) {
        $reservationError = 'You must be logged in to reserve.';
    } else {
        $userId = $_SESSION['user_id'];
        $reservationDate = $_POST['reservation_date'] ?? '';
        $notes = $_POST['notes'] ?? '';
        if (!$reservationDate) {
            $reservationError = 'Please select a reservation date.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, service_id, package_name, reservation_date, notes) VALUES (?, ?, ?, ?, ?)');
            $reservationSuccess = $stmt->execute([
                $userId,
                $serviceId,
                $service['package_name'],
                $reservationDate,
                $notes
            ]);
            if (!$reservationSuccess) {
                $reservationError = 'Failed to reserve. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Details - <?php echo htmlspecialchars($service['item']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
      body { font-family: 'Montserrat', Arial, sans-serif; }
      .catering-font { font-family: 'Playfair Display', serif; }
      .yellow-bg { background: #facc15; }
    </style>
</head>
<body class="bg-white text-black">
    <section class="yellow-bg text-black pt-16 pb-8 mb-8 shadow-lg">
        <div class="max-w-2xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-2 catering-font">Service Details</h2>
            <p class="text-lg opacity-90">All information about this package</p>
        </div>
    </section>
    <main class="max-w-4xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col md:flex-row gap-8 items-start">
            <!-- Service Details (Left) -->
            <div class="flex-1 w-full">
                <h3 class="text-3xl font-bold catering-font mb-4 text-yellow-600 text-center"><?php echo htmlspecialchars($service['package_name']); ?></h3>
                <?php if (!empty($service['image'])): ?>
                    <img src="/uplaods/<?php echo htmlspecialchars($service['image']); ?>" alt="Service Image" class="h-64 w-full object-cover rounded mb-6" />
                <?php endif; ?>
                <div class="w-full mb-3"><span class="font-semibold">category:</span> <?php echo htmlspecialchars($service['category']); ?></div>
                <div class="w-full mb-3"><span class="font-semibold">item:</span> <?php echo htmlspecialchars($service['item']); ?></div>
                <div class="w-full mb-3 text-lg"><span class="font-semibold">Price:</span> ₱<?php echo htmlspecialchars($service['price'] ?? $service['packs']); ?></div>
                <div class="w-full mb-3"><span class="font-semibold">Packs:</span> <?php echo htmlspecialchars($service['packs']); ?></div>
                <div class="w-full mb-3"><span class="font-semibold">Location:</span> <?php echo htmlspecialchars($service['location']); ?></div>
                <div class="w-full mb-3"><span class="font-semibold">Description:</span> <?php echo htmlspecialchars($service['description'] ?? ''); ?></div>
                <div class="w-full mb-3 text-xs text-gray-400 text-right">Added: <?php echo htmlspecialchars($service['created_at']); ?></div>
            </div>
            <!-- Reservation Form (Right) -->
            <div class="flex-1 w-full">
                <div class="bg-yellow-50 rounded-xl p-6 shadow">
                    <h4 class="text-xl font-bold mb-4 text-yellow-700">Reserve this Package</h4>
                    <?php if ($reservationSuccess): ?>
                        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">Reservation successful!</div>
                    <?php elseif ($reservationError): ?>
                        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?php echo htmlspecialchars($reservationError); ?></div>
                    <?php endif; ?>
                    <form method="post" action="create_invoice" class="space-y-4">
                        <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($serviceId); ?>">
                        <input type="hidden" name="package_name" value="<?php echo htmlspecialchars($service['package_name']); ?>">
                        <input type="hidden" name="amount" value="<?php echo htmlspecialchars($service['price']); ?>">
                        <input type="hidden" name="user_email" value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                        <div>
                            <label for="reservation_date" class="block font-semibold mb-1">Reservation Date</label>
                            <input type="date" id="reservation_date" name="reservation_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label for="notes" class="block font-semibold mb-1">Notes</label>
                            <textarea id="notes" name="notes" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Any special instructions?"></textarea>
                        </div>
                        <button type="submit" name="reserve" class="w-full bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-2 px-6 rounded">Reserve & Pay with GCash</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>
</html> 