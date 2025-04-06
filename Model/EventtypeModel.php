<?php
class EventType
{
    private $conn;
    private $table = "EventTypes";

    public $EventTypeId;
    public $TypeName;
    public $IsDeleted;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả loại sự kiện (bao gồm cả đã xóa)
    public function getAllEventTypes()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy danh sách loại sự kiện chưa bị xóa
    public function getActiveEventTypes()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE IsDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy loại sự kiện theo ID
    public function getEventTypeById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE EventTypeId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm mới loại sự kiện
    public function createEventType($typeName)
    {
        // Kiểm tra xem tên loại sự kiện đã tồn tại chưa
        $checkQuery = "SELECT COUNT(*) FROM " . $this->table . " WHERE TypeName = :TypeName";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":TypeName", $typeName);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            return false; // Tên loại sự kiện đã tồn tại
        }

        $query = "INSERT INTO " . $this->table . " (TypeName, IsDeleted) VALUES (:TypeName, 0)";
        $stmt = $this->conn->prepare($query);

        $typeName = htmlspecialchars(strip_tags($typeName));

        $stmt->bindParam(":TypeName", $typeName);

        return $stmt->execute();
    }

    // Cập nhật loại sự kiện
    public function updateEventType($eventTypeId, $typeName)
    {// Kiểm tra xem tên loại sự kiện đã tồn tại chưa
        $checkQuery = "SELECT COUNT(*) FROM " . $this->table . " WHERE TypeName = :TypeName";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":TypeName", $typeName);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            return false; // Tên loại sự kiện đã tồn tại
        }
        $query = "UPDATE " . $this->table . " 
                  SET TypeName = :TypeName
                  WHERE EventTypeId = :EventTypeId";
        $stmt = $this->conn->prepare($query);

        $typeName = htmlspecialchars(strip_tags($typeName));
        $eventTypeId = intval($eventTypeId);

        $stmt->bindParam(":TypeName", $typeName);
        $stmt->bindParam(":EventTypeId", $eventTypeId);

        return $stmt->execute();
    }

    // Xóa loại sự kiện (chuyển IsDeleted thành 1)
    public function deleteEventType($eventTypeId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 1 WHERE EventTypeId = :EventTypeId";
        $stmt = $this->conn->prepare($query);

        $eventTypeId = intval($eventTypeId);
        $stmt->bindParam(":EventTypeId", $eventTypeId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Khôi phục loại sự kiện (chuyển IsDeleted thành 0)
    public function restoreEventType($eventTypeId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 0 WHERE EventTypeId = :EventTypeId";
        $stmt = $this->conn->prepare($query);

        $eventTypeId = intval($eventTypeId);
        $stmt->bindParam(":EventTypeId", $eventTypeId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>
