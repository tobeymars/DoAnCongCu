<?php
// Kiểm tra và thiết lập session nếu chưa bắt đầu
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Import các file cần thiết
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventtypeModel.php';
require_once __DIR__ . '/../../Model/VenuesModel.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';

// Xác thực token
$token = $_GET["token"] ?? "";
if (empty($token)) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Token is missing"]);
    exit();
}

$userData = JWTHelper::verifyToken($token);
if (!$userData) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Invalid token"]);
    exit();
}

// Kiểm tra ID sự kiện
$eventId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$eventId) {
    die("ID sự kiện không hợp lệ.");
}

// Thiết lập kết nối database
$database = new Database();
$conn = $database->getConnection();

// Khởi tạo các model
$eventModel = new Event($conn);
$eventTypeModel = new EventType($conn);
$venueModel = new Venue($conn);

// Lấy thông tin sự kiện
$event = $eventModel->getEventById($eventId);
if (!$event) {
    die("Sự kiện không tồn tại.");
}

// Lấy dữ liệu cho dropdown
$eventTypes = $eventTypeModel->getAllEventTypes()->fetchAll(PDO::FETCH_ASSOC);
$venues = $venueModel->getAllVenues()->fetchAll(PDO::FETCH_ASSOC);

// Xác định header dựa trên vai trò
$roleId = $userData['RoleId'] ?? null;
if ($roleId == 2) {
    include '../shares/header.php';
} else {
    include '../shares/adminhd.php';
}

// Format ngày tháng
$eventDate = !empty($event['EventDate'])
    ? (new DateTime($event['EventDate']))->format('Y-m-d')
    : date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Sự Kiện | Hệ Thống Quản Lý</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            padding-top: 50px;
            padding-left: 275px;
            color: #333;
        }

        .event-form-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            padding: 30px;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: #4e73df;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }

        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }

        .page-header {
            border-bottom: 1px solid #e3e6f0;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-secondary {
            background-color: #858796;
            border-color: #858796;
        }

        .btn-secondary:hover {
            background-color: #717384;
            border-color: #6e707e;
        }
        .preview-container {
            text-align: center;
            margin: 1.5rem 0;
            padding: 1.5rem;
            border: 2px dashed #e2e8f0;
            border-radius: var(--border-radius);
            background-color: #f8fafc;
        }

        .preview {
            max-width: 100%;
            max-height: 300px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .preview:hover {
            transform: scale(1.02);
        }

        .file-input-container {
            position: relative;
            margin-top: 1rem;
        }

        .file-input-label {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color:  #4e73df;
            color: white;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .file-input-label:hover {
            background-color: #05278d;
        }

        input[type="file"] {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }

        .file-name {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #718096;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="page-header d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-edit me-2"></i>Chỉnh Sửa Sự Kiện</h2>
            <a href="events.php?token=<?= htmlspecialchars($token) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>

        <div class="event-form-container">
            <form id="editEventForm" action="../../Controller/EventController.php?action=update" method="POST"enctype="multipart/form-data">
                <input type="hidden" name="EventId" value="<?= htmlspecialchars($event['EventId']) ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="CreatedBy" value="<?= htmlspecialchars($event['CreatedBy']) ?>">
                <input type="hidden" id="userId" name="user_id">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="eventName" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i> Tên sự kiện
                        </label>
                        <input type="text" class="form-control" id="eventName" name="EventName"
                            value="<?= htmlspecialchars($event['EventName']) ?>" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="eventDate" class="form-label">
                            <i class="fas fa-clock me-1"></i> Ngày diễn ra
                        </label>
                        <input type="date" class="form-control" id="eventDate" name="EventDate"
                            value="<?= $eventDate ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="eventType" class="form-label">
                            <i class="fas fa-tag me-1"></i> Loại sự kiện
                        </label>
                        <select class="form-select" id="eventType" name="EventTypeId" required>
                            <option value="">-- Chọn loại sự kiện --</option>
                            <?php foreach ($eventTypes as $type): ?>
                                <option value="<?= htmlspecialchars($type['EventTypeId']) ?>"
                                    <?= $type['EventTypeId'] == $event['EventTypeId'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type['TypeName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="venue" class="form-label">
                            <i class="fas fa-map-marker-alt me-1"></i> Địa điểm
                        </label>
                        <select class="form-select" id="venue" name="VenueId" required>
                            <option value="">-- Chọn địa điểm --</option>
                            <?php foreach ($venues as $venue): ?>
                                <option value="<?= htmlspecialchars($venue['VenueId']) ?>"
                                    <?= $venue['VenueId'] == $event['VenueId'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($venue['VenueName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left me-1"></i> Mô tả chi tiết
                    </label>
                    <textarea class="form-control" id="description" name="Description" rows="5"><?= htmlspecialchars($event['Description']) ?></textarea>
                </div>
                <div class="preview-container">
                        <label class="form-label">Hình ảnh</label>
                        <div class="image-preview mb-3">
                            <img class="preview" src="/quanlysukien/images/<?= htmlspecialchars($venue['images']) ?>" alt="Preview">
                            <input type="hidden" name="CurrentImage" value="<?= htmlspecialchars($venue['images']) ?>">
                        </div>

                        <div class="file-input-container">
                            <label for="images-upload" class="file-input-label">
                                <i class="fas fa-cloud-upload-alt me-2"></i>Chọn ảnh mới
                            </label>
                            <input id="images-upload" type="file" class="form-control" name="Images">
                            <div class="file-name mt-2" id="file-name">Chưa chọn file</div>
                        </div>
                    </div>
                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Cập Nhật Sự Kiện
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="window.history.back();">
                        <i class="fas fa-arrow-left me-1"></i> Quay Về
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Lấy thông tin người dùng từ localStorage
            const userInfo = JSON.parse(localStorage.getItem("userInfo"));
            if (userInfo && userInfo.user_id) {
                document.getElementById("userId").value = userInfo.user_id;
            } else {
                console.error("Không tìm thấy user_id trong localStorage");
            }

            // Xử lý form submit
            document.getElementById("editEventForm").addEventListener("submit", function(event) {
                const eventName = document.getElementById("eventName").value.trim();
                const eventDate = document.getElementById("eventDate").value;
                const eventType = document.getElementById("eventType").value;
                const venue = document.getElementById("venue").value;

                if (!eventName || !eventDate || !eventType || !venue) {
                    event.preventDefault();
                    alert("Vui lòng điền đầy đủ thông tin bắt buộc!");
                }
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
        const fileInput = document.getElementById('images-upload');
        const previewImage = document.querySelector(".preview");
        const fileName = document.getElementById('file-name');

        fileInput.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = "block";
                    fileName.textContent = file.name;
                };
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = "Chưa chọn file";
            }
        });
    });
    </script>
</body>

</html>