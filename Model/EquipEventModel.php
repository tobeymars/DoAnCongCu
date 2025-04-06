<?php
class EventEquipment {
    private $conn;
    private $table = "equipmentevents"; // Tên bảng

    public $id;
    public $eventId;
    public $equipmentId;
    public $date;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function isEquipmentInEvent($eventId, $equipmentId) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE EventId = :eventId AND EquipmentId = :equipmentId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':equipmentId', $equipmentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0; // Nếu trả về > 0 nghĩa là đã tồn tại
    }
    private function getAvailableQuantity($equipmentId) {
        $query = "SELECT (e.Quantity - IFNULL(SUM(ee.soluong), 0)) as AvailableQuantity
                  FROM equipments e
                  LEFT JOIN equipmentevents ee ON e.EquipmentId = ee.EquipmentId
                  WHERE e.EquipmentId = :equipmentId
                  GROUP BY e.EquipmentId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':equipmentId', $equipmentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    // Phương thức để tạo bản ghi mới
    public function create($eventId, $equipmentId, $date, $quantity)
    {
        // Kiểm tra thiết bị đã tồn tại trong sự kiện chưa
        if ($this->isEquipmentInEvent($eventId, $equipmentId)) {
            return ["error" => "Thiết bị đã có trong sự kiện này."];
        }

        // Kiểm tra số lượng trong kho
        $availableQuantity = $this->getAvailableQuantity($equipmentId);
        if ($quantity > $availableQuantity) {
            return ["error" => "Số lượng nhập vượt quá số lượng trong kho (Còn lại: $availableQuantity)."];
        }
        $query = "INSERT INTO " . $this->table . " (EventId, EquipmentId, Date, soluong) VALUES (:eventId, :equipmentId, :date, :quantity)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':equipmentId', $equipmentId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return ["success" => "Thêm thiết bị thành công."];
        } else {
            return ["error" => "Lỗi khi thêm thiết bị vào sự kiện."];
        }
    }
    // Lấy danh sách thiết bị theo EventId
    public function getEquipmentsByEventId($eventId) {
        $query = "SELECT ee.id, ee.equipmentId, eq.EquipmentName, ee.date, ee.soluong
                  FROM " . $this->table . " ee
                  JOIN equipments eq ON ee.equipmentId = eq.EquipmentId
                  WHERE ee.eventId = :eventId";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Cập nhật thiết bị trong sự kiện
    public function update($id, $eventId, $equipmentId, $date, $quantity)
    {
        $query = "UPDATE " . $this->table . " SET EventId = :eventId, EquipmentId = :equipmentId, Date = :date, soluong = :quantity WHERE Id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':eventId', $eventId, PDO::PARAM_INT);
        $stmt->bindParam(':equipmentId', $equipmentId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Xóa mềm thiết bị khỏi sự kiện
    public function delete($id) {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 1 WHERE Id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Khôi phục thiết bị bị xóa mềm
    public function restore($id) {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 0 WHERE Id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}