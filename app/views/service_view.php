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
    <main class="max-w-2xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col items-center">
            <h3 class="text-3xl font-bold catering-font mb-4 text-yellow-600 text-center"><?php echo htmlspecialchars($service['item']); ?></h3>
            <?php if (!empty($service['image'])): ?>
                <img src="/uplaods/<?php echo htmlspecialchars($service['image']); ?>" alt="Service Image" class="h-64 w-full object-cover rounded mb-6" />
            <?php endif; ?>
            <div class="w-full mb-3 text-lg"><span class="font-semibold">Price:</span> ₱<?php echo htmlspecialchars($service['price'] ?? $service['packs']); ?></div>
            <div class="w-full mb-3"><span class="font-semibold">Packs:</span> <?php echo htmlspecialchars($service['packs']); ?></div>
            <div class="w-full mb-3"><span class="font-semibold">Location:</span> <?php echo htmlspecialchars($service['location']); ?></div>
            <div class="w-full mb-3"><span class="font-semibold">Description:</span> <?php echo htmlspecialchars($service['description'] ?? ''); ?></div>
            <div class="w-full mb-3 text-xs text-gray-400 text-right">Added: <?php echo htmlspecialchars($service['created_at']); ?></div>
            <a href="services.php" class="mt-6 bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-2 px-6 rounded">Reservation</a>
        </div>
    </main>
</body>
</html> 