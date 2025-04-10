<?php
require_once __DIR__ . "/../Users/JWTHelper.php";
$token = $_GET["token"] ?? "";
$userData = JWTHelper::verifyToken($token);
if (!$userData) {
    echo json_encode(["error" => "Invalid token"]);
    exit();
}
$roleId = $userData['RoleId'];
if ($roleId == 2) {
    include '../shares/header.php';
} else {
    include '../shares/adminhd.php';
}

$paddingLeftStyle = ($roleId == 1) ? 'body {padding-left: 250px;} .app-container { padding-top: 45px;}' : '.app-container {padding-top: 90px;}';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        <?php echo $paddingLeftStyle; ?> :root {
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
        }

        .app-container {
            display: flex;
            flex: 1;
            width: 100%;
        }

        .sidebar {
            width: 280px;
            background: var(--background-dark);
            color: white;
            padding: 25px 0;
            display: flex;
            flex-direction: column;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: calc(100vh - 80px);
            transition: transform 0.3s ease;
            z-index: 10;
        }

        .sidebar-header {
            padding: 20px 25px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar-header h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 14px;
            opacity: 0.7;
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

        .sidebar a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 12px 25px;
            display: flex;
            align-items: center;
            margin: 5px 0;
            transition: var(--transition);
            border-left: 3px solid transparent;
            font-weight: 500;
        }

        .sidebar a i {
            margin-right: 15px;
            font-size: 18px;
            min-width: 20px;
            text-align: center;
        }

        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar a.active {
            background: linear-gradient(to right, rgba(67, 97, 238, 0.1), transparent);
            color: white;
            border-left: 3px solid #4361ee;
        }

        .content {
            flex: 1;
            padding: 30px;
            margin-left: 280px;
            width: calc(100% - 280px);
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

        .toast {
            position: fixed;
            top: 80px;
            right: 20px;
            background: var(--success-color);
            color: white;
            padding: 15px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: flex;
            align-items: center;
            z-index: 100;
            opacity: 0;
            transform: translateY(-20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.error {
            background: var(--error-color);
        }

        .toast i {
            margin-right: 10px;
            font-size: 20px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .content {
                margin-left: 0;
                width: 100%;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .menu-toggle {
                display: block;
                position: fixed;
                top: 95px;
                left: 15px;
                background: #4361ee;
                color: white;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 100;
                cursor: pointer;
                box-shadow: var(--box-shadow);
            }
        }

        @media (max-width: 576px) {
            .content {
                padding: 20px 15px;
            }
        }
    </style>
</head>

<body>
    <div class="app-container">
        <!-- Sidebar Toggle Button for Mobile -->
        <div class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </div>

        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="user-avatar">
                    <span id="user-initials">US</span>
                </div>
                <h3>Tài khoản</h3>
                <p id="sidebar-username">username</p>
            </div>
            <a href="#" onclick="showSection('info')" class="active" id="tab-info">
                <i class="fas fa-user"></i> Thông tin cá nhân
            </a>
            <a href="#" onclick="showSection('account')" id="tab-account">
                <i class="fas fa-lock"></i> Tài khoản
            </a>
            <a href="/Quanlysukien/View/Event/indexU.php">
                <i class="fas fa-calendar-alt"></i> My Event
            </a>
            <a href="/Quanlysukien/View/Booking/indexU.php" onclick="Booking(event)">
                <i class="fas fa-book"></i> My Booking
            </a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <!-- Personal Info Section -->
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

                    <form id="editForm">
                        <div class="form-group">
                            <label for="fullName">Họ tên</label>
                            <div class="input-group">
                                <input type="text" id="fullName" name="fullName" placeholder="Nhập họ tên">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-group">
                                <input type="email" id="email" name="email" placeholder="Nhập email">
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sdt">Số điện thoại</label>
                            <div class="input-group">
                                <input type="text" id="sdt" name="sdt" placeholder="Nhập số điện thoại">
                                <i class="fas fa-phone"></i>
                            </div>
                        </div>

                        <button type="button" id="saveButton" class="btn">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>

            <!-- Account Section -->
            <div id="account" class="section">
                <h2 class="page-title">Thông tin tài khoản</h2>

                <div class="card">
                    <div class="card-header">
                        <div style="display: flex; align-items: center;">
                            <div class="card-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h3>Chi tiết tài khoản</h3>
                        </div>
                    </div>

                    <div class="account-info">
                        <div class="info-item">
                            <div class="info-label">Tên tài khoản:</div>
                            <div class="info-value" id="username">username</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Trạng thái:</div>
                            <div class="info-value"><span class="badge" style="background-color: var(--success-color); color: white; padding: 5px 10px; border-radius: 20px; font-size: 12px;">Hoạt động</span></div>
                        </div>
                    </div>

                    <button onclick="window.location.href='change_password.php?token=<?php echo urlencode($token); ?>'" class="btn" style="margin-top: 20px; background-color: var(--warning-color);">
                        <i class="fas fa-key"></i> Đổi mật khẩu
                    </button>
                </div>
            </div>
            <!-- Vùng chứa nội dung -->
            <div id="content"></div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="toast" id="toast">
        <i class="fas fa-check-circle"></i>
        <span id="toastMessage">Cập nhật thông tin thành công!</span>
    </div>

    <script>
        // Get user data
        let userInfo = localStorage.getItem("userInfo");
        if (userInfo) {
            let user = JSON.parse(userInfo);
            document.getElementById("fullName").value = user.fullName || "";
            document.getElementById("email").value = user.email || "";
            document.getElementById("username").innerText = user.username || "";
            document.getElementById("sidebar-username").innerText = user.username || "";
            document.getElementById("sdt").value = user.sdt ? user.sdt : "";

            // Set user initials
            if (user.fullName) {
                const nameParts = user.fullName.split(" ");
                const initials = nameParts.length > 1 ?
                    (nameParts[0][0] + nameParts[nameParts.length - 1][0]).toUpperCase() :
                    nameParts[0][0].toUpperCase();
                document.getElementById("user-initials").innerText = initials;
            }

        } else {
            showToast("Không có dữ liệu người dùng!", true);
            setTimeout(() => {
                window.location.href = "/Quanlysukien/index.php";
            }, 2000);
        }

        // Show/hide sections
        function showSection(sectionId) {
            document.querySelectorAll('.section').forEach(el => el.classList.remove('active'));
            document.getElementById(sectionId).classList.add('active');

            document.querySelectorAll('.sidebar a').forEach(el => el.classList.remove('active'));
            document.getElementById(`tab-${sectionId}`).classList.add('active');
        }

        // Save button event
        document.getElementById("saveButton").addEventListener("click", function() {
            let updatedUser = {
                fullName: document.getElementById("fullName").value,
                email: document.getElementById("email").value,
                username: document.getElementById("username").innerText,
                sdt: document.getElementById("sdt").value,
            };

            localStorage.setItem("userInfo", JSON.stringify(updatedUser));

            let formData = new FormData();
            formData.append("token", localStorage.getItem("token"));
            formData.append("fullName", updatedUser.fullName);
            formData.append("email", updatedUser.email);
            formData.append("sdt", updatedUser.sdt);

            fetch("/Quanlysukien/Controller/UserController.php?action=updateUser", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showToast("Cập nhật thông tin thành công!");

                        // Update user initials after successful update
                        if (updatedUser.fullName) {
                            const nameParts = updatedUser.fullName.split(" ");
                            const initials = nameParts.length > 1 ?
                                (nameParts[0][0] + nameParts[nameParts.length - 1][0]).toUpperCase() :
                                nameParts[0][0].toUpperCase();
                            document.getElementById("user-initials").innerText = initials;
                        }
                    } else {
                        showToast("Lỗi: " + result.error, true);
                    }
                })
                .catch(error => {
                    console.error("Lỗi cập nhật:", error);
                    showToast("Đã xảy ra lỗi khi cập nhật", true);
                });
        });

        // Toast function
        function showToast(message, isError = false) {
            const toast = document.getElementById("toast");
            const toastMessage = document.getElementById("toastMessage");

            if (isError) {
                toast.classList.add("error");
                toast.querySelector("i").className = "fas fa-exclamation-circle";
            } else {
                toast.classList.remove("error");
                toast.querySelector("i").className = "fas fa-check-circle";
            }

            toastMessage.textContent = message;
            toast.classList.add("show");

            setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }

        // Mobile menu toggle
        if (document.getElementById("menuToggle")) {
            document.getElementById("menuToggle").addEventListener("click", function() {
                document.getElementById("sidebar").classList.toggle("active");
            });
        }
        document.addEventListener("DOMContentLoaded", function() {
            let token = localStorage.getItem("token");
            let eventLink = document.querySelector("a[href='/Quanlysukien/View/Event/indexU.php']");
            if (eventLink && token) {
                eventLink.href = `/Quanlysukien/View/Event/indexU.php?token=${encodeURIComponent(token)}`;
            }
        });

        function Booking(event) {
            let userInfo = localStorage.getItem("userInfo");

            if (userInfo) {
                event.preventDefault(); // Ngừng hành động mặc định của thẻ <a>

                let userData = JSON.parse(userInfo);

                // Thay vì tìm phần tử <a>, trực tiếp chuyển hướng người dùng
                let bookingUrl = `/Quanlysukien/View/Booking/indexU.php?Id=${encodeURIComponent(userData.user_id)}`;
                window.location.href = bookingUrl; // Chuyển hướng đến trang mới với URL đã được cập nhật
            } else {
                console.error("Không tìm thấy thông tin người dùng trong localStorage");
            }
        }
    </script>
</body>

</html>