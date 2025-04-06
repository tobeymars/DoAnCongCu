<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EquipmentModel.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID thiết bị.");
}

$database = new Database();
$conn = $database->getConnection();
$model = new Equipment($conn);
$equipment = $model->getEquipmentById($_GET['id']);

if (!$equipment) {
    die("Thiết bị không tồn tại.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác nhận xóa thiết bị</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container mt-5">
    <h1>Xác nhận xóa thiết bị</h1>
    <p><strong>Tên thiết bị:</strong> <?= htmlspecialchars($equipment['EquipmentName']) ?></p>
    <p><strong>Loại:</strong> <?= htmlspecialchars($equipment['EquipmentTypeName']) ?></p>

    <form action="../../controller/EquipmentController.php?action=delete" method="POST">
        <input type="hidden" name="EquipmentId" value="<?= $equipment['EquipmentId'] ?>">
        <p>Bạn có chắc chắn muốn xóa thiết bị này không?</p>
        <button type="submit" class="btn btn-danger btn-custom">Xác nhận xóa</button>
        <a href="index.php" class="btn btn-secondary btn-custom">Hủy</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
