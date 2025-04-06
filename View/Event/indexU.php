<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';

$token = $_GET["token"] ?? "";
$userData = JWTHelper::verifyToken($token);
if (!$userData) {
    echo '<script type="text/javascript">';
    echo 'alert("Bạn cần đăng nhập để xem các sự kiện mình tổ chức!");'; // Hiển thị thông báo
    echo 'window.location.href = "../../View/Users/register.php";'; // Chuyển hướng đến trang đăng ký sau khi thông báo
    echo '</script>';
    exit();
}

$userId = $userData['user_id'];
$database = new Database();
$conn = $database->getConnection();

$eventModel = new Event($conn);
$userEvents = $eventModel->getUserEvents($userId);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh Sách Sự Kiện Của Bạn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
        padding-top: 40px;
    }

    .container {
        margin-top: 30px;
    }

    ul {
        margin-top: 10px;
        margin-bottom: 1rem;
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

    th,
    td {
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

<body>
    <div class="container mt-5">
        <h2 class="text-center">Danh Sách Sự Kiện Của Bạn</h2>
        <a href="create.php?token=<?= urlencode($token) ?>" class="btn btn-primary btn-add" id="btn-add">+ Thêm Sự Kiện</a>
        <?php if (empty($userEvents)): ?>
            <p class="text-center">Bạn chưa có sự kiện nào.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tên Sự Kiện</th>
                        <th>Ngày Diễn Ra</th>
                        <th>Địa Điểm</th>
                        <th>Loại Sự Kiện</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                        <th>Thiết bị</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userEvents as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event['EventName']) ?></td>
                            <td><?= htmlspecialchars($event['EventDate']) ?></td>
                            <td><?= htmlspecialchars($event['VenueName']) ?></td>
                            <td><?= htmlspecialchars($event['TypeName']) ?></td>
                            <td>
                                <?= $event['status'] == 1
                                    ? '<span class="status-inactive">Hoàn thành</span>'
                                    : '<span class="status-active">Còn hiệu lực</span>';
                                ?>
                            </td>
                            <td>
                                <?php if ($event['IsDeleted'] == 0): ?>
                                    <a href="edit.php?id=<?= $event['EventId'] ?>&token=<?= urlencode($token) ?>" class="btn btn-warning btn-sm btn-edit" data-id="<?= $event['EventId'] ?>">Sửa</a>
                                    <a href="delete.php?id=<?= $event['EventId'] ?>&token=<?= urlencode($token) ?>" class="btn btn-danger btn-sm" data-id="<?= $event['EventId'] ?>">Xóa</a>
                                <?php else: ?>
                                    <a href="restore.php?id=<?= $event['EventId'] ?>&token=<?= urlencode($token) ?>" class="btn btn-success btn-sm" data-id="<?= $event['EventId'] ?>">Khôi phục</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/Quanlysukien/View/EquipEvent/indexU.php?eventId=<?= $event['EventId'] ?>&token=<?= urlencode($token) ?>" class="btn btn-info btn-sm">Xem Thiết Bị</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <a  href="/Quanlysukien/View/Users/detail.php?token=<?= urlencode($token) ?>" class="btn btn-secondary btn-back" style="margin-top: 20px;"> <i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const token = localStorage.getItem("token");
        console.log("Token từ localStorage: ", token);
        document.getElementById("btn-add").addEventListener("click", function(e) {
            e.preventDefault();

            if (token) {
                window.location.href = "create.php?token=" + encodeURIComponent(token);
            } else {
                alert("Vui lòng đăng nhập để tiếp tục.");
                window.location.href = "/Quanlysukien/View/Users/register.php";
            }
        });
        // Xử lý sự kiện cho tất cả nút "Sửa"
        document.querySelectorAll(".btn-edit").forEach(function(btn) {
            btn.addEventListener("click", function(e) {
                e.preventDefault();

                let eventId = this.getAttribute("data-id");
                if (token) {
                    window.location.href = "edit.php?id=" + eventId + "&token=" + encodeURIComponent(token);
                } else {
                    alert("Vui lòng đăng nhập để tiếp tục.");
                }
            });
        });
    </script>
</body>
<?php include '../shares/footer.php'; ?>
</html>