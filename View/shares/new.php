<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu đã thiết kế lại</title>
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #45a049;
            --text-color: #fff;
            --hover-bg: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .navbar {
            width: 100%;
            background: linear-gradient(135deg, #333333, #222222);
            padding: 12px 0;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        .nav-container {
            width: 90%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(15px);
            padding: 8px 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-weight: bold;
            font-size: 1.5rem;
            color: var(--primary-color);
            text-decoration: none;
            letter-spacing: 1px;
        }

        .nav-list {
            list-style: none;
            display: flex;
            gap: 10px;
            margin: 0;
            padding: 0;
        }

        .nav-item {
            display: inline-block;
        }

        .nav-link {
            text-decoration: none;
            color: var(--text-color);
            padding: 10px 16px;
            transition: all 0.3s ease;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link:hover {
            background: var(--hover-bg);
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        .nav-link i {
            font-size: 1.1rem;
        }

        /* Menu hamburger cho thiết bị di động */
        .menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
        }
        
        .bar {
            width: 25px;
            height: 3px;
            background-color: var(--text-color);
            margin: 3px 0;
            transition: 0.4s;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }
            
            .nav-list {
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background: #333;
                flex-direction: column;
                align-items: center;
                padding: 20px 0;
                gap: 15px;
                transform: translateY(-150%);
                transition: transform 0.3s ease-in-out;
                z-index: 999;
            }
            
            .nav-list.active {
                transform: translateY(0);
            }
            
            .nav-container {
                width: 95%;
                padding: 10px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/Quanlysukien/View/Home/home.php?" class="logo">QLSK</a>
            
            <div class="menu-toggle" id="mobile-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>
            
            <ul class="nav-list" id="nav-menu">
                <li class="nav-item">
                    <a class="nav-link active" href="/Quanlysukien/View/Home/home.php?">
                        <i class="fas fa-home"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Quanlysukien/View/Home/Events.php">
                        <i class="fas fa-calendar-alt"></i> Sự kiện
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/Quanlysukien/View/Home/Contact.php">
                        <i class="fas fa-envelope"></i> Liên hệ
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <script>
        // JavaScript cho menu di động
        document.getElementById('mobile-menu').addEventListener('click', function() {
            document.getElementById('nav-menu').classList.toggle('active');
        });
        
        // Highlight menu hiện tại
        document.addEventListener('DOMContentLoaded', function() {
            const currentLocation = window.location.href;
            const menuItems = document.querySelectorAll('.nav-link');
            
            menuItems.forEach(item => {
                if(currentLocation.includes(item.getAttribute('href'))) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>