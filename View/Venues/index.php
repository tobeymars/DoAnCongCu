<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/VenuesModel.php';

$database = new Database();
$conn = $database->getConnection();
$model = new Venue($conn);
$venues = $model->getAllVenues()->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Địa Điểm | Hệ Thống Quản Lý Sự Kiện</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --light-bg: #f8f9fc;
            --card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: #5a5c69;
            padding-top: 40px;
            padding-left: 275px;
            padding-bottom: 2rem;
        }
        
        .container {
            margin-top: 2rem;
            max-width: 1200px;
        }
        
        .page-header {
            margin-bottom: 2rem;
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 1rem;
        }
        
        .page-header h1 {
            font-weight: 700;
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: #858796;
            font-size: 1rem;
        }
        
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 0.75rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
        }
        
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 0.375rem 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 0.35rem;
            box-shadow: 0 0.125rem 0.25rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        
        .btn-primary i {
            margin-right: 0.5rem;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            background-color: #f8f9fc;
            color: #6e707e;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.1em;
            border-bottom: 2px solid #e3e6f0;
            vertical-align: middle;
        }
        
        .table td {
            vertical-align: middle;
            padding: 0.75rem;
            border-color: #e3e6f0;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .venue-image {
            width: 100px;
            height: 70px;
            object-fit: cover;
            border-radius: 0.35rem;
            box-shadow: 0 0.125rem 0.25rem 0 rgba(58, 59, 69, 0.15);
            transition: transform 0.3s;
        }
        
        .venue-image:hover {
            transform: scale(1.1);
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-align: center;
            white-space: nowrap;
            display: inline-block;
        }
        
        .status-active {
            background-color: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
            border: 1px solid rgba(28, 200, 138, 0.25);
        }
        
        .status-inactive {
            background-color: rgba(231, 74, 59, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(231, 74, 59, 0.25);
        }
        
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.25rem;
            margin-right: 0.25rem;
            font-weight: 600;
        }
        
        .btn-edit {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: #fff;
        }
        
        .btn-delete {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: #fff;
        }
        
        .btn-restore {
            background-color: #1cc88a;
            border-color: #1cc88a;
            color: #fff;
        }
        
        .venue-capacity {
            font-weight: 700;
            color: #5a5c69;
        }
        
        .pagination {
            margin-top: 1rem;
            justify-content: center;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                border: 0;
            }
            
            .venue-image {
                width: 80px;
                height: 60px;
            }
            
            .btn-action {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-header d-sm-flex align-items-center justify-content-between">
            <div>
                <h1><i class="fas fa-map-marker-alt me-2"></i> Quản Lý Địa Điểm</h1>
                <p class="page-subtitle">Xem, thêm, sửa và quản lý các địa điểm tổ chức sự kiện</p>
            </div>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Thêm Địa Điểm Mới
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Danh Sách Địa Điểm</h2>
                <div>
                    <span class="badge bg-primary"><?= count($venues) ?> địa điểm</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col" width="25%">Tên Địa Điểm</th>
                                <th scope="col" width="30%">Địa Chỉ</th>
                                <th scope="col" width="10%">Sức Chứa</th>
                                <th scope="col" width="15%">Hình Ảnh</th>
                                <th scope="col" width="10%">Trạng Thái</th>
                                <th scope="col" width="10%">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($venues)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">Không có địa điểm nào. Hãy thêm địa điểm mới!</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($venues as $venue): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($venue['VenueName']) ?></strong>
                                        </td>
                                        <td>
                                            <i class="fas fa-location-dot me-1 text-muted"></i>
                                            <?= htmlspecialchars($venue['Address']) ?>
                                        </td>
                                        <td>
                                            <span class="venue-capacity">
                                                <i class="fas fa-users me-1 text-primary"></i>
                                                <?= number_format($venue['Capacity']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <img src="/quanlysukien/images/<?= htmlspecialchars($venue['images']) ?>" 
                                                 class="venue-image" 
                                                 alt="<?= htmlspecialchars($venue['VenueName']) ?>"
                                                 title="<?= htmlspecialchars($venue['VenueName']) ?>">
                                        </td>
                                        <td>
                                            <?php if ($venue['IsDeleted'] == 0): ?>
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-circle-check me-1"></i> Hoạt động
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge status-inactive">
                                                    <i class="fas fa-circle-xmark me-1"></i> Đã khóa
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <?php if ($venue['IsDeleted'] == 0): ?>
                                                    <a href="edit.php?id=<?= $venue['VenueId'] ?>" 
                                                       class="btn btn-action btn-edit me-1" 
                                                       title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="delete.php?id=<?= $venue['VenueId'] ?>" 
                                                       class="btn btn-action btn-delete" 
                                                       title="Xóa">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="restore.php?id=<?= $venue['VenueId'] ?>" 
                                                       class="btn btn-action btn-restore" 
                                                       title="Khôi phục">
                                                        <i class="fas fa-undo"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Hiển thị <?= count($venues) ?> địa điểm</small>
                    <!-- Phân trang có thể thêm ở đây nếu cần -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Thêm hiệu ứng khi hover vào ảnh
        document.querySelectorAll('.venue-image').forEach(img => {
            img.addEventListener('mouseover', function() {
                this.style.cursor = 'pointer';
            });
            
            // Thêm modal hiển thị ảnh lớn nếu muốn
            img.addEventListener('click', function() {
                // Có thể thêm code để hiển thị modal với ảnh lớn hơn
            });
        });
    </script>
</body>

</html>