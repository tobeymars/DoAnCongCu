<?php
// Kiểm tra và thiết lập session nếu chưa bắt đầu
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Import các file cần thiết
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';

// Xác thực token
$token = $_GET["token"] ?? "";
if (empty($token)) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Token is missing"]);
    exit();
}

$userData = JWTHelper::verifyToken($token);
if (!$userData) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid token"]);
    exit();
}

// Kiểm tra ID sự kiện
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$eventId) {
    die("ID sự kiện không hợp lệ.");
}

// Thiết lập kết nối database
$database = new Database();
$conn = $database->getConnection();

// Lấy thông tin sự kiện
$model = new Event($conn);
$event = $model->getEventById($eventId);
if (!$event) {
    die("Sự kiện không tồn tại.");
}

// Định dạng ngày tháng
$eventDate = !empty($event['EventDate']) 
    ? (new DateTime($event['EventDate']))->format('d-m-Y') 
    : 'Không có ngày';

// Xác định header dựa trên vai trò
$roleId = $userData['RoleId'] ?? null;
if ($roleId == 2) {
    include '../shares/header.php';
} else {
    include '../shares/adminhd.php';
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Xóa Sự Kiện | Hệ Thống Quản Lý</title>
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
        
        .delete-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 30px;
            margin-bottom: 30px;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .warning-icon {
            color: #e74a3b;
            font-size: 3.5rem;
            margin-bottom: 20px;
        }
        
        .event-details {
            background-color: #f8f9fa;
            border-left: 4px solid #4e73df;
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
            color: #4e73df;
            min-width: 140px;
        }
        
        .delete-message {
            color: #e74a3b;
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
        
        .btn-danger {
            background-color: #e74a3b;
            border-color: #e74a3b;
            padding: 8px 20px;
        }
        
        .btn-danger:hover {
            background-color: #d52a1a;
            border-color: #c92a1a;
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
            color: #4e73df;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="delete-container">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle warning-icon"></i>
                <h2 class="page-header">Xác Nhận Xóa Sự Kiện</h2>
            </div>
            
            <div class="event-details">
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-calendar-alt me-2"></i>Tên sự kiện:
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars($event['EventName']) ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-clock me-2"></i>Ngày diễn ra:
                    </div>
                    <div class="detail-value">
                        <?= $eventDate ?>
                    </div>
                </div>
                
                <?php if (!empty($event['Description'])): ?>
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-align-left me-2"></i>Mô tả:
                    </div>
                    <div class="detail-value">
                        <?= htmlspecialchars(substr($event['Description'], 0, 100)) ?>
                        <?= strlen($event['Description']) > 100 ? '...' : '' ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center delete-message">
                <i class="fas fa-trash-alt me-2"></i>
                Hành động này không thể hoàn tác. Bạn có chắc chắn muốn xóa sự kiện này không?
            </div>
            
            <form id="deleteEventForm" action="../../controller/EventController.php?action=delete" method="POST">
                <input type="hidden" name="EventId" value="<?= htmlspecialchars($event['EventId']) ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" id="userId" name="user_id">
                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i> Xác Nhận Xóa
                    </button>
                    <a href="index.php?token=<?= htmlspecialchars($token) ?>" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Hủy Bỏ
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Lấy thông tin người dùng từ localStorage
        const userInfo = JSON.parse(localStorage.getItem("userInfo"));
        if (userInfo && userInfo.user_id) {
            document.getElementById("userId").value = userInfo.user_id;
        } else {
            console.error("Không tìm thấy user_id trong localStorage");
        }
        
        // Xác nhận lần cuối trước khi xóa
        document.getElementById("deleteEventForm").addEventListener("submit", function(event) {
            if (!confirm("Bạn thực sự muốn xóa sự kiện này?")) {
                event.preventDefault();
            }
        });
    });
    </script>
</body>
</html>