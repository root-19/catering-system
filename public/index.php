<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Catering</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts: Playfair Display for elegant catering look -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; }
        .catering-font { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-white min-h-screen flex flex-col justify-between">
    <!-- Header -->
    <header class="bg-yellow-400 text-black shadow-md">
        <div class="container mx-auto flex justify-between items-center py-4 px-6">
            <div class="flex items-center gap-3">
                <img src="https://img.icons8.com/ios-filled/50/000000/restaurant-table.png" alt="Catering Logo" class="h-10 w-10">
                <span class="text-2xl catering-font font-bold tracking-wide">CaterServe</span>
            </div>
            <a href="/public/login.php" class="bg-black text-yellow-400 px-5 py-2 rounded-lg font-semibold hover:bg-yellow-500 hover:text-black transition">Login</a>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1 flex flex-col items-center justify-center text-center px-4">
        <h1 class="catering-font text-4xl md:text-6xl font-bold text-black mt-12 mb-6">Delightful Catering Reservations</h1>
        <p class="text-lg md:text-2xl text-gray-700 max-w-2xl mb-8">Experience exquisite flavors and seamless service for your special events. From intimate gatherings to grand celebrations, <span class="font-semibold text-yellow-500">CaterServe</span> brings culinary excellence to your table. Reserve your unforgettable catering experience today!</p>
        <a href="/public/register.php" class="inline-block bg-yellow-400 text-black font-bold text-lg px-8 py-3 rounded-full shadow-lg hover:bg-black hover:text-yellow-400 transition mb-12">Start Reservation</a>
        <div class="flex flex-wrap justify-center gap-8 mt-8">
            <div class="bg-white border-2 border-yellow-400 rounded-xl p-6 w-72 shadow-md">
                <h2 class="catering-font text-2xl text-black mb-2">Weddings & Events</h2>
                <p class="text-gray-600">Elegant menus and flawless service for your most memorable occasions.</p>
            </div>
            <div class="bg-white border-2 border-yellow-400 rounded-xl p-6 w-72 shadow-md">
                <h2 class="catering-font text-2xl text-black mb-2">Corporate Catering</h2>
                <p class="text-gray-600">Professional catering solutions for meetings, conferences, and office parties.</p>
            </div>
            <div class="bg-white border-2 border-yellow-400 rounded-xl p-6 w-72 shadow-md">
                <h2 class="catering-font text-2xl text-black mb-2">Private Parties</h2>
                <p class="text-gray-600">Customizable menus for birthdays, anniversaries, and family gatherings.</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-black text-yellow-400 py-6 mt-12">
        <div class="container mx-auto text-center text-sm">
            &copy; 2024 CaterServe. All rights reserved. | Designed for exceptional catering experiences.
        </div>
    </footer>
</body>
</html>
