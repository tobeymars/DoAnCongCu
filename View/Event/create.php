<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventtypeModel.php';
require_once __DIR__ . '/../../Model/VenuesModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';

$token = $_GET["token"] ?? "";
$userData = JWTHelper::verifyToken($token);
if (!$userData) {
    echo json_encode(["error" => "Invalid token"]);
    exit();
}
$createdBy = $userData['user_id'];
$database = new Database();
$conn = $database->getConnection();

// Gọi model riêng
$eventTypeModel = new EventType($conn);
$venueModel = new Venue($conn);

// Lấy dữ liệu
$eventTypes = $eventTypeModel->getAllEventTypes()->fetchAll(PDO::FETCH_ASSOC);
$venues = $venueModel->getAllVenuesHome()->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sự Kiện Mới | Hệ Thống Quản Lý</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --primary-light: #6610f2;
            --secondary-color: #1cc88a;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
            --dark-color: #5a5c69;
            --light-color: #f8f9fc;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding-top: 40px;
            color: #5a5c69;
        }
        
        .form-container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0.25rem 1rem rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            margin-bottom: 30px;
            max-width: 850px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }
        
        .form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        }
        
        .form-icon {
            color: var(--primary-color);
            font-size: 2.5rem;
            margin-bottom: 20px;
            background: rgba(78, 115, 223, 0.1);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            margin-right: auto;
            transition: all 0.3s ease;
        }
        
        .form-icon:hover {
            transform: scale(1.05);
            background: rgba(78, 115, 223, 0.15);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 30px;
            color: #2e384d;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
        }
        
        .page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            border-radius: 3px;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }
        
        .form-label i {
            margin-right: 8px;
            color: var(--primary-color);
            width: 18px;
            text-align: center;
            font-size: 0.95rem;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background-color: #f9fafc;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
            background-color: #fff;
        }
        
        .form-control::placeholder {
            color: #b9bdc7;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            flex: 1;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 25px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.95rem;
        }
        
        .btn i {
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            border: none;
            flex: 2;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #4169e1, #5e72e4);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
        }
        
        .btn-secondary {
            background: #f0f0f5;
            border: none;
            color: var(--dark-color);
            flex: 1;
        }
        
        .btn-secondary:hover {
            background: #e4e4ee;
            color: var(--dark-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        }
        
        .form-section {
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.1);
        }
        
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-container {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .required-field::after {
            content: '*';
            color: var(--danger-color);
            margin-left: 3px;
        }
        
        /* Form help text */
        .form-text {
            font-size: 0.85rem;
            color: #8e94a3;
            margin-top: 5px;
        }
        
        @media (max-width: 992px) {
            body {
                padding-left: 0;
            }
            
            .form-container {
                margin: 15px;
                padding: 1.5rem;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
        .image-uploader {
            border: 2px dashed #e3e6f0;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            margin-bottom: 1rem;
            position: relative;
        }
        
        .image-uploader:hover {
            border-color: var(--primary-color);
        }
        
        .image-uploader-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
            opacity: 0.7;
        }
        
        .uploader-text {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        .uploader-text strong {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        #imageInput {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }
        
        .image-preview-container {
            display: none;
            margin-top: 1.5rem;
            text-align: center;
        }
        
        .image-preview-wrapper {
            display: inline-block;
            position: relative;
            margin: 0 auto;
            max-width: 100%;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 250px;
            display: block;
        }
        
        .image-preview-remove {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: rgba(255, 255, 255, 0.9);
            color: #e74a3b;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0.8;
            transition: all 0.2s;
        }
        
        .image-preview-remove:hover {
            opacity: 1;
            transform: scale(1.1);
        }   
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="form-container">
            <div class="text-center">
                <div class="form-icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h2 class="page-header">Thêm Sự Kiện Mới</h2>
            </div>
            
            <form action="../../Controller/EventController.php?action=create" enctype="multipart/form-data" method="POST"id="venueForm">
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-info-circle"></i> Thông Tin Cơ Bản
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required-field">
                            <i class="fas fa-signature"></i> Tên sự kiện
                        </label>
                        <input type="text" class="form-control" name="EventName" required placeholder="Nhập tên sự kiện">
                        <div class="form-text">Tên sự kiện nên ngắn gọn và dễ nhớ</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">
                            <i class="fas fa-align-left"></i> Mô tả
                        </label>
                        <textarea class="form-control" name="Description" rows="3" placeholder="Mô tả chi tiết về sự kiện"></textarea>
                    </div>
                </div>
                <div class="form-group">
                        <label class="form-label d-block">Hình ảnh địa điểm</label>
                        <div class="image-uploader" id="imageUploader">
                            <div class="image-uploader-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <p class="uploader-text"><strong>Nhấp để tải ảnh lên</strong> hoặc kéo và thả file vào đây</p>
                            <p class="uploader-text text-muted">Hỗ trợ: JPG, JPEG, PNG (Tối đa 5MB)</p>
                            <input type="file" class="d-none" name="Images" id="imageInput" accept="image/*" onchange="previewImage(event)">
                        </div>
                        
                        <div class="image-preview-container" id="imagePreviewContainer">
                            <div class="image-preview-wrapper">
                                <img id="imagePreview" src="#" alt="Xem trước hình ảnh" class="image-preview">
                                <div class="image-preview-remove" onclick="removeImage()">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-calendar-alt"></i> Thời Gian & Địa Điểm
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required-field">
                                <i class="fas fa-calendar-day"></i> Ngày diễn ra
                            </label>
                            <input type="date" class="form-control" name="EventDate" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label required-field">
                                <i class="fas fa-map-marker-alt"></i> Địa điểm
                            </label>
                            <select class="form-select" name="VenueId" required>
                                <option value="">-- Chọn địa điểm --</option>
                                <?php foreach ($venues as $venue): ?>
                                    <option value="<?= htmlspecialchars($venue['VenueId']) ?>">
                                        <?= htmlspecialchars($venue['VenueName']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-tag"></i> Phân Loại
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required-field">
                            <i class="fas fa-sitemap"></i> Loại sự kiện
                        </label>
                        <select class="form-select" name="EventTypeId" required>
                            <option value="">-- Chọn loại sự kiện --</option>
                            <?php foreach ($eventTypes as $type): ?>
                                <option value="<?= htmlspecialchars($type['EventTypeId']) ?>">
                                    <?= htmlspecialchars($type['TypeName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <input type="hidden" name="CreatedBy" value="<?= htmlspecialchars($createdBy) ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Tạo Sự Kiện
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.history.back();">
                        <i class="fas fa-arrow-left"></i> Quay Về
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Thiết lập giá trị mặc định cho trường ngày là ngày hiện tại
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const formattedDate = today.toISOString().substr(0, 10);
            document.querySelector('input[name="EventDate"]').value = formattedDate;
        });
        document.addEventListener('DOMContentLoaded', function() {
            const imageUploader = document.getElementById('imageUploader');
            const imageInput = document.getElementById('imageInput');
            
            imageUploader.addEventListener('click', function() {
                imageInput.click();
            });
            
            // Thêm tính năng kéo thả
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                imageUploader.addEventListener(eventName, preventDefaults, false);
            });
            
            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }
            
            ['dragenter', 'dragover'].forEach(eventName => {
                imageUploader.addEventListener(eventName, highlight, false);
            });
            
            ['dragleave', 'drop'].forEach(eventName => {
                imageUploader.addEventListener(eventName, unhighlight, false);
            });
            
            function highlight() {
                imageUploader.classList.add('border-primary');
            }
            
            function unhighlight() {
                imageUploader.classList.remove('border-primary');
            }
            
            imageUploader.addEventListener('drop', handleDrop, false);
            
            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                
                if (files.length) {
                    imageInput.files = files;
                    previewImage({ target: imageInput });
                }
            }
            
            // Hiệu ứng cho form
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
        });
        
        function previewImage(event) {
            const input = event.target;
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');
            const imageUploader = document.getElementById('imageUploader');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = "block";
                    imageUploader.style.display = "none";
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                removeImage();
            }
        }
        
        function removeImage() {
            const previewContainer = document.getElementById('imagePreviewContainer');
            const imageUploader = document.getElementById('imageUploader');
            const imageInput = document.getElementById('imageInput');
            
            previewContainer.style.display = "none";
            imageUploader.style.display = "block";
            imageInput.value = '';
        }
        
        // Form validation
        document.getElementById('venueForm').addEventListener('submit', function(event) {
            const nameInput = document.getElementById('venueName');
            const addressInput = document.getElementById('address');
            const capacityInput = document.getElementById('capacity');
            const imageInput = document.getElementById('imageInput');
            
            let valid = true;
            
            if (!nameInput.value.trim()) {
                nameInput.classList.add('is-invalid');
                valid = false;
            } else {
                nameInput.classList.remove('is-invalid');
            }
            
            if (!addressInput.value.trim()) {
                addressInput.classList.add('is-invalid');
                valid = false;
            } else {
                addressInput.classList.remove('is-invalid');
            }
            
            if (!capacityInput.value || capacityInput.value < 1) {
                capacityInput.classList.add('is-invalid');
                valid = false;
            } else {
                capacityInput.classList.remove('is-invalid');
            }
            
            if (!imageInput.files.length) {
                document.getElementById('imageUploader').classList.add('border-danger');
                valid = false;
            } else {
                document.getElementById('imageUploader').classList.remove('border-danger');
            }
            
            if (!valid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>