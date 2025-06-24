<?php
// require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../models/ServiceModel.php';
use App\Models\ServiceModel;
$menu = ServiceModel::getMenu();
$locations = ServiceModel::getLocations();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      body { font-family: 'Montserrat', Arial, sans-serif; }
      .catering-font { font-family: 'Playfair Display', serif; }
      .yellow-bg { background: #facc15; }
      .hover-scale { transition: all 0.3s ease; }
      .hover-scale:hover { transform: scale(1.02); box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.1); }
      .fade-in { animation: fadeIn 0.5s ease-in; }
      @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-white text-black">
    <section class="yellow-bg text-black pt-20 pb-12 mb-8 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font" data-aos="fade-up">Service Management</h2>
            <p class="text-lg opacity-90" data-aos="fade-up" data-aos-delay="100">Manage and add catering services</p>
        </div>
    </section>
    <main class="max-w-6xl mx-auto px-4 pb-12">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 rounded-xl bg-green-50 p-4 border border-green-200" data-aos="fade-up">
                <div class="flex"><div class="flex-shrink-0"><i class="fas fa-check-circle text-green-400 text-xl"></i></div><div class="ml-3"><p class="text-sm font-medium text-green-800"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p></div></div>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 rounded-xl bg-red-50 p-4 border border-red-200" data-aos="fade-up">
                <div class="flex"><div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-400 text-xl"></i></div><div class="ml-3"><p class="text-sm font-medium text-red-800"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p></div></div>
            </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8" data-aos="fade-up">
            <form method="post" action="" class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" id="category" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <?php foreach ($menu as $cat => $items): ?>
                                <option value="<?= $cat ?>"><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="item" class="block text-sm font-medium text-gray-700 mb-2">Menu Item</label>
                        <select name="item" id="item" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <?php foreach (reset($menu) as $item): ?>
                                <option value="<?= $item ?>"><?= $item ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="packs" class="block text-sm font-medium text-gray-700 mb-2">Number of Packs</label>
                        <select name="packs" id="packs" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <?php for ($i = 50; $i <= 100; $i += 10): ?>
                                <option value="<?= $i ?>"><?= $i ?> packs</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-6">
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <select name="location" id="location" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc ?>"><?= $loc ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2"></textarea>
                </div>
                <div class="mb-6">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Image</label>
                    <input type="file" name="image" id="image" accept="image/*" class="w-full border border-gray-300 rounded-lg px-3 py-2" />
                </div>
                <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded-lg shadow hover-scale">Add Service</button>
            </form>
        </div>
        <!-- Table of added services -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden" data-aos="fade-up">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu Item</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Packs</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($service['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['category']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['item']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['packs']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['location']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['description'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if (!empty($service['image'])): ?>
                                            <img src="/resources/image/<?php echo htmlspecialchars($service['image']); ?>" alt="Service Image" class="h-12 w-12 object-cover rounded" />
                                        <?php else: ?>
                                            <span class="text-gray-400">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($service['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No services found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>
        AOS.init({ duration: 800, once: true, offset: 50 });
        // Dynamic menu item update
        const menu = <?php echo json_encode($menu); ?>;
        document.getElementById('category').addEventListener('change', function() {
            const items = menu[this.value];
            const itemSelect = document.getElementById('item');
            itemSelect.innerHTML = '';
            items.forEach(function(item) {
                const opt = document.createElement('option');
                opt.value = item;
                opt.textContent = item;
                itemSelect.appendChild(opt);
            });
        });

        <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?php echo addslashes($_SESSION['success']); ?>',
            confirmButtonColor: '#facc15'
        });
        <?php unset($_SESSION['success']); endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo addslashes($_SESSION['error']); ?>',
            confirmButtonColor: '#f87171'
        });
        <?php unset($_SESSION['error']); endif; ?>
    </script>
</body>
</html>
