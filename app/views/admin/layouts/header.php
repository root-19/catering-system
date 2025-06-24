<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #000;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .mobile-menu {
            transition: all 0.3s ease-in-out;
        }
        .user-menu {
            transition: all 0.2s ease;
        }
        .user-menu:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Admin Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo and Brand -->
                <!-- <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-gray-800 text-xl font-bold flex items-center">
                            <i class="fas fa-shield-alt mr-2 text-gray-600"></i>
                            Admin Panel
                        </span>
                    </div>
                </div> -->

                <!-- Desktop Navigation -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/admin/dashboard" class="nav-link text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-tachometer-alt mr-2 text-gray-500"></i>Dashboard
                        </a>
                        <a href="/admin/users" class="nav-link text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-users mr-2 text-gray-500"></i>Users
                        </a>
                        <a href="/admin/service" class="nav-link text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-service mr-2 text-gray-500"></i>Service
                        </a>
                        <a href="/admin/accounts" class="nav-link text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-service mr-2 text-gray-500"></i>accounts
                        </a>
                          <a href="/admin/user_review" class="nav-link text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                            <i class="fas fa-service mr-2 text-gray-500"></i>Review
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <div class="relative">
                            <div class="flex items-center space-x-4">
                                <span class="text-gray-600 flex items-center">
                                    <i class="fas fa-user-circle mr-2 text-gray-500"></i>
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </span>
                                <a href="/logout" class="user-menu bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium transition-all duration-200">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="mobile-menu-button bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-white focus:ring-gray-500 transition-all duration-200">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="mobile-menu hidden md:hidden bg-white rounded-b-lg shadow-xl">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="/admin/dashboard" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium transition-all duration-200">
                        <i class="fas fa-tachometer-alt mr-2 text-gray-500"></i>Dashboard
                    </a>
                    <a href="/admin/users" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium transition-all duration-200">
                        <i class="fas fa-users mr-2 text-gray-500"></i>Users
                    </a>
                    <a href="/admin/settings" class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium transition-all duration-200">
                        <i class="fas fa-cog mr-2 text-gray-500"></i>Settings
                    </a>
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex items-center px-3">
                            <span class="text-gray-600 flex items-center">
                                <i class="fas fa-user-circle mr-2 text-gray-500"></i>
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </span>
                            <a href="/logout" class="ml-auto bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-base font-medium transition-all duration-200">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle with smooth animation
        document.querySelector('.mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.querySelector('.mobile-menu');
            mobileMenu.classList.toggle('hidden');
            if (!mobileMenu.classList.contains('hidden')) {
                mobileMenu.style.opacity = '0';
                setTimeout(() => {
                    mobileMenu.style.opacity = '1';
                }, 10);
            }
        });
    </script>
</body>
</html> 