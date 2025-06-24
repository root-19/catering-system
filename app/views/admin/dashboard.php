<?php

use root_dev\Config\Database;

require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../../config/database.php';

// Create database connection
$db = \Database::connect();

// Get total users count from database
$sql = "SELECT COUNT(*) as total FROM users";
$stmt = $db->query($sql);
$totalUsers = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SMS Connect</title>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
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

                <!-- Messages Card -->
                <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-full bg-yellow-100">
                            <i class="fas fa-envelope text-3xl text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-lg">Total Reservations</p>
                            <p class="text-3xl font-bold text-yellow-500">0</p>
                        </div>
                    </div>
                </div>

                <!-- Revenue Card -->
                <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6 hover-scale" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 rounded-full bg-yellow-100">
                            <i class="fas fa-chart-line text-3xl text-yellow-500"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-lg">Total Revenue</p>
                            <p class="text-3xl font-bold text-yellow-500">₱0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white border-2 border-yellow-400 rounded-2xl shadow-lg p-6" data-aos="fade-up" data-aos-delay="400">
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

    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
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
