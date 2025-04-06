<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . "/../../Model/Bookingdetail.php";
require_once __DIR__ . '/../../config/db.php';
$database = new Database();
$conn = $database->getConnection();
$bookingModel = new BookingDetails($conn);
$bookingId = $_GET['id'];
// Lấy danh sách đặt vé của người dùng
$bookings = $bookingModel->getBookingDetailsByBId($bookingId);
// Kiểm tra nếu có dữ liệu
if (!empty($bookings)) {
    $totalAmount = 0;
    foreach ($bookings as $booking) {
        $totalAmount += $booking['Price'] * $booking['Quantity'];
    }
} else {
    // Xử lý khi không có booking
    echo "Không tìm thấy đơn hàng nào.";
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Toán</title>
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
        background-color: white;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .card-header {
        background: linear-gradient(135deg, #4a90e2, #6c5ce7);
        color: white;
        padding: 20px;
        font-weight: 600;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #dce1e6;
        box-shadow: none;
        transition: all 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 0.25rem rgba(74, 144, 226, 0.25);
    }

    .form-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #4a90e2, #6c5ce7);
        border: none;
        border-radius: 8px;
        padding: 12px 24px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #3a80d2, #5c4cd7);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .payment-method-icon {
        font-size: 1.5rem;
        margin-right: 10px;
        color: #4a90e2;
    }

    .booking-id-badge {
        background-color: #e3f2fd;
        padding: 5px 10px;
        border-radius: 5px;
        font-weight: 500;
        color: #1976d2;
        display: inline-block;
    }

    .input-icon-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
    }

    .form-text {
        font-size: 0.825rem;
        color: #6c757d;
    }

    .payment-icon-wrapper {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .payment-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        padding: 15px;
        border-radius: 8px;
        transition: all 0.3s;
        width: 30%;
    }

    .payment-option:hover {
        background-color: #f0f7ff;
    }

    .payment-option.active {
        background-color: #e3f2fd;
        border: 2px solid #4a90e2;
    }

    .payment-option i {
        font-size: 2rem;
        margin-bottom: 10px;
        color: #4a90e2;
    }
</style>

<body>
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Thanh Toán</h2>
                    <div>
                        <span class="text-muted me-2">Mã đơn:</span>
                        <span class="booking-id-badge"><?php echo htmlspecialchars($bookingId); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="payment-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-credit-card me-2"></i> Thông Tin Thanh Toán
                        </div>
                        <a href="javascript:history.back()" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Quay Lại
                        </a>
                    </div>

                    <div class="card-body p-4">
                        <form action="../../Controller/PaymentController.php?action=create" method="POST" id="paymentForm" class="needs-validation" novalidate>
                            <input type="hidden" name="BookingId" value="<?= htmlspecialchars($bookingId) ?>">

                            <!-- Phương thức thanh toán với icon -->
                            <div class="mb-4">
                                <label class="form-label">Chọn Phương Thức Thanh Toán</label>
                                <div class="payment-icon-wrapper">
                                    <div class="payment-option active" data-method="Thẻ tín dụng">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Thẻ tín dụng</span>
                                    </div>
                                    <div class="payment-option" data-method="Tiền mặt">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>Tiền mặt</span>
                                    </div>
                                    <div class="payment-option" data-method="Chuyển khoản ngân hàng">
                                        <i class="fas fa-university"></i>
                                        <span>Chuyển khoản</span>
                                    </div>
                                </div>
                                <input type="hidden" name="PaymentMethod" id="PaymentMethod" value="Thẻ tín dụng">
                            </div>

                            <!-- Số tiền -->
                            <div class="mb-4">
                                <label for="Amount" class="form-label">Số Tiền</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="Amount" name="Amount" value="<?= number_format($totalAmount, 0, ',', '.') ?>" required readonly>
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                                <div class="form-text">Vui lòng nhập số tiền không có dấu phẩy hoặc dấu chấm</div>
                            </div>

                            <!-- Ngày thanh toán -->
                            <div class="mb-4">
                                <label for="PaymentDate" class="form-label">Ngày Thanh Toán</label>
                                <div class="input-icon-wrap">
                                    <input type="date" class="form-control" id="PaymentDate" name="PaymentDate" required>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Hoàn Tất Thanh Toán
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
    <script>
        // Xử lý chọn phương thức thanh toán
        document.addEventListener('DOMContentLoaded', function() {
            const paymentOptions = document.querySelectorAll('.payment-option');
            const paymentMethodInput = document.getElementById('PaymentMethod');

            paymentOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Xóa class active khỏi tất cả các option
                    paymentOptions.forEach(opt => opt.classList.remove('active'));

                    // Thêm class active vào option được chọn
                    this.classList.add('active');

                    // Cập nhật giá trị vào input ẩn
                    paymentMethodInput.value = this.dataset.method;
                });
            });

            // Thiết lập ngày mặc định là hôm nay
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('PaymentDate').value = today;

            // Form validation
            const form = document.getElementById('paymentForm');
            form.addEventListener('submit', function(event) {
                var amountField = document.getElementById('Amount');
                var amountValue = amountField.value;
                // Loại bỏ dấu phẩy và dấu chấm
                amountField.value = amountValue.replace(/[^0-9]/g, '');
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    </script>
</body>

</html>