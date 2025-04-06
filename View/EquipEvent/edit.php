<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/EquipEventModel.php';

$database = new Database();
$conn = $database->getConnection();
$eventEquipmentModel = new EventEquipment($conn);

$id = $_GET['id'] ?? null;
$equipment = null;
if ($id) {
    $equipment = $eventEquipmentModel->getEquipmentsByEventId($id);
}

if (!$equipment) {
    die("Thiết bị không tồn tại");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chỉnh sửa thiết bị sự kiện</title>
</head>
<body>
    <h2>Chỉnh sửa thiết bị sự kiện</h2>
    <form action="../../Controller/EventEquipmentController.php?action=update" method="post">
        <input type="hidden" name="Id" value="<?= $equipment['id'] ?>">
        <label>Ngày sử dụng:</label>
        <input type="date" name="Date" value="<?= $equipment['date'] ?>" required>
        <label>Số lượng</label>
        <input type="number" name="quantity" value="<?= $equipment['soluong'] ?>" required>
        <button type="submit">Cập nhật</button>
    </form>
</body>
</html>