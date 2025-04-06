<?php
class EquipmentType
{
    private $conn;
    private $table = "equipmenttypes";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả loại thiết bị (bao gồm cả đã bị xóa)
    public function getAllEquipmentTypes()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy danh sách loại thiết bị chưa bị xóa
    public function getActiveEquipmentTypes()
    {
        $query = "SELECT * FROM " . $this->table . " WHERE IsDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy loại thiết bị theo ID
    public function getEquipmentTypeById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE EquipmentTypeId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tạo loại thiết bị mới
    public function createEquipmentType($equipmentTypeName)
    {
        $query = "INSERT INTO " . $this->table . " (EquipmentTypeName, IsDeleted) VALUES (?, 0)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$equipmentTypeName]);
    }

    // Cập nhật loại thiết bị
    public function updateEquipmentType($id, $equipmentTypeName)
    {
        $query = "UPDATE " . $this->table . " SET EquipmentTypeName = ? WHERE EquipmentTypeId = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$equipmentTypeName, $id]);
    }

    // Xóa loại thiết bị (soft delete)
    public function deleteEquipmentType($id)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 1 WHERE EquipmentTypeId = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    // Khôi phục loại thiết bị
    public function restoreEquipmentType($id)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 0 WHERE EquipmentTypeId = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
?>
