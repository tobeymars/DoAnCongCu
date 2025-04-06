<?php
class TicketType
{
    private $conn;
    private $table = "TicketTypes";

    public $TicketTypeId;
    public $EventId;
    public $TicketName;
    public $Price;
    public $IsDeleted;
    public $Quantity;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả các loại vé
    public function getAllTicketTypes()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy các loại vé chưa bị xóa
    public function getActiveTicketTypes()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE IsDeleted = 0 and Quantity > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    // Lấy loại vé theo ID
    public function getTicketTypeById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE TicketTypeId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createTicketType($eventId, $ticketName, $price, $quantity)
    {
        // Kiểm tra xem TicketName đã tồn tại trong EventId chưa (và chưa bị xóa)
        $checkQuery = "SELECT COUNT(*) FROM " . $this->table . " 
                   WHERE EventId = :EventId AND TicketName = :TicketName AND IsDeleted = 0";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":EventId", $eventId, PDO::PARAM_INT);
        $checkStmt->bindParam(":TicketName", $ticketName);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() > 0) {
            // Trùng vé trong cùng sự kiện -> không cho thêm
            return false;
        }

        // Nếu không trùng thì tiến hành thêm mới
        $query = "INSERT INTO " . $this->table . " (EventId, TicketName, Price, Quantity, IsDeleted) 
              VALUES (:EventId, :TicketName, :Price, :Quantity, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":EventId", $eventId, PDO::PARAM_INT);
        $stmt->bindParam(":TicketName", $ticketName);
        $stmt->bindParam(":Price", $price);
        $stmt->bindParam(":Quantity", $quantity, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateTicketType($ticketTypeId, $ticketName, $price, $quantity)
    {
        // Bước 1: Lấy EventId của TicketType cần cập nhật
        $getEventIdQuery = "SELECT EventId FROM " . $this->table . " WHERE TicketTypeId = :TicketTypeId";
        $getStmt = $this->conn->prepare($getEventIdQuery);
        $getStmt->bindParam(":TicketTypeId", $ticketTypeId, PDO::PARAM_INT);
        $getStmt->execute();
        $eventId = $getStmt->fetchColumn();

        if (!$eventId) {
            return false;
        }

        // Bước 2: Kiểm tra trùng TicketName trong cùng EventId (loại trừ chính bản thân nó ra)
        $checkQuery = "SELECT COUNT(*) FROM " . $this->table . " 
                       WHERE EventId = :EventId AND TicketName = :TicketName 
                       AND TicketTypeId != :TicketTypeId AND IsDeleted = 0";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":EventId", $eventId, PDO::PARAM_INT);
        $checkStmt->bindParam(":TicketName", $ticketName);
        $checkStmt->bindParam(":TicketTypeId", $ticketTypeId, PDO::PARAM_INT);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() > 0) {
            // Trùng tên vé trong cùng EventId
            return false;
        }

        // Bước 3: Thực hiện cập nhật
        $query = "UPDATE " . $this->table . " 
                  SET TicketName = :TicketName, Price = :Price, Quantity = :Quantity 
                  WHERE TicketTypeId = :TicketTypeId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":TicketTypeId", $ticketTypeId, PDO::PARAM_INT);
        $stmt->bindParam(":TicketName", $ticketName);
        $stmt->bindParam(":Price", $price);
        $stmt->bindParam(":Quantity", $quantity, PDO::PARAM_INT);

        return $stmt->execute();
    }
    
    public function updateTicketQuantity($ticketTypeId, $updatedQuantity)
    {
        $sql = "UPDATE " . $this->table ." SET Quantity = :updatedQuantity WHERE TicketTypeId = :TicketTypeId";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":updatedQuantity", $updatedQuantity, PDO::PARAM_INT);
        $stmt->bindParam(":TicketTypeId", $ticketTypeId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Xóa loại vé (chuyển IsDeleted thành 1)
    public function deleteTicketType($ticketTypeId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 1 WHERE TicketTypeId = :TicketTypeId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":TicketTypeId", $ticketTypeId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Khôi phục loại vé (chuyển IsDeleted thành 0)
    public function restoreTicketType($ticketTypeId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 0 WHERE TicketTypeId = :TicketTypeId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":TicketTypeId", $ticketTypeId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
