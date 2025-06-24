<?php
require_once __DIR__ . '/layouts/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Reviews</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
      body { font-family: 'Montserrat', Arial, sans-serif; }
      .catering-font { font-family: 'Playfair Display', serif; }
      .yellow-bg { background: #facc15; }
      .hover-scale { transition: all 0.3s ease; }
      .hover-scale:hover { transform: scale(1.02); box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.1); }
      .star-rating input[type="radio"] { display: none; }
      .star-rating label { color: #ccc; cursor: pointer; font-size: 2em; }
      .star-rating input[type="radio"]:checked ~ label { color: #f5b301; }
      .star-rating label:hover,
      .star-rating label:hover ~ label { color: #f5b301; }
    </style>
</head>
<body class="bg-white text-black">
    <section class="yellow-bg text-black pt-20 pb-12 mb-8 shadow-lg">
        <div class="max-w-2xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font">Reviews</h2>
            <p class="text-lg opacity-90">Share your experience and rate our service!</p>
        </div>
    </section>
    <main class="max-w-2xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-2xl shadow-lg p-8 hover-scale mb-8">
            <h3 class="text-2xl font-bold catering-font mb-4 text-yellow-600 text-center">Leave a Review</h3>
            <form action="/review/submit" method="POST" class="space-y-4">
                <div>
                    <label for="review_text" class="block font-semibold mb-1">Your Review:</label>
                    <textarea name="review_text" id="review_text" rows="4" class="w-full border rounded p-2" required></textarea>
                </div>
                <div>
                    <label class="block font-semibold mb-1">Your Rating:</label>
                    <span class="star-rating flex flex-row-reverse justify-center">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" required>
                            <label for="star<?= $i ?>">&#9733;</label>
                        <?php endfor; ?>
                    </span>
                </div>
                <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-white font-bold py-2 px-4 rounded">Submit Review</button>
            </form>
        </div>

        <?php if (isset($reviews) && is_array($reviews)): ?>
            <div class="bg-white rounded-2xl shadow-lg p-8 hover-scale">
                <h3 class="text-xl font-bold catering-font mb-4 text-yellow-600">Reviews</h3>
                <?php if (count($reviews) === 0): ?>
                    <p class="text-gray-500 text-center">No reviews yet.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="border-b border-gray-100 pb-4 mb-4">
                            <div class="flex items-center mb-1">
                                <span class="font-semibold mr-2"><?= htmlspecialchars($review['username']) ?></span>
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
                            <div class="text-gray-700"><?= nl2br(htmlspecialchars($review['review_text'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
