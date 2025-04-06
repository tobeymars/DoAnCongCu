<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventtypeModel.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID loại sự kiện.");
}

$database = new Database();
$conn = $database->getConnection();
$model = new EventType($conn);
$eventType = $model->getEventTypeById($_GET['id']);

if (!$eventType) {
    die("Loại sự kiện không tồn tại.");
}

if (!$eventType['IsDeleted']) {
    die("Loại sự kiện này không cần khôi phục.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khôi phục loại sự kiện</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            padding-top: 50px;
            padding-left: 275px;
            color: #333;
        }
        
        .restore-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 30px;
            margin-bottom: 30px;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .restore-icon {
            color: #1cc88a;
            font-size: 3.5rem;
            margin-bottom: 20px;
        }
        
        .event-details {
            background-color: #f8f9fa;
            border-left: 4px solid #1cc88a;
            padding: 15px;
            margin: 20px 0;
            border-radius: 0 5px 5px 0;
        }
        
        .event-details .detail-item {
            padding: 5px 0;
            display: flex;
            border-bottom: 1px dashed #e3e6f0;
        }
        
        .event-details .detail-item:last-child {
            border-bottom: none;
        }
        
        .event-details .detail-label {
            font-weight: 600;
            color: #1cc88a;
            min-width: 140px;
        }
        
        .restore-message {
            color: #1cc88a;
            font-weight: 600;
            margin: 20px 0;
            font-size: 1.1rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
            justify-content: center;
        }
        
        .btn-success {
            background-color: #1cc88a;
            border-color: #1cc88a;
            padding: 8px 20px;
        }
        
        .btn-success:hover {
            background-color: #17a673;
            border-color: #169b6b;
        }
        
        .btn-secondary {
            background-color: #858796;
            border-color: #858796;
            padding: 8px 20px;
        }
        
        .btn-secondary:hover {
            background-color: #717384;
            border-color: #6e707e;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 25px;
            color: #1cc88a;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="restore-container">
            <div class="text-center">
                <i class="fas fa-undo restore-icon"></i>
                <h2 class="page-header">Khôi Phục loại Sự Kiện</h2>
            </div>
            
            <div class="event-details">
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-calendar-alt me-2"></i>Tên sự kiện:
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars($eventType['TypeName']) ?>
                    </div>
                </div>
            </div>
            
            <div class="text-center restore-message">
                <i class="fas fa-check-circle me-2"></i>
                Sự kiện này hiện đang bị xóa tạm thời. Bạn có muốn khôi phục lại sự kiện này không?
            </div>
            
            <form id="restoreEventForm" action="../../controller/EventTypeController.php?action=restore" method="POST">
            <input type="hidden" name="EventTypeId" value="<?= $eventType['EventTypeId'] ?>">
                <div class="action-buttons">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-undo me-1"></i> Khôi Phục Loại Sự Kiện
                    </button>
                    <a href="trash.php" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Hủy Bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
