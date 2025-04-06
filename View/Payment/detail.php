<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/PaymentModel.php';
$database = new Database();
$conn = $database->getConnection();
$model = new Payment($conn);
$bookingId = $_GET['id'];
$payments = $model->getPaymentByBookingId($bookingId);
if ($bookingId) {
    // Tạo tên file QR
    $fileName = 'Booking_' . $bookingId . '.png';
    // Đường dẫn tới thư mục chứa mã QR
    $qrFilePath = $_SERVER['DOCUMENT_ROOT'] . '/quanlysukien/Controller/QR/' . $fileName;

    // Kiểm tra sự tồn tại của tệp QR
    if (file_exists($qrFilePath)) {
        $qrSrc = '/quanlysukien/Controller/QR/' . $fileName;  // Tạo đường dẫn đến tệp QR nếu tồn tại
    } else {
        $qrSrc = '';  // Nếu tệp không tồn tại, đặt giá trị trống cho $qrSrc
    }
} else {
    echo "Không có thông tin đơn hàng.";
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Thanh Toán</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" rel="stylesheet">
</head>

<style>
    body {
        padding-top: 90px;
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .payment-card {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .payment-card:hover {
        transform: translateY(-5px);
    }

    .card-header {
        background: linear-gradient(135deg, #4a90e2, #6c5ce7);
        color: white;
        padding: 20px;
        font-weight: 600;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        background-color: #f2f7ff;
        font-weight: 600;
        color: #495057;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }

    .payment-method {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .amount {
        font-weight: 600;
        color: #2e7d32;
    }

    .no-payment {
        background-color: #fff3f3;
        border-left: 4px solid #f44336;
        padding: 15px 20px;
        color: #d32f2f;
        font-weight: 500;
        border-radius: 5px;
        margin: 20px 0;
    }

    .booking-id {
        background-color: #e3f2fd;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: 500;
        color: #1976d2;
        display: inline-block;
    }

    .download-button {
        display: flex;
        padding: 12px 24px;
        background-color: #4CAF50;
        /* Màu nền đẹp */
        color: white;
        /* Màu chữ */
        text-decoration: none;
        /* Xóa gạch dưới */
        font-size: 16px;
        /* Kích thước chữ */
        border: none;
        /* Xóa viền */
        border-radius: 8px;
        /* Bo tròn các góc */
        text-align: center;
        /* Canh giữa chữ */
        font-weight: bold;
        /* Chữ đậm */
        cursor: pointer;
        /* Thay đổi con trỏ khi hover */
        transition: background-color 0.3s ease, transform 0.3s ease;
        height: 50px;
        margin-top: 30px;
        margin-left: 20px;
    }

    .download-button:hover {
        background-color: #45a049;
        /* Màu nền khi hover */
        transform: scale(1.05);
        /* Tăng kích thước khi hover */
    }

    .download-button:active {
        background-color: #388e3c;
        /* Màu nền khi click */
    }
</style>

<body>
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="mb-3">Thông Tin Thanh Toán</h2>
                <p class="text-muted">Mã đơn đặt: <span class="booking-id"><?php echo htmlspecialchars($bookingId); ?></span></p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="payment-card bg-white">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Chi Tiết Thanh Toán</h5>
                        <a href="/Quanlysukien/View/Booking/indexU.php" onclick="Booking(event)" class="btn btn-light btn-sm"><i class="fas fa-arrow-left me-1"></i>Quay Lại</a>
                    </div>

                    <?php if (empty($payments)) { ?>
                        <div class="p-4">
                            <a href="create.php?id=<?= urlencode($bookingId) ?>" class="btn btn-primary btn-add" id="btn-add">Thanh toán</a>
                            <div class="no-payment">
                                <i class="fas fa-exclamation-circle me-2"></i>Chưa có thanh toán nào cho đơn này.
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Mã Thanh Toán</th>
                                        <th scope="col">Số Tiền</th>
                                        <th scope="col">Phương Thức</th>
                                        <th scope="col">Ngày Thanh Toán</th>
                                        <th>QR code</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment) {
                                        // Kiểm tra nếu PaymentMethod có dữ liệu
                                        $paymentMethod = isset($payment['PaymentMethod']) ? trim($payment['PaymentMethod']) : 'Không xác định';

                                        // Xác định icon theo phương thức thanh toán
                                        $paymentIcon = 'fa-credit-card'; // Mặc định là thẻ tín dụng

                                        if (stripos($paymentMethod, 'momo') !== false) {
                                            $paymentIcon = 'fa-wallet';
                                        } elseif (stripos($paymentMethod, 'banking') !== false || stripos($paymentMethod, 'chuyển khoản ngân hàng') !== false) {
                                            $paymentIcon = 'fa-university';
                                        } elseif (stripos($paymentMethod, 'cash') !== false || stripos($paymentMethod, 'tiền mặt') !== false) {
                                            $paymentIcon = 'fa-money-bill';
                                        }
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($payment['PaymentId']); ?></td>
                                            <td class="amount"><?php echo number_format($payment['Amount'], 0, ',', '.'); ?> VNĐ</td>
                                            <td>
                                                <div class="payment-method">
                                                    <i class="fas <?php echo $paymentIcon; ?>"></i>
                                                    <span><?php echo htmlspecialchars($paymentMethod); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <i class="far fa-calendar-alt me-1"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($payment['PaymentDate'])); ?>
                                            </td>
                                            <td style="display: flex;">
                                                <?php if ($qrSrc): ?>
                                                    <img src="<?= $qrSrc ?>" alt="Mã QR đơn hàng" />
                                                    <br>
                                                    <a href="<?= $qrSrc ?>" download="QRCode_<?= uniqid() ?>.png" class="download-button">Tải mã QR về</a>
                                                <?php else: ?>
                                                    <span>Chưa xác nhận</span> <!-- Hiển thị thông báo nếu không có mã QR -->
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <script>
         function Booking(event) {
            let userInfo = localStorage.getItem("userInfo");

            if (userInfo) {
                event.preventDefault(); // Ngừng hành động mặc định của thẻ <a>

                let userData = JSON.parse(userInfo);

                // Thay vì tìm phần tử <a>, trực tiếp chuyển hướng người dùng
                let bookingUrl = `/Quanlysukien/View/Booking/indexU.php?Id=${encodeURIComponent(userData.user_id)}`;
                window.location.href = bookingUrl; // Chuyển hướng đến trang mới với URL đã được cập nhật
            } else {
                console.error("Không tìm thấy thông tin người dùng trong localStorage");
            }
        }
    </script>
</body>

</html>