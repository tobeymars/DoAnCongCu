<?php
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventModel.php';

$database = new Database();
$conn = $database->getConnection();
$Eventmodel = new Event($conn);

// Ensure user is logged in to proceed with booking
if (!empty($user_id)) {
    // Fetch user details from database (you already have this part)
}

// Lấy ID sự kiện từ URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$event = null;
$tickets = [];

if ($event_id > 0) {
    $event = $Eventmodel->getEventById($event_id);

    // Truy vấn danh sách vé từ bảng tickettypes
    $stmt = $conn->prepare("SELECT TicketTypeId, TicketName, Price, Quantity FROM tickettypes WHERE EventId = ? AND IsDeleted = 0");
    $stmt->execute([$event_id]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($tickets as &$ticket) {
        $stmt_check_exist = $conn->prepare("
            SELECT bd.BookingDetailId 
            FROM bookings b
            INNER JOIN bookingdetails bd ON b.BookingId = bd.BookingId
            WHERE b.UserId = ? AND b.EventId = ? AND bd.TicketTypeId = ? AND b.IsDeleted = 0 AND bd.IsDeleted = 0
        ");
        $stmt_check_exist->execute([$user_id, $event_id, $ticket['TicketTypeId']]);
        $ticket['has_booked'] = $stmt_check_exist->rowCount() > 0;
    }
    unset($ticket); // good practice
}

if (!$event) {
    die("Sự kiện không tồn tại hoặc đã bị xóa!");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Sự Kiện</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../Home/event_details.css">
    <link rel="stylesheet" href="../Home/form_tickket.css">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg p-4">
            <h1 class="text-center"><?= htmlspecialchars($event['EventName']) ?></h1>

            <!-- Hình ảnh sự kiện -->
            <?php if (!empty($event['ImageUrl'])): ?>
                <img src="<?= htmlspecialchars($event['ImageUrl']) ?>" class="event-image" alt="<?= htmlspecialchars($event['EventName']) ?>">
            <?php endif; ?>

            <!-- Thông tin sự kiện -->
            <div class="row">
                <div class="col-md-8">
                    <div class="event-info">
                        <p><strong>📜 Mô tả:</strong> <span class="text-white"><?= nl2br(htmlspecialchars($event['Description'])) ?></span></p>
                        <p><strong>📅 Ngày tổ chức:</strong> <span class="text-white"><?= (new DateTime($event['EventDate']))->format('d-m-Y H:i') ?></span></p>
                        <p><strong>📍 Địa điểm:</strong> <span class="text-white"><?= htmlspecialchars($event['VenueName'] ?? 'Không rõ') ?></span></p>
                        <p><strong>🔖 Loại sự kiện:</strong> <span class="text-white"><?= htmlspecialchars($event['TypeName'] ?? 'Không rõ') ?></span></p>
                        <p><strong>👤 Người tổ chức:</strong> <span class="text-white"><?= htmlspecialchars($event['CreatedBy'] ?? 'Ẩn danh') ?></span></p>
                        <p><strong>📢 Trạng thái:</strong>
                            <?= ($event['status'] == 1)
                                ? '<span class="text-success">Đang diễn ra</span>'
                                : '<span class="text-warning">Sắp diễn ra</span>' ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <button id="showBookingForm" class="btn btn-primary btn-lg w-100 mb-3">Đặt vé ngay</button>
                    <a href="javascript:history.back()" class="btn btn-outline-secondary w-100">⬅ Quay lại</a>
                </div>
            </div>

            <!-- Danh sách vé -->
            <h3 class="mt-4">🎟 Loại Vé & Giá</h3>
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Loại Vé</th>
                        <th>Giá (VNĐ)</th>
                        <th>Số Lượng Còn</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?= htmlspecialchars($ticket['TicketName']) ?></td>
                            <td><?= number_format($ticket['Price'], 0, ',', '.') ?> đ</td>
                            <td><?= htmlspecialchars($ticket['Quantity']) ?></td>
                            <td>
                                <?php if ($ticket['has_booked']): ?>
                                    <span style="color: red;">Bạn đã đặt vé này</span>
                                <?php else: ?>
                                    <span style="color: green;">Chưa đặt</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form đặt vé -->
    <?php if (!isset($_SESSION['user_id'])): ?>
        <p class='text-danger text-center mt-4'>⚠ Vui lòng <a href="login.php">đăng nhập</a> để đặt vé!</p>
    <?php else: ?>
        <div id="bookingOverlay" class="booking-overlay" style="display: none;">
            <div id="bookingForm" class="booking-form">
                <button id="closeBookingForm" class="close-button">&times;</button>
                <form action="process_booking.php" method="POST" class="mt-4">
                    <input type="hidden" name="event_id" value="<?= $event_id ?>">
                    <h5 class="mt-4">📅 Thông tin người dùng</h5>
                    <!-- Mã Người Dùng -->
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Mã Người Dùng:</label>
                        <input type="number" name="user_id" id="user_id" class="form-control" value="<?= $_SESSION['user_id'] ?>" readonly>
                    </div>

                    <!-- Họ và Tên -->
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Họ và Tên:</label>
                        <input type="text" name="full_name" id="full_name" class="form-control" value="<?= htmlspecialchars($_SESSION['full_name']) ?>" readonly>
                    </div>

                    <!-- Tên Người Dùng -->
                    <div class="mb-3">
                        <label for="user_name" class="form-label">Tên Người Dùng:</label>
                        <input type="text" name="user_name" id="user_name" class="form-control" value="<?= htmlspecialchars($_SESSION['username']) ?>" readonly>
                    </div>

                    <!-- Thông tin sự kiện -->
                    <h5 class="mt-4">📅 Thông Tin Sự Kiện</h5>
                    <p><strong>Tên sự kiện:</strong> <?= htmlspecialchars($event['EventName']) ?></p>
                    <p><strong>Ngày tổ chức:</strong> <?= (new DateTime($event['EventDate']))->format('d-m-Y H:i') ?></p>

                    <h4>🎟 Chọn Loại Vé</h4>
                    <?php foreach ($tickets as $ticket): ?>
                        <?php if (!$ticket['has_booked']): ?>
                        <div class="mb-2">
                            <label>
                                <input type="checkbox" name="ticket_types[<?= $ticket['TicketTypeId'] ?>]" value="<?= $ticket['TicketTypeId'] ?>">
                                <?= htmlspecialchars($ticket['TicketName']) ?> - <?= number_format($ticket['Price'], 0, ',', '.') ?> VNĐ (Còn: <?= $ticket['Quantity'] ?>)
                            </label>
                            <input type="number" name="ticket_quantities[<?= $ticket['TicketTypeId'] ?>]" class="form-control mt-1" placeholder="Số lượng" min="1" max="<?= $ticket['Quantity'] ?>">
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-success booking-button">Đặt vé</button>
                </form>
            </div>
        </div>
    <?php endif; ?>


    <script>
        //Ẩn hiện form đặt vé
        document.getElementById("showBookingForm").addEventListener("click", function() {
            document.getElementById("bookingOverlay").style.display = "flex";
        });
        document.getElementById("closeBookingForm").addEventListener("click", function() {
            document.getElementById("bookingOverlay").style.display = "none";
        });

        //Xử lý đặt vé trong form
        document.querySelector("form").addEventListener("submit", function(event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch("../Home/process_booking.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.success);
                        location.reload();
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => console.error("Lỗi:", error));
        });
    </script>
    <!-- <script src="../Home/event_details.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>