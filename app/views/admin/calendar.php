<?php

use root_dev\Config\Database;

require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../app/models/Review.php';

// Create database connection
$db = \Database::connect();

$reservationsForCalendar = $db->query("SELECT o.id, o.reservation_date, u.username, s.package_name FROM orders o JOIN users u ON o.user_id = u.id JOIN services s ON o.service_id = s.id")->fetchAll(PDO::FETCH_ASSOC);


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
        /* Custom FullCalendar header and button styles */
        #calendar .fc-toolbar {
            background: #facc15;
            border-radius: 1rem 1rem 0 0;
            padding: 1rem 1.5rem 0.5rem 1.5rem;
        }
        #calendar .fc-toolbar-title {
            color: #b45309;
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
        }
        #calendar .fc-button {
            background: #fde68a;
            color: #b45309;
            border: none;
            border-radius: 0.5rem;
            margin: 0 0.25rem;
            font-weight: 600;
            transition: background 0.2s;
        }
        #calendar .fc-button:hover, #calendar .fc-button.fc-button-active {
            background: #facc15;
            color: #000;
        }
        #calendar .fc-daygrid-day.fc-day-today {
            background: #fef9c3;
            border-radius: 0.5rem;
        }
        #calendar .fc-event {
            background: #facc15 !important;
            color: #000 !important;
            border: none !important;
            border-radius: 0.5rem !important;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(251,191,36,0.08);
            transition: transform 0.2s;
        }
        #calendar .fc-event:hover {
            transform: scale(1.04);
            box-shadow: 0 4px 16px rgba(251,191,36,0.18);
        }
        /* Tooltip styling */
        .fc-tooltip {
            position: absolute;
            z-index: 1000;
            background: #fffbe6;
            border: 1px solid #facc15;
            padding: 10px 16px;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(251,191,36,0.18);
            color: #b45309;
            font-size: 1rem;
            font-family: 'Montserrat', Arial, sans-serif;
            pointer-events: none;
            min-width: 220px;
            max-width: 320px;
            display: none;
        }
        @media (max-width: 640px) {
            #calendar { font-size: 0.92rem; }
            .fc-tooltip { font-size: 0.95rem; min-width: 150px; }
        }
    </style>
</head>
<body class="bg-white text-black">
    <section class="yellow-bg text-black pt-20 pb-12 mb-8 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font text-black" data-aos="fade-up">
                Intelligent Calendar
            </h2>
            <p class="text-lg opacity-90 mb-2" data-aos="fade-up" data-aos-delay="100">
                View and manage all reservations/orders placed by users in a smart calendar interface.
            </p>
        </div>
    </section>

    <div class="max-w-6xl mx-auto px-4">
      <div class="bg-white border-2 border-yellow-400 rounded-3xl shadow-2xl p-8 mb-10 hover-scale fade-in transition-all duration-300" data-aos="fade-up" data-aos-delay="550">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-3xl font-bold text-yellow-600 catering-font">Intelligent Calendar</h2>
          <span class="inline-block px-4 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold shadow">Live</span>
        </div>
        <div id="calendar" class="rounded-xl overflow-hidden border border-yellow-200 shadow-inner"></div>
      </div>

    </div>
</body>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            once: true
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

