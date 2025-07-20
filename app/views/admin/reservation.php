<?php
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

// Fetch all orders with user and service info
$pdo = Database::connect();
$orders = $pdo->query('SELECT o.*, u.username, u.email, s.package_name AS service_name, s.price FROM orders o JOIN users u ON o.user_id = u.id JOIN services s ON o.service_id = s.id ORDER BY o.created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Accounts Management</title>
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
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font text-black" data-aos="fade-up">Reservation Management</h2>
            <p class="text-lg opacity-90" data-aos="fade-up" data-aos-delay="100">All reservations/orders placed by users</p>
        </div>
    </section>
    <main class="max-w-6xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden" data-aos="fade-up">
            <div class="overflow-x-auto">
                <table class="max-w-7xl divide-y divide-gray-200">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['username']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['service_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['reservation_date']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱<?php echo number_format($order['amount'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <button class="show-details-btn bg-yellow-400 hover:bg-yellow-500 text-black px-3 py-1 rounded" 
                                            data-order='<?php echo json_encode($order); ?>'>View</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <div id="orderModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
            <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-black text-2xl">&times;</button>
            <h3 class="text-xl font-bold mb-4">Order Details</h3>
            <table class="w-full text-sm mb-2" id="orderDetailsTable"></table>
        </div>
    </div>
    <script>
        AOS.init({ duration: 800, once: true, offset: 50 });
        document.querySelectorAll('.show-details-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const order = JSON.parse(this.getAttribute('data-order'));
                let html = '';
                for (const key in order) {
                    if (order.hasOwnProperty(key)) {
                        html += `<tr><td class='font-semibold pr-2 py-1'>${key.replace(/_/g, ' ').toUpperCase()}</td><td class='py-1'>${order[key]}</td></tr>`;
                    }
                }
                document.getElementById('orderDetailsTable').innerHTML = html;
                document.getElementById('orderModal').classList.remove('hidden');
            });
        });
        document.getElementById('closeModal').onclick = function() {
            document.getElementById('orderModal').classList.add('hidden');
        };
        // Optional: close modal on background click
        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    </script>
</body>
</html>
 
