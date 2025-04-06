<?php include '../shares/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Từ chối truy cập</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> <!-- Google Font -->
    <style>
        body {
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        h1 {
            font-size: 36px;
            color: #e74c3c;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }

        a {
            font-size: 16px;
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        a:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .button-container {
            margin-top: 20px;
        }

        .button-container a {
            padding: 12px 25px;
            background-color: #3498db;
            color: white;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .button-container a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Từ chối truy cập</h1>
        <p>Rất tiếc, tài khoản của bạn đã bị khóa hoặc không có quyền truy cập vào hệ thống. Vui lòng liên hệ với quản trị viên.</p>
        <div class="button-container">
            <a href="/Quanlysukien/View/Home/home.php">Quay lại trang chủ</a>
        </div>
    </div>
</body>
</html>