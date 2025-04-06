<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EquipEventModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';

$token = $_GET["token"] ?? "";
$eventId = $_GET["eventId"] ?? null;

$userData = JWTHelper::verifyToken($token);
if (!$userData) {
    echo json_encode(["error" => "Invalid token"]);
    exit();
}

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
<style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
        padding-top: 40px;
    }
</style>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Danh Sách Thiết Bị</h2>
        <div class="d-flex justify-content-between">
            <a href="/Quanlysukien/View/Event/indexU.php?token=<?= urlencode($token) ?>" class="btn btn-secondary">Quay lại</a>
            <a href="create.php?token=<?= urlencode($token) ?>&eventId=<?= htmlspecialchars($eventId) ?>" class="btn btn-primary">Thêm Thiết Bị</a>
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
<?php include '../shares/footer.php'; ?>
</html>