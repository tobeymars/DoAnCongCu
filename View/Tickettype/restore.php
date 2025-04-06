<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/TicketTypeModel.php';
require_once __DIR__ . '/../../Model/EventModel.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID loại vé.");
}

$database = new Database();
$conn = $database->getConnection();
$model = new TicketType($conn);
$ticket = $model->getTicketTypeById($_GET['id']);
$eventModel = new Event($conn);
$events = $eventModel->getAllEvents()->fetchAll(PDO::FETCH_ASSOC);

if (!$ticket) {
    die("Loại vé không tồn tại hoặc chưa bị xóa.");
}

// Tìm thông tin sự kiện
$eventName = '';
foreach ($events as $event) {
    if ($event['EventId'] == $ticket['EventId']) {
        $eventName = $event['EventName'];
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Khôi Phục Loại Vé</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            padding: 50px 0 0 275px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 10px;
            border: none;
        }
        .card-header1 {
            background-color: #f8f9fa;
            border-radius: 10px 10px 0 0 !important;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
        }
        .ticket-info {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
        }
        .btn {
            border-radius: 5px;
            padding: 8px 20px;
            font-weight: 500;
        }
        .btn-success {
            background-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .badge {
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .ticket-name {
            font-size: 1.2rem;
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-header1 d-flex align-items-center">
                        <i class="fas fa-ticket-alt me-2"></i>
                        <h2 class="m-0 fs-4">Xác Nhận Khôi Phục Loại Vé</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Bạn đang khôi phục loại vé đã bị xóa trước đó. Vui lòng xem lại thông tin và xác nhận.
                        </div>
                        
                        <div class="ticket-info">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-tag me-2"></i>Loại Vé:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="ticket-name"><?= htmlspecialchars($ticket['TicketName']) ?></span>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-money-bill me-2"></i>Giá Vé:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="badge bg-primary"><?= number_format($ticket['Price'], 0, ',', '.') ?> VNĐ</span>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-sort-amount-up me-2"></i>Số Lượng:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?= $ticket['Quantity'] ?> vé
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-calendar-event me-2"></i>Sự kiện:</strong>
                                </div>
                                <div class="col-md-8">
                                    <a href="../events/view.php?id=<?= $ticket['EventId'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($eventName) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <form action="../../controller/TicketTypeController.php?action=restore" method="POST" id="restoreForm">
                            <input type="hidden" name="TicketTypeId" value="<?= $ticket['TicketTypeId'] ?>">
                            
                            <div class="d-flex justify-content-end btn-group mt-3">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Hủy
                                </a>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#confirmModal">
                                    <i class="fas fa-undo me-1"></i> Khôi Phục
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="index.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách loại vé
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal xác nhận -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Xác nhận khôi phục</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn khôi phục loại vé <strong><?= htmlspecialchars($ticket['TicketName']) ?></strong> không?
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Lưu ý: Loại vé này sẽ trở lại trạng thái hoạt động và có thể được mua ngay lập tức.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-success" onclick="document.getElementById('restoreForm').submit();">
                        <i class="fas fa-check me-1"></i> Xác nhận khôi phục
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>