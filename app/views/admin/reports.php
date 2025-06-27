<?php
require_once __DIR__ . '/../../../config/database.php';

$pdo = Database::connect();

// Handle filters
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';
$status = $_GET['status'] ?? '';
$service = $_GET['service'] ?? '';
$export = isset($_GET['export']) && $_GET['export'] === 'csv';

// Build query with filters
$where = [];
$params = [];
if ($dateFrom) {
    $where[] = 'o.reservation_date >= ?';
    $params[] = $dateFrom;
}
if ($dateTo) {
    $where[] = 'o.reservation_date <= ?';
    $params[] = $dateTo;
}
if ($status) {
    $where[] = 'o.payment_status = ?';
    $params[] = $status;
}
if ($service) {
    $where[] = 's.package_name = ?';
    $params[] = $service;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// Fetch all services for filter dropdown
$servicesList = $pdo->query('SELECT DISTINCT package_name FROM services')->fetchAll(PDO::FETCH_COLUMN);

// Fetch filtered orders
$sql = "SELECT o.*, u.username, u.email, s.package_name AS service_name, s.price FROM orders o JOIN users u ON o.user_id = u.id JOIN services s ON o.service_id = s.id $whereSql ORDER BY o.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Export to CSV if requested (must be before any output)
if ($export) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="orders_report.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Order ID', 'External ID', 'User', 'Email', 'Service', 'Reservation Date', 'Amount', 'Status', 'Created At']);
    foreach ($orders as $order) {
        fputcsv($output, [
            $order['id'],
            $order['external_id'] ?? '',
            $order['username'],
            $order['email'],
            $order['service_name'],
            $order['reservation_date'],
            $order['amount'],
            $order['payment_status'] ?? 'pending',
            $order['created_at'],
        ]);
    }
    fclose($output);
    exit;
}

require_once __DIR__ . '/layouts/header.php';

// Summary stats
$totalBookings = count($orders);
$totalRevenue = 0;
$totalPaid = 0;
$totalPending = 0;
foreach ($orders as $order) {
    $totalRevenue += (float)($order['amount'] ?? 0);
    if (($order['payment_status'] ?? '') === 'paid') $totalPaid++;
    if (($order['payment_status'] ?? '') === 'pending') $totalPending++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; }
        .yellow-bg { background: #facc15; }
        .hover-scale { transition: all 0.3s ease; }
        .hover-scale:hover { transform: scale(1.02); box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.1); }
    </style>
</head>
<body class="bg-white text-black">
    <section class="yellow-bg text-black pt-20 pb-12 mb-8 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font text-black">Reports & Performance</h2>
            <p class="text-lg opacity-90">Detailed summaries of bookings, payments, and history management</p>
        </div>
    </section>
    <main class="max-w-6xl mx-auto px-4 pb-12">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale">
                <p class="text-gray-600 text-lg">Total Bookings</p>
                <p class="text-3xl font-bold text-yellow-500"><?php echo $totalBookings; ?></p>
            </div>
            <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale">
                <p class="text-gray-600 text-lg">Total Revenue</p>
                <p class="text-3xl font-bold text-yellow-500">₱<?php echo number_format($totalRevenue, 2); ?></p>
            </div>
            <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale">
                <p class="text-gray-600 text-lg">Paid Orders</p>
                <p class="text-3xl font-bold text-yellow-500"><?php echo $totalPaid; ?></p>
            </div>
            <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale">
                <p class="text-gray-600 text-lg">Pending Payments</p>
                <p class="text-3xl font-bold text-yellow-500"><?php echo $totalPending; ?></p>
            </div>
        </div>
        <!-- Filters -->
        <form method="get" class="mb-8 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700">Date From</label>
                <input type="date" name="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>" class="border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Date To</label>
                <input type="date" name="date_to" value="<?php echo htmlspecialchars($dateTo); ?>" class="border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="border rounded px-3 py-2">
                    <option value="">All</option>
                    <option value="paid" <?php if ($status==='paid') echo 'selected'; ?>>Paid</option>
                    <option value="pending" <?php if ($status==='pending') echo 'selected'; ?>>Pending</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Service</label>
                <select name="service" class="border rounded px-3 py-2">
                    <option value="">All</option>
                    <?php foreach ($servicesList as $svc): ?>
                        <option value="<?php echo htmlspecialchars($svc); ?>" <?php if ($service===$svc) echo 'selected'; ?>><?php echo htmlspecialchars($svc); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded-lg transition">Filter</button>
            </div>
            <div>
                <button type="submit" name="export" value="csv" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded-lg transition">Export to CSV</button>
            </div>
        </form>
        <!-- Orders Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">External ID</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation Date</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($order['external_id'] ?? ''); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['username']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['service_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['reservation_date']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱<?php echo number_format($order['amount'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['payment_status'] ?? 'pending'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($order['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
