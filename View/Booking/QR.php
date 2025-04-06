<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/Bookingdetail.php';
require_once __DIR__ . '/../../Model/PaymentModel.php';

// Kiểm tra ID sự kiện
$bookingId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$bookingId) {
    die("ID sự kiện không hợp lệ.");
}
$roleId = isset($_GET['roleId']) ? intval($_GET['roleId']) : null;
$database = new Database();
$conn = $database->getConnection();
$bookingModel = new BookingDetails($conn);
$bookings = $bookingModel->getBookingDetailsByBookingId($bookingId);

// Lấy thông tin booking đầu tiên (vì tất cả đều thuộc cùng một booking)
$booking = $bookings[0] ?? null;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn đặt vé</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/dom-to-image@2.6.0/dist/dom-to-image.min.js"></script>
    <style>
        body {
            padding-top: 60px;
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .booking-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 30px;
        }

        .booking-header {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            margin: -25px -25px 20px -25px;
        }

        .booking-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .booking-id {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 5px 10px;
            border-radius: 5px;
            margin-top: 10px;
            display: inline-block;
        }

        .event-details {
            background-color: #f9f9f9;
            border-left: 4px solid #2575fc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .ticket-details {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .detail-box {
            flex: 1;
            min-width: 200px;
            background-color: #f9f9f9;
            border-left: 4px solid #6a11cb;
            padding: 15px;
            border-radius: 5px;
        }

        .detail-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }

        .location-details {
            background-color: #f9f9f9;
            border-left: 4px solid #11a0cb;
            padding: 15px;
            border-radius: 5px;
        }

        .icon {
            margin-right: 8px;
            color: #6a11cb;
        }

        .actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #2575fc;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1a5dc4;
        }

        .btn-outline {
            border: 1px solid #6a11cb;
            color: #6a11cb;
            background-color: transparent;
        }

        .btn-outline:hover {
            background-color: #6a11cb;
            color: white;
        }

        @media (max-width: 768px) {
            .ticket-details {
                flex-direction: column;
            }
        }

        .preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .preview:hover {
            transform: scale(1.02);
        }
    </style>
</head>

<body>
    <div id="ticket-content" class="container" enctype="multipart/form-data">
        <?php if ($booking): ?>
            <div class="booking-card">
                <div class="booking-header">
                    <h1>Chi tiết đơn đặt vé</h1>
                    <div class="booking-id">
                        <i class="fas fa-ticket-alt icon"></i>Mã vé: #<?php echo $booking['BookingId']; ?>
                    </div>
                </div>
                <!-- Thông tin sự kiện -->
                <div class="event-details">
                    <h2><i class="fas fa-calendar-alt icon"></i><?php echo htmlspecialchars($booking['EventName']); ?></h2>
                    <p><i class="fas fa-clock icon"></i>Ngày tổ chức: <?php echo date('d/m/Y', strtotime($booking['EventDate'])); ?></p>
                    <img class="preview" src="/quanlysukien/images/<?= htmlspecialchars($booking['images']) ?>">
                </div>

                <!-- Thông tin vé -->
                <h3><i class="fas fa-ticket-alt icon"></i>Thông tin vé</h3>
                <div class="ticket-details">
                    <div class="detail-box">
                        <div class="detail-label">Loại vé</div>
                        <div class="detail-value"><?php echo $booking['TicketName']; ?></div>
                    </div>

                    <div class="detail-box">
                        <div class="detail-label">Số lượng</div>
                        <div class="detail-value"><?php echo $booking['Quantity']; ?></div>
                    </div>

                    <div class="detail-box">
                        <div class="detail-label">Ngày đặt</div>
                        <div class="detail-value"><?php echo date('d/m/Y', strtotime($booking['BookingDate'])); ?></div>
                    </div>

                    <div class="detail-box">
                        <div class="detail-label">Tổng tiền</div>
                        <div class="detail-value"><?php echo number_format($booking['Price'] * $booking['Quantity'], 0, ',', '.'); ?> VND</div>
                    </div>
                </div>
                <!-- Thông tin địa điểm -->
                <h3><i class="fas fa-map-marker-alt icon"></i>Địa điểm</h3>
                <div class="location-details">
                    <div class="detail-label">Địa điểm tổ chức</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['VenueName']); ?></div>
                    <div class="detail-label mt-2">Địa chỉ</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['Address']); ?></div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle icon"></i>Không tìm thấy thông tin đặt vé!
            </div>
        <?php endif; ?>
    </div>
</body>

</html>