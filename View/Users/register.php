<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . '/../../Controller/UserController.php';
$database = new Database();
$conn = $database->getConnection();
$controller = new UserController($conn);
$message = $controller->registerUser();
$loginMessage = $controller->loginUser();
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tài khoản</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="icon-container" id="iconContainer">
        <img src="https://cdn-icons-png.flaticon.com/128/2787/2787936.png" alt="Event Icon" width="380px">
    </div>
    <div class="container hidden position-relative" id="Form">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h2>🚀 Xin chào</h2>
                <p>Tham gia ngay để không bỏ lỡ những khoảnh khắc đáng nhớ và trải nghiệm độc đáo!</p>
            </div>
            <div class="overlay-panel overlay-right">
                <h2>🎉 Chào mừng trở lại!</h2>
                <p>Đăng nhập ngay để tiếp tục hành trình khám phá những sự kiện thú vị!</p>
            </div>
        </div>
        <div class="row d-flex justify-content-between position-relative">
            <div class="col-md-5 login-form">
                <h2 class="text-center">Đăng nhập</h2>
                <?php if (isset($loginMessage)) echo "<div class='alert alert-info text-center'>$loginMessage</div>"; ?>
                <form method="POST" action="/Quanlysukien/Controller/UserController.php?action=login">
                    <div class="input-group">
                        <input type="text" name="username" class="form-control" required>
                        <label>Tên đăng nhập</label>
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <label>Mật khẩu</label>
                        <span class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                    </div>
                    <button type="submit" class="btn btn-primary custom-btn">Đăng nhập</button>
                    <button type="button" id="showRegister" class="btn btn-link" style="justify-content: center;width: 100%;">Chưa có tài khoản? Đăng ký</button>
                </form>
            </div>
            <div class="col-md-5 register-form">
                <h2 class="text-center">Đăng ký</h2>
                <div id="registerMessage" class="alert text-center mt-2" style="display: none;"></div>
                <?php if (isset($message)) echo "<div class='alert alert-info text-center'>$message</div>"; ?>
                <form method="POST" action="/Quanlysukien/Controller/UserController.php?action=register">
                    <div class="input-group">
                        <input type="text" name="fullname" class="form-control" required>
                        <label>Họ và tên</label>
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                    </div>
                    <div class="input-group">
                        <input type="text" name="username" class="form-control" required>
                        <label>Tên đăng nhập</label>
                        <span class="input-icon"><i class="fas fa-user"></i></span>
                    </div>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" required>
                        <label>Email</label>
                        <span class="input-icon"><i class="fas fa-envelope"></i></span>
                    </div>
                    <div class="input-group">
                        <input type="password" name="password" id="passwordnew" class="form-control" required>
                        <label>Mật khẩu</label>
                        <span class="toggle-password" onclick="togglePassword('passwordnew')">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                    </div>

                    <div class="input-group">
                        <input type="password" name="confirm_password" id="passwordaccess" class="form-control" required>
                        <label>Xác nhận mật khẩu</label>
                        <span class="toggle-password" onclick="togglePassword('passwordaccess')">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                    </div>

                    <input type="hidden" name="role" value="2">

                    <button type="submit" class="btn btn-primary custom-btn">Đăng ký</button>
                    <button type="button" id="showLogin" class="btn btn-link" style="justify-content: center;width: 100%;">Đã có tài khoản? Đăng nhập</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const iconContainer = document.getElementById("iconContainer");
            const registerForm = document.getElementById("Form");
            const body = document.body;
            const overlay = document.querySelector(".overlay");

            setTimeout(() => {
                iconContainer.classList.add("shrink");
                setTimeout(() => {
                    body.style.backgroundColor = "#f8f9fa";
                    iconContainer.classList.add("hidden");
                    registerForm.classList.remove("hidden");
                    registerForm.style.opacity = "1";
                    registerForm.style.transform = "translateY(0)";
                }, 500);
            }, 500);

            document.getElementById("showRegister").addEventListener("click", function() {
                overlay.classList.remove("move-right");
                overlay.classList.add("move-left");
            });

            document.getElementById("showLogin").addEventListener("click", function() {
                overlay.classList.remove("move-right");
                overlay.classList.add("move-right");
            });
            document.querySelector("form[action*='login']").addEventListener("submit", async function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: "POST",
                    body: formData
                });

                const data = await response.json();
                if (data.error) {
                    if (data.error === "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.") {
                        window.location.href = "/Quanlysukien/View/Users/Denied.php";
                    } else {
                        alert(data.error);
                    }
                    return;
                }
                if (data.token) {
                    localStorage.setItem("token", data.token);
                    // Gọi API lấy thông tin user và lưu vào localStorage
                    fetch(`/Quanlysukien/View/Users/getUser.php?token=${encodeURIComponent(data.token)}`)
                        .then(response => response.json())
                        .then(userInfo => {
                            if (userInfo.error) {
                                alert(userInfo.error);
                            } else {
                                localStorage.setItem("userInfo", JSON.stringify(userInfo));
                                if (userInfo.RoleId == 1) {
                                    window.location.href = "/Quanlysukien/View/Home/homeadmin.php";
                                } else {
                                    window.location.href = "/Quanlysukien/View/Home/home.php";
                                }
                            }
                        })
                        .catch(error => console.error("Lỗi lấy thông tin người dùng:", error));
                    window.location.href = "/Quanlysukien/View/Home/home.php";
                } else {
                    alert("Sai tài khoản hoặc mật khẩu");
                }
            });
            document.querySelector("form[action*='register']").addEventListener("submit", async function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: "POST",
                    body: formData
                });

                const data = await response.json();
                const messageBox = document.getElementById("registerMessage");

                messageBox.innerText = data.message;
                messageBox.className = "alert text-center mt-2 " + (data.success ? "alert-success" : "alert-danger");
                messageBox.style.display = "block"; // Hiển thị thông báo

                if (data.success) {
                    this.reset(); // Xóa nội dung form nếu đăng ký thành công
                }
            });
        });

        function togglePassword(id) {
            const passwordField = document.getElementById(id);
            if (!passwordField) return;
            const toggleSpan = passwordField.parentElement.querySelector(".toggle-password");
            if (!toggleSpan) return;
            const toggleIcon = toggleSpan.querySelector("i");
            if (!toggleIcon) return;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }
    </script>

    <link rel="stylesheet" href="styles.css">
</body>

</html>