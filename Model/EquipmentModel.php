<?php
class Equipment {
    private $conn;
    private $table = "equipments";

    public $EquipmentId;
    public $EquipmentTypeId;
    public $EventId;
    public $EquipmentName;
    public $Quantity;
    public $Status;
    public $IsDeleted;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách tất cả thiết bị với loại thiết bị
    public function getAllEquipments() {
        $query = "SELECT e.EquipmentId, e.EquipmentName, e.Quantity, e.Status, e.IsDeleted, 
                         et.EquipmentTypeName
                  FROM " . $this->table . " e
                  JOIN equipmenttypes et ON e.EquipmentTypeId = et.EquipmentTypeId
                  ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy danh sách tất cả loại thiết bị
    public function getAllEquipmentTypes()
    {
        $query = "SELECT * FROM equipmenttypes WHERE IsDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách thiết bị chưa bị xóa
    public function getActiveEquipments() {
        $query = "SELECT e.EquipmentId, e.EquipmentName, e.Quantity, e.Status, 
                         et.EquipmentTypeName
                  FROM " . $this->table . " e
                  JOIN equipmenttypes et ON e.EquipmentTypeId = et.EquipmentTypeId
                  WHERE e.IsDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Lấy thiết bị theo ID
    public function getEquipmentById($id) {
        $query = "SELECT e.*, et.EquipmentTypeName 
              FROM equipments e 
              LEFT JOIN equipmenttypes et ON e.EquipmentTypeId = et.EquipmentTypeId 
              WHERE e.EquipmentId = :id 
              LIMIT 1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm thiết bị mới
    public function createEquipment($equipmentTypeId, $equipmentName, $quantity, $status) {
        $query = "INSERT INTO " . $this->table . " (EquipmentTypeId, EquipmentName, Quantity, Status, IsDeleted) 
                  VALUES (:EquipmentTypeId, :EquipmentName, :Quantity, :Status, 0)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":EquipmentTypeId", $equipmentTypeId);
        $stmt->bindParam(":EquipmentName", $equipmentName);
        $stmt->bindParam(":Quantity", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":Status", $status);

        return $stmt->execute();
    }

    // Cập nhật thông tin thiết bị
    public function updateEquipment($id, $name, $quantity, $equipmentTypeId)
    {
        $query = "UPDATE equipments 
                  SET EquipmentName = :name, 
                      Quantity = :quantity, 
                      EquipmentTypeId = :equipmentTypeId  
                  WHERE EquipmentId = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':equipmentTypeId', $equipmentTypeId, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }


    // Xóa mềm thiết bị 
    public function deleteEquipment($equipmentId) {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 1 WHERE EquipmentId = :EquipmentId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":EquipmentId", $equipmentId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Khôi phục thiết bị 
    public function restoreEquipment($equipmentId) {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 0 WHERE EquipmentId = :EquipmentId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":EquipmentId", $equipmentId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
