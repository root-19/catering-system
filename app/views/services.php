<?php
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../config/database.php';

// Fetch all services from the database
$pdo = Database::connect();
$services = $pdo->query("SELECT * FROM services ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Catering Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
      body { font-family: 'Montserrat', Arial, sans-serif; }
      .catering-font { font-family: 'Playfair Display', serif; }
      .yellow-bg { background: #facc15; }
      .hover-scale { transition: all 0.3s ease; }
      .hover-scale:hover { transform: scale(1.02); box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.1); }
    </style>
</head>
<body class="bg-white text-black">
    <section class="yellow-bg text-black pt-20 pb-12 mb-8 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font">Our Catering Services</h2>
            <p class="text-lg opacity-90">Explore our delicious menu and catering options for your next event!</p>
        </div>
    </section>
    <main class="max-w-6xl mx-auto px-4 pb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover-scale flex flex-col">
                        <h3 class="text-2xl font-bold catering-font mb-2 text-yellow-600 text-center"><?php echo htmlspecialchars($service['package_name']); ?></h3>
                        <?php if (!empty($service['image'])): ?>
                            <img src="/uplaods/<?php echo htmlspecialchars($service['image']); ?>" alt="Service Image" class="h-48 w-full object-cover rounded mb-4" onerror="this.onerror=null;this.src='https://via.placeholder.com/400x200?text=No+Image';" />
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x200?text=No+Image" alt="No Image" class="h-48 w-full object-cover rounded mb-4" />
                        <?php endif; ?>
                        <div class="mb-2 text-gray-700"><span class="font-semibold">Menu Items:</span> <?php echo htmlspecialchars($service['item']); ?></div>
                        <div class="mb-2 text-gray-700 text-lg font-semibold">₱<?php echo htmlspecialchars($service['price'] ?? $service['packs']); ?></div>
                        <div class="mb-2 text-gray-700"><span class="font-semibold">Packs:</span> <?php echo htmlspecialchars($service['packs']); ?></div>
                        <div class="mb-2 text-gray-700"><span class="font-semibold">Location:</span> <?php echo htmlspecialchars($service['location']); ?></div>
                        <div class="mb-2 text-gray-700"><span class="font-semibold">Description:</span> <?php echo htmlspecialchars($service['description'] ?? ''); ?></div>
                        <div class="mt-auto flex gap-2 pt-4">
                            <button class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-2 px-4 rounded">Add</button>
                            <a href="/services/view?id=<?php echo urlencode($service['id']); ?>" class="flex-1 bg-gray-200 hover:bg-gray-300 text-black font-bold py-2 px-4 rounded text-center">View</a>
                        </div>
                        <div class="mt-2 text-xs text-gray-400 text-right">Added: <?php echo htmlspecialchars($service['created_at']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 text-center text-gray-500 text-lg">No services found.</div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
