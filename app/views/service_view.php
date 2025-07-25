<?php
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../models/ServiceModel.php';
use App\Models\ServiceModel;


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="max-w-xl mx-auto mt-20 text-center text-red-500">Invalid service ID.</div>';
    exit;
}

$serviceId = (int)$_GET['id'];
$pdo = Database::connect();
$stmt = $pdo->prepare('SELECT * FROM services WHERE id = ?');
$stmt->execute([$serviceId]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    echo '<div class="max-w-xl mx-auto mt-20 text-center text-red-500">Service not found.</div>';
    exit;
}

// Reservation form handling
$reservationSuccess = false;
$reservationError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
    if (!isset($_SESSION['user_id'])) {
        $reservationError = 'You must be logged in to reserve.';
    } else {
        $userId = $_SESSION['user_id'];
        $reservationDate = $_POST['reservation_date'] ?? '';
        $notes = $_POST['notes'] ?? '';
        if (!$reservationDate) {
            $reservationError = 'Please select a reservation date.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, service_id, package_name, reservation_date, notes) VALUES (?, ?, ?, ?, ?)');
            $reservationSuccess = $stmt->execute([
                $userId,
                $serviceId,
                $service['package_name'],
                $reservationDate,
                $notes
            ]);
            if (!$reservationSuccess) {
                $reservationError = 'Failed to reserve. Please try again.';
            }
        }
    }
}

// Fetch reserved dates for this service
$reservedDates = [];
$reservedStmt = $pdo->prepare('SELECT reservation_date FROM orders WHERE service_id = ?');
$reservedStmt->execute([$serviceId]);
while ($row = $reservedStmt->fetch(PDO::FETCH_ASSOC)) {
    $reservedDates[] = $row['reservation_date'];
}

// Get menu for dropdowns
$menu = ServiceModel::getMenu();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Details - <?php echo htmlspecialchars($service['item']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
      body { font-family: 'Montserrat', Arial, sans-serif; }
      .catering-font { font-family: 'Playfair Display', serif; }
      .yellow-bg { background: #facc15; }
      .flatpickr-day.reserved-date, .flatpickr-day.reserved-date:hover {
        background: #facc15 !important;
        color: #fff !important;
        cursor: not-allowed;
      }
      .main-flex {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 70vh;
      }
      .service-card, .reservation-card {
        background: #fff;
        border-radius: 1.5rem;
        box-shadow: 0 6px 32px 0 rgba(0,0,0,0.10), 0 1.5px 6px 0 rgba(0,0,0,0.08);
        padding: 2.5rem 2rem;
        margin: 1rem;
        width: 100%;
        max-width: 480px;
      }
      .service-card img {
        border-radius: 1rem;
        box-shadow: 0 2px 12px 0 rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
        background: #f9fafb;
      }
      .reservation-card {
        background: #fffbea;
        border: 1.5px solid #facc15;
      }
      .reservation-card h4 {
        color: #b45309;
      }
      .reserve-btn {
        background: linear-gradient(90deg, #facc15 60%, #fbbf24 100%);
        color: #fff;
        font-weight: bold;
        border: none;
        border-radius: 0.5rem;
        padding: 0.75rem 0;
        font-size: 1.1rem;
        transition: background 0.2s;
        box-shadow: 0 2px 8px 0 rgba(250,204,21,0.15);
      }
      .reserve-btn:hover {
        background: linear-gradient(90deg, #fbbf24 60%, #facc15 100%);
      }
      @media (min-width: 900px) {
        .main-flex {
          flex-direction: row;
          align-items: flex-start;
        }
        .service-card, .reservation-card {
          margin: 1.5rem;
        }
      }
    </style>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body class="bg-white text-black">
    <section class="yellow-bg text-black pt-16 pb-8 mb-8 shadow-lg">
        <div class="max-w-2xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-2 catering-font">Service Details</h2>
            <p class="text-lg opacity-90">All information about this package</p>
        </div>
    </section>
    <main class="main-flex">
        <!-- Service Details (Left) -->
        <div class="service-card">
            <h3 class="text-3xl font-bold catering-font mb-4 text-yellow-600 text-center" id="displayPackageName"><?php echo htmlspecialchars($service['package_name']); ?></h3>
            <?php if (!empty($service['image'])): ?>
                <img src="/uplaods/<?php echo htmlspecialchars($service['image']); ?>" alt="Service Image" class="h-64 w-full object-cover" />
            <?php endif; ?>
            <div class="w-full mb-3 flex items-center">
                <span class="font-semibold">category:</span> <span id="displayCategory" class="ml-1"><?php echo htmlspecialchars($service['category']); ?></span>
                <button type="button" id="editCategoryItemBtn" class="ml-2 px-2 py-1 text-xs bg-yellow-400 hover:bg-yellow-500 rounded">Edit</button>
            </div>
            <div class="w-full mb-3 flex items-center">
                <span class="font-semibold">item:</span> <span id="displayItem" class="ml-1"><?php echo htmlspecialchars($service['item']); ?></span>
            </div>
            <div class="w-full mb-3 text-lg"><span class="font-semibold">Price:</span> ₱<?php echo htmlspecialchars($service['price'] ?? $service['packs']); ?></div>
            <?php
                            // Calculate downpayment as 30% of price
                            $price = isset($service['price']) ? floatval($service['price']) : 0;
                            $downpayment = $price * 0.3;
                        ?>
                        <div class="mb-2 text-gray-700 text-base"><span class="font-semibold">Downpayment:</span> ₱<?php echo number_format($downpayment, 2); ?></div>
            <div class="w-full mb-3"><span class="font-semibold">Packs:</span> <?php echo htmlspecialchars($service['packs']); ?></div>
            <div class="w-full mb-3"><span class="font-semibold">Location:</span> <?php echo htmlspecialchars($service['location']); ?></div>
            <div class="w-full mb-3"><span class="font-semibold">Description:</span> <?php echo htmlspecialchars($service['description'] ?? ''); ?></div>
            <!-- <div class="w-full mb-3 text-xs text-gray-400 text-right">Added: <?php echo htmlspecialchars($service['created_at']); ?></div> -->
        </div>
        <!-- Reservation Form (Right) -->
        <div class="reservation-card">
            <h4 class="text-2xl font-bold mb-4">Reserve this Package</h4>
            <?php if ($reservationSuccess): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">Reservation successful!</div>
            <?php elseif ($reservationError): ?>
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?php echo htmlspecialchars($reservationError); ?></div>
            <?php endif; ?>
            <form method="post" action="create_invoice" class="space-y-4" id="reservationForm">
                <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($serviceId); ?>">
                <input type="hidden" name="package_name" id="formPackageName" value="<?php echo htmlspecialchars($service['package_name']); ?>">
                <input type="hidden" name="amount" id="amount" value="<?php echo htmlspecialchars(number_format($downpayment, 2, '.', '')); ?>">
                <input type="hidden" name="user_email" value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                <input type="hidden" name="category" id="formCategory" value="<?php echo htmlspecialchars($service['category']); ?>">
                <input type="hidden" name="item" id="formItem" value="<?php echo htmlspecialchars($service['item']); ?>">
                <div>
                    <label for="reservation_date" class="block font-semibold mb-1">Reservation Date</label>
                    <input type="text" id="reservation_date" name="reservation_date" class="w-full border border-gray-300 rounded px-3 py-2" required placeholder="Select a date">
                </div>
                <div>
                    <label for="notes" class="block font-semibold mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Any special instructions?"></textarea>
                </div>
                <div>
                    <label for="payment_method" class="block font-semibold mb-1">Payment Method</label>
                    <select id="payment_method" name="payment_method" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="downpayment">Pay downpayment now (GCash), remaining in cash</option>
                        <option value="full_gcash">Pay full amount now (GCash)</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1" id="paymentInfo">You will pay only the downpayment (₱<?php echo number_format($downpayment, 2); ?>) now via GCash. The remaining balance will be paid in cash.</p>
                </div>
                <button type="submit" name="reserve" class="reserve-btn w-full" id="reserveBtn">Reserve & Pay with GCash</button>
            </form>
        </div>
    </main>
    <!-- Edit Category/Item Modal -->
    <div id="editCategoryItemModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
      <div class="bg-white rounded-lg p-0 w-full max-w-2xl relative flex flex-col md:flex-row shadow-lg">
        <div class="md:w-1/2 p-6 bg-gray-50 border-r border-gray-200 flex flex-col justify-center">
          <h3 class="text-lg font-bold mb-3 text-yellow-700">Available Categories</h3>
          <ul class="mb-0 text-sm text-gray-700 space-y-2 max-h-96 overflow-y-auto pr-2">
            <?php foreach ($menu as $cat => $items): ?>
              <li><span class="font-semibold text-yellow-700"><?php echo htmlspecialchars($cat); ?>:</span> <span class="text-gray-600"><?php echo implode(', ', $items); ?></span></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <div class="md:w-1/2 p-8 flex flex-col justify-center relative">
          <button id="closeEditCategoryItemModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
          <h2 class="text-2xl font-bold mb-6 text-yellow-700">Edit Package, Category & Item</h2>
          <div class="mb-5">
            <label class="block text-sm font-semibold mb-1 text-gray-700">Package Name</label>
            <input type="text" id="modalPackageName" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-yellow-400" value="<?php echo htmlspecialchars($service['package_name']); ?>">
          </div>
          <div class="mb-5">
            <label class="block text-sm font-semibold mb-1 text-gray-700">Category</label>
            <input type="text" id="modalCategory" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-yellow-400" value="<?php echo htmlspecialchars($service['category']); ?>">
          </div>
          <div class="mb-6">
            <label class="block text-sm font-semibold mb-1 text-gray-700">Item</label>
            <input type="text" id="modalItem" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-yellow-400" value="<?php echo htmlspecialchars($service['item']); ?>">
          </div>
          <button id="saveCategoryItemBtn" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-8 rounded-lg shadow self-end">Save</button>
        </div>
      </div>
    </div>
    <script>
        // Pass reserved dates from PHP to JS
        const reservedDates = <?php echo json_encode($reservedDates); ?>;
        // Pass menu from PHP to JS
        const menu = <?php echo json_encode($menu); ?>;
        document.addEventListener('DOMContentLoaded', function() {
            // Replace native date input with flatpickr
            flatpickr('#reservation_date', {
                dateFormat: 'Y-m-d',
                disable: reservedDates,
                onDayCreate: function(dObj, dStr, fp, dayElem) {
                    // Color reserved dates yellow
                    if (reservedDates.includes(dayElem.dateObj.toISOString().slice(0, 10))) {
                        dayElem.classList.add('reserved-date');
                    }
                },
                minDate: 'today',
            });

            // Category/Item dropdown logic
            const categorySelect = document.getElementById('category');
            const itemSelect = document.getElementById('item');
            categorySelect.addEventListener('change', function() {
                const selectedCat = this.value;
                itemSelect.innerHTML = '<option value="">Select item</option>';
                if (menu[selectedCat]) {
                    menu[selectedCat].forEach(function(item) {
                        const opt = document.createElement('option');
                        opt.value = item;
                        opt.textContent = item;
                        itemSelect.appendChild(opt);
                    });
                }
            });

            // SweetAlert2 on form submit if reserved date is selected (double check)
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const dateInput = document.getElementById('reservation_date');
                if (reservedDates.includes(dateInput.value)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Already scheduled',
                        text: 'Please choose an available date.',
                        confirmButtonColor: '#facc15'
                    });
                    e.preventDefault();
                }
            });
        });

        // Payment method logic
        const paymentMethod = document.getElementById('payment_method');
        const amountInput = document.getElementById('amount');
        const paymentInfo = document.getElementById('paymentInfo');
        const reserveBtn = document.getElementById('reserveBtn');
        const downpayment = <?php echo json_encode(number_format($downpayment, 2, '.', '')); ?>;
        const fullAmount = <?php echo json_encode(number_format($price, 2, '.', '')); ?>;

        paymentMethod.addEventListener('change', function() {
            if (this.value === 'downpayment') {
                amountInput.value = downpayment;
                paymentInfo.textContent = `You will pay only the downpayment (₱${Number(downpayment).toLocaleString(undefined, {minimumFractionDigits:2})}) now via GCash. The remaining balance will be paid in cash.`;
                reserveBtn.textContent = 'Reserve & Pay Downpayment with GCash';
            } else {
                amountInput.value = fullAmount;
                paymentInfo.textContent = `You will pay the full amount (₱${Number(fullAmount).toLocaleString(undefined, {minimumFractionDigits:2})}) now via GCash.`;
                reserveBtn.textContent = 'Reserve & Pay Full Amount with GCash';
            }
        });

        // Category/Item modal logic
        const editCategoryItemBtn = document.getElementById('editCategoryItemBtn');
        const editCategoryItemModal = document.getElementById('editCategoryItemModal');
        const closeEditCategoryItemModal = document.getElementById('closeEditCategoryItemModal');
        const modalCategory = document.getElementById('modalCategory');
        const modalItem = document.getElementById('modalItem');
        const saveCategoryItemBtn = document.getElementById('saveCategoryItemBtn');
        const displayCategory = document.getElementById('displayCategory');
        const displayItem = document.getElementById('displayItem');
        const displayPackageName = document.getElementById('displayPackageName');
        const formCategory = document.getElementById('formCategory');
        const formItem = document.getElementById('formItem');
        const formPackageName = document.getElementById('formPackageName');
        const modalPackageName = document.getElementById('modalPackageName');

        editCategoryItemBtn.addEventListener('click', function() {
          // Set modal to current values
          modalPackageName.value = formPackageName.value;
          modalCategory.value = formCategory.value;
          modalItem.value = formItem.value;
          editCategoryItemModal.classList.remove('hidden');
        });
        closeEditCategoryItemModal.addEventListener('click', function() {
          editCategoryItemModal.classList.add('hidden');
        });
        editCategoryItemModal.addEventListener('click', function(e) {
          if (e.target === editCategoryItemModal) editCategoryItemModal.classList.add('hidden');
        });
        saveCategoryItemBtn.addEventListener('click', function() {
          if (!modalPackageName.value || !modalCategory.value || !modalItem.value) {
            Swal.fire({ icon: 'warning', title: 'Please fill in all fields.' });
            return;
          }
          displayPackageName.textContent = modalPackageName.value;
          displayCategory.textContent = modalCategory.value;
          displayItem.textContent = modalItem.value;
          formPackageName.value = modalPackageName.value;
          formCategory.value = modalCategory.value;
          formItem.value = modalItem.value;
          editCategoryItemModal.classList.add('hidden');
        });
    </script>
</body>
</html> 