<?php
class Booking
{
    private $conn;
    private $table = "Bookings";

    public $BookingId;
    public $UserId;
    public $EventId;
    public $BookingDate;
    public $Status;
    public $IsDeleted;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tất cả đơn(admin)
    public function getAllBookings()
    {
        $query = "SELECT b.BookingId, b.UserId, u.FullName, u.username, b.EventId, e.EventName, b.BookingDate, b.Status, b.IsDeleted
                  FROM " . $this->table . " b
                  JOIN users u ON b.UserId = u.UserId
                  JOIN events e ON b.EventId = e.EventId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    // Lấy tất cả đơn(user)
    public function getUserBookings($userId)
    {
        $query = "SELECT b.BookingId, b.UserId, u.username, b.EventId, e.EventName, 
                         b.BookingDate, b.Status, b.IsDeleted
                  FROM " . $this->table . " b
                  JOIN users u ON b.UserId = u.UserId
                  JOIN events e ON b.EventId = e.EventId
                  WHERE b.UserId = :UserId and b.IsDeleted = 0 ";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":UserId", $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Trả về danh sách các đặt vé của người dùng
    }    
    // Cập nhật trạng thái đơn(admin)
    public function updateBooking($bookingId)
    {
        $query = "UPDATE " . $this->table . " 
                  SET Status = 2 
                  WHERE BookingId = :BookingId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":BookingId", $bookingId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function cancelBooking($bookingId)
    {
        // Cập nhật trạng thái trong bảng booking
        $query = "UPDATE " . $this->table . " 
              SET Status = 3 
              WHERE BookingId = :BookingId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":BookingId", $bookingId, PDO::PARAM_INT);
        $stmt->execute();

        // Cập nhật IsDeleted trong bảng bookingdetail
        $queryDetail = "UPDATE bookingdetails 
                    SET IsDeleted = 1 
                    WHERE BookingId = :BookingId";
        $stmtDetail = $this->conn->prepare($queryDetail);
        $stmtDetail->bindParam(":BookingId", $bookingId, PDO::PARAM_INT);
        return $stmtDetail->execute();
    }
    public function getUserEmailAndFullNameByBookingId($bookingId)
    {
        // Truy vấn lấy Email, FullName, thông tin từ bảng bookingdetail và tickettype
        $sql = "SELECT u.Email, u.FullName, 
                   bd.BookingDetailId, bd.Quantity, bd.IsDeleted, 
                   t.Price, t.TicketName
            FROM users u
            JOIN " . $this->table . " b ON u.UserId = b.UserId
            JOIN bookingdetails bd ON b.BookingId = bd.BookingId
            JOIN tickettypes t ON bd.TicketTypeId = t.TicketTypeId
            WHERE b.BookingId = :BookingId";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':BookingId', $bookingId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Nếu tìm thấy, trả về mảng chứa Email, FullName và thông tin bookingdetail, tickettype
        if ($result) {
            return $result;
        } else {
            return null;  // Nếu không tìm thấy, trả về null
        }
    }
    // Xóa đơn (chuyển IsDeleted thành 1)(admin)
    public function deleteBooking($bookingId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 1 WHERE BookingId = :BookingId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":BookingId", $bookingId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Khôi phục đơn (chuyển IsDeleted thành 0)(admin)
    public function restoreBooking($bookingId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 0 WHERE BookingId = :BookingId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":BookingId", $bookingId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
