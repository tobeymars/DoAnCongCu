<?php // delete.php - Xóa mềm thiết bị
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/EquipEventModel.php';

if (!isset($_GET['id'])) {
    die("Thiếu ID thiết bị.");
}

$database = new Database();
$conn = $database->getConnection();
$eventEquipmentModel = new EventEquipment($conn);

$equipments = $eventEquipmentModel->getEquipmentsByEventId($_GET['id']);
if (!$equipment) {
    die("Thiết bị không tồn tại.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Danh sách thiết bị sự kiện</title>
</head>
<body>
    <h2>Danh sách thiết bị sự kiện</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Tên thiết bị</th>
            <th>Ngày</th>
            <th>Hành động</th>
        </tr>
        <?php foreach ($equipments as $equipment) { ?>
            <tr>
                <td><?= $equipment['id'] ?></td>
                <td><?= $equipment['EquipmentName'] ?></td>
                <td><?= $equipment['date'] ?></td>
                <td>
                    <form action="../../Controller/EventEquipmentController.php?action=delete" method="post" style="display:inline;">
                        <input type="hidden" name="Id" value="<?= $equipment['id'] ?>">
                        <button type="submit" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>