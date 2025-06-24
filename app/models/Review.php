<?php
namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

class Review {
    private $db;
    protected $table = 'reviews';

    public function __construct() {
        $this->db = \Database::connect();
    }

    public function addReview($userId, $reviewText, $rating) {
        $stmt = $this->db->prepare("INSERT INTO reviews (user_id, review_text, rating, status) VALUES (?, ?, ?, 'pending')");
        return $stmt->execute([$userId, $reviewText, $rating]);
    }

    public function getReviews() {
        $stmt = $this->db->prepare("
            SELECT r.*, u.username
            FROM reviews r
            JOIN users u ON r.user_id = u.id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateStatus($reviewId, $status) {
        $stmt = $this->db->prepare("UPDATE reviews SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $reviewId]);
    }

    public function countPendingReviews() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM reviews WHERE status = 'pending'");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
    }
}
