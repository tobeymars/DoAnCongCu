<?php
class Review {
    private $conn;
    private $table = "reviews";

    public $ReviewId;
    public $UserId;
    public $EventId;
    public $VenueId;
    public $Rating;
    public $Comment;
    public $ReviewDate;

    public function __construct($db) {
        $this->conn = $db;
        if ($this->conn === null) {
            throw new Exception("Kết nối cơ sở dữ liệu không tồn tại.");
        }
    }

    public function create() {
        try {
            $query = "INSERT INTO " . $this->table . " 
                     (UserId, EventId, VenueId, Rating, Comment) 
                     VALUES (:UserId, :EventId, :VenueId, :Rating, :Comment)";

            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':UserId', $this->UserId, PDO::PARAM_INT);
            $stmt->bindParam(':EventId', $this->EventId, PDO::PARAM_INT);
            $stmt->bindParam(':VenueId', $this->VenueId, PDO::PARAM_INT);
            $stmt->bindParam(':Rating', $this->Rating, PDO::PARAM_INT);
            $stmt->bindParam(':Comment', $this->Comment, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Lỗi khi tạo đánh giá: " . $e->getMessage());
            return false;
        }
    }

    public function getReviewsByVenue($venueId) {
        try {
            $query = "SELECT r.*, u.FullName 
                      FROM " . $this->table . " r 
                      JOIN users u ON r.UserId = u.UserId 
                      WHERE r.VenueId = :VenueId 
                      ORDER BY r.ReviewDate DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':VenueId', $venueId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Lỗi khi lấy đánh giá: " . $e->getMessage());
            return false;
        }
    }
}
?>
