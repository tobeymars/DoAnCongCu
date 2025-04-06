<?php
require_once __DIR__ . "/../Users/JWTHelper.php";
function getAuthenticatedUser()
{
    $headers = getallheaders();
    if (!isset($headers["Authorization"])) {
        return null;
    }
    $token = str_replace("Bearer ", "", $headers["Authorization"]);
    return JWTHelper::verifyToken($token);
}

$user = getAuthenticatedUser();
?>
<?php
$current_page = $_SERVER['REQUEST_URI'];
?>
<link rel="stylesheet" href="../shares/admin-style.css">

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<header class="admin-header">
    <div class="admin-logo">
        <h1>Admin Panel</h1>
    </div>
    <div class="user-info">
        <p>Đang tải...</p>
    </div>
</header>

<body>
    <div class="admin-sidebar">
        <nav class="admin-nav">
            <ul>
                <li class="<?= (strpos($current_page, '/View/Home/')!== false) ? 'active' : '' ?>">
                    <a href="/Quanlysukien/View/Home/homeadmin.php"><i class="fas fa-tachometer-alt"></i> Thống kê</a>
                </li>
                <li class="<?= (strpos($current_page, '/View/Event/') !== false) ? 'active' : '' ?>">
                    <a href="/Quanlysukien/View/Event/index.php"><i class="fas fa-calendar-alt"></i> Sự kiện</a>
                </li>
                <li class="<?= (strpos($current_page, '/View/Users/') !== false) ? 'active' : '' ?>">
                    <a href="/Quanlysukien/View/Users/index.php"><i class="fas fa-users"></i> Người dùng</a>
                </li>
                <li class="<?= (strpos($current_page, '/View/Venues/') !== false) ? 'active' : '' ?>">
                    <a href="/Quanlysukien/View/Venues/index.php"><i class="fas fa-map-marker-alt"></i> Địa điểm</a>
                </li>
                <li class="<?= (strpos($current_page, '/View/Eventtype/') !== false) ? 'active' : '' ?>">
                    <a href="/Quanlysukien/View/Eventtype/index.php"><i class="fas fa-tag text"></i> Loại sự kiện</a>
                </li>
                <li class="<?= (strpos($current_page, '/View/Tickettype/') !== false) ? 'active' : '' ?>">
                    <a href="/Quanlysukien/View/Tickettype/index.php"><i class="fa-solid fa-ticket"></i> Vé</a>
                </li>
                <li class="<?= (strpos($current_page, '/View/Booking/') !== false) ? 'active' : '' ?>">
                    <a href="/Quanlysukien/View/Booking/index.php"><i class="fa-solid fa-file-invoice"></i> Đơn đặt</a>
                </li>
                <li class="<?= (strpos($current_page, '/View/Equipment/') !== false) ? 'active' : '' ?>">
                    <a href="/Quanlysukien/View/Equipment/index.php"><i class="fa-solid fa-toolbox"></i> Thiết bị</a>
                </li>
                <li class="<?= (strpos($current_page, '/View/EquipmentType/') !== false) ? 'active' : '' ?>">
                    <a href="/Quanlysukien/View/EquipmentType/index.php"><i class="fa-solid fa-microchip"></i> Loại thiết bị</a>
                </li>
            </ul>
        </nav>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let currentPage = window.location.pathname.split("/").pop();
            if (currentPage === "register.php") {
                document.querySelector(".user-info").style.display = "none";
                return;
            }
            let token = localStorage.getItem("token");
            let userInfo = document.querySelector(".user-info");
            if (token) {
                fetch("/Quanlysukien/View/Users/getUser.php?token=" + encodeURIComponent(token))
                    .then(response => response.json())
                    .then(data => {
                        let userInfo = document.querySelector(".user-info");
                        if (data.username) {
                            userInfo.innerHTML = `
                    <p>Xin chào, ${data.fullName}</p>
                    <button class="user-icon" onclick="viewProfile()">
                        <i class="fas fa-user"></i>
                    </button>
                    <button class="logout-icon" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                `;
                        } else {
                            userInfo.innerHTML = `<button onclick="window.location.href='/Quanlysukien/View/Users/register.php'">Đăng nhập</button>`;
                        }
                    })
                    .catch(error => {
                        console.error("Lỗi khi lấy user:", error);
                        document.querySelector(".user-info").innerHTML = `<button onclick="window.location.href='/Quanlysukien/View/Users/register.php'">Đăng nhập</button>`;
                    });
            } else {
                document.querySelector(".user-info").innerHTML = `<button onclick="window.location.href='/Quanlysukien/View/Users/register.php'">Đăng nhập</button>`;
            }
        });

        function logout() {
            localStorage.removeItem("token");
            localStorage.removeItem("userInfo");
            window.location.href = "/Quanlysukien/View/users/register.php";
        }

        function viewProfile() {
            let token = localStorage.getItem("token");
            if (!token) {
                alert("Bạn chưa đăng nhập!");
                return;
            }

            fetch(`/Quanlysukien/View/Users/getUser.php?token=${encodeURIComponent(token)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert("Lỗi: " + data.error);
                    } else {
                        localStorage.setItem("userInfo", JSON.stringify(data));
                        window.location.href = `/Quanlysukien/View/Users/detail.php?token=${encodeURIComponent(token)}`;
                    }
                })
                .catch(error => console.error("Lỗi lấy thông tin người dùng:", error));
        }
    </script>