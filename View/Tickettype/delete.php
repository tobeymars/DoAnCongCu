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
    die("Loại vé không tồn tại.");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Xóa Loại Vé</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --danger-color: #e74c3c;
            --danger-hover: #c0392b;
            --light-bg: #f8f9fa;    
            --light-text: #7f8c8d;
            --border-color: #eaeaea;
            
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            padding: 50px 0 0 275px;
        }

        .page-header {
            background: linear-gradient(135deg, var(--danger-color) 0%, #e57373 100%);
            color: white;
            padding: 25px 0;
            margin-bottom: 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-weight: 600;
            margin: 0;
            font-size: 2rem;
            text-align: center;
        }

        .delete-container {
            max-width: 700px;
            margin: 0 auto 30px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }

        .delete-header {
            background-color: rgba(231, 76, 60, 0.1);
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .delete-header h3 {
            color: var(--danger-color);
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }

        .delete-header h3 i {
            margin-right: 12px;
        }

        .delete-body {
            padding: 30px;
        }

        .ticket-card {
            background-color: rgba(52, 152, 219, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid var(--primary-color);
        }

        .ticket-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .ticket-info-item {
            flex: 1 0 calc(50% - 15px);
            margin-bottom: 15px;
        }

        .ticket-info-label {
            font-size: 0.85rem;
            color: var(--light-text);
            margin-bottom: 5px;
        }

        .ticket-info-value {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .ticket-name {
            color: var(--primary-color);
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }

        .ticket-name i {
            margin-right: 12px;
        }

        .warning-message {
            background-color: rgba(231, 76, 60, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            display: flex;
            align-items: center;
        }

        .warning-message i {
            color: var(--danger-color);
            font-size: 1.5rem;
            margin-right: 15px;
        }

        .warning-message p {
            margin: 0;
            font-weight: 500;
            color: var(--dark-text);
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            padding: 12px 20px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-danger:hover {
            background-color: var(--danger-hover);
            border-color: var(--danger-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
        }

        .btn-secondary {
            background-color: #95a5a6;
            border-color: #95a5a6;
            padding: 12px 20px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d;
            border-color: #7f8c8d;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-color);
            text-decoration: none;
            margin-bottom: 15px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .back-link:hover {
            color: var(--secondary-color);
        }

        .back-link i {
            margin-right: 8px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .price-display {
            font-family: 'Arial', sans-serif;
            font-size: 1.3rem;
            color: var(--danger-color);
            font-weight: 700;
        }

        .event-info {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .event-icon {
            background-color: rgba(52, 152, 219, 0.1);
            height: 36px;
            width: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }

        .event-icon i {
            color: var(--primary-color);
        }

        .event-name {
            font-weight: 500;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .ticket-info-item {
                flex: 1 0 100%;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .delete-body {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div class="container">
            <h1 class="page-title"><i class="fas fa-trash-alt me-2"></i> Xác Nhận Xóa Loại Vé</h1>
        </div>
    </div>

    <div class="container">
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách loại vé
        </a>

        <div class="delete-container">
            <div class="delete-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Xác nhận xóa loại vé</h3>
            </div>

            <div class="delete-body">
                <div class="ticket-card">
                    <h4 class="ticket-name">
                        <i class="fas fa-ticket-alt"></i>
                        <?= htmlspecialchars($ticket['TicketName']) ?>
                    </h4>

                    <div class="ticket-info">
                        <div class="ticket-info-item">
                            <div class="ticket-info-label">Mã Loại Vé</div>
                            <div class="ticket-info-value">#<?= $ticket['TicketTypeId'] ?></div>
                        </div>

                        <div class="ticket-info-item">
                            <div class="ticket-info-label">Giá Vé</div>
                            <div class="ticket-info-value price-display">
                                <?= number_format($ticket['Price'], 0, ',', '.') ?> VNĐ
                            </div>
                        </div>

                        <div class="ticket-info-item">
                            <div class="ticket-info-label">Số Lượng Vé</div>
                            <div class="ticket-info-value">
                                <i class="fas fa-ticket-alt me-1" style="font-size: 0.9rem;"></i>
                                <?= $ticket['Quantity'] ?> vé
                            </div>
                        </div>

                        <div class="ticket-info-item">
                            <div class="ticket-info-label">Thuộc Sự Kiện</div>
                            <div class="event-info">
                                <div class="event-icon">
                                    <i class="fas fa-calendar-event"></i>
                                </div>
                                <div class="event-name">
                                    <?php
                                    foreach ($events as $event) {
                                        if ($event['EventId'] == $ticket['EventId']) {
                                            echo htmlspecialchars($event['EventName']);
                                            break;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="warning-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Bạn đang chuẩn bị xóa loại vé này. Hành động này không thể hoàn tác và có thể ảnh hưởng đến các đơn hàng liên quan.</p>
                </div>

                <form action="../../controller/TicketTypeController.php?action=delete" method="POST">
                    <input type="hidden" name="TicketTypeId" value="<?= $ticket['TicketTypeId'] ?>">
                    
                    <div class="actions">
                        <a href="index.php" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-times"></i> Hủy Bỏ
                        </a>
                        <button type="submit" class="btn btn-danger flex-grow-1">
                            <i class="fas fa-trash-alt"></i> Xác Nhận Xóa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>