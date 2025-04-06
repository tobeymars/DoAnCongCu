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

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../shares/style.css">
</head>
<header class="navbar1">
    <nav class="nav-container">
        <ul class="nav-list">
            <li class="nav-item"><a class="nav-link1" href="/Quanlysukien/View/Home/home.php">Trang chủ</a></li>
            <li class="nav-item"><a class="nav-link1" href="/Quanlysukien/View/Home/Event.php">Sự kiện</a></li>
            <li class="nav-item"><a class="nav-link1" href="/Quanlysukien/View/Home/Venue.php">Địa điểm</a></li>
            <li class="nav-item"><a class="nav-link1" href="/Quanlysukien/View/Home/Contact.php">Liên hệ</a></li>
        </ul>
    </nav>
    <div class="user-info">
        <p>Đang tải...</p>
    </div>
</header>

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
                        localStorage.removeItem("token");
                        localStorage.removeItem("userInfo");
                        userInfo.innerHTML = `<button onclick="window.location.href='/Quanlysukien/View/Users/register.php'">Đăng nhập</button>`;
                    }
                })
                .catch(error => {
                    localStorage.removeItem("token");
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