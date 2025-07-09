<?php
namespace App\Models;

require_once __DIR__ . '/../../config/database.php';
use PDO;

class Posting {
    private $pdo;

    public function __construct() {
        $this->pdo = \Database::connect();
    }

    public function getAllPostings() {
        $stmt = $this->pdo->prepare('SELECT * FROM posting ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 