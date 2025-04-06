<?php include '../shares/adminhd.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Địa Điểm Mới | Hệ Thống Quản Lý Sự Kiện</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --text-primary: #5a5c69;
            --text-secondary: #858796;
            --bg-gradient: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            --card-shadow: 0 0.5rem 1rem 0 rgba(58, 59, 69, 0.1);
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 80px 0;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        
        .form-wrapper {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            position: relative;
        }
        
        .form-header {
            background: var(--bg-gradient);
            padding: 2rem;
            text-align: center;
            position: relative;
            color: white;
        }
        
        .form-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
        }
        
        .form-header p {
            opacity: 0.9;
            font-size: 0.95rem;
            margin-bottom: 0;
        }
        
        .back-button {
            position: absolute;
            top: 1rem;
            left: 1rem;
            color: white;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            opacity: 0.9;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .back-button:hover {
            opacity: 1;
            transform: translateX(-3px);
            color: white;
        }
        
        .back-button i {
            margin-right: 0.5rem;
        }
        
        .form-body {
            padding: 2.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #e3e6f0;
            font-size: 0.9rem;
            color: var(--text-primary);
            transition: all 0.2s ease-in-out;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.15);
        }
        
        .form-text {
            color: var(--text-secondary);
            font-size: 0.75rem;
            margin-top: 0.25rem;
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
        
        .btn-submit {
            background: var(--bg-gradient);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 0.5rem;
            display: block;
            width: 100%;
            transition: all 0.3s;
            box-shadow: 0 0.25rem 0.5rem rgba(78, 115, 223, 0.1);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(78, 115, 223, 0.2);
            background: linear-gradient(135deg, #4468c8 0%, #1c43a9 100%);
            color: white;
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .btn-submit i {
            margin-right: 0.5rem;
        }
        
        .animated-label {
            position: absolute;
            left: 1rem;
            top: 0.75rem;
            padding: 0 0.25rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
            pointer-events: none;
            transition: all 0.2s;
            background-color: white;
        }
        
        .form-floating {
            position: relative;
        }
        
        .form-floating input:focus + .animated-label,
        .form-floating input:not(:placeholder-shown) + .animated-label,
        .form-floating textarea:focus + .animated-label,
        .form-floating textarea:not(:placeholder-shown) + .animated-label {
            top: -0.5rem;
            left: 0.75rem;
            font-size: 0.75rem;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .capacity-addon {
            background-color: #f8f9fc;
            color: var(--text-secondary);
            border-color: #e3e6f0;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .form-body {
                padding: 1.5rem;
            }
            
            .form-header {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-wrapper">
            <div class="form-header">
                <a href="index.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
                <h1><i class="fas fa-map-marker-alt me-2"></i> Thêm Địa Điểm Mới</h1>
                <p>Nhập thông tin chi tiết cho địa điểm sự kiện mới</p>
            </div>
            
            <div class="form-body">
                <form action="../../controller/VenuesController.php?action=createVenue" method="POST" enctype="multipart/form-data" id="venueForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="venueName" class="form-label">Tên địa điểm</label>
                                <input type="text" class="form-control" id="venueName" name="VenueName" required placeholder="Nhập tên địa điểm">
                                <div class="form-text">Ví dụ: Trung tâm Hội nghị Quốc gia, Nhà văn hóa Thanh niên</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="capacity" class="form-label">Sức chứa</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="capacity" name="Capacity" required min="1" placeholder="Nhập số lượng người">
                                    <span class="input-group-text capacity-addon">người</span>
                                </div>
                                <div class="form-text">Số lượng người tối đa mà địa điểm có thể phục vụ</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <input type="text" class="form-control" id="address" name="Address" required placeholder="Nhập địa chỉ đầy đủ">
                        <div class="form-text">Cung cấp địa chỉ chính xác để người tham dự dễ dàng tìm kiếm</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control" id="description" name="Description" rows="4" placeholder="Mô tả thêm về địa điểm (tiện ích, đặc điểm nổi bật...)"></textarea>
                        <div class="form-text">Thông tin chi tiết giúp người dùng hiểu rõ hơn về địa điểm</div>
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
                    
                    <div class="form-group mb-0 mt-4">
                        <button type="submit" class="btn btn-submit">
                            <i class="fas fa-plus-circle"></i> Thêm Địa Điểm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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