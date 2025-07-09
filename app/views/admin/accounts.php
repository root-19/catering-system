<?php
require_once __DIR__ . '/layouts/header.php';
require_once __DIR__ . '/../../../config/database.php';

// Connect to database
$db = Database::connect();

// Initialize variables
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        // Check if email already exists in helpers
        $stmt = $db->prepare('SELECT id FROM helpers WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'A helper with this email already exists.';
        } else {
            // Insert new helper with role
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = 'INSERT INTO helpers (username, email, password, role) VALUES (?, ?, ?, \'helper\')';
            $result = $db->prepare($insertQuery)->execute([$username, $email, $hashedPassword]);
            if ($result) {
                $success = 'Helper account created successfully!';
            } else {
                $error = 'Failed to create helper account.';
            }
        }
    }
}

// Fetch all helpers
$helpers = $db->query('SELECT id, username, email, role, created_at FROM helpers ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
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
    </style>
</head>
<body class="bg-white text-black">
    <section class="yellow-bg text-black pt-20 pb-12 mb-8 shadow-lg">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <h2 class="text-4xl md:text-5xl font-bold mb-3 catering-font" data-aos="fade-up">Admin Accounts Management</h2>
            <p class="text-lg opacity-90" data-aos="fade-up" data-aos-delay="100">Add and manage admin accounts</p>
        </div>
    </section>
    <main class="max-w-6xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8" data-aos="fade-up">
            <form method="POST" class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                        <input type="text" name="username" class="w-full border border-gray-300 rounded-lg px-3 py-2" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-3 py-2" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" name="confirm_password" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                    </div>
                </div>
                <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2 px-6 rounded-lg shadow hover-scale">Add Admin</button>
            </form>
        </div>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden" data-aos="fade-up">
            <h2 class="text-xl font-bold mb-4 px-8 pt-8">Current Admins</h2>
            <div class="overflow-x-auto px-8 pb-8">
                <table class="min-w-full bg-white border border-gray-200 rounded">
                    <thead class="bg-yellow-50">
                        <tr>
                            <th class="py-2 px-4 border-b">ID</th>
                            <th class="py-2 px-4 border-b">Username</th>
                            <th class="py-2 px-4 border-b">Email</th>
                            <th class="py-2 px-4 border-b">Role</th>
                            <th class="py-2 px-4 border-b">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($helpers as $helper): ?>
                            <tr class="hover:bg-yellow-50 transition-colors duration-200">
                                <td class="py-2 px-4 border-b text-center"><?php echo $helper['id']; ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($helper['username']); ?></td>
                                <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($helper['email']); ?></td>
                                <td class="py-2 px-4 border-b text-center"><?php echo htmlspecialchars($helper['role']); ?></td>
                                <td class="py-2 px-4 border-b text-center"><?php echo htmlspecialchars($helper['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
