<?php
require_once __DIR__ . '/../app/models/Review.php';
use App\Models\Review;
$reviewModel = new Review();
$allReviews = $reviewModel->getReviews();
$reviews = array_filter($allReviews, function($review) {
    return $review['status'] !== 'pending';
});

require_once __DIR__ . '/../app/models/Posting.php';
use App\Models\Posting;
$postingModel = new Posting();
$postings = $postingModel->getAllPostings(); // Adjust method name as per your model
?>
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
            <a href="login" class="bg-black text-yellow-400 px-5 py-2 rounded-lg font-semibold hover:bg-yellow-500 hover:text-black transition">Login</a>
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

    <!-- Review Section -->
    <section class="max-w-6xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-2xl shadow-lg p-8 hover-scale mb-8">
            <h3 class="text-xl font-bold catering-font mb-4 text-yellow-600 text-center">What Our Customers Say</h3>
            <?php if (empty($reviews)): ?>
                <p class="text-gray-500 text-center">No reviews yet.</p>
            <?php else: ?>
                <div class="flex flex-wrap justify-center gap-8">
                    <?php foreach ($reviews as $review): ?>
                        <div class="w-72 min-h-[200px] bg-white border-2 border-yellow-400 rounded-xl p-6 shadow-md flex flex-col">
                            <div class="flex items-center mb-2 flex-row gap-2">
                                <span class="flex items-center justify-center h-8 w-8 rounded-full bg-yellow-400 text-black font-bold text-lg shadow-md">
                                    <?= strtoupper(substr($review['username'], 0, 1)) ?>
                                </span>
                                <span class="font-semibold"><?= htmlspecialchars($review['username']) ?></span>
                                <span class="text-xs text-gray-400">(<?= htmlspecialchars($review['created_at']) ?>)</span>
                            </div>
                            <div class="flex items-center mb-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $review['rating']): ?>
                                        <span class="text-yellow-400 text-lg">&#9733;</span>
                                    <?php else: ?>
                                        <span class="text-gray-300 text-lg">&#9733;</span>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <div class="text-gray-700 flex-1 mb-2"><?= nl2br(htmlspecialchars($review['review_text'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Posting Section -->
    <section class="max-w-6xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h3 class="text-2xl font-bold catering-font mb-6 text-yellow-600 text-center">Latest Announcements</h3>
            <?php if (empty($postings)): ?>
                <p class="text-gray-500 text-center">No announcements at this time.</p>
            <?php else: ?>
                <div class="flex flex-wrap justify-center gap-8">
                    <?php foreach ($postings as $idx => $post): 
                        $images = [];
                        for ($i = 1; $i <= 5; $i++) {
                            if (!empty($post['image'.$i])) {
                                $images[] = '/uplaods/' . htmlspecialchars($post['image'.$i]);
                            }
                        }
                    ?>
                        <div class="w-96 min-h-[350px] bg-yellow-50 border-2 border-yellow-400 rounded-xl p-6 shadow-md flex flex-col items-center">
                            <div class="text-gray-700 text-lg flex-1 mb-4 w-full text-center"><?= nl2br(htmlspecialchars($post['description'])) ?></div>
                            <?php if (count($images) > 0): ?>
                                <div class="relative flex items-center justify-center mb-4 w-full h-56">
                                    <button type="button" class="absolute left-2 z-10 bg-yellow-400 hover:bg-yellow-600 text-black rounded-full p-3 shadow top-1/2 -translate-y-1/2 text-xl" onclick="showPrevImage(<?= $idx ?>)">
                                        &#8592;
                                    </button>
                                    <img id="post-image-<?= $idx ?>" src="<?= $images[0] ?>" data-images='<?= json_encode($images) ?>' data-index="0" class="h-56 w-full object-cover rounded shadow mx-auto transition-all duration-300" />
                                    <button type="button" class="absolute right-2 z-10 bg-yellow-400 hover:bg-yellow-600 text-black rounded-full p-3 shadow top-1/2 -translate-y-1/2 text-xl" onclick="showNextImage(<?= $idx ?>)">
                                        &#8594;
                                    </button>
                                </div>
                            <?php endif; ?>
                            <div class="text-xs text-gray-400 text-right mt-auto w-full"><?= htmlspecialchars($post['created_at']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script>
    function showPrevImage(idx) {
        const img = document.getElementById('post-image-' + idx);
        const images = JSON.parse(img.getAttribute('data-images'));
        let current = parseInt(img.getAttribute('data-index'));
        current = (current - 1 + images.length) % images.length;
        img.src = images[current];
        img.setAttribute('data-index', current);
    }
    function showNextImage(idx) {
        const img = document.getElementById('post-image-' + idx);
        const images = JSON.parse(img.getAttribute('data-images'));
        let current = parseInt(img.getAttribute('data-index'));
        current = (current + 1) % images.length;
        img.src = images[current];
        img.setAttribute('data-index', current);
    }
    document.addEventListener('DOMContentLoaded', function() {
        <?php foreach ($postings as $idx => $post): 
            $images = [];
            for ($i = 1; $i <= 5; $i++) {
                if (!empty($post['image'.$i])) {
                    $images[] = '/uplaods/' . htmlspecialchars($post['image'.$i]);
                }
            }
            if (count($images) > 1): ?>
            setInterval(function() { showNextImage(<?= $idx ?>); }, 3000);
        <?php endif; endforeach; ?>
    });
    </script>

    <!-- Footer -->
    <footer class="bg-black text-yellow-400 py-6 mt-12">
        <div class="container mx-auto text-center text-sm">
            &copy; 2024 CaterServe. All rights reserved. | Designed for exceptional catering experiences.
        </div>
    </footer>
</body>
</html>
