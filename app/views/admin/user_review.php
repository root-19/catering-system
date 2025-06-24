<?php
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../models/Review.php';
$reviewModel = new \App\Models\Review();
$reviews = $reviewModel->getReviews();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reviews - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', Arial, sans-serif; }
        .catering-font { font-family: 'Playfair Display', serif; }
        .yellow-bg { background: #facc15; }
        .hover-scale { transition: all 0.3s ease; }
        .hover-scale:hover { transform: scale(1.02); box-shadow: 0 10px 25px -5px rgba(251, 191, 36, 0.1); }
        .fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-white text-black">
    <section class="yellow-bg text-black pt-20 pb-12 mb-8 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font" data-aos="fade-up">User Reviews</h2>
            <p class="text-lg opacity-90" data-aos="fade-up" data-aos-delay="100">Manage and approve user reviews</p>
        </div>
    </section>
    <main class="max-w-6xl mx-auto px-4 pb-12">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 rounded-xl bg-green-50 p-4 border border-green-200" data-aos="fade-up">
                <div class="flex"><div class="flex-shrink-0"><i class="fas fa-check-circle text-green-400 text-xl"></i></div><div class="ml-3"><p class="text-sm font-medium text-green-800"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p></div></div>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 rounded-xl bg-red-50 p-4 border border-red-200" data-aos="fade-up">
                <div class="flex"><div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-400 text-xl"></i></div><div class="ml-3"><p class="text-sm font-medium text-red-800"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p></div></div>
            </div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden" data-aos="fade-up">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Review</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($review['id']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($review['username']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($review['review_text']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($review['rating']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($review['status']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if ($review['status'] === 'pending'): ?>
                                            <form method="POST" action="/admin/review/approve" onsubmit="return confirm('Approve this review?');">
                                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Approve</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-green-600 font-semibold">Approved</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No reviews found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>
        AOS.init({ duration: 800, once: true, offset: 50 });
    </script>
</body>
</html>
