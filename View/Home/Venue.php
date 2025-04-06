<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/VenuesModel.php';
// Tạo đối tượng kết nối CSDL
$database = new Database();
$db = $database->getConnection();

// Tạo đối tượng Event và lấy danh sách sự kiện
$venuemodels = new Venue($db);
$venues = $venuemodels->getAllVenuesHome()->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Địa điểm sự kiện</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3a86ff;
            --secondary-color: #ff006e;
            --accent-color: #fb5607;
            --background-color: #f8f9fa;
            --card-color: #ffffff;
            --text-color: #333333;
            --text-light: #6c757d;
            --border-radius: 12px;
            --box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        ul {
            margin: 15px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            padding-top: 80px;
        }

        .venues-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .page-header p {
            font-size: 1.1rem;
            color: var(--text-light);
            max-width: 700px;
            margin: 0 auto;
        }

        .filter-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 15px 25px;
            background-color: var(--card-color);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .search-box {
            display: flex;
            align-items: center;
            background-color: #f1f3f5;
            border-radius: 30px;
            padding: 8px 15px;
            width: 100%;
            max-width: 800px;
        }

        .search-box input {
            border: none;
            background: transparent;
            padding: 8px 10px;
            width: 100%;
            outline: none;
            font-size: 1rem;
        }

        .search-box i {
            color: var(--text-light);
            margin-right: 10px;
        }

        .filter-options {
            display: flex;
            gap: 10px;
        }

        .venues-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .venue-card {
            background-color: var(--card-color);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .venue-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
        }

        .venue-image {
            height: 200px;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #e9ecef;
        }

        .venue-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .venue-card:hover .venue-image img {
            transform: scale(1.05);
        }

        .venue-image i {
            font-size: 3rem;
            color: #adb5bd;
        }

        .venue-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--accent-color);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .venue-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .venue-name {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .venue-meta {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: var(--text-light);
        }

        .venue-meta i {
            margin-right: 5px;
            color: var(--secondary-color);
        }

        .venue-description {
            margin-bottom: 20px;
            color: var(--text-light);
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            flex-grow: 1;
        }

        .venue-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .venue-address {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .venue-address i {
            margin-right: 5px;
            color: var(--accent-color);
        }

        .venue-action {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #f1f3f5;
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .view-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 15px;
            text-align: center;
        }

        .view-btn:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }

        .no-venues {
            text-align: center;
            padding: 60px 20px;
            background-color: var(--card-color);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .no-venues-icon {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .no-venues-text {
            font-size: 1.5rem;
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .no-venues-subtext {
            color: var(--text-light);
            margin-bottom: 30px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .browse-btn {
            display: inline-flex;
            align-items: center;
            padding: 12px 25px;
            background-color: var(--primary-color);
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .browse-btn i {
            margin-right: 8px;
        }

        .browse-btn:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .venues-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .filter-options {
                display: none;
            }

            .filter-bar {
                padding: 12px 15px;
            }
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .filter-btn {
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 15px;
            background-color: white;
            border: 1px solid #edf2f9;
            color: var(--secondary);
        }

        .filter-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
    </style>
</head>

<body>
    <div class="venues-container">
        <div class="page-header">
            <h1>Địa điểm tổ chức sự kiện</h1>
            <p>Khám phá những địa điểm tuyệt vời để tổ chức sự kiện của bạn với không gian đẹp và tiện nghi đẳng cấp</p>
        </div>
        <div class="filter-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Tìm kiếm địa điểm..." id="searchInput">
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-filter me-1"></i> Lọc
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item filter-option" href="#" data-filter="all">Tất cả</a></li>
                    <li><a class="dropdown-item filter-option" href="#" data-filter="Available">Sẵn sàng cho sự kiện</a></li>
                    <li><a class="dropdown-item filter-option" href="#" data-filter="Booked">Đã đặt</a></li>
                </ul>
            </div>
        </div>
        <?php if (count($venues) > 0): ?>
            <div class="venues-grid">
                <?php foreach ($venues as $venue): ?>
                    <div class="venue-card">
                        <div class="venue-image">
                            <?php if (!empty($venue['images'])): ?>
                                <img src="/quanlysukien/images/<?= htmlspecialchars($venue['images']) ?>" alt="<?= htmlspecialchars($venue['VenueName']) ?>">
                            <?php else: ?>
                                <i class="fas fa-building"></i>
                            <?php endif; ?>
                            <div class="venue-badge">Địa điểm</div>
                        </div>
                        <div class="venue-content">
                            <h3 class="venue-name"><?php echo htmlspecialchars($venue['VenueName']); ?></h3>
                            <div class="venue-meta">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo htmlspecialchars(substr($venue['Address'], 0, 40)) . (strlen($venue['Address']) > 40 ? '...' : ''); ?></span>
                            </div>
                            <p class="venue-description"><?php echo htmlspecialchars($venue['Capacity']) ?> Người</p>
                            <div class="venue-footer">
                                <div class="venue-address" data-status="<?= $venue['Status']; ?>">
                                    <?php if ($venue['Status'] == 'Available'): ?>
                                        <i class="fas fa-calendar-check"></i>
                                        <span style="color:#7fff89">Sẵn sàng cho sự kiện</span>
                                    <?php elseif ($venue['Status'] == 'Booked'): ?>
                                        <i class="fas fa-calendar-times"></i>
                                        <span style="color:rgb(242, 8, 8);">Đã đặt</span>
                                    <?php endif; ?>
                                </div>
                                <!-- Nút đặt sự kiện -->
                                <?php if ($venue['Status'] == 'Available'): ?>
                                    <a href="../Event/chondiadiem.php?venue_id=<?= $venue['VenueId']; ?>" class="btn btn-primary btn-sm">
                                        Đặt Sự Kiện
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-venues">
                <div class="no-venues-icon">
                    <i class="fas fa-building-circle-xmark"></i>
                </div>
                <h2 class="no-venues-text">Không tìm thấy địa điểm nào</h2>
                <p class="no-venues-subtext">Hiện tại chưa có địa điểm nào được đăng ký trong hệ thống. Vui lòng quay lại sau.</p>
                <a href="/" class="browse-btn">
                    <i class="fas fa-home"></i> Về trang chủ
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const venueCards = document.querySelectorAll('.venue-card');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                venueCards.forEach(card => {
                    const venueName = card.querySelector('.venue-name').textContent.toLowerCase();
                    const venueDescription = card.querySelector('.venue-description').textContent.toLowerCase();
                    const venueAddress = card.querySelector('.venue-meta span').textContent.toLowerCase();

                    if (venueName.includes(searchTerm) ||
                        venueDescription.includes(searchTerm) ||
                        venueAddress.includes(searchTerm)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
            document.getElementById("dropdownMenuButton").addEventListener("click", function() {
                const dropdownMenu = document.querySelector(".dropdown-menu");
                dropdownMenu.style.display = (dropdownMenu.style.display === "none" || dropdownMenu.style.display === "") ? "block" : "none";
            });

            // Xử lý lọc khi chọn một tùy chọn
            document.querySelectorAll(".filter-option").forEach(option => {
                option.addEventListener("click", function(e) {
                    e.preventDefault();
                    const filter = this.dataset.filter; // Lấy filter từ data-filter
                    const venues = document.querySelectorAll(".venue-card"); // Lấy danh sách tất cả venues

                    venues.forEach(venue => {
                        const venueStatus = venue.querySelector(".venue-address").getAttribute("data-status"); // Lấy data-status của venue
                        if (filter === "all" || venueStatus === filter) {
                            venue.style.display = ""; // Hiển thị venue phù hợp
                        } else {
                            venue.style.display = "none"; // Ẩn venue không phù hợp
                        }
                    });

                    // Cập nhật tiêu đề dropdown
                    document.querySelector("#dropdownMenuButton").innerHTML =
                        `<i class="fas fa-filter me-1"></i> ${this.textContent}`;

                    // Ẩn dropdown sau khi chọn
                    document.querySelector(".dropdown-menu").style.display = "none";
                });
            });

            // Ẩn dropdown nếu click ra ngoài
            document.addEventListener("click", function(e) {
                const dropdown = document.querySelector(".dropdown-menu");
                const button = document.getElementById("dropdownMenuButton");
                if (!dropdown.contains(e.target) && !button.contains(e.target)) {
                    dropdown.style.display = "none";
                }
            });
        });
    </script>
</body>
<?php include '../shares/footer.php'; ?>

</html>