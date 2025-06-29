<!-- app/views/dashboard.php -->

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

// Remove fetching credits from database
require_once __DIR__ . '/../../config/database.php';
// use root_dev\Config\Database;
$db = \Database::connect();

// Fetch user's orders
$user_id = $_SESSION['user_id'];
$orders_query = "SELECT o.*, s.item as service_name, s.image as service_image 
                 FROM orders o 
                 LEFT JOIN services s ON o.service_id = s.id 
                 WHERE o.user_id = ? 
                 ORDER BY o.created_at DESC";
$stmt = $db->prepare($orders_query);
$stmt->execute([$user_id]);
$user_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($_SESSION['username']); ?></title>
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
        box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.1); /* yellow shadow */
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
        <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font" data-aos="fade-up">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p class="text-lg opacity-90" data-aos="fade-up" data-aos-delay="100">Your Reservation Dashboard</p>
      </div>
    </section>

    <!-- User Orders Section -->
    <section class="max-w-6xl mx-auto px-4 mb-12">
      <div class="text-center mb-8">
        <h3 class="text-3xl font-bold text-gray-800 mb-2 catering-font" data-aos="fade-up">My Reservations</h3>
        <p class="text-gray-600" data-aos="fade-up" data-aos-delay="100">View all your catering reservations</p>
      </div>

      <?php if (empty($user_orders)): ?>
        <!-- No Orders Message -->
        <div class="text-center py-12" data-aos="fade-up">
          <div class="bg-gray-50 rounded-lg p-8 max-w-md mx-auto">
            <i class="fas fa-utensils text-4xl text-gray-400 mb-4"></i>
            <h4 class="text-xl font-semibold text-gray-700 mb-2">No Reservations Yet</h4>
            <p class="text-gray-500 mb-4">You haven't made any catering reservations yet.</p>
            <a href="/services" class="inline-block bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-2 px-6 rounded-lg transition duration-300">
              Browse Services
            </a>
          </div>
        </div>
      <?php else: ?>
        <!-- Orders Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($user_orders as $order): ?>
            <div class="bg-white rounded-lg shadow-lg overflow-hidden card-hover" data-aos="fade-up">
              <!-- Service Image -->
              <?php if (!empty($order['service_image'])): ?>
                <div class="h-48 bg-gray-200 overflow-hidden">
                  <img src="/uplaods/<?php echo htmlspecialchars($order['service_image']); ?>" 
                       alt="<?php echo htmlspecialchars($order['service_name']); ?>" 
                       class="w-full h-full object-cover">
                </div>
              <?php else: ?>
                <div class="h-48 bg-gray-200 flex items-center justify-center">
                  <i class="fas fa-utensils text-4xl text-gray-400"></i>
                </div>
              <?php endif; ?>
              
              <!-- Order Details -->
              <div class="p-6">
                <h4 class="text-xl font-semibold text-gray-800 mb-2">
                  <?php echo htmlspecialchars($order['service_name'] ?? 'Service'); ?>
                </h4>
                <p class="text-yellow-600 font-semibold mb-2">
                  Package: <?php echo htmlspecialchars($order['package_name']); ?>
                </p>
                
                <div class="space-y-2 text-sm text-gray-600 mb-4">
                  <div class="flex items-center">
                    <i class="fas fa-calendar-alt w-4 mr-2 text-yellow-500"></i>
                    <span>Date: <?php echo date('F j, Y', strtotime($order['reservation_date'])); ?></span>
                  </div>
                  <div class="flex items-center">
                    <i class="fas fa-dollar-sign w-4 mr-2 text-yellow-500"></i>
                    <span>Amount: ₱<?php echo number_format($order['amount'], 2); ?></span>
                  </div>
                  <div class="flex items-center">
                    <i class="fas fa-clock w-4 mr-2 text-yellow-500"></i>
                    <span>Booked: <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></span>
                  </div>
                </div>
                
                <?php if (!empty($order['notes'])): ?>
                  <div class="bg-gray-50 rounded p-3 mb-4">
                    <p class="text-sm text-gray-700">
                      <strong>Notes:</strong> <?php echo htmlspecialchars($order['notes']); ?>
                    </p>
                  </div>
                <?php endif; ?>
                
                <div class="flex justify-between items-center">
                  <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-semibold">
                    Confirmed
                  </span>
                  <button class="text-yellow-600 hover:text-yellow-700 font-semibold text-sm">
                    View Details
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>

    <script>
      AOS.init({
        duration: 800,
        once: true,
        offset: 50
      });
      
      // Prevent form resubmission on page refresh
      if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
      }
      
      // Copy API Key function with animation
      function copyApiKey() {
        const apiKeyInput = document.querySelector('input[readonly]');
        apiKeyInput.select();
        document.execCommand('copy');
        
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
        button.classList.add('bg-green-600');
        
        setTimeout(() => {
          button.innerHTML = originalText;
          button.classList.remove('bg-green-600');
        }, 2000);
      }

      // Add fade-in animation to cards
      document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card-hover');
        cards.forEach((card, index) => {
          card.style.opacity = '0';
          card.style.animation = `fadeIn 0.5s ease-in ${index * 0.1}s forwards`;
        });
      });
    </script>
</body>
</html>
