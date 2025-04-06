<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/TickettypeModel.php';
require_once __DIR__ . '/../../Model/EventModel.php';

$database = new Database();
$conn = $database->getConnection();

// Khởi tạo model
$ticketTypeModel = new TicketType($conn);
$eventModel = new Event($conn);

// Lấy dữ liệu
$ticketTypes = $ticketTypeModel->getAllTicketTypes()->fetchAll(PDO::FETCH_ASSOC);
$events = $eventModel->getAllEvents()->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh Sách Loại Vé</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --success-color: #78e08f;
            --danger-color: #eb4d4b;
            --warning-color: #f6b93b;
            --light-color: #f5f6fa;
            --dark-color: #2f3640;
        }

        body {
            background-color: #f1f2f6;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            padding-top: 80px;
            padding-left: 275px;
        }

        .page-header {
            background: var(--primary-color);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-weight: 600;
            font-size: 1.8rem;
            margin: 0;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-add {
            background-color: var(--accent-color);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            background-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-add i {
            margin-right: 8px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: rgba(60, 99, 130, 0.05);
            color: var(--primary-color);
            font-weight: 600;
            border-top: none;
            padding: 15px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(130, 204, 221, 0.1);
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-color: rgba(0, 0, 0, 0.03);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-block;
            width: 130px;
        }

        .status-active {
            background-color: rgba(120, 224, 143, 0.15);
            color: #27ae60;
        }

        .status-inactive {
            background-color: rgba(235, 77, 75, 0.15);
            color: #c0392b;
        }

        .btn-action {
            padding: 6px 15px;
            border-radius: 8px;
            margin: 0 3px;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-edit {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: white;
        }

        .btn-edit:hover {
            background-color: #e59f1a;
            border-color: #e59f1a;
            color: white;
        }

        .btn-delete {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }

        .btn-delete:hover {
            background-color: #d44a47;
            border-color: #d44a47;
            color: white;
        }

        .btn-restore {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .btn-restore:hover {
            background-color: #68c97d;
            border-color: #68c97d;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }

        .card-footer {
            background-color: white;
            padding: 15px 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 767.98px) {
            .table-responsive {
                border: none;
            }

            .page-header {
                border-radius: 0;
                margin-bottom: 20px;
            }

            .card {
                border-radius: 10px;
                margin-bottom: 20px;
            }

            .btn-action {
                padding: 5px 10px;
                font-size: 0.8rem;
                display: inline-block;
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="page-title"><i class="fa-solid fa-ticket"></i> Quản Lý Loại Vé</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"> Danh Sách Loại Vé</h5>
                <a href="create.php" class="btn btn-primary btn-add">
                    <i class="fas fa-plus"></i> Thêm Loại Vé
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Loại Vé</th>
                            <th>Giá</th>
                            <th>Số Lượng</th>
                            <th>Sự Kiện</th>
                            <th>Trạng thái</th>
                            <th>Xóa</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($ticketTypes) > 0): ?>
                            <?php foreach ($ticketTypes as $ticket): ?>
                                <tr>
                                    <td>
                                        <span class="fw-medium"><?= htmlspecialchars($ticket['TicketName']) ?></span>
                                    </td>
                                    <td><?= number_format($ticket['Price'], 0, ',', '.') ?> VND</td>
                                    <td><?= htmlspecialchars($ticket['Quantity']) ?></td>
                                    <td>
                                        <?php
                                        foreach ($events as $event) {
                                            if ($event['EventId'] == $ticket['EventId']) {
                                                echo htmlspecialchars($event['EventName']);
                                                break;
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= $ticket['Quantity'] > 0
                                            ? '<span class="status-badge status-active"><i class="fas fa-check-circle me-1"></i> Còn bán</span>'
                                            : '<span class="status-badge status-inactive"><i class="fas fa-times-circle me-1"></i> Ngừng bán</span>'; ?>
                                    </td>
                                    <td>
                                        <?= $ticket['IsDeleted'] == 0
                                            ? '  <span class="status-badge status-active">
                                                <i class="fas fa-check-circle me-1"></i> Còn sử dụng
                                            </span>'
                                            : '<span class="status-badge status-inactive">
                                                <i class="fas fa-times-circle me-1"></i> Hết sử dụng
                                            </span>'; ?>
                                    </td>
                                    <td>
                                        <?php if ($ticket['IsDeleted'] == 0): ?>
                                            <a href="edit.php?id=<?= $ticket['TicketTypeId'] ?>" class="btn btn-action btn-edit">
                                                <i class="fas fa-edit me-1"></i> Sửa</a>
                                            <a href="delete.php?id=<?= $ticket['TicketTypeId'] ?>" class="btn btn-action btn-delete">
                                                <i class="fas fa-trash-alt me-1"></i> Xóa</a>
                                        <?php else: ?>
                                            <a href="restore.php?id=<?= $ticket['TicketTypeId'] ?>" class="btn btn-action btn-restore">
                                                <i class="fas fa-undo me-1"></i> Khôi phục</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="fas fa-folder-open fa-3x mb-3"></i>
                                    <p>Chưa có loại vé nào. Hãy thêm loại vé mới!</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hiệu ứng cho nút
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('mousedown', function() {
                this.style.transform = 'scale(0.98)';
            });
            button.addEventListener('mouseup', function() {
                this.style.transform = '';
            });
        });
    </script>
</body>

</html>