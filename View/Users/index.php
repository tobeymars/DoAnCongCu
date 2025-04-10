<?php
session_start();
// Ví dụ kiểm tra quyền
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 1) {
    header('Location: ../AccessDenied.php');
    exit();
}
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/UserModel.php';
$database = new Database();
$conn = $database->getConnection();
$model = new UserModel($conn);
$users = $model->getAllUser()->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../shares/adminhd.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary1-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            padding-left: 250px;
            padding-top: 40px;
        }

        .container {
            margin-top: 30px;
            padding: 0 25px;
        }

        .page-header {
            padding-bottom: 15px;
            margin-bottom: 25px;
            border-bottom: 1px solid #e3e6f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 0;
            font-size: 1.75rem;
        }

        .btn-add-user {
            background-color: var(--secondary1-color);
            border-color: var(--secondary1-color);
            color: white;
            font-weight: 600;
            border-radius: 5px;
            padding: 10px 20px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-add-user:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 30px;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 1.25rem;
            color: var(--dark-color);
            font-weight: 700;
            margin: 0;
        }

        .card-body {
            padding: 0;
        }

        .search-box {
            position: relative;
            width: 250px;
        }

        .search-box input {
            border-radius: 20px;
            padding-left: 40px;
            border: 1px solid #e3e6f0;
            transition: all 0.3s;
        }

        .search-box input:focus {
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
            border-color: #bac8f3;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 10px;
            color: var(--secondary1-color);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background-color: #f8f9fc;
            border-bottom: 2px solid #e3e6f0;
            color: var(--secondary1-color);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.05em;
            padding: 15px;
            vertical-align: middle;
        }

        .table tbody tr {
            border-bottom: 1px solid #e3e6f0;
            transition: all 0.2s;
        }

        .table tbody tr:last-child {
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
            transform: scale(1.003);
        }

        .table td {
            padding: 15px;
            color: var(--dark-color);
            vertical-align: middle;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--secondary1-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
        }

        .username {
            font-weight: 700;
            color: var(--secondary1-color);
        }

        .status-badge {
            padding: 8px 12px;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 5px;
            letter-spacing: 0.05em;
        }

        .status-active {
            background-color: rgba(28, 200, 138, 0.1);
            color: var(--success-color);
        }

        .status-inactive {
            background-color: rgba(231, 74, 59, 0.1);
            color: var(--danger-color);
        }

        .role-badge {
            background-color: rgba(78, 115, 223, 0.1);
            color: var(--secondary1-color);
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .btn-action {
            padding: 8px 15px;
            font-size: 0.8rem;
            border-radius: 5px;
            margin-right: 5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-action:last-child {
            margin-right: 0;
        }

        .btn-edit {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: var(--dark-color);
        }

        .btn-edit:hover {
            background-color: #e0ae37;
            border-color: #e0ae37;
            color: var(--dark-color);
        }

        .btn-delete {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: white;
        }

        .btn-delete:hover {
            background-color: #d13c2d;
            border-color: #d13c2d;
        }

        .pagination {
            margin-top: 20px;
            justify-content: center;
        }

        .pagination .page-item .page-link {
            color: var(--secondary1-color);
            padding: 8px 14px;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--secondary1-color);
            border-color: var(--secondary1-color);
            color: white;
        }

        .empty-state {
            padding: 50px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--secondary1-color);
            opacity: 0.5;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: var(--dark-color);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--secondary1-color);
            margin-bottom: 20px;
        }

        @media (max-width: 992px) {
            body {
                padding-left: 0;
            }

            .container {
                padding: 0 15px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .card-header {
                flex-direction: column;
                gap: 15px;
            }

            .search-box {
                width: 100%;
            }
        }

        .success-message {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            display: none; /* Ẩn thông báo ban đầu */
        }
        .btn-restore {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: white;
        }

        .btn-restore:hover {
            background-color: #68c97d;
            border-color: #68c97d;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1><i class="fas fa-users me-2"></i>Quản Lý Người Dùng</h1>
            <a href="registeradmin.php" class="btn btn-add-user">
                <i class="fas fa-user-plus"></i> Thêm Người Dùng Mới
            </a>
        </div>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message"id="successMessage">
                <?= $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); // Xóa thông báo sau khi hiển thị 
            ?>
        <?php endif;
        ?>
        <div class="card">
            <div class="card-header">
                <h2>Danh Sách Người Dùng</h2>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm người dùng...">
                </div>
            </div>
            <div class="card-body">
                <?php if (count($users) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Người Dùng</th>
                                    <th>Email</th>
                                    <th>Vai Trò</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">
                                <?php foreach ($users as $user): ?>
                                    <tr class="user-row">
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <?= strtoupper(substr($user['FullName'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="username"><?= htmlspecialchars($user['Username']) ?></div>
                                                    <div class="fullname"><?= htmlspecialchars($user['FullName']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-envelope text-secondary me-2"></i><?= htmlspecialchars($user['Email']) ?>
                                        </td>
                                        <td>
                                            <span class="role-badge">
                                                <?php if (strtolower($user['RoleName']) === 'admin'): ?>
                                                    <i class="fas fa-user-shield me-1"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-user me-1"></i>
                                                <?php endif; ?>
                                                <?= htmlspecialchars($user['RoleName']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($user['IsDeleted'] == 1): ?>
                                                <span class="status-badge status-inactive">
                                                    <i class="fas fa-ban me-1"></i> Bị khóa
                                                </span>
                                            <?php else: ?>
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-check-circle me-1"></i> Hoạt động
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                        <?php if ($user['IsDeleted'] == 0): ?>
                                            <a href="edit.php?id=<?= $user['UserId'] ?>" class="btn btn-action btn-edit">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <a href="delete.php?id=<?= $user['UserId'] ?>" class="btn btn-action btn-delete">
                                                <i class="fas fa-trash-alt"></i> Xóa
                                            </a>
                                            <?php else: ?>
                                                <a href="restore.php?id=<?= $user['UserId'] ?>" class="btn btn-action btn-restore">
                                                <i class="fas fa-undo me-1"></i> Khôi phục</a>
                                                <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-user-slash"></i>
                        <h3>Không có người dùng nào</h3>
                        <p>Danh sách người dùng trống. Hãy thêm người dùng mới để bắt đầu.</p>
                        <a href="registeradmin.php" class="btn btn-add-user">
                            <i class="fas fa-user-plus"></i> Thêm Người Dùng Mới
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(username) {
            if (confirm("Bạn có chắc chắn muốn xóa người dùng " + username + "?")) {
                window.location.href = "delete.php?username=" + username;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const userTableBody = document.getElementById('userTableBody');
            const rows = userTableBody.querySelectorAll('tr.user-row');

            searchInput.addEventListener('keyup', function() {
                const searchTerm = searchInput.value.toLowerCase();

                rows.forEach(row => {
                    const username = row.querySelector('.username').textContent.toLowerCase();
                    const fullname = row.querySelector('.fullname').textContent.toLowerCase();
                    const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const role = row.querySelector('.role-badge').textContent.toLowerCase();

                    if (username.includes(searchTerm) ||
                        fullname.includes(searchTerm) ||
                        email.includes(searchTerm) ||
                        role.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
        window.onload = function() {
        var message = document.getElementById('successMessage');
        if (message) {
            message.style.display = 'block'; // Hiển thị thông báo
            setTimeout(function() {
                message.style.display = 'none'; // Ẩn thông báo sau 3 giây
            }, 3000); // 3000ms = 3 giây
        }
    };
    </script>
</body>

</html>