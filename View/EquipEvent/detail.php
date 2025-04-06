<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EquipEventModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';

$eventId = $_GET["eventId"] ?? null;

if (!$eventId) {
    echo json_encode(["error" => "Missing Event ID"]);
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$eventEquipmentModel = new EventEquipment($conn);
$equipments = $eventEquipmentModel->getEquipmentsByEventId($eventId);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh Sách Thiết Bị</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>body{padding-left: 275px;}</style>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Danh Sách Thiết Bị</h2>
        <div class="d-flex justify-content-between">
            <a href="/Quanlysukien/View/Event/index.php" class="btn btn-secondary">Quay lại</a>
        </div>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Tên Thiết Bị</th>
                    <th>Ngày Gán</th>
                    <th>Số lượng</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($equipments)): ?>
                    <tr>
                        <td colspan="2" class="text-center">Không có thiết bị nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($equipments as $equipment): ?>
                        <tr>
                            <td><?= htmlspecialchars($equipment['EquipmentName']) ?></td>
                            <td><?= htmlspecialchars($equipment['date']) ?></td>
                            <td><?= htmlspecialchars($equipment['soluong']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>