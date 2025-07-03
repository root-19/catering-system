<?php
require_once __DIR__ . '/../../app/models/ServiceModel.php';
use App\Models\ServiceModel;
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $package_name = $_POST['package_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $item = $_POST['item'] ?? '';
    $packs = $_POST['packs'] ?? '';
    $location = $_POST['location'] ?? '';
    $description = $_POST['description'] ?? '';
    $date_time = $_POST['date_time'] ?? '';
    $price = $_POST['price'] ?? '';

    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Missing ID']);
        exit;
    }

    $result = ServiceModel::updateService([
        'id' => $id,
        'package_name' => $package_name,
        'category' => $category,
        'item' => $item,
        'packs' => $packs,
        'location' => $location,
        'description' => $description,
        'date_time' => $date_time,
        'price' => $price
    ]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
} 