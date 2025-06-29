<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo htmlspecialchars($_SESSION['username']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
      body { font-family: 'Montserrat', Arial, sans-serif; }
      .catering-font { font-family: 'Playfair Display', serif; }
      .hover-scale {
        transition: all 0.3s ease;
      }
      .hover-scale:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.1);
      }
      .card-hover {
        transition: all 0.3s ease;
      }
      .card-hover:hover {
        transform: translateY(-5px);
      }
      .fade-in {
        animation: fadeIn 0.5s ease-in;
      }
      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
      }
      .glass-effect {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
      }
    </style>
</head>
<body class="bg-white text-black">
    <?php require_once __DIR__ . '/layouts/header.php'; ?>

    <!-- Yellow Header -->
    <section class="bg-yellow-400 text-black pt-20 pb-12 mb-8 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font" data-aos="fade-up">My Profile</h2>
            <p class="text-lg opacity-90" data-aos="fade-up" data-aos-delay="100">Manage your account settings and preferences</p>
        </div>
    </section>

    <main class="max-w-6xl mx-auto px-4 mb-12">
        <!-- Profile Header Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8 card-hover" data-aos="fade-up">
            <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
                <div class="w-24 h-24 rounded-full bg-yellow-100 flex items-center justify-center border-4 border-yellow-200">
                    <span class="text-yellow-600 text-4xl font-bold">
                        <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                    </span>
                </div>
                <div class="text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2 catering-font"><?php echo htmlspecialchars($_SESSION['username']); ?></h1>
                    <p class="text-gray-600 mb-2">
                        <i class="fas fa-user-tag mr-2 text-yellow-500"></i>
                        Role: <?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?>
                    </p>
                    <p class="text-gray-600">
                        <i class="fas fa-calendar-alt mr-2 text-yellow-500"></i>
                        Member since: <?php echo date('F Y'); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 fade-in" data-aos="fade-up">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 fade-in" data-aos="fade-up">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Profile Management Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Update Profile Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="100">
                <div class="bg-yellow-400 px-6 py-4">
                    <h2 class="text-xl font-semibold text-black flex items-center">
                        <i class="fas fa-user-edit mr-3"></i>
                        Update Profile
                    </h2>
                </div>
                <div class="p-6">
                    <form action="/profile/update" method="POST" class="space-y-4">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user mr-2 text-yellow-500"></i>
                                Username
                            </label>
                            <input type="text" name="username" id="username" 
                                   value="<?php echo htmlspecialchars($_SESSION['username']); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200">
                        </div>
                        <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-3 px-6 rounded-lg transition duration-300 hover-scale">
                            <i class="fas fa-save mr-2"></i>
                            Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="200">
                <div class="bg-yellow-400 px-6 py-4">
                    <h2 class="text-xl font-semibold text-black flex items-center">
                        <i class="fas fa-lock mr-3"></i>
                        Change Password
                    </h2>
                </div>
                <div class="p-6">
                    <form action="/profile/password" method="POST" class="space-y-4">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-key mr-2 text-yellow-500"></i>
                                Current Password
                            </label>
                            <input type="password" name="current_password" id="current_password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200">
                        </div>
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-lock mr-2 text-yellow-500"></i>
                                New Password
                            </label>
                            <input type="password" name="new_password" id="new_password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200">
                        </div>
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-check-circle mr-2 text-yellow-500"></i>
                                Confirm New Password
                            </label>
                            <input type="password" name="confirm_password" id="confirm_password" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200">
                        </div>
                        <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-3 px-6 rounded-lg transition duration-300 hover-scale">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="mt-12" data-aos="fade-up" data-aos-delay="300">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden card-hover">
                <div class="bg-yellow-400 px-6 py-4">
                    <h2 class="text-xl font-semibold text-black flex items-center">
                        <i class="fas fa-bolt mr-3"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="/dashboard" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-yellow-50 transition duration-200 group">
                            <i class="fas fa-tachometer-alt text-2xl text-yellow-500 mr-4 group-hover:scale-110 transition duration-200"></i>
                            <div>
                                <h3 class="font-semibold text-gray-800">Dashboard</h3>
                                <p class="text-sm text-gray-600">View your reservations</p>
                            </div>
                        </a>
                        <a href="/services" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-yellow-50 transition duration-200 group">
                            <i class="fas fa-utensils text-2xl text-yellow-500 mr-4 group-hover:scale-110 transition duration-200"></i>
                            <div>
                                <h3 class="font-semibold text-gray-800">Services</h3>
                                <p class="text-sm text-gray-600">Browse catering services</p>
                            </div>
                        </a>
                        <a href="/review" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-yellow-50 transition duration-200 group">
                            <i class="fas fa-star text-2xl text-yellow-500 mr-4 group-hover:scale-110 transition duration-200"></i>
                            <div>
                                <h3 class="font-semibold text-gray-800">Reviews</h3>
                                <p class="text-sm text-gray-600">Share your experience</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    // Initialize AOS animations
    AOS.init({
        duration: 800,
        once: true,
        offset: 50
    });
    
    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    // Add fade-in animation to success/error messages
    document.addEventListener('DOMContentLoaded', function() {
        const messages = document.querySelectorAll('.fade-in');
        messages.forEach(message => {
            message.style.opacity = '0';
            message.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                message.style.transition = 'all 0.5s ease';
                message.style.opacity = '1';
                message.style.transform = 'translateY(0)';
            }, 100);
        });
    });
    </script>
</body>
</html> 