<?php
namespace App\Controller;

use App\Models\Review;

require_once __DIR__ . '/../models/Review.php';

class ReviewController {
    public function submit() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? null;
            $reviewText = trim($_POST['review_text'] ?? '');
            $rating = intval($_POST['rating'] ?? 0);

            if (!$userId || !$reviewText || $rating < 1 || $rating > 5) {
                $_SESSION['error'] = 'Please complete all fields and select a valid service and rating.';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            $reviewModel = new Review();
            if ($reviewModel->addReview($userId, $reviewText, $rating)) {
                $_SESSION['success'] = 'Review submitted successfully!';
            } else {
                $_SESSION['error'] = 'Failed to submit review. Please try again.';
            }

            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    public function show() {
        $reviewModel = new Review();
        $reviews = $reviewModel->getReviews();
        require __DIR__ . '/../views/review.php';
    }

    public function approve() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'])) {
            $reviewId = intval($_POST['review_id']);
            $reviewModel = new Review();
            if ($reviewModel->updateStatus($reviewId, 'approved')) {
                $_SESSION['success'] = 'Review approved successfully!';
            } else {
                $_SESSION['error'] = 'Failed to approve review.';
            }
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
