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
            $category = $_POST['category'] ?? '';
            $item = $_POST['item'] ?? '';
            $packs = $_POST['packs'] ?? '';
            $location = $_POST['location'] ?? '';
            $description = $_POST['description'] ?? '';
            $imageName = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageTmp = $_FILES['image']['tmp_name'];
                $imageName = uniqid('service_', true) . '_' . basename($_FILES['image']['name']);
                $targetPath = __DIR__ . '/../../../resources/image/' . $imageName;
                move_uploaded_file($imageTmp, $targetPath);
            }
            if ($category && $item && $packs && $location) {
                $this->insertService($category, $item, $packs, $location, $description, $imageName);
                $_SESSION['success'] = 'Service added successfully!';
            } else {
                $_SESSION['error'] = 'All fields are required.';
            }
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            // For GET requests, show the service management page
            $this->index();
        }
    }

    private function insertService($category, $item, $packs, $location, $description, $image)
    {
        require_once __DIR__ . '/../../config/database.php';
        $stmt = \Database::connect()->prepare("INSERT INTO services (category, item, packs, location, description, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category, $item, $packs, $location, $description, $image]);
    }

    private function getAllServices()
    {
        require_once __DIR__ . '/../../config/database.php';
        $stmt = \Database::connect()->query("SELECT * FROM services ORDER BY created_at DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
} 