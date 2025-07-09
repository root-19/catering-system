<?php

use root_dev\Config\Database;

require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../app/models/Review.php';

// Create database connection
$db = \Database::connect();

// Get total users count from database
$sql = "SELECT COUNT(*) as total FROM users";
$stmt = $db->query($sql);
$totalUsers = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

// Get total services count from database
$sql = "SELECT COUNT(*) as total FROM services";
$stmt = $db->query($sql);
$totalServices = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

// Get total pending reviews
$reviewModel = new \App\Models\Review();
$totalPendingReviews = $reviewModel->countPendingReviews();

// Get total reservations (orders)
$sql = "SELECT COUNT(*) as total FROM orders";
$stmt = $db->query($sql);
$totalReservations = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

// Get total revenue from orders
$sql = "SELECT SUM(amount) as total FROM orders";
$stmt = $db->query($sql);
$totalRevenue = $stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;

// Fetch all reservations for the calendar
$reservationsForCalendar = $db->query("SELECT o.id, o.reservation_date, u.username, s.package_name FROM orders o JOIN users u ON o.user_id = u.id JOIN services s ON o.service_id = s.id")->fetchAll(PDO::FETCH_ASSOC);

// Fetch monthly data for the last 12 months
function getMonthlyData($db, $table, $dateColumn, $aggregate = 'COUNT(*)', $where = '', $statusValue = null) {
    $months = [];
    $data = [];
    $now = new DateTime();
    for ($i = 11; $i >= 0; $i--) {
        $month = $now->format('Y-m');
        $months[] = $month;
        $now->modify('-1 month');
    }
    $months = array_reverse($months);
    $results = array_fill_keys($months, 0);
    $whereClause = $where ? "WHERE $where" : '';
    $sql = "SELECT DATE_FORMAT($dateColumn, '%Y-%m') as month, $aggregate as total FROM $table $whereClause GROUP BY month ORDER BY month";
    $stmt = $db->prepare($sql);
    if ($statusValue !== null) {
        $stmt->execute([$statusValue]);
    } else {
        $stmt->execute();
    }
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($results[$row['month']])) {
            $results[$row['month']] = $row['total'];
        }
    }
    return array_values($results);
}

$monthlyUsers = getMonthlyData($db, 'users', 'created_at');
$monthlyServices = getMonthlyData($db, 'services', 'created_at');
$monthlyPendingReviews = getMonthlyData($db, 'reviews', 'created_at', 'COUNT(*)', 'status = ?', 'pending');
$monthlyReservations = getMonthlyData($db, 'orders', 'reservation_date');
$monthlyRevenue = getMonthlyData($db, 'orders', 'reservation_date', 'SUM(amount)');

$monthsLabels = [];
$now = new DateTime();
for ($i = 11; $i >= 0; $i--) {
    $monthsLabels[] = $now->format('M');
    $now->modify('-1 month');
}
$monthsLabels = array_reverse($monthsLabels);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helper Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; }
        .catering-font { font-family: 'Playfair Display', serif; }
        .yellow-bg {
            background: #facc15; /* Tailwind yellow-400 */
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
    </style>
</head>
<body class="bg-white text-black">
    <div class="min-h-screen">
        <!-- Welcome Section -->
        <div class="yellow-bg text-black p-8 shadow-lg" data-aos="fade-down">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-4xl font-bold mb-2 catering-font">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <p class="text-black opacity-80">Manage your system efficiently</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <!-- Total Users Card -->
                <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-full bg-yellow-100">
                            <i class="fas fa-users text-3xl text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-lg">Total Users</p>
                            <p class="text-3xl font-bold text-yellow-500"><?php echo $totalUsers; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Total Services Card -->
                <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-full bg-yellow-100">
                            <i class="fas fa-concierge-bell text-3xl text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-lg">Total Services</p>
                            <p class="text-3xl font-bold text-yellow-500"><?php echo $totalServices; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Pending Reviews Card -->
                <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-full bg-yellow-100">
                            <i class="fas fa-star-half-alt text-3xl text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-lg">Pending Reviews</p>
                            <p class="text-3xl font-bold text-yellow-500"><?php echo $totalPendingReviews; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Messages Card -->
                <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale" data-aos="fade-up" data-aos-delay="400">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-full bg-yellow-100">
                            <i class="fas fa-envelope text-3xl text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-lg">Total Reservations</p>
                            <p class="text-3xl font-bold text-yellow-500"><?php echo $totalReservations; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Revenue Card -->
                <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale" data-aos="fade-up" data-aos-delay="500">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-full bg-yellow-100">
                            <i class="fas fa-chart-line text-3xl text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-lg">Total Revenue</p>
                            <p class="text-3xl font-bold text-yellow-500">₱<?php echo number_format($totalRevenue, 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Line Chart -->
            <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 mb-8" data-aos="fade-up" data-aos-delay="550">
                <h2 class="text-2xl font-bold text-yellow-600 mb-6">Monthly Analytics Overview</h2>
                <canvas id="analyticsLineChart" height="100"></canvas>
            </div>

            <!-- System Status -->
            <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6" data-aos="fade-up" data-aos-delay="600">
                <h2 class="text-2xl font-bold text-yellow-600 mb-6">System Status</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-server text-yellow-500"></i>
                            <span class="text-gray-700">Server Status</span>
                        </div>
                        <span class="px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-medium">Online</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-clock text-yellow-500"></i>
                            <span class="text-gray-700">Last Updated</span>
                        </div>
                        <span class="text-yellow-600"><?php echo date('Y-m-d H:i:s'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
        });

        // Chart.js - Monthly Analytics (Real Data)
        const months = <?php echo json_encode($monthsLabels); ?>;
        const usersData = <?php echo json_encode($monthlyUsers); ?>;
        const servicesData = <?php echo json_encode($monthlyServices); ?>;
        const reviewsData = <?php echo json_encode($monthlyPendingReviews); ?>;
        const reservationsData = <?php echo json_encode($monthlyReservations); ?>;
        const revenueData = <?php echo json_encode($monthlyRevenue); ?>;

        const ctx = document.getElementById('analyticsLineChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Total Users',
                        data: usersData,
                        borderColor: '#facc15',
                        backgroundColor: 'rgba(250,204,21,0.1)',
                        tension: 0.4,
                        fill: false,
                        pointRadius: 4,
                        pointBackgroundColor: '#facc15',
                    },
                    {
                        label: 'Total Services',
                        data: servicesData,
                        borderColor: '#34d399',
                        backgroundColor: 'rgba(52,211,153,0.1)',
                        tension: 0.4,
                        fill: false,
                        pointRadius: 4,
                        pointBackgroundColor: '#34d399',
                    },
                    {
                        label: 'Pending Reviews',
                        data: reviewsData,
                        borderColor: '#f472b6',
                        backgroundColor: 'rgba(244,114,182,0.1)',
                        tension: 0.4,
                        fill: false,
                        pointRadius: 4,
                        pointBackgroundColor: '#f472b6',
                    },
                    {
                        label: 'Total Reservations',
                        data: reservationsData,
                        borderColor: '#60a5fa',
                        backgroundColor: 'rgba(96,165,250,0.1)',
                        tension: 0.4,
                        fill: false,
                        pointRadius: 4,
                        pointBackgroundColor: '#60a5fa',
                    },
                    {
                        label: 'Total Revenue',
                        data: revenueData,
                        borderColor: '#f59e42',
                        backgroundColor: 'rgba(245,158,66,0.1)',
                        tension: 0.4,
                        fill: false,
                        pointRadius: 4,
                        pointBackgroundColor: '#f59e42',
                        yAxisID: 'y1',
                    },
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                stacked: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                    },
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Count',
                        },
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                        title: {
                            display: true,
                            text: 'Revenue (₱)',
                        },
                    },
                },
            },
        });

        // Calendar events from PHP
        const calendarEvents = <?php echo json_encode(array_map(function($r) {
            return [
                'title' => $r['package_name'] . ' - ' . $r['username'],
                'start' => $r['reservation_date'],
                'allDay' => true,
                'id' => $r['id'],
                'username' => $r['username'],
                'package_name' => $r['package_name'],
                'reservation_date' => $r['reservation_date'],
            ];
        }, $reservationsForCalendar)); ?>;

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 500,
                events: calendarEvents,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                eventColor: '#facc15',
                eventTextColor: '#000',
                eventDidMount: function(info) {
                    let tooltip = document.createElement('div');
                    tooltip.className = 'fc-tooltip';
                    tooltip.style.position = 'absolute';
                    tooltip.style.zIndex = 1000;
                    tooltip.style.background = '#fffbe6';
                    tooltip.style.border = '1px solid #facc15';
                    tooltip.style.padding = '8px 12px';
                    tooltip.style.borderRadius = '8px';
                    tooltip.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
                    tooltip.style.display = 'none';
                    tooltip.innerHTML =
                        // '<strong>Order ID:</strong> ' + info.event.extendedProps.id + '<br>' +
                        '<strong>User:</strong> ' + info.event.extendedProps.username + '<br>' +
                        '<strong>Package:</strong> ' + info.event.extendedProps.package_name + '<br>' +
                        '<strong>Date:</strong> ' + info.event.extendedProps.reservation_date;

                    document.body.appendChild(tooltip);

                    info.el.addEventListener('mouseenter', function(e) {
                        tooltip.style.display = 'block';
                        tooltip.style.left = (e.pageX + 10) + 'px';
                        tooltip.style.top = (e.pageY + 10) + 'px';
                    });
                    info.el.addEventListener('mousemove', function(e) {
                        tooltip.style.left = (e.pageX + 10) + 'px';
                        tooltip.style.top = (e.pageY + 10) + 'px';
                    });
                    info.el.addEventListener('mouseleave', function() {
                        tooltip.style.display = 'none';
                    });
                },
            });
            calendar.render();
        });
    </script>
</body>
</html>


<script>
    const menuButton = document.querySelector('button[aria-controls="mobile-menu"]');
    const mobileMenu = document.getElementById('mobile-menu');

    menuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });
</script>
