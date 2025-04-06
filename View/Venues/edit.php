<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/VenuesModel.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID");
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
    <title>Chỉnh sửa Địa Điểm</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --text-primary: #5a5c69;
            --border-radius: 8px;
            --box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-primary);
            padding-bottom: 2rem;
        }

        .container {
            max-width: 850px;
            margin-top: 2.5rem;
            margin-bottom: 2.5rem;
            background: white;
            padding: 0;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }

        .header {
            background: #4e73df;
            color: white;
            padding: 1.5rem;
            margin-bottom: 2rem;
            position: relative;
            
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
            text-align: center;
        }

        .form-container {
            padding: 0 2.5rem 2.5rem;
        }

        .form-label {
            font-weight: 600;
            color: #4e5155;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }

        .preview-container {
            text-align: center;
            margin: 1.5rem 0;
            padding: 1.5rem;
            border: 2px dashed #e2e8f0;
            border-radius: var(--border-radius);
            background-color: #f8fafc;
        }

        .preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .preview:hover {
            transform: scale(1.02);
        }

        .file-input-container {
            position: relative;
            margin-top: 1rem;
        }

        .file-input-label {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color:  #4e73df;
            color: white;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .file-input-label:hover {
            background-color: #05278d;
        }

        input[type="file"] {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }

        .file-name {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #718096;
        }

        .btn-action {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: var(--border-radius);
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        .btn-primary:hover {
            background-color: #05278d;
            border-color: #05278d;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: #fff;
            color: var(--primary-color);
            border: 1px solid #e2e8f0;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background-color: #f8fafc;
            color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        textarea {
            min-height: 120px;
        }

        /* Two-column layout */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-col {
            flex: 1;
            min-width: 250px;
        }

        @media (max-width: 768px) {
            .container {
                margin-top: 1rem;
            }
            
            .form-container {
                padding: 0 1.5rem 1.5rem;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="container">
    <div class="header">
        <h1><i class="fas fa-map-marker-alt me-2"></i>Chỉnh sửa Địa Điểm</h1>
    </div>

    <div class="form-container">
        <form action="../../controller/VenuesController.php?action=updateVenue" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="VenueId" value="<?= $venue['VenueId'] ?>">

            <div class="form-row">
                <div class="form-col">
                    <div class="mb-4">
                        <label class="form-label">Tên địa điểm</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-building"></i></span>
                            <input type="text" class="form-control" name="VenueName" value="<?= htmlspecialchars($venue['VenueName']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Địa chỉ</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-location-dot"></i></span>
                            <input type="text" class="form-control" name="Address" value="<?= htmlspecialchars($venue['Address']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Sức chứa</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-users"></i></span>
                            <input type="number" class="form-control" name="Capacity" value="<?= htmlspecialchars($venue['Capacity']) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-col">
                    <div class="mb-4">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control" name="Description" rows="3" placeholder="Mô tả chi tiết về địa điểm..."><?= htmlspecialchars($venue['Description']) ?></textarea>
                    </div>

                    <div class="preview-container">
                        <label class="form-label">Hình ảnh</label>
                        <div class="image-preview mb-3">
                            <img class="preview" src="/quanlysukien/images/<?= htmlspecialchars($venue['images']) ?>" alt="Preview">
                            <input type="hidden" name="CurrentImage" value="<?= htmlspecialchars($venue['images']) ?>">
                        </div>

                        <div class="file-input-container">
                            <label for="images-upload" class="file-input-label">
                                <i class="fas fa-cloud-upload-alt me-2"></i>Chọn ảnh mới
                            </label>
                            <input id="images-upload" type="file" class="form-control" name="Images">
                            <div class="file-name mt-2" id="file-name">Chưa chọn file</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="button-group">
                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Quay lại danh sách
                </a>
                <button type="submit" class="btn btn-primary btn-action w-100">
                    <i class="fas fa-save me-2"></i> Cập Nhật Địa Điểm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById('images-upload');
        const previewImage = document.querySelector(".preview");
        const fileName = document.getElementById('file-name');

        fileInput.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = "block";
                    fileName.textContent = file.name;
                };
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = "Chưa chọn file";
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>