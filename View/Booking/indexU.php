<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . "/../../Model/BookingModel.php";
require_once __DIR__ . '/../../config/db.php';
$database = new Database();
$conn = $database->getConnection();
$bookingModel = new Booking($conn);

$userId = isset($_GET['Id']) ? $_GET['Id'] : null;

if (!$userId) {
    echo '<script type="text/javascript">';
    echo 'alert("Bạn cần đăng nhập để xem các đơn đặt!");'; // Hiển thị thông báo
    echo 'window.location.href = "../../View/Users/register.php";'; // Chuyển hướng đến trang đăng ký sau khi thông báo
    echo '</script>';
    exit();
}

// Lấy danh sách đặt vé của người dùng
$bookings = $bookingModel->getUserBookings($userId);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách đặt vé</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding-top: 80px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        header {
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            color: #333;
            font-size: 28px;
        }

        table {
            width: 80%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: #f1f1f1;
            transition: 0.3s;
        }

        td {
            color: #333;
        }

        .status {
            font-weight: bold;
            padding: 6px 12px;
            border-radius: 5px;
        }

        .pending {
            background: #ffcc00;
            color: #333;
        }

        .confirmed {
            background: #28a745;
            color: white;
        }

        .canceled {
            background: #dc3545;
            color: white;
        }

        .btn-back {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-back:hover {
            background: #0056b3;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            min-width: 90px;
        }

        .status-pending {
            background-color: rgba(246, 194, 62, 0.1);
            color: var(--warning);
        }

        .status-confirmed {
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--success);
        }

        .status-cancelled {
            background-color: rgba(231, 74, 59, 0.1);
            color: var(--danger);
        }

        /* Thiết kế cho trạng thái "Chưa thanh toán" */
        .status-unknown {
            background-color: #f39c12;
            /* Màu vàng cam nổi bật */
            color: #fff;
            /* Chữ trắng để tương phản */
        }
    </style>
</head>

<body>
    <header>
        <h2>Danh sách đặt vé của bạn</h2>
    </header>

    <table>
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Sự kiện</th>
                <th>Ngày</th>
                <th>Trạng thái</th>
                <th>Chi tiết</th>
                <th>Thanh toán</th>
                <th>Hủy đơn</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= htmlspecialchars($booking["BookingId"]) ?></td>
                        <td><?= htmlspecialchars($booking["EventName"]) ?></td>
                        <td><?= htmlspecialchars($booking["BookingDate"]) ?></td>
                        <td>
                            <?php
                            $statusBadges = [
                                'Pending' => '<span class="status-badge status-pending"><i class="fas fa-clock me-1"></i> Chờ xác nhận</span>',
                                'Confirmed' => '<span class="status-badge status-confirmed"><i class="fas fa-check-circle me-1"></i> Đã xác nhận</span>',
                                'Cancelled' => '<span class="status-badge status-cancelled"><i class="fas fa-times-circle me-1"></i> Đã hủy</span>',
                            ];
                            echo $statusBadges[$booking['Status']] ?? '<span class="status-badge status-unknown"><i class="fas fa-money-bill me-1"></i> Chưa thanh toán</span>';
                            ?>
                        </td>
                        <td> <a href="detail.php?action=getById&id=<?= $booking['BookingId'] ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                        <td> <a href="/Quanlysukien/view/payment/detail.php?id=<?= $booking['BookingId'] ?>" class="btn btn-sm btn-outline-primary" title="Thanh toán">
                                <!-- Icon thanh toán -->
                                <i class="fas fa-credit-card"></i>
                            </a>
                        </td>
                        <td>
                            <a href="javascript:void(0);" onclick="cancelBooking(<?= (int)$booking['BookingId'] ?>, '<?= $booking['Status'] ?>')" class="btn btn-sm btn-outline-danger" title="Hủy đơn">
                                <i class="fas fa-times " style="color: #dc3545;"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Không có vé nào được đặt!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a id="backLink" href="#" class="btn btn-secondary btn-back" style="margin-top: 20px;">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
    <!-- Thêm SweetAlert2 từ CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let userInfo = localStorage.getItem("userInfo");
            let token = localStorage.getItem("token");
            let url = "/Quanlysukien/View/Users/detail.php?token=" + encodeURIComponent(token);

            // Gắn URL vào thẻ <a>
            document.getElementById("backLink").href = url;
            // Kiểm tra xem userInfo có tồn tại không và nếu có thì lấy RoleId
            if (userInfo) {
                try {
                    let userData = JSON.parse(userInfo);
                    let roleId = userData.RoleId; // Giả sử RoleId có trong userInfo

                    // Tìm tất cả các link và thêm RoleId vào URL
                    let detailLinks = document.querySelectorAll('a[href*="detail.php?action=getById"]');
                    detailLinks.forEach(link => {
                        let url = new URL(link.href);
                        url.searchParams.set('roleId', roleId); // Thêm roleId vào URL
                        link.href = url.toString(); // Cập nhật lại href của link
                    });

                } catch (error) {
                    console.error("Lỗi khi phân tích userInfo:", error);
                }
            }

        });

        function cancelBooking(bookingId, status) {
            // Kiểm tra trạng thái của booking trước khi hủy
            if (status === 'Confirmed') {
                Swal.fire({
                    title: 'Thông báo!',
                    text: 'Không thể hủy đơn vì đơn này đã thanh toán (Confirmed).',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return; // Dừng lại nếu không thể hủy vì đã thanh toán
            }

            if (status === 'Pending') {
                Swal.fire({
                    title: 'Thông báo!',
                    text: 'Không thể hủy đơn vì đơn này chưa xác nhận (Pending).',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return; // Dừng lại nếu không thể hủy vì chưa xác nhận
            }

            if (status === 'Cancelled') {
                Swal.fire({
                    title: 'Thông báo!',
                    text: 'Không thể hủy đơn vì đơn này đã bị hủy trước đó (Cancelled).',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return; // Dừng lại nếu không thể hủy vì đã hủy trước đó
            }

            // Nếu trạng thái hợp lệ, yêu cầu xác nhận hủy
            if (confirm("Bạn có chắc chắn muốn hủy đơn này không?")) {
                fetch(`/Quanlysukien/Controller/BookingController.php?action=cancel`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            'BookingId': bookingId,
                        }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Lỗi máy chủ hoặc trang không tồn tại');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Thông báo khi hủy đơn thành công
                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Đơn đã được hủy thành công.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload(); // Tải lại trang để cập nhật trạng thái đơn
                            });
                        } else {
                            // Thông báo khi có lỗi
                            Swal.fire({
                                title: 'Thông báo!',
                                text: data.error || 'Không thể hủy đơn.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Có lỗi xảy ra:', error);
                        // Thông báo lỗi khi có sự cố trong quá trình thực thi
                        Swal.fire({
                            title: 'Có lỗi xảy ra!',
                            text: 'Có lỗi xảy ra khi hủy đơn.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            }
        }
    </script>
</body>
<?php include '../shares/footer.php'; ?>

</html>