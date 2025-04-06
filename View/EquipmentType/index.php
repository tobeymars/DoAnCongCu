<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EquipmentTypeModel.php';

$database = new Database();
$conn = $database->getConnection();
$model = new EquipmentType($conn);
$equipmentTypes = $model->getAllEquipmentTypes()->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh Sách Loại Thiết Bị</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            padding-left: 275px;
        }
        .container {
            margin-top: 50px;
        }
        h1 {
            color: #007bff;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-add {
            display: block;
            width: 200px;
            margin: 20px auto;
        }
        table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }
        th, td {
            text-align: center;
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-inactive {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Danh Sách Loại Thiết Bị</h1>
        <a href="create.php" class="btn btn-primary btn-add">+ Thêm Loại Thiết Bị</a>

        <table class="table table-bordered table-hover bg-white">
            <thead class="table-primary">
                <tr>
                    <th>Loại Thiết Bị</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipmentTypes as $equipmentType): ?>
                    <tr>
                        <td><?= htmlspecialchars($equipmentType['EquipmentTypeName']) ?></td>
                        <td>
                            <?= $equipmentType['IsDeleted'] == 1
                                ? '<span class="status-inactive">Hết sử dụng</span>'
                                : '<span class="status-active">Còn sử dụng</span>';
                            ?>
                        </td>
                        <td>
                            <?php if ($equipmentType['IsDeleted'] == 0): ?>
                                <a href="edit.php?id=<?= $equipmentType['EquipmentTypeId'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="delete.php?id=<?= $equipmentType['EquipmentTypeId'] ?>" class="btn btn-danger btn-sm">Xóa</a>
                            <?php else: ?>
                                <a href="restore.php?id=<?= $equipmentType['EquipmentTypeId'] ?>" class="btn btn-success btn-sm">Khôi phục</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
