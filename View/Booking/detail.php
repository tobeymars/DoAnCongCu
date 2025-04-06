<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/Bookingdetail.php';
require_once __DIR__ . '/../../Model/PaymentModel.php';
// Kiểm tra ID sự kiện
$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$bookingId) {
    die("ID sự kiện không hợp lệ.");
}
$roleId = isset($_GET['roleId']) ? intval($_GET['roleId']) : null; // Ép kiểu về số nguyên
if ($roleId === 2) {
    include '../shares/header.php';
} else {
    include '../shares/adminhd.php';
}

$paddingLeftStyle = ($roleId === 1) ? 'body {padding-left: 275px;}' : '';
// Thiết lập kết nối database
$database = new Database();
$conn = $database->getConnection();
$bookingModel = new BookingDetails($conn);
$bookings = $bookingModel->getBookingDetailsByBookingId($bookingId);
$PaymentModel = new Payment($conn);
$payments = $PaymentModel->getPaymentByBookingId($bookingId);
// Kiểm tra xem có thanh toán hay không
$isPaid = !empty($payments); // Kiểm tra xem mảng thanh toán có phần tử nào không
$paymentStatus = $isPaid ? "Đã thanh toán" : "Chưa thanh toán";
$statusClass = $isPaid ? "active" : "cancelled";
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đặt Vé</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        <?php echo $paddingLeftStyle; ?> :root {
            --primary-color1: #4e73df;
            --secondary-color1: #1cc88a;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
            --border-color: #e3e6f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fc;
            color: #333;
            line-height: 1.6;
            padding-top: 60px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--primary-color1);
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .card-body {
            padding: 20px;
        }

        .booking-id {
            background-color: rgba(78, 115, 223, 0.1);
            color: var(--primary-color);
            padding: 8px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: inline-block;
            font-weight: 600;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
        }

        th {
            background-color: #f8f9fc;
            font-weight: 600;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid var(--border-color);
            color: var(--dark-color);
        }

        td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }

        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .active {
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--secondary-color1);
        }

        .cancelled {
            background-color: rgba(231, 74, 59, 0.1);
            color: var(--danger-color);
        }

        .ticket-type {
            display: flex;
            align-items: center;
        }

        .ticket-icon {
            margin-right: 8px;
            color: var(--primary-color1);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--dark-color);
        }

        .back-button {
            display: inline-block;
            padding: 8px 15px;
            background-color: var(--primary-color1);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #375abb;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            th,
            td {
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <span>Chi Tiết Đặt Vé</span>
                <span class="booking-id">Mã đơn: #<?php echo $bookingId; ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($bookings)): ?>
                    <div class="empty-state">
                        <i class="fas fa-ticket-alt fa-3x" style="color: #ddd; margin-bottom: 15px;"></i>
                        <h3>Không tìm thấy thông tin đặt vé</h3>
                        <p>Không có thông tin chi tiết nào cho đơn hàng này.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Loại vé</th>
                                    <th>Số lượng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thanh toán</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $detail): ?>
                                    <tr>
                                        <td>
                                            <div class="ticket-type">
                                                <i class="fas fa-ticket-alt ticket-icon"></i>
                                                <?php echo htmlspecialchars($detail['TicketName']); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($detail['Quantity']); ?></td>
                                        <td><?php echo number_format($detail['Quantity'] * $detail['Price'], 0, ',', '.') . ' ₫'; ?></td>
                                        <td>
                                            <span class="status <?php echo $detail['IsDeleted'] ? 'cancelled' : 'active'; ?>">
                                                <?php echo $detail['IsDeleted'] ? 'Đã hủy' : 'Hoạt động'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <p>
                                                <span class="status <?= $statusClass; ?>"><?= $paymentStatus; ?></span>
                                            </p>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <a href="javascript:history.back()" class="back-button">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</body>

</html>