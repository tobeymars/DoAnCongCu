<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/BookingModel.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../../Model/UserModel.php';

$database = new Database();
$conn = $database->getConnection();
$bookingModel = new Booking($conn);
$bookings = $bookingModel->getAllBookings()->fetchAll(PDO::FETCH_ASSOC);

$userModel = new UserModel($conn);
$eventModel = new Event($conn);

// Phân loại trạng thái đặt vé để hiển thị thống kê
$stats = [
    'total' => count($bookings),
    'pending' => 0,
    'confirmed' => 0,
    'cancelled' => 0,
    'deleted' => 0
];

foreach ($bookings as $booking) {
    if ($booking['Status'] == 'Pending') $stats['pending']++;
    if ($booking['Status'] == 'Confirmed') $stats['confirmed']++;
    if ($booking['Status'] == 'Cancelled') $stats['cancelled']++;
    if ($booking['IsDeleted'] == 1) $stats['deleted']++;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đặt Vé</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4e73df;
            --success: #1cc88a;
            --info: #36b9cc;
            --warning: #f6c23e;
            --danger: #e74a3b;
            --secondary: #858796;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }

        body {
            background-color: #f8f9fa;
            font-family: 'Nunito', 'Segoe UI', Roboto, Arial, sans-serif;
            color: #444;
            padding: 50px 0 0 275px;
        }

        .page-header {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-title {
            font-size: 26px;
            color: var(--dark);
            margin: 0;
            font-weight: 700;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            text-align: center;
            transition: transform 0.2s;
            border-left: 4px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.primary {
            border-color: var(--primary);
        }

        .stat-card.success {
            border-color: var(--success);
        }

        .stat-card.warning {
            border-color: var(--warning);
        }

        .stat-card.danger {
            border-color: var(--danger);
        }

        .stat-card h3 {
            font-size: 16px;
            color: var(--dark);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
        }

        .stat-card .icon {
            margin-bottom: 10px;
            font-size: 28px;
        }

        .stat-card.primary .icon {
            color: var(--primary);
        }

        .stat-card.success .icon {
            color: var(--success);
        }

        .stat-card.warning .icon {
            color: var(--warning);
        }

        .stat-card.danger .icon {
            color: var(--danger);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #edf2f9;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 18px;
            color: var(--dark);
            margin: 0;
            font-weight: 600;
        }

        .table-responsive {
            padding: 0;
        }

        .card-body {
            padding: 0;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            border-top: none;
            background-color: #f8f9fc;
            padding: 12px 15px;
            font-weight: 600;
            color: var(--dark);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            color: #5a5c69;
            font-size: 14px;
            border-color: #edf2f9;
        }

        .table tr:hover {
            background-color: #f8f9fc;
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

        .status-deleted {
            background-color: rgba(133, 135, 150, 0.1);
            color: var(--secondary);
        }

        .status-active {
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--success);
        }

        .btn {
            border-radius: 5px;
            padding: 7px 15px;
            font-size: 13px;
            font-weight: 600;
        }

        .btn-sm {
            padding: 5px 12px;
            font-size: 12px;
        }

        .btn-info {
            background-color: var(--info);
            border-color: var(--info);
        }

        .btn-danger {
            background-color: var(--danger);
            border-color: var(--danger);
        }

        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .search-box {
            position: relative;
            margin-bottom: 15px;
        }

        .search-box input {
            padding-left: 40px;
            border-radius: 30px;
            border: 1px solid #edf2f9;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 10px;
            color: var(--secondary);
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 15px;
            background-color: white;
            border: 1px solid #edf2f9;
            color: var(--secondary);
        }

        .filter-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state-icon {
            font-size: 50px;
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }

        /* Để cải thiện responsive */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-cards {
                grid-template-columns: 1fr 1fr;
            }

            .table-responsive {
                overflow-x: auto;
            }
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
    <div class="container py-4">
        <div class="page-header">
            <h1 class="page-title"><i class="fa-solid fa-file-invoice"></i> Quản Lý Đặt Vé</h1>
        </div>

        <div class="stats-cards">
            <div class="stat-card primary">
                <div class="icon"><i class="fa-solid fa-file-invoice"></i></div>
                <h3>Tổng Đơn Đặt</h3>
                <div class="value"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card warning">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <h3>Chờ Xác Nhận</h3>
                <div class="value"><?= $stats['pending'] ?></div>
            </div>
            <div class="stat-card success">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <h3>Đã Xác Nhận</h3>
                <div class="value"><?= $stats['confirmed'] ?></div>
            </div>
            <div class="stat-card danger">
                <div class="icon"><i class="fas fa-times-circle"></i></div>
                <h3>Đã Hủy/Xóa</h3>
                <div class="value"><?= $stats['cancelled'] + $stats['deleted'] ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-list me-2"></i>Danh Sách Đặt Vé</h5>
                <div class="d-flex">
                    <button class="btn btn-sm btn-outline-primary me-2" id="refreshBtn">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-1"></i> Lọc
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item filter-option" href="#" data-filter="all">Tất cả</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="pending">Chờ xác nhận</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="confirmed">Đã xác nhận</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="cancelled">Đã hủy</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="deleted">Đã xóa</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="active">Chưa xóa</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="p-3">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm theo tên người dùng, sự kiện...">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Người Đặt</th>
                                <th>Sự Kiện</th>
                                <th>Ngày Đặt</th>
                                <th>Trạng Thái</th>
                                <th>Tình Trạng</th>
                                <th class="text-center">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody id="bookingTableBody">
                            <?php if (count($bookings) > 0): ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr
                                        data-status="<?= strtolower($booking['Status']) ?>"
                                        data-deleted="<?= $booking['IsDeleted'] == 1 ? 'deleted' : 'active' ?>">
                                        <td>#<?= $booking['BookingId'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2 bg-light rounded-circle text-center" style="width: 32px; height: 32px; line-height: 32px;">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <?= htmlspecialchars($booking['FullName']) ?>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($booking['EventName']) ?></td>
                                        <td><?= (new DateTime($booking['BookingDate']))->format('d/m/Y H:i') ?></td>
                                        <td>
                                            <?php
                                            $statusBadges = [
                                                'Pending' => '<span class="status-badge status-pending"><i class="fas fa-clock me-1"></i>Chờ xác nhận</span>',
                                                'Confirmed' => '<span class="status-badge status-confirmed"><i class="fas fa-check-circle me-1"></i>Đã xác nhận</span>',
                                                'Cancelled' => '<span class="status-badge status-cancelled"><i class="fas fa-times-circle me-1"></i>Đã hủy</span>',
                                            ];
                                            echo $statusBadges[$booking['Status']] ?? '<span class="status-badge status-unknown"><i class="fas fa-money-bill me-1"></i> Chưa thanh toán</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <?= $booking['IsDeleted'] == 1
                                                ? '<span class="status-badge status-deleted"><i class="fas fa-trash-alt me-1"></i>Đã xóa</span>'
                                                : '<span class="status-badge status-active"><i class="fas fa-check me-1"></i>Hoạt động</span>'; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($booking['IsDeleted'] == 0): ?>
                                                    <a href="detail.php?action=getById&id=<?= $booking['BookingId'] ?>" class="btn btn-sm btn-outline-primary" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($booking['Status'] === 'Pending'): ?>
                                                        <button class="btn btn-sm btn-info btn-update" data-id="<?= $booking['BookingId'] ?>" title="Xác nhận đặt vé">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <a href="delete.php?id=<?= $booking['BookingId'] ?>" class="btn btn-sm btn-danger" title="Xóa đặt vé">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="restore.php?id=<?= $booking['BookingId'] ?>" class="btn btn-sm btn-success" title="Khôi phục đặt vé">
                                                        <i class="fas fa-trash-restore"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="fas fa-ticket-alt"></i>
                                            </div>
                                            <h5>Không có dữ liệu đặt vé</h5>
                                            <p>Chưa có đơn đặt vé nào được tạo.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="toast-container"></div>
    </div>

    <!-- Modal xác nhận -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận thao tác</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="confirmMessage">Bạn có chắc chắn muốn thực hiện thao tác này?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-primary" id="confirmAction">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Xử lý nút làm mới
            document.getElementById('refreshBtn').addEventListener('click', function() {
                location.reload();
            });

            // Xử lý tìm kiếm
            document.getElementById('searchInput').addEventListener('keyup', function() {
                const searchValue = this.value.toLowerCase();
                const rows = document.querySelectorAll('#bookingTableBody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Xử lý lọc
            document.querySelectorAll('.filter-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    e.preventDefault();
                    const filter = this.dataset.filter;
                    const rows = document.querySelectorAll('#bookingTableBody tr');

                    rows.forEach(row => {
                        if (filter === 'all') {
                            row.style.display = '';
                        } else if (filter === 'deleted' || filter === 'active') {
                            row.style.display = row.dataset.deleted === filter ? '' : 'none';
                        } else {
                            row.style.display = row.dataset.status === filter ? '' : 'none';
                        }
                    });

                    // Cập nhật tiêu đề dropdown
                    document.querySelector('#dropdownMenuButton').innerHTML =
                        `<i class="fas fa-filter me-1"></i> ${this.textContent}`;
                });
            });

            // Xử lý nút xác nhận đặt vé
            document.querySelectorAll(".btn-update").forEach(button => {
                button.addEventListener("click", function() {
                    const bookingId = this.getAttribute("data-id");
                    const row = this.closest('tr');

                    fetch("/quanlysukien/Controller/BookingController.php?action=update", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: "BookingId=" + bookingId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Thay đổi trạng thái trực tiếp trên UI
                                row.querySelector('td:nth-child(5)').innerHTML =
                                    '<span class="status-badge status-confirmed"><i class="fas fa-check-circle me-1"></i>Đã xác nhận</span>';

                                // Cập nhật data-status
                                row.dataset.status = 'confirmed';

                                // Xóa nút xác nhận
                                this.remove();

                                // Hiển thị thông báo
                                showToast('Xác nhận đặt vé thành công!', 'success');

                                // Cập nhật thống kê
                                updateStats();
                            } else {
                                showToast(data.error || 'Có lỗi xảy ra!', 'danger');
                            }
                        })
                        .catch(error => {
                            console.error("Lỗi:", error);
                            showToast('Có lỗi xảy ra khi xử lý yêu cầu!', 'danger');
                        });
                });
            });
        });

        // Hàm tạo thông báo toast
        function showToast(message, type = 'info') {
            const toastContainer = document.querySelector('.toast-container');
            const toastId = 'toast-' + Date.now();

            const toast = document.createElement('div');
            toast.className = `toast show bg-${type} text-white`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            toast.id = toastId;

            toast.innerHTML = `
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">Thông báo</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            `;

            toastContainer.appendChild(toast);

            // Tự động đóng sau 5 giây
            setTimeout(() => {
                const toastElement = document.getElementById(toastId);
                if (toastElement) {
                    toastElement.remove();
                }
            }, 5000);
        }

        // Hàm cập nhật số liệu thống kê
        function updateStats() {
            const stats = {
                total: document.querySelectorAll('#bookingTableBody tr').length,
                pending: document.querySelectorAll('#bookingTableBody tr[data-status="pending"]').length,
                confirmed: document.querySelectorAll('#bookingTableBody tr[data-status="confirmed"]').length,
                cancelled: document.querySelectorAll('#bookingTableBody tr[data-status="cancelled"]').length,
                deleted: document.querySelectorAll('#bookingTableBody tr[data-deleted="deleted"]').length
            };

            document.querySelector('.stat-card.primary .value').textContent = stats.total;
            document.querySelector('.stat-card.warning .value').textContent = stats.pending;
            document.querySelector('.stat-card.success .value').textContent = stats.confirmed;
            document.querySelector('.stat-card.danger .value').textContent = stats.cancelled + stats.deleted;
        }
        document.addEventListener("DOMContentLoaded", function() {
            let userInfo = localStorage.getItem("userInfo");

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
    </script>
</body>

</html>