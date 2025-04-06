<?php
class Payment
{
    private $conn;
    private $table = "Payments";  // Tên bảng thanh toán

    public $PaymentId;
    public $BookingId;
    public $Amount;
    public $PaymentMethod;
    public $PaymentDate;
    public $IsDeleted;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy thông tin thanh toán theo BookingId
    public function getPaymentByBookingId($bookingId)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE BookingId = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $bookingId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm thanh toán mới
    public function createPayment($bookingId, $amount, $paymentMethod, $paymentDate)
    {
        // Kiểm tra xem thông tin thanh toán đã tồn tại chưa (có thể dựa trên BookingId hoặc PaymentDate)
        $checkQuery = "SELECT COUNT(*) FROM " . $this->table . " WHERE BookingId = :BookingId AND PaymentDate = :PaymentDate";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":BookingId", $bookingId);
        $checkStmt->bindParam(":PaymentDate", $paymentDate);
        $paymentDate = date('Y-m-d H:i:s');  // Ngày thanh toán
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            return false;  // Thanh toán đã tồn tại cho BookingId và PaymentDate
        }

        // Thêm thanh toán mới vào bảng
        $query = "INSERT INTO " . $this->table . " (BookingId, Amount, PaymentMethod, PaymentDate, IsDeleted)
              VALUES (:BookingId, :Amount, :PaymentMethod, :PaymentDate, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":BookingId", $bookingId);
        $stmt->bindParam(":Amount", $amount);
        $stmt->bindParam(":PaymentMethod", $paymentMethod);
        $stmt->bindParam(":PaymentDate", $paymentDate);

        if ($stmt->execute()) {
            // Cập nhật trạng thái của booking thành 'Pending' (Status = 1)
            $updateQuery = "UPDATE bookings SET Status = 1 WHERE BookingId = :BookingId";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(":BookingId", $bookingId);

            return $updateStmt->execute();
        }

        return false;
    }


    // Cập nhật thanh toán
    public function updatePayment($paymentId, $amount, $paymentMethod)
    {
        $query = "UPDATE " . $this->table . " SET Amount = :Amount, PaymentMethod = :PaymentMethod WHERE PaymentId = :PaymentId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":Amount", $amount);
        $stmt->bindParam(":PaymentMethod", $paymentMethod);
        $stmt->bindParam(":PaymentId", $paymentId);
        return $stmt->execute();
    }

    // Xóa thanh toán (đánh dấu IsDeleted = 1)
    public function deletePayment($paymentId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 1 WHERE PaymentId = :PaymentId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":PaymentId", $paymentId);
        return $stmt->execute();
    }

    // Khôi phục thanh toán (đánh dấu IsDeleted = 0)
    public function restorePayment($paymentId)
    {
        $query = "UPDATE " . $this->table . " SET IsDeleted = 0 WHERE PaymentId = :PaymentId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":PaymentId", $paymentId);
        return $stmt->execute();
    }
}
?>
