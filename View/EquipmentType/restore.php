<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EquipmentTypeModel.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID loại thiết bị.");
}

$database = new Database();
$conn = $database->getConnection();
$model = new EquipmentType($conn);
$equipmentType = $model->getEquipmentTypeById($_GET['id']);

if (!$equipmentType) {
    die("Loại thiết bị không tồn tại.");
}

if (!$equipmentType['IsDeleted']) {
    die("Loại thiết bị này không cần khôi phục.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khôi phục loại thiết bị</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Khôi phục loại thiết bị</h1>

    <p><strong>Loại thiết bị:</strong> <?= htmlspecialchars($equipmentType['EquipmentTypeName']) ?></p>

    <form action="../../controller/EquipmentTypeController.php?action=restore" method="POST">
        <input type="hidden" name="EquipmentTypeId" value="<?= $equipmentType['EquipmentTypeId'] ?>">
        <p>Bạn có chắc chắn muốn khôi phục loại thiết bị này không?</p>
        <button type="submit" class="btn btn-success btn-custom">Xác nhận khôi phục</button>
        <a href="index.php" class="btn btn-secondary btn-custom">Hủy</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
