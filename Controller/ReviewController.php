<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '../../Model/ReviewModel.php';

class ReviewController
{
    private $model;
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->model = new Review($this->conn);
    }

    // Thêm đánh giá cho địa điểm
    public function createReview()
    {
        // Trả về JSON
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode([
                'success' => false,
                'message' => 'Bạn cần đăng nhập để đánh giá.'
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $venueId = $_POST['venue_id'] ?? null;
            $rating = $_POST['rating'] ?? null;
            $comment = $_POST['comment'] ?? '';

            if (empty($venueId) || empty($rating)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Thiếu thông tin đánh giá.'
                ]);
                exit;
            }

            $this->model->UserId = $_SESSION['user_id'];
            $this->model->VenueId = (int)$venueId;
            $this->model->Rating = (int)$rating;
            $this->model->Comment = trim($comment);
            $this->model->EventId = null;

            if ($this->model->create()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Đánh giá đã được lưu.'
                ]);
                exit;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Lỗi khi lưu đánh giá.'
                ]);
                exit;
            }
        }
    }

    public function getReviewsByVenueId()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['venue_id'])) {
            $venueId = (int)$_GET['venue_id'];
            $reviews = $this->model->getReviewsByVenue($venueId);
            echo json_encode($reviews);
            exit;
        }
    }
}

// Xử lý URL
if (isset($_GET['action'])) {
    $controller = new ReviewController();

    if ($_GET['action'] === 'create') {
        $controller->createReview();
    } elseif ($_GET['action'] === 'getByVenue') {
        $controller->getReviewsByVenueId();
    }
}
