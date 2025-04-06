<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/EventModel.php';

class EventController
{
    private $model;
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->model = new Event($this->conn);
    }

    // Lấy tất cả sự kiện
    public function getAllEvents()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $events = $this->model->getAllEvents()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($events);
        }
    }

    // Lấy sự kiện chưa bị xóa (IsDeleted = 0)
    public function getActiveEvents()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $events = $this->model->getActiveEvents()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($events);
        }
    }

    // Lấy danh sách đặt vé của user
    public function getUserEvents()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['userId'])) {
            $userId = $_GET['userId'];
            $events = $this->model->getUserEvents($userId);
            echo json_encode($events);
        }
    }
    // Lấy sự kiện theo ID
    public function getEventById()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
            $event = $this->model->getEventById($_GET['id']);
            if ($event) {
                echo json_encode($event);
            } else {
                echo json_encode(["error" => "Sự kiện không tồn tại"]);
            }
        }
    }

    // Thêm sự kiện mới
    public function createEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventName = trim($_POST['EventName']);
            $description = trim($_POST['Description']);
            $eventDate = $_POST['EventDate'];
            $createdBy = $_POST['CreatedBy'];
            $venueId = $_POST['VenueId'];
            $eventTypeId = $_POST['EventTypeId'];
            $imagePath = '';
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/quanlysukien/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            if (!is_writable($uploadDir)) {
                die(json_encode(["error" => "Thư mục không có quyền ghi!"]));
            }
            if (isset($_FILES['Images']) && $_FILES['Images']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['Images']['name'], PATHINFO_EXTENSION);
                $imageName = time() . '_' . uniqid() . '.' . $ext;
                $targetPath = $uploadDir . $imageName;

                if (move_uploaded_file($_FILES['Images']['tmp_name'], $targetPath)) {
                    $imagePath = $imageName;
                } else {
                    die(json_encode(["error" => "Lỗi khi lưu ảnh!"]));
                }
            }
            // Lấy token từ GET hoặc POST (tùy bạn đang gửi như thế nào)
            $token = $_GET['token'] ?? $_POST['token'] ?? "";
            if (empty($eventName) || empty($eventDate) || empty($createdBy) || empty($venueId) || empty($eventTypeId)) {
                echo json_encode(["error" => "Vui lòng nhập đầy đủ thông tin"]);
                exit();
            }

            if ($this->model->createEvent($eventName, $description, $eventDate, $createdBy, $venueId, $eventTypeId, $imagePath)) {
                header("Location: ../view/Event/indexU.php?token=". urlencode($token));
                exit();
            } else {
                echo "<script>alert('Lỗi! Không thể tạo sự kiện.'); window.history.back();</script>";
            }
        }
    }

    // Cập nhật sự kiện
    public function updateEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventId = $_POST['EventId'];
            $eventName = trim($_POST['EventName']);
            $description = trim($_POST['Description']);
            $eventDate = $_POST['EventDate'];
            $venueId = $_POST['VenueId'];
            $eventTypeId = $_POST['EventTypeId'];
            $userId = $_POST['user_id'] ?? null; 
            $imagePath = $_POST['CurrentImage'] ?? '';
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/quanlysukien/images/';
            if (isset($_FILES['Images']) && $_FILES['Images']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['Images']['name'], PATHINFO_EXTENSION);
                $imageName = time() . '_' . uniqid() . '.' . $ext;
                $targetPath = $uploadDir . $imageName;
                if (move_uploaded_file($_FILES['Images']['tmp_name'], $targetPath)) {
                    if (!empty($imagePath) && file_exists($uploadDir . $imagePath) && $imagePath !== "default.jpg") {
                        unlink($uploadDir . $imagePath);
                    }
                    $imagePath = $imageName;
                } else {
                    die(json_encode(["error" => "Lỗi khi lưu ảnh!"]));
                }
            }
            if (empty($eventId) || empty($eventName) || empty($eventDate) || empty($venueId) || empty($eventTypeId)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            // Lấy RoleId của người đang đăng nhập
        $query = "SELECT RoleId FROM users WHERE UserId = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            echo json_encode(["error" => "Không tìm thấy thông tin người dùng"]);
            exit();
        }

            $roleId = $result['RoleId'];

            // Cập nhật sự kiện
            $updateSuccess = $this->model->updateEvent($eventId, $eventName, $description, $eventDate, $venueId, $eventTypeId, $imagePath);

            if ($updateSuccess) {
                if ($roleId == 1) {
                    $redirectUrl = "../view/Event/index.php?token=" . urlencode($_POST['token']);
                }else{
                    $redirectUrl = "../view/Event/indexU.php?token=" . urlencode($_POST['token']);
                }
                header("Location: $redirectUrl");
                exit();
            } else {
                echo "<script>alert('Cập nhật thất bại!'); window.history.back();</script>";
            }
        }
    }

    // Xóa sự kiện (Chuyển IsDeleted = 1)
    public function deleteEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventId = $_POST['EventId'];
            $userId = $_POST['user_id'] ?? null; 
            if (empty($eventId)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }
            // Lấy RoleId của người đang đăng nhập
            $query = "SELECT RoleId FROM users WHERE UserId = :userId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                echo json_encode(["error" => "Không tìm thấy thông tin người dùng"]);
                exit();
            }

            $roleId = $result['RoleId'];
            // Cập nhật sự kiện
            $updateSuccess = $this->model->deleteEvent($eventId);

            if ($updateSuccess) {
                if ($roleId == 1) {
                    $redirectUrl = "../View/Event/index.php?token=" . urlencode($_POST['token']);
                }else{
                    $redirectUrl = "../view/Event/indexU.php?token=" . urlencode($_POST['token']);
                }
                header("Location: $redirectUrl");
                exit();
            } else {
                echo "<script>alert('Cập nhật thất bại!'); window.history.back();</script>";
            }
        }
    }
    // Hoàn thành sự kiện (Chuyển Status = 1)
    public function eventComplete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'], $_GET['user_id'])) {
            $eventId = $_GET['id'];
            $userId = $_GET['user_id'];

            if (empty($eventId) || empty($userId)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            // Lấy RoleId của người dùng
            $query = "SELECT RoleId FROM users WHERE UserId = :userId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                echo json_encode(["error" => "Không tìm thấy thông tin người dùng"]);
                exit();
            }

            $roleId = $result['RoleId'];

            // Cập nhật sự kiện
            $completeSuccess = $this->model->Eventcomplete($eventId);
            if ($completeSuccess) {
                $redirectUrl = ($roleId == 1)
                    ? "../View/Event/index.php?token=" . urlencode($_GET['token'])
                    : "../view/Event/indexU.php?token=" . urlencode($_GET['token']);

                header("Location: $redirectUrl");
                exit();
            } else {
                echo json_encode(["error" => "Cập nhật thất bại"]);
            }
        }
    }

    // Khôi phục sự kiện (Chuyển IsDeleted = 0)
    public function restoreEvent()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventId = $_POST['EventId'];
              $userId = $_POST['user_id'] ?? null; 
            if (empty($eventId)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            // Lấy RoleId của người đang đăng nhập
            $query = "SELECT RoleId FROM users WHERE UserId = :userId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                echo json_encode(["error" => "Không tìm thấy thông tin người dùng"]);
                exit();
            }

            $roleId = $result['RoleId'];
            // Cập nhật sự kiện
            $updateSuccess = $this->model->restoreEvent($eventId);

            if ($updateSuccess) {
                if ($roleId == 1) {
                    $redirectUrl = "../View/Event/index.php?token=" . urlencode($_POST['token']);
                }else{
                    $redirectUrl = "../view/Event/indexU.php?token=" . urlencode($_POST['token']);
                }
                header("Location: $redirectUrl");
                exit();
            } else {
                echo "<script>alert('Cập nhật thất bại!'); window.history.back();</script>";
            }
        }
    }
}

// Xử lý request từ URL
if (isset($_GET['action'])) {
    $controller = new EventController();

    if ($_GET['action'] === 'getAll') {
        $controller->getAllEvents();
    } elseif ($_GET['action'] === 'getActive') {
        $controller->getActiveEvents();
    } elseif ($_GET['action'] === 'getById') {
        $controller->getEventById();
    } elseif ($_GET['action'] === 'create') {
        $controller->createEvent();
    } elseif ($_GET['action'] === 'update') {
        $controller->updateEvent();
    } elseif ($_GET['action'] === 'delete') {
        $controller->deleteEvent();
    } elseif ($_GET['action'] === 'restore') {
        $controller->restoreEvent();
    } elseif ($_GET['action'] === 'complete') {
        $controller->Eventcomplete();
    }
}
