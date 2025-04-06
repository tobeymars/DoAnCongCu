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

if (!$equipment['IsDeleted']) {
    die("Thiết bị này không cần khôi phục.");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Khôi phục thiết bị</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Khôi phục thiết bị</h1>

    <p><strong>Tên thiết bị:</strong> <?= htmlspecialchars($equipment['EquipmentName']) ?></p>
    <p><strong>Loại:</strong> <?= htmlspecialchars($equipment['EquipmentTypeName']) ?></p>

    <form action="../../controller/EquipmentController.php?action=restore" method="POST">
        <input type="hidden" name="EquipmentId" value="<?= $equipment['EquipmentId'] ?>">
        <p>Bạn có chắc chắn muốn khôi phục thiết bị này không?</p>
        <button type="submit" class="btn btn-success btn-custom">Xác nhận khôi phục</button>
        <a href="index.php" class="btn btn-secondary btn-custom">Hủy</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
