<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/VenuesModel.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../../Model/UserModel.php';
session_start();
// Ví dụ kiểm tra quyền
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 1) {
    header('Location: ../AccessDenied.php');
    exit();
}
$database = new Database();
$conn = $database->getConnection();
$Venuemodel = new Venue($conn);
$venues = $Venuemodel->getAllVenues()->fetchAll(PDO::FETCH_ASSOC);
$totalVenues = count($venues);
$Eventmodel = new Event($conn);
$recentEvents = $Eventmodel->getEventsInCurrentMonth()->fetchAll(PDO::FETCH_ASSOC);
$events = $Eventmodel->getAllEvents()->fetchAll(PDO::FETCH_ASSOC);
$totalEvents = count($events);
$pendingEvents = count(array_filter($events, function ($event) {
    return $event['status'] == 0;
}));
$completedEvents = count(array_filter($events, function ($event) {
    return $event['status'] == 1;
}));
$Usermodel = new UserModel(($conn));
$users = $Usermodel->getAllUser()->fetchAll(PDO::FETCH_ASSOC);
$totalUsers = count($users);
$eventofvenues = $Eventmodel->countEventsPerVenueInCurrentMonth()->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include '../shares/adminhd.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Quản lý sự kiện</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<style>
    .container2 {
        max-width: 800px;
        margin-left: 150px;
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 15px;
    }

    .chart-table-container {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .chart-container {
        position: relative;
        height: 400px;
        width: 55%;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid #ddd;
    }

    th,
    td {
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f4f4f4;
    }
</style>
<div class="admin-content">
    <div class="admin-container">
        <div class="page-header">
            <h2><i class="fas fa-chart-line"></i> Thống kê</h2>
            <p>Xin chào Admin, chào mừng quay trở lại với hệ thống quản lý.</p>
        </div>

        <div class="dashboard-cards">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tổng số sự kiện</h3>
                    <div class="card-icon"><i class="fas fa-calendar-alt"></i></div>
                </div>
                <div class="card-content"><?php echo $totalEvents; ?></div>
                <div class="card-footer">Cập nhật gần nhất: <?php echo date('d/m/Y H:i'); ?></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tổng số người dùng</h3>
                    <div class="card-icon"><i class="fas fa-users"></i></div>
                </div>
                <div class="card-content"><?php echo $totalUsers; ?></div>
                <div class="card-footer">Cập nhật gần nhất: <?php echo date('d/m/Y H:i'); ?></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Sự kiện đã hoàn thành</h3>
                    <div class="card-icon"><i class="fas fa-check-circle"></i></div>
                </div>
                <div class="card-content"><?php echo $completedEvents; ?></div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Địa điểm</h3>
                    <div class="card-icon"><i class="fas fa-map-marker-alt"></i></div>
                </div>
                <div class="card-content"><?php echo $totalVenues; ?></div>
            </div>
        </div>
        <div class="container2">
            <h1 style="text-align: center; color: #333;">Biểu đồ số sự kiện theo địa điểm</h1>
            <div class="chart-table-container">
                <!-- Bảng số liệu -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Địa điểm</th>
                                <th>Sự kiện</th>
                            </tr>
                        </thead>
                        <tbody id="venueData">
                            <!-- Dữ liệu sẽ được chèn vào đây từ PHP -->
                            <?php foreach ($eventofvenues as $venue): ?>
                                <tr>
                                    <td><?= $venue['VenueName']; ?></td>
                                    <td><?= $venue['EventCount']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Biểu đồ -->
                <div class="chart-container">
                    <canvas id="eventChart"></canvas>
                </div>
            </div>
        </div>
        <div class="admin-section">
            <div class="section-header">
                <h3>Kết luận</h3>
            </div>

            <div class="activity-log">
                <div class="activity-item">
                    <div class="activity-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="activity-details">
                        <?php
                        // Lấy địa điểm thu hút nhất
                        $maxEventVenue = null;
                        $maxEventCount = 0;
                        foreach ($eventofvenues as $venue) {
                            if ($venue['EventCount'] > $maxEventCount) {
                                $maxEventVenue = $venue['VenueName'];
                                $maxEventCount = $venue['EventCount'];
                            }
                        }
                        ?>
                        <p><strong><?= $maxEventVenue; ?></strong> là địa điểm thu hút nhất trong tháng này với <?= $maxEventCount; ?> sự kiện</p>
                        <span class="activity-time">Cập nhật gần nhất: <?php echo date('d/m/Y H:i'); ?></span>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon"><i class="fas fa-calendar-plus"></i></div>
                    <div class="activity-details">
                        <p><strong>Workshop Marketing</strong> đã được tạo bởi <strong>Trần Thị B</strong></p>
                        <span class="activity-time">Hôm nay, 09:15</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="admin-section">
            <div class="section-header">
                <h3>Sự kiện trong tháng <?php echo date('m/Y'); ?></h3>
                <a href="/Quanlysukien/View/Event/index.php" class="view-all-btn">Xem tất cả <i class="fas fa-arrow-right"></i></a>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tên sự kiện</th>
                        <th>Ngày</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentEvents)): ?>
                        <?php foreach ($recentEvents as $event): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['EventName']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($event['EventDate'])); ?></td>
                                <td>
                                    <?php
                                    $statusText = ($event['status'] == 0) ? "Chưa hoàn thành" : "Hoàn thành";
                                    $statusClass = ($event['status'] == 0) ? "pending" : "completed";
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($statusText); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Không có sự kiện nào trong tháng.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
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
                            <p>Xin chào, <strong>${data.username}</strong></p>
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

    // Thêm các tính năng JavaScript cho trang admin
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa mục này?')) {
                // Xử lý xóa
                alert('Đã xóa thành công!');
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        let token = localStorage.getItem("token");
        let eventLink = document.querySelector(".nav-link1[href='/Quanlysukien/View/Event/index.php']");

        if (eventLink && token) {
            eventLink.href = `/Quanlysukien/View/Event/index.php?token=${encodeURIComponent(token)}`;
        }
    });
    // Chuyển dữ liệu từ PHP sang JavaScript (JSON)
    const eventData = <?php echo json_encode($eventofvenues); ?>;

    // Tạo các mảng chứa tên địa điểm và số sự kiện
    const venueNames = eventData.map(item => item.VenueName);
    const eventCounts = eventData.map(item => item.EventCount);

    // Vẽ biểu đồ
    const ctx = document.getElementById('eventChart').getContext('2d');
    const eventChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: venueNames,
            datasets: [{
                label: 'Số sự kiện trong tháng',
                data: eventCounts,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Thống kê số lượng sự kiện theo địa điểm',
                    font: {
                        size: 16
                    }
                },
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số sự kiện'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Địa điểm'
                    }
                }
            }
        }
    });
</script>
</body>

</html>