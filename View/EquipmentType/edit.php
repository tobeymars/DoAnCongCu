<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EquipmentTypeModel.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID");
}

$database = new Database();
$conn = $database->getConnection();
$model = new EquipmentType($conn);
$equipmentType = $model->getEquipmentTypeById($_GET['id']);

if (!$equipmentType) {
    die("Loại thiết bị không tồn tại.");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa Loại Thiết Bị</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 500px;
            margin-top: 50px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        .btn-custom {
            width: 100%;
            font-size: 16px;
        }

        .form-label {
            font-weight: bold;
        }

        .back-link {
            text-align: center;
            display: block;
            margin-top: 15px;
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Chỉnh sửa Loại Thiết Bị</h1>
    <form action="../../controller/EquipmentTypeController.php?action=update" method="POST">
        <input type="hidden" name="EquipmentTypeId" value="<?= $equipmentType['EquipmentTypeId'] ?>">

        <div class="mb-3">
            <label class="form-label">Loại thiết bị:</label>
            <input type="text" class="form-control" name="EquipmentTypeName" value="<?= htmlspecialchars($equipmentType['EquipmentTypeName']) ?>" required>
        </div>

        <button type="submit" class="btn btn-primary btn-custom">Cập Nhật</button>
    </form>

    <a href="index.php" class="back-link text-primary">← Quay lại danh sách</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
