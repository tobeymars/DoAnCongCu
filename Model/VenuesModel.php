<?php
class Venue
{
    private $conn;
    private $table = "Venues";

    public $VenueId;
    public $VenueName;
    public $Address;
    public $Capacity;
    public $Description;
    public $Status;
    public $IsDeleted;
    public $Images;
    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function getAllVenues()
    {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function getAllVenuesHome()
    {
        $query = "SELECT * FROM " . $this->table. " WHERE IsDeleted = 0 and Status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function getActiveVenues() {
        $query = "SELECT * FROM venues WHERE IsDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getVenueById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE VenueId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function createVenue($venueName, $address, $capacity, $description, $status, $imagePath)
    {
        // Kiểm tra xem tên địa điểm đã tồn tại chưa
        $checkQuery = "SELECT COUNT(*) FROM " . $this->table . " WHERE VenueName = :VenueName";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":VenueName", $venueName);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            return false; // Tên địa điểm đã tồn tại
        }

        // Nếu chưa tồn tại, tiến hành thêm mới
        $query = "INSERT INTO " . $this->table . " (VenueName, Address, Capacity, Description, Status, Images) 
              VALUES (:VenueName, :Address, :Capacity, :Description, :Status, :Images)";
        $stmt = $this->conn->prepare($query);
        $venueName = htmlspecialchars(strip_tags($venueName));
        $address = htmlspecialchars(strip_tags($address));
        $capacity = intval($capacity);
        $description = htmlspecialchars(strip_tags($description));
        $status = htmlspecialchars(strip_tags($status));
        $stmt->bindParam(":VenueName", $venueName);
        $stmt->bindParam(":Address", $address);
        $stmt->bindParam(":Capacity", $capacity);
        $stmt->bindParam(":Description", $description);
        $stmt->bindParam(":Status", $status);
        $stmt->bindParam(":Images", $imagePath);

        return $stmt->execute();
    }
    public function updateVenue($venueId, $venueName, $address, $capacity, $description, $status, $imagePath = null)
    {
        // Kiểm tra xem tên địa điểm đã tồn tại (trừ chính nó nếu đang cập nhật)
        $checkQuery = "SELECT COUNT(*) FROM " . $this->table . " WHERE VenueName = :VenueName AND VenueId != :VenueId";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":VenueName", $venueName);
        $venueId = intval($venueId);
        $checkStmt->bindParam(":VenueId", $venueId, PDO::PARAM_INT);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            return false; // Tên địa điểm đã tồn tại
        }
    if (empty($imagePath)) {
        $queryOldImage = "SELECT images FROM " . $this->table . " WHERE VenueId = :VenueId";
        $stmtOldImage = $this->conn->prepare($queryOldImage);
        $stmtOldImage->bindParam(":VenueId", $venueId, PDO::PARAM_INT);
        $stmtOldImage->execute();
        $oldImage = $stmtOldImage->fetchColumn();

        if ($oldImage !== false) {
            $imagePath = $oldImage;
        }
    }
        // Chuẩn bị câu lệnh UPDATE
    $query = "UPDATE " . $this->table . " 
    SET VenueName = :VenueName, Address = :Address, Capacity = :Capacity, 
        Description = :Description, Status = :Status, Images = :Images
    WHERE VenueId = :VenueId";

        $stmt = $this->conn->prepare($query);
        $venueName = htmlspecialchars(strip_tags($venueName));
        $address = htmlspecialchars(strip_tags($address));
        $capacity = intval($capacity);
        $description = htmlspecialchars(strip_tags($description));
        $status = htmlspecialchars(strip_tags($status));
        $venueId = intval($venueId);
        $stmt->bindParam(":VenueName", $venueName);
        $stmt->bindParam(":Address", $address);
        $stmt->bindParam(":Capacity", $capacity);
        $stmt->bindParam(":Description", $description);
        $stmt->bindParam(":Status", $status);
        $stmt->bindParam(":VenueId", $venueId);

        if ($imagePath !== null) {
            $stmt->bindParam(":Images", $imagePath);
        }

        return $stmt->execute();
    }
    public function deleteVenue($venueId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 1 WHERE VenueId = :VenueId";
        $stmt = $this->conn->prepare($query);
        $venueId = intval($venueId);
        $stmt->bindParam(":VenueId", $venueId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function restoreVenue($venueId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 0 WHERE VenueId = :VenueId";
        $stmt = $this->conn->prepare($query);
        $venueId = intval($venueId);
        $stmt->bindParam(":VenueId", $venueId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
