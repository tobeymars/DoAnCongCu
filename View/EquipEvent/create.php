<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../../Model/EquipmentModel.php';
require_once __DIR__ . '/../../Model/EquipEventModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';
$eventId = $_GET["eventId"] ?? "";
$database = new Database();
$conn = $database->getConnection();
$eventModel = new Event($conn);
$event = $eventModel->getEventById($eventId);

if (!$event) {
    echo "<p class='text-danger text-center'>Sự kiện không tồn tại hoặc bạn không có quyền truy cập.</p>";
    exit();
}

$equipmentModel = new Equipment($conn);
$equipments = $equipmentModel->getActiveEquipments();
// Lấy danh sách thiết bị đã có trong sự kiện này
$equipmentModel1 = new EventEquipment($conn);
$existingEquipments = $equipmentModel1->getEquipmentsByEventId($eventId);
$existingEquipmentIds = array_unique(array_column($existingEquipments, 'equipmentId'));

if (!$equipments) {
    echo "<p class='text-danger text-center'>Không thể tải danh sách thiết bị.</p>";
    exit();
}
$equipments = $equipments->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Thiết Bị Cho Sự Kiện</title>
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
        <h2 class="text-center">Thêm Thiết Bị Cho Sự Kiện</h2>
        <form action="../../Controller/EquipeventController.php?action=create" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="EventId" value="<?= htmlspecialchars($eventId) ?>">

            <div class="mb-3">
                <label class="form-label">Sự Kiện:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($event['EventName']) ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Ngày Diễn Ra:</label>
                <input type="date" class="form-control" name="Date" value="<?= date('Y-m-d', strtotime($event['EventDate'])) ?>" readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Thiết Bị:</label>
                <select class="form-select" name="EquipmentId" required>
                    <option value="">-- Chọn thiết bị --</option>
                    <?php foreach ($equipments as $equipment): ?>
                        <?php
                        $equipmentId = $equipment['EquipmentId'] ?? $equipment['equipmentId']; // Đảm bảo key đúng
                        if (in_array($equipmentId, $existingEquipmentIds)) {
                            continue; // Bỏ qua thiết bị đã thêm
                        }
                        ?>
                        <option value="<?= htmlspecialchars($equipmentId) ?>">
                            <?= htmlspecialchars($equipment['EquipmentName']) ?> (Còn <?= $equipment['Quantity'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="quantity">Số lượng:</label>
                <input type="number" name="Quantity" id="quantity" value="1" min="1" required>
            </div>
            <input type="hidden" name="CreatedBy" value="<?= htmlspecialchars($createdBy) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <div style="display: flex; justify-content: space-between; margin: 20px; gap: 15px;">
                <button type="submit" class="btn btn-success w-50" style="padding: 12px 20px; font-size: 16px; border-radius: 8px; transition: all 0.3s ease;">Thêm</button>
                <button type="button" class="btn btn-secondary w-50" onclick="window.history.back()" style="padding: 12px 20px; font-size: 16px; border-radius: 8px; transition: all 0.3s ease;">Quay Lại</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include '../shares/footer.php'; ?>

</html>

<script>
    document.getElementById("quantity").addEventListener("input", function() {
        let maxQuantity = <?= json_encode(array_column($equipments, 'Quantity', 'EquipmentId')) ?>;
        let equipmentSelect = document.querySelector("select[name='EquipmentId']");
        let selectedEquipment = equipmentSelect.value;
        let quantityInput = document.getElementById("quantity");

        if (!selectedEquipment) {
            alert("Vui lòng chọn thiết bị trước khi nhập số lượng.");
            quantityInput.value = 1; // Reset số lượng về 1
            return;
        }

        if (maxQuantity[selectedEquipment] && quantityInput.value > maxQuantity[selectedEquipment]) {
            alert("Số lượng nhập vào vượt quá số lượng có sẵn: " + maxQuantity[selectedEquipment]);
            quantityInput.value = maxQuantity[selectedEquipment];
        }
    });
</script>