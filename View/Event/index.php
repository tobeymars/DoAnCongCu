<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../../Model/EventtypeModel.php';
require_once __DIR__ . '/../../Model/VenuesModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';
session_start();
// Ví dụ kiểm tra quyền
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 1) {
    header('Location: ../AccessDenied.php');
    exit();
}
$database = new Database();
$conn = $database->getConnection();
$model = new Event($conn);
$events = $model->getAllEvents()->fetchAll(PDO::FETCH_ASSOC);
// Gọi model riêng
$eventTypeModel = new EventType($conn);
$venueModel = new Venue($conn);

// Lấy dữ liệu
$eventTypes = $eventTypeModel->getAllEventTypes()->fetchAll(PDO::FETCH_ASSOC);
$venues = $venueModel->getAllVenues()->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../shares/adminhd.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sự Kiện | Danh Sách</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            padding-top: 50px;
            padding-left: 275px;
        }

        .container {
            margin-top: 20px;
            padding: 0 25px;
        }

        .page-header {
            padding-bottom: 15px;
            margin-bottom: 25px;
            border-bottom: 1px solid #e3e6f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 0;
            font-size: 1.75rem;
        }

        .btn-add-event {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            font-weight: 600;
            border-radius: 5px;
            padding: 10px 20px;
            transition: all 0.2s;
        }

        .btn-add-event:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 1.25rem;
            color: var(--dark-color);
            font-weight: 700;
            margin: 0;
        }

        .card-body {
            padding: 0;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            color: var(--secondary-color);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            padding: 15px;
            vertical-align: middle;
        }

        .table tbody tr {
            border-bottom: 1px solid #e3e6f0;
            transition: all 0.2s;
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
            transform: scale(1.003);
        }

        .table td {
            padding: 15px;
            color: var(--dark-color);
            vertical-align: middle;
        }

        .event-name {
            font-weight: 700;
            color: var(--primary-color);
        }

        .event-date {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .event-description {
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .badge {
            padding: 8px 12px;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 5px;
            letter-spacing: 0.05em;
        }

        .badge-active {
            background-color: var(--success-color);
            color: white;
        }

        .badge-completed {
            background-color: var(--danger-color);
            color: white;
        }

        .badge-deleted {
            background-color: var(--secondary-color);
            color: white;
        }

        .badge-not-deleted {
            background-color: var(--light-color);
            color: var(--dark-color);
            border: 1px solid #e3e6f0;
        }

        .btn-action {
            padding: 6px 12px;
            font-size: 0.8rem;
            border-radius: 5px;
            margin-right: 5px;
            font-weight: 600;
        }

        .btn-action:last-child {
            margin-right: 0;
        }

        .btn-complete {
            background-color: var(--info-color);
            border-color: var(--info-color);
            color: white;
        }

        .btn-edit {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: #5a5c69;
        }

        .btn-delete {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }

        .btn-restore {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .btn-action i {
            margin-right: 5px;
        }

        .action-cell {
            width: 240px;
        }

        .empty-state {
            padding: 50px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--secondary-color);
            opacity: 0.5;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--secondary-color);
            margin-bottom: 20px;
        }

        @media (max-width: 1200px) {
            .action-cell {
                width: auto;
            }

            .btn-action {
                padding: 5px 10px;
                margin-bottom: 5px;
                display: inline-block;
            }
        }

        @media (max-width: 992px) {
            body {
                padding-left: 0;
            }

            .container {
                padding: 0 15px;
            }

            .table-responsive {
                border-radius: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-calendar-alt"></i> Quản Lý Sự Kiện</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Danh Sách Sự Kiện</h2>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="#" onclick="filterEvents('all')">Tất cả</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterEvents('active')">Còn hiệu lực</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterEvents('completed')">Hoàn thành</a></li>
                        <li><a class="dropdown-item" href="#" onclick="filterEvents('deleted')">Đã xóa</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($events) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sự Kiện</th>
                                    <th>Ngày</th>
                                    <th>Địa Điểm</th>
                                    <th>Loại</th>
                                    <th>Người tạo</th>
                                    <th>Trạng thái</th>
                                    <th>Xóa</th>
                                    <th>Thiết bị</th>
                                    <th class="action-cell">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $event): ?>
                                    <tr class="event-row <?= $event['IsDeleted'] == 1 ? 'deleted' : ($event['status'] == 1 ? 'completed' : 'active') ?>">
                                        <td>
                                            <div class="event-name"><?= htmlspecialchars($event['EventName']) ?></div>
                                        </td>
                                        <td>
                                            <div class="event-date">
                                                <i class="far fa-calendar-alt"></i> <?= (new DateTime($event['EventDate']))->format('d-m-Y') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-danger"></i> <?= htmlspecialchars($event['VenueName']) ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-tag text-primary"></i> <?= htmlspecialchars($event['TypeName']) ?>
                                        </td>
                                        <td>
                                            <i class="fas fa-user text-secondary"></i> <?= htmlspecialchars($event['CreatedBy']) ?>
                                        </td>
                                        <td>
                                            <?php if ($event['status'] == 1): ?>
                                                <span class="badge badge-completed">Hoàn thành</span>
                                            <?php else: ?>
                                                <span class="badge badge-active">Còn hiệu lực</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($event['IsDeleted'] == 1): ?>
                                                <span class="badge badge-deleted">Đã xóa</span>
                                            <?php else: ?>
                                                <span class="badge badge-not-deleted">Chưa xóa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="action-cell">
                                            <?php if ($event['IsDeleted'] == 0): ?>
                                                <button onclick="completeEvent(<?= $event['EventId'] ?>)" class="btn btn-action btn-complete">
                                                    <i class="fas fa-check-circle"></i> Hoàn thành
                                                </button>
                                                <a href="edit.php?id=<?= $event['EventId'] ?>&token=<?= urlencode($token) ?>" class="btn btn-action btn-edit" data-id="<?= $event['EventId'] ?>">
                                                    <i class="fas fa-edit"></i> Sửa
                                                </a>
                                                <a href="delete.php?id=<?= $event['EventId'] ?>&token=<?= urlencode($token) ?>" class="btn btn-action btn-delete" data-id="<?= $event['EventId'] ?>">
                                                    <i class="fas fa-trash-alt"></i> Xóa
                                                </a>
                                            <?php else: ?>
                                                <a href="restore.php?id=<?= $event['EventId'] ?>&token=<?= urlencode($token) ?>" class="btn btn-action btn-restore" data-id="<?= $event['EventId'] ?>">
                                                    <i class="fas fa-trash-restore"></i> Khôi phục
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="/Quanlysukien/View/EquipEvent/detail.php?eventId=<?= $event['EventId'] ?>" class="btn btn-info btn-sm">Xem Thiết Bị</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <h3>Không có sự kiện nào</h3>
                        <p>Bạn chưa có sự kiện nào được tạo. Hãy bắt đầu bằng việc thêm sự kiện mới.</p>
                        <a href="add.php" class="btn btn-add-event">
                            <i class="fas fa-plus-circle"></i> Thêm Sự Kiện Mới
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const token = localStorage.getItem("token");
        console.log("Token từ localStorage: ", token);

        // Xử lý sự kiện cho tất cả nút "Sửa"
        document.querySelectorAll(".btn-edit").forEach(function(btn) {
            btn.addEventListener("click", function(e) {
                e.preventDefault();

                let eventId = this.getAttribute("data-id");
                if (token) {
                    window.location.href = "edit.php?id=" + eventId + "&token=" + encodeURIComponent(token);
                } else {
                    alert("Vui lòng đăng nhập để tiếp tục.");
                }
            });
        });

        // Xử lý sự kiện cho tất cả nút "Xóa"
        document.querySelectorAll(".btn-delete").forEach(function(btn) {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                let eventId = this.getAttribute("data-id");
                if (token) {
                    window.location.href = "delete.php?id=" + eventId + "&token=" + encodeURIComponent(token);
                } else {
                    alert("Vui lòng đăng nhập để tiếp tục.");
                }
            });
        });

        // Xử lý sự kiện cho tất cả nút "Khôi phục"
        document.querySelectorAll(".btn-restore").forEach(function(btn) {
            btn.addEventListener("click", function(e) {
                e.preventDefault();
                let eventId = this.getAttribute("data-id");
                if (token) {
                    window.location.href = "restore.php?id=" + eventId + "&token=" + encodeURIComponent(token);
                } else {
                    alert("Vui lòng đăng nhập để tiếp tục.");
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const userInfo = JSON.parse(localStorage.getItem("userInfo"));
            if (!userInfo || !userInfo.user_id) {
                console.error("Không tìm thấy user_id trong localStorage");
            }
        });

        function completeEvent(eventId) {
            if (confirm("Bạn có chắc chắn muốn đánh dấu sự kiện này là hoàn thành không?")) {
                const userInfo = JSON.parse(localStorage.getItem("userInfo"));
                if (!userInfo || !userInfo.user_id) {
                    alert("Không tìm thấy thông tin người dùng!");
                    return;
                }

                const userId = userInfo.user_id;
                const url = `/quanlysukien/controller/EventController.php?action=complete&id=${eventId}&user_id=${userId}`;

                window.location.href = url;
            }
        }

        function filterEvents(filter) {
            const rows = document.querySelectorAll('.event-row');

            rows.forEach(row => {
                if (filter === 'all') {
                    row.style.display = '';
                } else if (filter === 'active' && row.classList.contains('active')) {
                    row.style.display = '';
                } else if (filter === 'completed' && row.classList.contains('completed')) {
                    row.style.display = '';
                } else if (filter === 'deleted' && row.classList.contains('deleted')) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>

</html>