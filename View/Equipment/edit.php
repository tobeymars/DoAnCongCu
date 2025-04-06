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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $controller = new EquipmentController();
    $controller->updateEquipment();
    header("Location: index.php");
    exit();
}
$equipmentTypes = $model->getAllEquipmentTypes($_GET['id']); 
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa thiết bị</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Chỉnh sửa thiết bị</h2>
    <form action="../../Controller/EquipmentController.php?action=update" method="POST">
        <input type="hidden" name="EquipmentId" value="<?= $equipment['EquipmentId'] ?>">
        <div class="mb-3">
            <label class="form-label">Tên Thiết Bị</label>
            <input type="text" name="EquipmentName" class="form-control" value="<?= htmlspecialchars($equipment['EquipmentName']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Số Lượng</label>
            <input type="number" name="Quantity" class="form-control" value="<?= $equipment['Quantity'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Loại Thiết Bị</label>
            <select name="EquipmentTypeId" class="form-control" required>
                <?php foreach ($equipmentTypes as $type): ?>
                    <option value="<?= $type['EquipmentTypeId'] ?>" <?= ($type['EquipmentTypeId'] == $equipment['EquipmentTypeId']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($type['EquipmentTypeName']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="index.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>
</body>
</html>
