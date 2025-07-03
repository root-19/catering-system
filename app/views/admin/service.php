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
          <form method="POST" action="" enctype="multipart/form-data" class="p-8">
                <div class="mb-6">
                    <label for="package_name" class="block text-sm font-medium text-gray-700 mb-2">Package Name</label>
                    <input type="text" name="package_name" id="package_name" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
                </div>
                <div id="menu-items-wrapper">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 menu-item-row">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category[]" class="w-full border border-gray-300 rounded-lg px-3 py-2 category-select">
                                <?php foreach ($menu as $cat => $items): ?>
                                    <option value="<?= $cat ?>"><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Menu Item</label>
                            <select name="item[]" class="w-full border border-gray-300 rounded-lg px-3 py-2 item-select">
                                <?php foreach (reset($menu) as $item): ?>
                                    <option value="<?= $item ?>"><?= $item ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <button type="button" id="add-menu-item" class="mb-6 bg-gray-200 hover:bg-gray-300 text-black font-bold py-1 px-4 rounded">+ Add another item</button>
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
                <div class="mb-6">
                    <label for="date_time" class="block text-sm font-medium text-gray-700 mb-2">Date and Time</label>
                    <input type="datetime-local" name="date_time" id="date_time" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
                </div>
                <div class="mb-6">
                    <label for="packs" class="block text-sm font-medium text-gray-700 mb-2">Packs (50-100)</label>
                    <input type="number" name="packs" id="packs" min="50" max="100" step="1" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
                </div>
                <div class="mb-6">
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Total Price</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2" required />
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
                            <!-- <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th> -->
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Package Name</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu Item</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Packs</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <!-- <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th> -->
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                    <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($service['id']); ?></td> -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['package_name'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['category']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['item']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['packs']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['location']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['description'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if (!empty($service['image'])): ?>
                                            <img src="/uplaods/<?php echo htmlspecialchars($service['image']); ?>" alt="Service Image" class="h-12 w-12 object-cover rounded" />
                                        <?php else: ?>
                                            <span class="text-gray-400">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($service['date_time'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($service['created_at']); ?></td> -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <button 
                                            class="update-service-btn bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded"
                                            data-service='<?php echo json_encode($service); ?>'
                                        >
                                            Update
                                        </button>
                                    </td>
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
    <!-- Update Service Modal -->
    <div id="updateServiceModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
      <div class="bg-white rounded-lg p-8 w-full max-w-lg relative">
        <button id="closeUpdateModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        <h2 class="text-2xl font-bold mb-4">Update Service</h2>
        <form id="updateServiceForm">
          <input type="hidden" name="id" id="update_id">
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Package Name</label>
            <input type="text" name="package_name" id="update_package_name" class="w-full border rounded px-3 py-2">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Category <span class="text-xs text-gray-400">(one per line)</span></label>
            <textarea name="category" id="update_category" class="w-full border rounded px-3 py-2" rows="2"></textarea>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Menu Item <span class="text-xs text-gray-400">(one per line)</span></label>
            <textarea name="item" id="update_item" class="w-full border rounded px-3 py-2" rows="2"></textarea>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Packs</label>
            <input type="number" name="packs" id="update_packs" class="w-full border rounded px-3 py-2">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Location</label>
            <input type="text" name="location" id="update_location" class="w-full border rounded px-3 py-2">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" id="update_description" class="w-full border rounded px-3 py-2"></textarea>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Date & Time</label>
            <input type="datetime-local" name="date_time" id="update_date_time" class="w-full border rounded px-3 py-2">
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Price</label>
            <input type="number" name="price" id="update_price" class="w-full border rounded px-3 py-2">
          </div>
          <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded-lg shadow">Save Changes</button>
        </form>
      </div>
    </div>
    <script>
        AOS.init({ duration: 800, once: true, offset: 50 });
        // Dynamic menu item update
        const menu = <?php echo json_encode($menu); ?>;
        // For dynamic rows
        function updateMenuItems(row) {
            const categorySelect = row.querySelector('.category-select');
            const itemSelect = row.querySelector('.item-select');
            categorySelect.addEventListener('change', function() {
                const items = menu[this.value];
                itemSelect.innerHTML = '';
                items.forEach(function(item) {
                    const opt = document.createElement('option');
                    opt.value = item;
                    opt.textContent = item;
                    itemSelect.appendChild(opt);
                });
            });
        }
        // Initial row
        document.querySelectorAll('.menu-item-row').forEach(updateMenuItems);
        // Add new row
        document.getElementById('add-menu-item').addEventListener('click', function() {
            const wrapper = document.getElementById('menu-items-wrapper');
            const firstRow = wrapper.querySelector('.menu-item-row');
            const newRow = firstRow.cloneNode(true);
            // Reset values
            newRow.querySelectorAll('select, input').forEach(el => {
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
                if (el.tagName === 'INPUT') el.value = '';
            });
            // Add remove button if not present
            if (!newRow.querySelector('.remove-menu-item')) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = 'Remove';
                btn.className = 'remove-menu-item ml-2 bg-red-200 hover:bg-red-400 text-black font-bold py-1 px-2 rounded';
                btn.onclick = function() {
                    newRow.remove();
                };
                newRow.appendChild(btn);
            }
            wrapper.appendChild(newRow);
            updateMenuItems(newRow);
        });
        // Remove row
        document.querySelectorAll('.remove-menu-item').forEach(btn => {
            btn.onclick = function() {
                btn.closest('.menu-item-row').remove();
            };
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
        // Update Service Modal logic
        // Open modal and fill data
        document.querySelectorAll('.update-service-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const service = JSON.parse(this.getAttribute('data-service'));
                document.getElementById('update_id').value = service.id;
                document.getElementById('update_package_name').value = service.package_name || '';
                document.getElementById('update_category').value = Array.isArray(service.category) ? service.category.join('\n') : (service.category || '');
                document.getElementById('update_item').value = Array.isArray(service.item) ? service.item.join('\n') : (service.item || '');
                document.getElementById('update_packs').value = service.packs || '';
                document.getElementById('update_location').value = service.location || '';
                document.getElementById('update_description').value = service.description || '';
                document.getElementById('update_date_time').value = service.date_time || '';
                document.getElementById('update_price').value = service.price || '';
                document.getElementById('updateServiceModal').classList.remove('hidden');
            });
        });
        // Close modal
        document.getElementById('closeUpdateModal').onclick = function() {
            document.getElementById('updateServiceModal').classList.add('hidden');
        };
        // AJAX submit
        document.getElementById('updateServiceForm').onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('/public/admin/update_service.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success', 'Service updated!', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Update failed', 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Update failed', 'error');
            });
        };
    </script>
</body>
</html>
