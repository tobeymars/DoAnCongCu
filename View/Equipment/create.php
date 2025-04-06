<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EquipmentTypeModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';

// $token = $_GET["token"] ?? "";
// $userData = JWTHelper::verifyToken($token);
// if (!$userData) {
//     echo json_encode(["error" => "Invalid token"]);
//     exit();
// }
// $createdBy = $userData['user_id'];
$database = new Database();
$conn = $database->getConnection();

$equipmentTypeModel = new EquipmentType($conn);
$equipmentTypes = $equipmentTypeModel->getActiveEquipmentTypes()->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Thiết Bị</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
        padding-left: 275px;
        padding-top: 60px;
    }
</style>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Thêm Thiết Bị</h2>
        <form action="../../Controller/EquipmentController.php?action=create" method="POST">
            <div class="mb-3">
                <label class="form-label">Tên Thiết Bị:</label>
                <input type="text" class="form-control" name="EquipmentName" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Số Lượng:</label>
                <input type="number" class="form-control" name="Quantity" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Loại Thiết Bị:</label>
                <select class="form-select" name="EquipmentTypeId" required>
                    <option value="">-- Chọn loại thiết bị --</option>
                    <?php foreach ($equipmentTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type['EquipmentTypeId']) ?>">
                            <?= htmlspecialchars($type['EquipmentTypeName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="CreatedBy" value="<?= htmlspecialchars($createdBy) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <button type="submit" class="btn btn-success w-100">Thêm Thiết Bị</button>
            <a href="index.php" class="back-link text-primary">← Quay lại danh sách</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>