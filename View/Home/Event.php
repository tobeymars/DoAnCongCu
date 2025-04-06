<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../../Model/EventtypeModel.php';
require_once __DIR__ . '/../../Model/VenuesModel.php';

// Tạo đối tượng kết nối CSDL
$database = new Database();
$db = $database->getConnection();
$venue = new Venue($db);
$venues = $venue->getActiveVenues();

// Tạo đối tượng Event và lấy danh sách sự kiện
$event = new Event($db);
$stmt = $event->getActiveEvents();
$eventtype = new EventType($db);
$eventtypes = $eventtype->getActiveEventTypes();
// Kiểm tra nếu có sự kiện
if ($stmt->rowCount() > 0) {
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $events = [];
}
if (isset($_GET['filter'])) {
    $filterType = $_GET['filter'];
    $events = array_filter($events, function ($event) use ($filterType) {
        return $event['TypeName'] === $filterType;
    });
}
if (isset($_GET['location'])) {
    $filterLocation = $_GET['location'];
    $events = array_filter($events, function ($event) use ($filterLocation) {
        return $event['VenueName'] === $filterLocation;
    });
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sự Kiện Sắp Tới | Quản Lý Sự Kiện</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3a86ff;
            --secondary-color: #ff006e;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
            --text-primary: #333333;
            --text-secondary: #666666;
            --accent-light: #8ecae6;
            --accent-dark: #023e8a;
            --success: #38b000;
            --warning: #ffbe0b;
            --danger: #d90429;
            --border-radius: 12px;
            --box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            line-height: 1.6;
            padding-top: 40px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header {
            background: linear-gradient(135deg, var(--accent-dark), var(--primary-color));
            color: white;
            padding: 30px 0;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='rgba(255,255,255,0.1)' fill-rule='evenodd'/%3E%3C/svg%3E") repeat;
            opacity: 0.6;
        }

        .header-content {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .subtitle {
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto;
            opacity: 0.9;
        }

        .filter-bar {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 15px 20px;
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            justify-content: space-between;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: var(--transition);
        }

        .search-box input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(58, 134, 255, 0.2);
            outline: none;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }

        .filter-options {
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 16px;
            background-color: var(--accent-light);
            color: var(--accent-dark);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
        }

        .filter-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .sort-btn {
            background-color: transparent;
            border: 1px solid #e0e0e0;
            color: var(--text-secondary);
        }

        .sort-btn:hover {
            background-color: #f0f0f0;
            color: var(--text-primary);
        }

        .event-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .event-card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            position: relative;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .event-category {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--secondary-color);
            color: white;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            z-index: 1;
        }

        .event-image {
            height: 200px;
            position: relative;
            background: linear-gradient(to right, var(--primary-color), var(--accent-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            overflow: hidden;
            /* Đảm bảo ảnh không tràn ra ngoài */
        }

        .event-image img {
            width: 100%;
            height: 100%;
            /* Độ cao 100% để vừa khít chiều dọc */
            object-fit: cover;
            /* Cắt ảnh sao cho vừa khít mà không méo */
            position: absolute;
            /* Định vị ảnh để phủ toàn bộ vùng chứa */
        }


        .event-date {
            position: absolute;
            left: 280px;
            bottom: 180px;
            background-color: var(--accent-dark);
            color: white;
            width: 70px;
            height: 70px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .event-day {
            font-size: 1.8rem;
            line-height: 1;
        }

        .event-month {
            font-size: 0.9rem;
            text-transform: uppercase;
        }

        .event-content {
            padding: 30px 20px 20px;
        }

        .event-title {
            margin-bottom: 15px;
            font-size: 1.4rem;
            color: var(--text-primary);
            font-weight: 700;
            line-height: 1.3;
        }

        .event-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .meta-item i {
            color: var(--primary-color);
            font-size: 1rem;
        }

        .event-description {
            color: var(--text-secondary);
            margin-bottom: 20px;
            font-size: 0.95rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .creator {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .creator-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: var(--accent-light);
            color: var(--accent-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .creator-name {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .details-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: var(--transition);
        }

        .details-btn:hover {
            background-color: var(--accent-dark);
        }

        .no-events {
            text-align: center;
            padding: 50px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .no-events-icon {
            font-size: 4rem;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        .no-events-text {
            font-size: 1.5rem;
            color: var(--text-secondary);
            margin-bottom: 15px;
        }

        .no-events-subtext {
            color: #999;
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .browse-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .browse-btn:hover {
            background-color: var(--accent-dark);
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }

            .event-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
                gap: 20px;
            }

            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-options {
                justify-content: space-between;
            }
        }

        @media (max-width: 480px) {
            .event-grid {
                grid-template-columns: 1fr;
            }

            .event-meta {
                flex-direction: column;
                gap: 8px;
            }
        }

        .container1 {
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: #f8f9fa;
            padding: 15px;
            border-right: 1px solid #ddd;
        }

        .menu-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            cursor: pointer;
            display: flex;
            /* Sử dụng flexbox để căn chỉnh nội dung */
            justify-content: space-between;
            /* Đảm bảo nội dung trong div được phân tách, mũi tên sẽ nằm ở bên phải */
            align-items: center;
        }

        .menu-header i.fas.fa-caret-right {
            margin-left: auto;
            /* Đẩy mũi tên sang phải */
            transition: transform 0.3s ease;
            /* Đảm bảo có hiệu ứng mượt */
            transform: rotate(0deg);
            /* Mặc định mũi tên không quay */
        }
        .menu-header i.fas.fa-tag.text {
            margin-right: 10px;

        }
        .menu-header i.fas.fa-map-marker-alt {
            margin-right: 10px;

        }
        .menu-list {
            display: none;
            /* Ẩn menu ban đầu */
            list-style: none;
            padding: 0;
        }

        .menu-list.active {
            display: block;
            /* Hiển thị menu khi class 'active' được thêm vào */
        }

        .menu-item {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            transition: background 0.3s;
        }

        .menu-item a {
            text-decoration: none;
            color: #333;
            display: block;
        }

        .menu-item:hover {
            background: #e9ecef;
        }

        .content {
            flex-grow: 1;
            padding: 20px;
        }

        .custom-pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .custom-pagination .page-link {
            padding: 10px 15px;
            background-color: #f1f1f1;
            border-radius: 10px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .custom-pagination .page-link:hover {
            background-color: #007BFF;
            color: white;
            transform: translateY(-2px);
        }

        .custom-pagination .page-link.active {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="container">
            <div class="header-content">
                <h1 class="page-title">Sự Kiện Sắp Tới</h1>
                <p class="subtitle">Khám phá và tham gia các sự kiện hấp dẫn đang diễn ra trong thời gian tới</p>
            </div>
        </div>
    </div>

    <div class="container1">
        <div class="sidebar">
            <div class="multi-level-menu">
                <div class="menu-header"><i class="fas fa-tag text"></i> Danh mục <i class="fas fa-caret-right"></i></div>
                <ul class="menu-list">
                    <?php foreach ($eventtypes as $type): ?>
                        <li class="menu-item">
                            <a href="?filter=<?php echo urlencode($type['TypeName']); ?>">
                                <?php echo htmlspecialchars($type['TypeName']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="multi-level-menu">
                <div class="menu-header"><i class="fas fa-map-marker-alt"></i> Địa điểm <i class="fas fa-caret-right"></i> </div>
                <ul class="menu-list">
                    <?php foreach ($venues as $v): ?>
                        <li class="menu-item">
                            <a href="?location=<?php echo urlencode($v['VenueName']); ?>">
                                <?php echo htmlspecialchars($v['VenueName']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="content">
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Tìm kiếm sự kiện..." id="searchInput">
                </div>
            </div>

            <?php
            // Giả sử $events chứa tất cả sự kiện và $currentPage là trang hiện tại
            $eventsPerPage = 6;
            $totalEvents = count($events);
            $totalPages = ceil($totalEvents / $eventsPerPage);
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $currentPage = max(1, min($currentPage, $totalPages)); // Đảm bảo trang hợp lệ
            $offset = ($currentPage - 1) * $eventsPerPage;
            $eventsToShow = array_slice($events, $offset, $eventsPerPage);
            ?>

            <?php if (count($eventsToShow) > 0): ?>
                <div class="event-grid">
                    <?php foreach ($eventsToShow as $event): ?>
                        <div class="event-card">
                            <div class="event-category"><?php echo htmlspecialchars($event['TypeName']); ?></div>
                            <div class="event-image">
                                <?php if (!empty($event['images'])): ?>
                                    <img src="/quanlysukien/images/<?= htmlspecialchars($event['images']) ?>" alt="Hình ảnh sự kiện">
                                <?php else: ?>
                                    <i class="fas fa-calendar-alt"></i>
                                <?php endif; ?>
                            </div>
                            <div class="event-date">
                                <div class="event-day"><?php echo date('d', strtotime($event['EventDate'])); ?></div>
                                <div class="event-month"><?php echo date('m/y', strtotime($event['EventDate'])); ?></div>
                            </div>
                            <div class="event-content">
                                <h3 class="event-title"><?php echo htmlspecialchars($event['EventName']); ?></h3>
                                <div class="event-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($event['VenueName']); ?></span>
                                    </div>
                                </div>
                                <p class="event-description"><?php echo nl2br(htmlspecialchars($event['Description'])); ?></p>

                                <div class="event-footer">
                                    <div class="creator">
                                        <div class="creator-avatar">
                                            <?php echo strtoupper(substr($event['CreatedBy'], 0, 1)); ?>
                                        </div>
                                        <span class="creator-name"><?php echo htmlspecialchars($event['CreatedBy']); ?></span>
                                    </div>
                                    <a href="event_details.php?id=<?php echo $event['EventId']; ?>" class="details-btn">
                                        Chi tiết <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="custom-pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link <?php echo ($i === $currentPage) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>

            <?php else: ?>
                <div class="no-events">
                    <div class="no-events-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h2 class="no-events-text">Không có sự kiện nào sắp tới</h2>
                    <p class="no-events-subtext">Hiện tại chưa có sự kiện nào được lên lịch trong thời gian tới. Vui lòng quay lại sau.</p>
                    <a href="Home.php" class="browse-btn">
                        <i class="fas fa-home"></i> Về trang chủ
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Tìm kiếm sự kiện
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const eventCards = document.querySelectorAll('.event-card');

            eventCards.forEach(card => {
                const title = card.querySelector('.event-title').textContent.toLowerCase();
                const description = card.querySelector('.event-description').textContent.toLowerCase();
                const category = card.querySelector('.event-category').textContent.toLowerCase();

                if (title.includes(searchValue) || description.includes(searchValue) || category.includes(searchValue)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        // Chọn tất cả các menu-header và gán sự kiện click cho chúng
        document.querySelectorAll('.menu-header').forEach(function(header) {
            header.addEventListener('click', function() {
                // Lấy menu con tương ứng
                const menuList = this.nextElementSibling;
                const icon = this.querySelectorAll('i')[1]; // Lấy icon mũi tên trong menu-header

                // Toggle class 'active' để hiển thị/ẩn menu
                menuList.classList.toggle('active');

                // Toggle icon mũi tên
                if (menuList.classList.contains('active')) { // Mũi tên sang phải
                    icon.style.transform = "rotate(90deg)"; // Quay mũi tên xuống
                } else {
                    icon.style.transform = "rotate(0deg)"; // Đặt lại mũi tên về trạng thái ban đầu
                }
            });
        });
    </script>

</body>
<?php include '../shares/footer.php'; ?>

</html>