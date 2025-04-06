<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EquipmentModel.php';
require_once __DIR__ . '/../../Model/EquipmentTypeModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';

// $token = $_GET["token"] ?? "";
// $userData = JWTHelper::verifyToken($token);
// if (!$userData || $userData["role"] !== "admin") {
//     echo json_encode(["error" => "Bạn không có quyền truy cập!"]);
//     exit();
// }
$database = new Database();
$conn = $database->getConnection();
$model = new Equipment($conn);
$equipments = $model->getAllEquipments()->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Thiết Bị</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body{
            padding-left: 275px;
        }
        h2.text-center {
            padding-top: 60px;
            /* Điều chỉnh khoảng cách theo ý muốn */
        }
        .text-success {
            color: green;
            font-weight: bold;
        }

        .text-danger {
            color: red;
            font-weight: bold;
        }

        .container {
            margin-top: 30px;
        }

        table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            text-align: center;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Danh Sách Thiết Bị</h2>
        <a href="create.php" class="btn btn-primary mb-3">Thêm Thiết Bị</a>
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-dark">
                <tr>
                    <th>Tên Thiết Bị</th>
                    <th>Loại Thiết Bị</th>
                    <th>Số Lượng</th>
                    <th>Trạng Thái</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipments as $equipment) : ?>
                    <tr>
                        <td><?= htmlspecialchars($equipment['EquipmentName']) ?></td>
                        <td><?= htmlspecialchars($equipment['EquipmentTypeName']) ?></td>
                        <td><?= htmlspecialchars($equipment['Quantity']) ?></td>
                        <td><?= $equipment['IsDeleted'] ? '<span class="text-danger">Đã xóa</span>' : '<span class="text-success">Có sẵn</span>' ?></td>
                        <td>
                            <?php if ($equipment['IsDeleted'] == 0): ?>
                                <a href="edit.php?id=<?= $equipment['EquipmentId'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="delete.php?id=<?= $equipment['EquipmentId'] ?>" class="btn btn-danger btn-sm">Xóa</a>
                            <?php else: ?>
                                <a href="restore.php?id=<?= $equipment['EquipmentId'] ?>" class="btn btn-success btn-sm">Khôi phục</a>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Xử lý sự kiện khi bấm nút xóa
        document.querySelectorAll(".delete-btn").forEach(button => {
            button.addEventListener("click", function() {
                let equipmentId = this.getAttribute("data-id");
                if (confirm("Bạn có chắc chắn muốn xóa thiết bị này không?")) {
                    fetch('../../controller/EquipmentController.php?action=delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'EquipmentId=' + equipmentId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                let row = button.closest("tr");
                                row.querySelector("td:nth-child(4)").innerHTML = '<span class="text-danger">Đã xóa</span>';
                                row.querySelector(".delete-btn").remove();
                                let restoreBtn = document.createElement("button");
                                restoreBtn.classList.add("btn", "btn-success", "btn-sm", "restore-btn");
                                restoreBtn.setAttribute("data-id", equipmentId);
                                restoreBtn.textContent = "Khôi phục";
                                restoreBtn.addEventListener("click", restoreEquipment);
                                row.querySelector("td:nth-child(5)").appendChild(restoreBtn);
                            } else {
                                alert("Xóa thất bại!");
                            }
                        });
                }
            });
        });

        // Xử lý sự kiện khi bấm nút khôi phục
        function restoreEquipment() {
            let equipmentId = this.getAttribute("data-id");
            fetch('../../controller/EquipmentController.php?action=restore', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'EquipmentId=' + equipmentId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let row = this.closest("tr");
                        row.querySelector("td:nth-child(4)").innerHTML = '<span class="text-success">Có sẵn</span>';
                        row.querySelector(".restore-btn").remove();
                        let deleteBtn = document.createElement("button");
                        deleteBtn.classList.add("btn", "btn-danger", "btn-sm", "delete-btn");
                        deleteBtn.setAttribute("data-id", equipmentId);
                        deleteBtn.textContent = "Xóa";
                        deleteBtn.addEventListener("click", function() {
                            let event = new Event("click");
                            this.dispatchEvent(event);
                        });
                        row.querySelector("td:nth-child(5)").appendChild(deleteBtn);
                    } else {
                        alert("Khôi phục thất bại!");
                    }
                });
        }
    });
</script>