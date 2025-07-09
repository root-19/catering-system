<?php
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../../config/database.php';

// Connect to database
$db = Database::connect();

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = trim($_POST['description'] ?? '');
    $imagePaths = [null, null, null, null, null];
    $uploadDir = __DIR__ . '/../../../uplaods/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    // Validate description
    if (empty($description)) {
        $error = 'Description is required.';
    } else {
        // Handle up to 5 images
        for ($i = 0; $i < 5; $i++) {
            if (isset($_FILES['images']['name'][$i]) && $_FILES['images']['name'][$i] !== '') {
                $tmpName = $_FILES['images']['tmp_name'][$i];
                $fileName = basename($_FILES['images']['name'][$i]);
                $fileType = $_FILES['images']['type'][$i];
                $fileSize = $_FILES['images']['size'][$i];
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $newName = 'post_' . time() . '_' . $i . '_' . uniqid() . '.' . $ext;
                $targetPath = $uploadDir . $newName;

                if (!in_array($fileType, $allowedTypes)) {
                    $error = 'Only JPG, PNG, GIF, and WEBP images are allowed.';
                    break;
                }
                if ($fileSize > $maxSize) {
                    $error = 'Each image must be less than 2MB.';
                    break;
                }
                if (!move_uploaded_file($tmpName, $targetPath)) {
                    $error = 'Failed to upload image: ' . htmlspecialchars($fileName);
                    break;
                }
                $imagePaths[$i] = $newName;
            }
        }
    }

    // If no errors, insert into DB
    if (!$error) {
        $stmt = $db->prepare('INSERT INTO posting (description, image1, image2, image3, image4, image5) VALUES (?, ?, ?, ?, ?, ?)');
        $result = $stmt->execute([
            $description,
            $imagePaths[0],
            $imagePaths[1],
            $imagePaths[2],
            $imagePaths[3],
            $imagePaths[4]
        ]);
        if ($result) {
            $success = 'Post created successfully!';
        } else {
            $error = 'Failed to create post.';
        }
    }
}

// Fetch all posts
$posts = $db->query('SELECT * FROM posting ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Achievements/Events</title>
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
    </style>
</head>
<body class="bg-white text-black">
    <section class="yellow-bg text-black pt-20 pb-12 mb-8 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font" data-aos="fade-up">Post Achievements / Successful Events</h2>
            <p class="text-lg opacity-90" data-aos="fade-up" data-aos-delay="100">Share your latest achievements and events with up to 5 images</p>
        </div>
    </section>
    <main class="max-w-3xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8" data-aos="fade-up">
            <form method="POST" enctype="multipart/form-data" class="p-8">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" class="w-full border border-gray-300 rounded-lg px-3 py-2" required rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload up to 5 images</label>
                    <input type="file" name="images[]" accept="image/*" multiple class="w-full border border-gray-300 rounded-lg px-3 py-2" max="5">
                    <p class="text-xs text-gray-500 mt-1">Max 5 images. JPG, PNG, GIF, WEBP. Max size: 2MB each.</p>
                </div>
                <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded-lg shadow hover-scale">Post</button>
            </form>
        </div>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden" data-aos="fade-up">
            <h2 class="text-xl font-bold mb-4 px-8 pt-8">Previous Posts</h2>
            <div class="space-y-8 px-8 pb-8">
                <?php foreach ($posts as $post): ?>
                    <div class="border-b pb-6 fade-in">
                        <div class="mb-2 text-gray-700"><?php echo nl2br(htmlspecialchars($post['description'])); ?></div>
                        <div class="flex flex-wrap gap-4 mt-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if (!empty($post['image'.$i])): ?>
                                    <img src="/uplaods/<?php echo htmlspecialchars($post['image'.$i]); ?>" alt="Post Image" class="w-32 h-32 object-cover rounded shadow">
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div class="text-xs text-gray-400 mt-2">Posted on <?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <script>
        AOS.init({ duration: 800, once: true, offset: 50 });
        <?php if ($success): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?php echo addslashes($success); ?>',
            confirmButtonColor: '#facc15'
        });
        <?php endif; ?>
        <?php if ($error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo addslashes($error); ?>',
            confirmButtonColor: '#f87171'
        });
        <?php endif; ?>
    </script>
</body>
</html>
