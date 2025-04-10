<?php
require_once __DIR__ . "/../Users/JWTHelper.php";
require_once __DIR__ . "/../../Model/UserModel.php";
require_once __DIR__ . "/../../config/db.php";
$token = $_GET['token'] ?? '';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đổi Mật Khẩu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        #changePasswordForm {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 35px 40px;
            transform: translateY(0);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        #changePasswordForm::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #4776E6 0%, #8E54E9 100%);
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 24px;
            position: relative;
            padding-bottom: 15px;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, #4776E6 0%, #8E54E9 100%);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
            font-size: 15px;
            font-weight: 500;
            transition: color 0.3s;
        }

        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9fafc;
            font-size: 15px;
            box-sizing: border-box;
            transition: all 0.3s;
        }

        input[type="password"]:focus {
            border-color: #8E54E9;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(142, 84, 233, 0.15);
            outline: none;
        }

        .form-group:focus-within label {
            color: #8E54E9;
        }

        .password-strength {
            height: 4px;
            margin-top: 8px;
            border-radius: 2px;
            background-color: #eee;
            overflow: hidden;
            display: none;
        }

        #newPassword:focus~.password-strength {
            display: block;
        }

        .strength-meter {
            height: 100%;
            width: 0;
            transition: width 0.5s ease, background-color 0.5s ease;
        }

        button[type="submit"] {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(90deg, #4776E6 0%, #8E54E9 100%);
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(71, 118, 230, 0.2);
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(71, 118, 230, 0.3);
        }

        button[type="submit"]:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(71, 118, 230, 0.2);
        }

        #result {
            margin-top: 25px;
            padding: 16px;
            border-radius: 8px;
            text-align: center;
            font-size: 15px;
            font-weight: 500;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }

        #result:not(:empty) {
            opacity: 1;
            transform: translateY(0);
        }

        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #2e7d32;
        }

        .error {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #c62828;
        }

        /* Animation d'apparition */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #changePasswordForm {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Responsive Design */
        @media screen and (max-width: 576px) {
            .container {
                padding: 15px;
            }

            #changePasswordForm {
                padding: 25px 20px;
            }

            h2 {
                font-size: 20px;
            }

            input[type="password"] {
                padding: 12px;
            }

            button[type="submit"] {
                padding: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <form id="changePasswordForm">
            <h2>Đổi Mật Khẩu</h2>
            <input type="hidden" id="token" value="<?php echo htmlspecialchars($token); ?>" />
            <div class="form-group">
                <label for="currentPassword">Mật khẩu hiện tại :</label>
                <input type="password" id="currentPassword" required>
            </div>

            <div class="form-group">
                <label for="newPassword">Mật khẩu mới :</label>
                <input type="password" id="newPassword" required>
                <div class="password-strength">
                    <div class="strength-meter"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Xác nhận mật khẩu mới :</label>
                <input type="password" id="confirmPassword" required>
            </div>
            <button type="submit" class="btn">Đổi mật khẩu</button>
        </form>

        <div id="result"></div>

        <script>
            document.getElementById("changePasswordForm").addEventListener("submit", async function(e) {
                e.preventDefault();

                const token = document.getElementById("token").value;
                const currentPassword = document.getElementById("currentPassword").value;
                const newPassword = document.getElementById("newPassword").value;
                const confirmPassword = document.getElementById("confirmPassword").value;

                const resultDiv = document.getElementById("result");
                resultDiv.textContent = '';
                resultDiv.className = '';

                const response = await fetch("/Quanlysukien/Controller/UserController.php?action=changePassword", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        token,
                        currentPassword,
                        newPassword,
                        confirmPassword
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    resultDiv.textContent = result.message;
                    resultDiv.className = "success";
                    window.location.href = "detail.php?token=" + encodeURIComponent(token);
                } else {
                    resultDiv.textContent = result.message || result.error || "Đã xảy ra lỗi.";
                    resultDiv.className = "error";
                }
            });
        </script>

</body>

</html>