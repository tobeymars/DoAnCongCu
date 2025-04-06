<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/UserModel.php';

$database = new Database();
$conn = $database->getConnection();
$model = new UserModel($conn);
// Kiểm tra nếu có ID vé cần chỉnh sửa
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Không tìm thấy vé!'); window.location.href='index.php';</script>";
    exit();
}
$users = $model->getUserInfo($_GET['id']);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Khôi Phục Người Dùng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            padding: 50px 0 0 275px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            border: none;
        }

        .card-header1 {
            background-color: #f8f9fa;
            border-radius: 10px 10px 0 0 !important;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
        }

        .ticket-info {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .btn-group {
            display: flex;
            gap: 10px;
        }

        .btn {
            border-radius: 5px;
            padding: 8px 20px;
            font-weight: 500;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .badge {
            font-size: 14px;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .ticket-name {
            font-size: 1.2rem;
            color: #333;
            font-weight: bold;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-header1 d-flex align-items-center">
                        <i class="fas fa-ticket-alt me-2"></i>
                        <h2 class="m-0 fs-4">Xác Nhận Khôi Phục Người Dùng</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Bạn đang khôi phục Người Dùng đã bị xóa trước đó. Vui lòng xem lại thông tin và xác nhận.
                        </div>

                        <div class="ticket-info">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-tag me-2"></i>Tài khoản:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="ticket-name"><?= htmlspecialchars($users['Username']) ?></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-tag me-2"></i>Tên người dùng:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="ticket-name"><?= htmlspecialchars($users['FullName']) ?></span>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-tag me-2"></i>Email:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="ticket-name"><?= htmlspecialchars($users['Email']) ?></span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong><i class="fas fa-sort-amount-up me-2"></i>Số điện thoại:</strong>
                                </div>
                                <div class="col-md-8">
                                    <?= $users['sdt'] ?>
                                </div>
                            </div>
                        </div>

                        <form action="../../controller/UserController.php?action=restore" method="POST">
                            <input type="hidden" name="UserId" value="<?= $users['UserId'] ?>">
                            <div class="d-flex justify-content-end btn-group mt-3">
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Hủy
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-undo me-1"></i> Khôi Phục
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="index.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách người dùng
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>