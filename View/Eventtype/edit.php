<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventTypeModel.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID");
}

$database = new Database();
$conn = $database->getConnection();
$model = new EventType($conn);
$eventType = $model->getEventTypeById($_GET['id']);

if (!$eventType) {
    die("Loại sự kiện không tồn tại.");
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Loại Sự Kiện</title>
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
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            padding: 20px 0;
        }
        
        .card {
            max-width: 550px;
            width: 100%;
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 25px 30px;
            border-bottom: none;
            position: relative;
        }
        
        .card-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 10px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z' fill='%23ffffff' opacity='.25'/%3E%3Cpath d='M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z' fill='%23ffffff' opacity='.5'/%3E%3Cpath d='M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z' fill='%23ffffff' opacity='.75'/%3E%3C/svg%3E") no-repeat;
            background-size: cover;
        }
        
        .form-header {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .form-subheader {
            font-size: 0.95rem;
            opacity: 0.8;
        }
        
        .card-body {
            padding: 35px;
            background-color: white;
        }
        
        .form-label {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: block;
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e1e5eb;
            font-size: 1rem;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-color);
            background-color: white;
        }
        
        .form-control::placeholder {
            color: #b3b3b3;
        }
        
        .form-text {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .btn {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            border: none;
            box-shadow: 0 4px 12px rgba(96, 163, 188, 0.4);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(96, 163, 188, 0.5);
        }
        
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(96, 163, 188, 0.4);
        }
        
        .btn-outline-secondary {
            color: #6c757d;
            border-color: #dee2e6;
            background-color: white;
        }
        
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            color: #495057;
            border-color: #ced4da;
        }
        
        .card-footer {
            background-color: white;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding: 20px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .back-link {
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }
        
        .back-link i {
            margin-right: 5px;
            transition: transform 0.2s ease;
        }
        
        .back-link:hover {
            color: var(--primary-color);
        }
        
        .back-link:hover i {
            transform: translateX(-3px);
        }
        
        .input-icon-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 1.1rem;
        }
        
        .input-with-icon {
            padding-left: 45px;
        }
        
        .form-floating {
            position: relative;
        }
        
        .floating-icon {
            position: absolute;
            top: 1rem;
            left: 1rem;
            color: #adb5bd;
            z-index: 2;
        }
        
        .form-floating label {
            padding-left: 2.5rem;
        }
        
        .form-floating > .form-control {
            padding-left: 2.5rem;
        }
        
        @media (max-width: 576px) {
            .card {
                margin: 0 15px;
                border-radius: 15px;
            }
            
            .card-header {
                padding: 20px 25px;
            }
            
            .card-body {
                padding: 25px;
            }
            
            .card-footer {
                padding: 15px 25px;
                flex-direction: column;
                gap: 15px;
            }
            
            .form-header {
                font-size: 1.5rem;
            }
        }
        
        /* Animate.css-like animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -20px, 0);
            }
            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }
        
        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease forwards;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
</head>

<body>

    <div class="card animate-fade-in-down">
        <div class="card-header">
            <h1 class="form-header">Chỉnh sửa Loại Sự Kiện</h1>
        </div>
        <div class="card-body animate-fade-in" style="animation-delay: 0.1s;">
            <form action="../../controller/EventTypeController.php?action=update" method="POST">
                <input type="hidden" name="EventTypeId" value="<?= $eventType['EventTypeId'] ?>">

                <div class="mb-4">
                    <label for="eventTypeName" class="form-label">Loại Sự Kiện</label>
                    <div class="input-icon-wrapper">
                        <i class="fa fa-tag input-icon"></i>
                        <input 
                            type="text" 
                            class="form-control input-with-icon" 
                            id="eventTypeName" 
                            name="TypeName" 
                            placeholder="Nhập tên loại sự kiện" 
                            required 
                            autofocus
                            value="<?= htmlspecialchars($eventType['TypeName']) ?>" 
                        >
                    </div>
                    <div class="form-text">
                        <i class="fa fa-info-circle me-1"></i>
                        Loại sự kiện nên ngắn gọn và mô tả chính xác
                    </div>
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>
                        Cập Nhật
                    </button>
                </div>
            </form>
            <div class="card-footer animate-fade-in" style="animation-delay: 0.2s;">
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Quay lại danh sách
            </a>        
            <small class="text-muted">Tất cả các trường có dấu * là bắt buộc</small>
        </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>