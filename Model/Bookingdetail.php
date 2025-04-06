<?php
class BookingDetails
{
    private $conn;
    private $table = "BookingDetails";

    public $BookingDetailId;
    public $BookingId;
    public $TicketTypeId;
    public $Quantity;
    public $IsDeleted;
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy chi tiết đơn(user) theo BookingId
    public function getBookingDetailsByBookingId($bookingId)
    {
        $query = "SELECT bd.BookingDetailId, bd.Quantity, bd.IsDeleted, 
                      v.VenueName, v.Address, e.VenueId, e.EventName, e.EventDate, e.images, t.Price, t.TicketName, b.EventId, b.BookingDate, b.BookingId
                  FROM " . $this->table . " bd
                  JOIN tickettypes t ON bd.TicketTypeId = t.TicketTypeId
                  JOIN bookings b ON bd.BookingId = b.BookingId
                  JOIN events e ON b.EventId = e.EventId
                  JOIN venues v ON e.VenueId = v.VenueId
                  WHERE bd.BookingId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $bookingId, PDO::PARAM_INT);
        $stmt->execute();
    
        // Trả về mảng kết quả
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Đảm bảo đây là mảng kết quả từ truy vấn
    }
    // Lấy chi tiết đơn(user) theo BookingId
    public function getBookingDetailsByBId($Id)
    {
        $query = "SELECT 
                bd.BookingDetailId, bd.Quantity, bd.IsDeleted, 
                v.VenueName, v.Address, e.VenueId, e.EventName, e.EventDate, e.images, 
                t.Price, t.TicketName, b.EventId, b.BookingDate, b.BookingId
              FROM " . $this->table . " bd
              JOIN tickettypes t ON bd.TicketTypeId = t.TicketTypeId
              JOIN bookings b ON bd.BookingId = b.BookingId
              JOIN events e ON b.EventId = e.EventId
              JOIN venues v ON e.VenueId = v.VenueId
              WHERE bd.BookingId = :BookingId";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':BookingId', $Id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Có thể xử lý thêm lỗi ở đây
            return [];
        }
    }

    // Cập nhật chi tiết đơn(user)
    public function updateBookingDetail($bookingDetailId, $quantity, $ticketTypeId)
    {
        $query = "UPDATE " . $this->table . " 
                  SET Quantity = :Quantity, TicketTypeId = :TicketTypeId
                  WHERE BookingDetailId = :BookingDetailId";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":BookingDetailId", $bookingDetailId, PDO::PARAM_INT);
        $stmt->bindParam(":Quantity", $quantity, PDO::PARAM_INT);
        $stmt->bindParam(":TicketTypeId", $ticketTypeId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
?>
