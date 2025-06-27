<?php
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../config/database.php';


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
            <h3 class="text-3xl font-bold catering-font mb-4 text-yellow-600 text-center"><?php echo htmlspecialchars($service['package_name']); ?></h3>
            <?php if (!empty($service['image'])): ?>
                <img src="/uplaods/<?php echo htmlspecialchars($service['image']); ?>" alt="Service Image" class="h-64 w-full object-cover" />
            <?php endif; ?>
            <div class="w-full mb-3"><span class="font-semibold">category:</span> <?php echo htmlspecialchars($service['category']); ?></div>
            <div class="w-full mb-3"><span class="font-semibold">item:</span> <?php echo htmlspecialchars($service['item']); ?></div>
            <div class="w-full mb-3 text-lg"><span class="font-semibold">Price:</span> ₱<?php echo htmlspecialchars($service['price'] ?? $service['packs']); ?></div>
            <div class="w-full mb-3"><span class="font-semibold">Packs:</span> <?php echo htmlspecialchars($service['packs']); ?></div>
            <div class="w-full mb-3"><span class="font-semibold">Location:</span> <?php echo htmlspecialchars($service['location']); ?></div>
            <div class="w-full mb-3"><span class="font-semibold">Description:</span> <?php echo htmlspecialchars($service['description'] ?? ''); ?></div>
            <div class="w-full mb-3 text-xs text-gray-400 text-right">Added: <?php echo htmlspecialchars($service['created_at']); ?></div>
        </div>
        <!-- Reservation Form (Right) -->
        <div class="reservation-card">
            <h4 class="text-2xl font-bold mb-4">Reserve this Package</h4>
            <?php if ($reservationSuccess): ?>
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">Reservation successful!</div>
            <?php elseif ($reservationError): ?>
                <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?php echo htmlspecialchars($reservationError); ?></div>
            <?php endif; ?>
            <form method="post" action="create_invoice" class="space-y-4">
                <input type="hidden" name="service_id" value="<?php echo htmlspecialchars($serviceId); ?>">
                <input type="hidden" name="package_name" value="<?php echo htmlspecialchars($service['package_name']); ?>">
                <input type="hidden" name="amount" value="<?php echo htmlspecialchars($service['price']); ?>">
                <input type="hidden" name="user_email" value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                <div>
                    <label for="reservation_date" class="block font-semibold mb-1">Reservation Date</label>
                    <input type="text" id="reservation_date" name="reservation_date" class="w-full border border-gray-300 rounded px-3 py-2" required placeholder="Select a date">
                </div>
                <div>
                    <label for="notes" class="block font-semibold mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Any special instructions?"></textarea>
                </div>
                <button type="submit" name="reserve" class="reserve-btn w-full">Reserve & Pay with GCash</button>
            </form>
        </div>
    </main>
    <script>
        // Pass reserved dates from PHP to JS
        const reservedDates = <?php echo json_encode($reservedDates); ?>;
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
    </script>
</body>
</html> 