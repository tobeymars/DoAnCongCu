<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/VenuesModel.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID địa điểm.");
}

$database = new Database();
$conn = $database->getConnection();
$model = new Venue($conn);
$venue = $model->getVenueById($_GET['id']);

if (!$venue) {
    die("Địa điểm không tồn tại.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận xóa địa điểm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --danger-color: #dc3545;
            --danger-hover: #bb2d3b;
            --success-color: #198754;
            --text-dark: #343a40;
            --gray-light: #f8f9fa;
            --border-radius: 8px;
            --box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            padding-top: 40px;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 750px;
            margin: 2rem auto;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        
        .header {
            background: var(--danger-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
            position: relative;
        }
        
        .header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
        }
        
        .header i {
            margin-right: 0.5rem;
        }
        
        .content {
            padding: 2rem;
        }
        
        .venue-details {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 2rem;
            background-color: var(--gray-light);
            border-radius: var(--border-radius);
            overflow: hidden;
        }
        
        .venue-image {
            flex: 0 0 40%;
            position: relative;
            overflow: hidden;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .venue-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .venue-image img:hover {
            transform: scale(1.05);
        }
        
        .venue-info {
            flex: 0 0 60%;
            padding: 1.5rem;
        }
        
        .info-item {
            margin-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 0.5rem;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .info-label {
            font-weight: 600;
            display: inline-block;
            min-width: 100px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        
        .status-active {
            background-color: var(--success-color);
        }
        
        .status-inactive {
            background-color: var(--danger-color);
        }
        
        .warning-message {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius);
        }
        
        .warning-message i {
            margin-right: 0.5rem;
        }
        
        .actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            gap: 0.5rem;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
            flex: 1;
        }
        
        .btn-danger:hover {
            background-color: var(--danger-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        
        .btn-cancel {
            background-color: #6c757d;
            color: white;
            flex: 1;
        }
        
        .btn-cancel:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }
        
        @media (max-width: 768px) {
            .venue-details {
                flex-direction: column;
            }
            
            .venue-image, .venue-info {
                flex: 0 0 100%;
            }
            
            .venue-image {
                height: 200px;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-trash-alt"></i>Xác nhận xóa địa điểm</h1>
        </div>
        
        <div class="content">
            <div class="venue-details">
                <div class="venue-image">
                    <img src="/quanlysukien/images/<?= htmlspecialchars($venue['images']) ?>" alt="<?= htmlspecialchars($venue['VenueName']) ?>">
                </div>
                
                <div class="venue-info">
                    <div class="info-item">
                        <span class="info-label">Tên:</span>
                        <span class="info-value"><?= htmlspecialchars($venue['VenueName']) ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Địa chỉ:</span>
                        <span class="info-value"><?= htmlspecialchars($venue['Address']) ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Sức chứa:</span>
                        <span class="info-value"><?= htmlspecialchars($venue['Capacity']) ?> người</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Trạng thái:</span>
                        <span class="status-badge <?= $venue['IsDeleted'] ? 'status-inactive' : 'status-active' ?>">
                            <?= $venue['IsDeleted'] ? 'Hết sử dụng' : 'Còn sử dụng' ?>
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Mô tả:</span>
                        <div class="info-value"><?= htmlspecialchars($venue['Description']) ?></div>
                    </div>
                </div>
            </div>
            
            <div class="warning-message">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Cảnh báo:</strong> Thao tác xóa không thể hoàn tác. Địa điểm sẽ bị đánh dấu là "Hết sử dụng" và không xuất hiện trong danh sách địa điểm hoạt động.
            </div>
            
            <form action="../../controller/VenuesController.php?action=deleteVenue" method="POST">
                <input type="hidden" name="VenueId" value="<?= $venue['VenueId'] ?>">
                
                <div class="actions">
                    <a href="index.php" class="btn btn-cancel">
                        <i class="fas fa-times"></i>Hủy bỏ
                    </a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i>Xác nhận xóa
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>