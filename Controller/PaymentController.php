<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/PaymentModel.php';

class PaymentController
{
    private $model;
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->model = new Payment($this->conn);
    }

    // Lấy thanh toán theo ID
    public function getPaymentByBookingId()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
            $payment = $this->model->getPaymentByBookingId($_GET['id']);
            if ($payment) {
                echo json_encode($payment);
            } else {
                echo json_encode(["error" => "Thanh toán không tồn tại"]);
            }
        }
    }

    // Thêm giao dịch thanh toán mới
    public function createPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['BookingId'];
            $amount = $_POST['Amount'];
            $paymentMethod = $_POST['PaymentMethod'];
            $paymentDate=$_POST['PaymentDate'];
            if (empty($bookingId) || empty($amount) || empty($paymentMethod) || empty($paymentDate)) {
                echo json_encode(["error" => "Vui lòng nhập đầy đủ thông tin"]);
                exit();
            }

            if ($this->model->createPayment($bookingId, $amount, $paymentMethod, $paymentDate)) {
                header("Location: ../View/Payment/detail.php?id=". urlencode($bookingId));
                exit();
            } else {
                echo "<script>alert('Lỗi! Không thể tạo thanh toán.'); window.history.back();</script>";
            }
        }
    }
}

// Xử lý request từ URL
if (isset($_GET['action'])) {
    $controller = new PaymentController();

    if ($_GET['action'] === 'getById') {
        $controller->getPaymentByBookingId();
    } elseif ($_GET['action'] === 'create') {
        $controller->createPayment();
    }
}
?>
