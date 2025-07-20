<?php
namespace App\Controller;
use App\Models\ServiceModel;
use App\Database\Database;
 
require_once __DIR__ . '/../models/ServiceModel.php';
class ServiceController
{
    public function index()
    {
        // Fetch all services from the database
        $services = $this->getAllServices();
        $menu = ServiceModel::getMenu();
        $locations = ServiceModel::getLocations();
        include __DIR__ . '/../views/admin/service.php';
    }

    public function handleForm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $categories = $_POST['category'] ?? [];
            $items = $_POST['item'] ?? [];
            $packageName = $_POST['package_name'] ?? '';
            $price = $_POST['price'] ?? '';
            $location = $_POST['location'] ?? '';
            $description = $_POST['description'] ?? '';
            $dateTime = $_POST['date_time'] ?? '';
            $packs = $_POST['packs'] ?? '';

            // Convert arrays to comma-separated strings
            $categoriesStr = is_array($categories) ? implode(',', $categories) : $categories;
            $itemsStr = is_array($items) ? implode(',', $items) : $items;

            $imageName = '';

            // Handle image upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageTmp = $_FILES['image']['tmp_name'];
                $originalName = basename($_FILES['image']['name']);
                $extension = pathinfo($originalName, PATHINFO_EXTENSION);
                $imageName = uniqid('service_', true) . '.' . $extension;

                $uploadDir = dirname(__DIR__, 2) . '/uplaods/';
                $targetPath = $uploadDir . $imageName;

                // Create folder if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                // Attempt to move the uploaded file
                if (!move_uploaded_file($imageTmp, $targetPath)) {
                    $_SESSION['error'] = 'Failed to move uploaded image. Please check folder permissions.';
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit;
                }
            } elseif (!empty($_FILES['image']['error']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                // Optional: show specific error code
                $_SESSION['error'] = 'Upload error code: ' . $_FILES['image']['error'];
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }

            // Validate fields before inserting
            $valid = true;
            $errorMsg = '';
            if (!$packageName) {
                $valid = false;
                $errorMsg = 'Package name is required.';
            } elseif (empty($categories) || !is_array($categories) || count(array_filter($categories)) === 0) {
                $valid = false;
                $errorMsg = 'At least one category is required.';
            } elseif (empty($items) || !is_array($items) || count(array_filter($items)) === 0) {
                $valid = false;
                $errorMsg = 'At least one menu item is required.';
            } elseif (!$location) {
                $valid = false;
                $errorMsg = 'Location is required.';
            } elseif (!$dateTime) {
                $valid = false;
                $errorMsg = 'Date and time is required.';
            } elseif (!$packs || !is_numeric($packs) || $packs < 50 || $packs > 100) {
                $valid = false;
                $errorMsg = 'Packs must be a number between 50 and 100.';
            } elseif ($price === '' || !is_numeric($price)) {
                $valid = false;
                $errorMsg = 'Total price is required and must be a number.';
            }

            if ($valid) {
                $serviceId = $this->insertService($packageName, $categoriesStr, $itemsStr, $location, $description, $imageName, $dateTime, $packs, $price);
                $_SESSION['success'] = 'Service added successfully!';
            } else {
                $_SESSION['error'] = $errorMsg;
            }

            // Redirect back to avoid form resubmission
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            // Show the form view
            $this->index();
        }
    }

    private function insertService($packageName, $categoriesStr, $itemsStr, $location, $description, $image, $dateTime, $packs, $price)
    {
        require_once __DIR__ . '/../../config/database.php';
        $stmt = \Database::connect()->prepare("INSERT INTO services (package_name, category, item, location, description, image, date_time, packs, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$packageName, $categoriesStr, $itemsStr, $location, $description, $image, $dateTime, $packs, $price]);
        return \Database::connect()->lastInsertId();
    }

    private function insertServiceItems($serviceId, $categories, $items)
    {
        require_once __DIR__ . '/../../config/database.php';
        $stmt = \Database::connect()->prepare("INSERT INTO service_items (service_id, category, item) VALUES (?, ?, ?)");
        for ($i = 0; $i < count($categories); $i++) {
            $cat = $categories[$i] ?? '';
            $item = $items[$i] ?? '';
            if ($cat && $item) {
                $stmt->execute([$serviceId, $cat, $item]);
            }
        }
    }

    
    private function getAllServices()
    {
        require_once __DIR__ . '/../../config/database.php';
        $stmt = \Database::connect()->query("SELECT * FROM services ORDER BY created_at DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
} 