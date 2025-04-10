<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            font-family: 'Montserrat', 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }

        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 550px;
            width: 90%;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .container::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #ff416c, #ff4b2b);
        }

        .icon {
            display: block;
            margin: 0 auto 1.5rem;
            width: 80px;
            height: 80px;
            background-color: rgba(220, 53, 69, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon svg {
            width: 40px;
            height: 40px;
            fill: #dc3545;
        }

        .container h1 {
            color: #dc3545;
            font-size: 2.2rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .container p {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .btn-home {
            display: inline-block;
            padding: 0.8rem 2rem;
            background: linear-gradient(90deg, #4776E6, #8E54E9);
            color: white;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 123, 255, 0.3);
        }
        
        .btn-home:active {
            transform: translateY(1px);
        }
        
        .decoration {
            position: absolute;
            opacity: 0.05;
            z-index: -1;
        }
        
        .circle1 {
            width: 200px;
            height: 200px;
            background: #dc3545;
            border-radius: 50%;
            top: -100px;
            right: -50px;
        }
        
        .circle2 {
            width: 150px;
            height: 150px;
            background: #007bff;
            border-radius: 50%;
            bottom: -70px;
            left: -30px;
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 2rem;
            }
            
            .container h1 {
                font-size: 1.8rem;
            }
            
            .container p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="decoration circle1"></div>
    <div class="decoration circle2"></div>
    
    <div class="icon">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 11c-.55 0-1-.45-1-1V8c0-.55.45-1 1-1s1 .45 1 1v4c0 .55-.45 1-1 1zm1 4h-2v-2h2v2z"/>
        </svg>
    </div>
    
    <h1>Access Denied</h1>
    <p>Xin lỗi, bạn không có quyền truy cập vào trang này. Vui lòng liên hệ quản trị viên nếu bạn cho rằng đây là lỗi.</p>
    <a href="Home/Home.php" class="btn-home">Quay về trang chủ</a>
</div>

</body>
</html>