<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? null;
    $category = trim($_POST['category'] ?? '');
    $item = trim($_POST['item'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $userId = $_SESSION['user_id'];

    if ($orderId) {
        $pdo = Database::connect();
        // Only update if the order belongs to the user
        $stmt = $pdo->prepare('UPDATE orders SET category = ?, item = ?, notes = ? WHERE id = ? AND user_id = ?');
        $success = $stmt->execute([$category, $item, $notes, $orderId, $userId]);
        if ($success) {
            $_SESSION['success'] = 'Order updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update order.';
        }
    } else {
        $_SESSION['error'] = 'Invalid order.';
    }
}
header('Location: /dashboard');
exit(); 