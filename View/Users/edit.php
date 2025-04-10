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
    <title>Chỉnh sửa người dùng</title>
</head>
<style>
     :root {
            --primary-color: #2c3e50;
            --primary-light: #4895ef;
            --primary-dark: #3f37c9;
            --text-color: #ecf0f1;
            --text-light: #666;
            --background-light: #f8f9fa;
            --background-dark: #2b2d42;
            --success-color: #4caf50;
            --warning-color: #ff9800;
            --error-color: #f44336;
            --border-radius: 10px;
            --box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            display: flex;
            min-height: 100vh;
            background-color: var(--background-light);
            color: var(--text-color);
            padding-top: 57px;
            padding-left: 275px;
        }

        .user-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: bold;
            margin: 0 auto 20px;
        }

        .page-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }

        .section {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .section.active {
            display: block;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
            width: 100%;
            max-width: 600px;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-light);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 8px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            transition: var(--transition);
        }

        input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid #e1e5ea;
            border-radius: var(--border-radius);
            font-size: 15px;
            transition: var(--transition);
            background-color: #f8f9fa;
        }

        input:focus {
            border-color: #4361ee;
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            background-color: white;
        }

        input:focus+i {
            color: #4361ee;
        }

        .btn {
            background: #4361ee;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            padding: 14px 22px;
            width: 100%;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .account-info {
            margin-top: 15px;
        }

        .info-item {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            width: 140px;
            color: var(--text-light);
        }

        .info-value {
            flex: 1;
            color: #333;
        }   
</style>
<body>
    <div id="info" class="section active">
        <h2 class="page-title">Thông tin cá nhân</h2>

        <div class="card">
            <div class="card-header">
                <div style="display: flex; align-items: center;">
                    <div class="card-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h3>Chỉnh sửa thông tin</h3>
                </div>
            </div>

            <form action="../../Controller/UserController.php?action=edituser" method="POST">
                <div class="form-group">
                <input type="hidden" value="<?= htmlspecialchars($users['UserId']) ?>" name="UserId">
                    <label for="fullName">Họ tên</label>
                    <div class="input-group">
                        <input type="text" value="<?= htmlspecialchars($users['FullName']) ?>" name="FullName" placeholder="Nhập họ tên">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <input type="email" value="<?= htmlspecialchars($users['Email']) ?>" name="Email" placeholder="Nhập email">
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="sdt">Số điện thoại</label>
                    <div class="input-group">
                        <input type="text" name="sdt" value="<?= htmlspecialchars($users['sdt']) ?>" placeholder="Nhập số điện thoại">
                        <i class="fas fa-phone"></i>
                    </div>
                </div>

                <button type="submit" class="btn">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
            </form>
        </div>
    </div>
</body>

</html>