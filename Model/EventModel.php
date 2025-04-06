<?php
class Event
{
    private $conn;
    private $table = "Events";

    public $EventId;
    public $EventName;
    public $Description;
    public $EventDate;
    public $CreatedBy;
    public $VenueId;
    public $EventTypeId;
    public $IsDeleted;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả sự kiện
    public function getAllEvents()
    {
        $query = " SELECT e.EventId, e.EventName, e.EventDate, e.Description, e.EventTypeId, e.status,
                   v.VenueName, t.TypeName, u.RoleId, u.username AS CreatedBy, e.IsDeleted
            FROM " . $this->table . " e
            JOIN venues v ON e.VenueId = v.VenueId
            JOIN eventtypes t ON e.EventTypeId = t.EventTypeId
            JOIN users u ON e.CreatedBy = u.UserId
         ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function getEventsInCurrentMonth()
    {
        $query = " SELECT e.EventId, e.EventName, e.EventDate, e.Description, e.status,
                          v.VenueName, t.TypeName, u.RoleId, u.username AS CreatedBy, e.IsDeleted
                   FROM " . $this->table . " e
                   JOIN venues v ON e.VenueId = v.VenueId
                   JOIN eventtypes t ON e.EventTypeId = t.EventTypeId
                   JOIN users u ON e.CreatedBy = u.UserId
                   WHERE MONTH(e.EventDate) = MONTH(CURDATE()) 
                   AND YEAR(e.EventDate) = YEAR(CURDATE())"; // Lọc sự kiện trong tháng hiện tại
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function countEventsPerVenueInCurrentMonth() {
        $query = "
            SELECT v.VenueName, COUNT(e.EventId) AS EventCount
            FROM venues v
            LEFT JOIN " . $this->table . " e ON e.VenueId = v.VenueId
            AND MONTH(e.EventDate) = MONTH(CURDATE()) 
            AND YEAR(e.EventDate) = YEAR(CURDATE()) 
            GROUP BY v.VenueId, v.VenueName
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function getActiveEvents()
    {
        $query = "SELECT e.EventId, e.EventName, e.EventDate, e.Description, 
                     v.VenueName, v.Address, v.Capacity, v.images, 
                     t.TypeName, u.username, u.FullName AS CreatedBy, 
                     e.images, e.IsDeleted, e.status
              FROM " . $this->table . " e
              JOIN venues v ON e.VenueId = v.VenueId
              JOIN eventtypes t ON e.EventTypeId = t.EventTypeId
              JOIN users u ON e.CreatedBy = u.UserId
              WHERE e.IsDeleted = 0 
              AND e.status = 0 
              AND e.EventDate > CURRENT_DATE
              ORDER BY e.EventDate ASC"; // Sắp xếp theo ngày sự kiện từ gần nhất tới xa nhất

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy tất cả sự kiện(user)
    public function getUserEvents($userId)
    {
        $query = "SELECT e.EventId, e.EventName, e.EventDate, e.Description, 
                v.VenueName, v.Address, v.Capacity, v.images, 
                t.TypeName, u.username, u.FullName AS CreatedBy, 
                e.IsDeleted, e.status
          FROM " . $this->table . " e
          JOIN venues v ON e.VenueId = v.VenueId
          JOIN eventtypes t ON e.EventTypeId = t.EventTypeId
          JOIN users u ON e.CreatedBy = u.UserId
          WHERE e.CreatedBy = :userId"; 
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
                $stmt->execute();                
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy sự kiện theo ID
    public function getEventById($id)
    {
        $query = "SELECT e.EventId, e.EventName, e.EventDate, e.Description, 
                v.VenueName, v.Address, v.Capacity, e.images, 
                t.TypeName, u.username, u.FullName AS CreatedBy, 
                e.IsDeleted, e.status
          FROM " . $this->table . " e
          JOIN venues v ON e.VenueId = v.VenueId
          JOIN eventtypes t ON e.EventTypeId = t.EventTypeId
          JOIN users u ON e.CreatedBy = u.UserId
          WHERE EventId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm sự kiện mới
    public function createEvent($eventName, $description, $eventDate, $createdBy, $venueId, $eventTypeId, $imagePath)
    {
        $query = "INSERT INTO " . $this->table . " (EventName, Description, EventDate, CreatedBy, VenueId, EventTypeId, IsDeleted, status, images) 
                  VALUES (:EventName, :Description, :EventDate, :CreatedBy, :VenueId, :EventTypeId, 0, 0, :Images)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":EventName", $eventName);
        $stmt->bindParam(":Description", $description);
        $stmt->bindParam(":EventDate", $eventDate);
        $stmt->bindParam(":CreatedBy", $createdBy, PDO::PARAM_INT);
        $stmt->bindParam(":VenueId", $venueId, PDO::PARAM_INT);
        $stmt->bindParam(":EventTypeId", $eventTypeId, PDO::PARAM_INT);
        $stmt->bindParam(":Images", $imagePath);
        return $stmt->execute();
    }

    // Cập nhật sự kiện
    public function updateEvent($eventId, $eventName, $description, $eventDate, $venueId, $eventTypeId, $imagePath)
    {
        if (empty($imagePath)) {
        $queryOldImage = "SELECT images FROM " . $this->table . " WHERE EventId = :EventId";
        $stmtOldImage = $this->conn->prepare($queryOldImage);
        $stmtOldImage->bindParam(":EventId", $venueId, PDO::PARAM_INT);
        $stmtOldImage->execute();
        $oldImage = $stmtOldImage->fetchColumn();

        if ($oldImage !== false) {
            $imagePath = $oldImage;
        }
        }
        $query = "UPDATE " . $this->table . " 
                  SET EventName = :EventName, Description = :Description, EventDate = :EventDate, VenueId = :VenueId, EventTypeId = :EventTypeId, Images = :Images
                  WHERE EventId = :EventId";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":EventId", $eventId, PDO::PARAM_INT);
        $stmt->bindParam(":EventName", $eventName);
        $stmt->bindParam(":Description", $description);
        $stmt->bindParam(":EventDate", $eventDate);
        $stmt->bindParam(":VenueId", $venueId, PDO::PARAM_INT);
        $stmt->bindParam(":EventTypeId", $eventTypeId, PDO::PARAM_INT);

        if ($imagePath !== null) {
            $stmt->bindParam(":Images", $imagePath);
        }
        return $stmt->execute();
    }

    // Xóa sự kiện (chuyển IsDeleted thành 1)
    public function deleteEvent($eventId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 1 WHERE EventId = :EventId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":EventId", $eventId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Hoàn thành sự kiện (chuyển status thành 1)
    public function Eventcomplete($eventId)
    {
        $query = "UPDATE " . $this->table . " SET status = 1 WHERE EventId = :EventId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":EventId", $eventId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Khôi phục sự kiện (chuyển IsDeleted thành 0)
    public function restoreEvent($eventId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 0 WHERE EventId = :EventId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":EventId", $eventId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
