<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/EventtypeModel.php';

class EventTypeController
{
    private $model;
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->model = new EventType($this->conn);
    }

    public function getAllEventTypes()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $eventTypes = $this->model->getAllEventTypes()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($eventTypes);
        
    }
    }
    public function getActiveEventTypes()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
            $eventTypes = $this->model->getActiveEventTypes($_GET['id']);
            if ($eventTypes) {
                echo json_encode($eventTypes);
            } else {
                echo json_encode(["error" => "Địa điểm không tồn tại"]);
            }
        }
    }

    public function createEventType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $typeName = trim($_POST['TypeName']);
            if (empty($typeName)) {
                echo json_encode(["error" => "Tên loại sự kiện không được để trống"]);
                exit();
            }
            
            if ($this->model->createEventType($typeName)) {
                header("Location: ../View/Eventtype/index.php");
                exit();
            } else {
                echo "<script>alert('Loại sự kiện đã tồn tại! Vui lòng nhập lại.'); window.history.back();</script>";
            }
        }
    }

    public function updateEventType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventTypeId = $_POST['EventTypeId'] ?? null;
            $typeName = trim($_POST['TypeName']);
            
            if (!$eventTypeId || empty($typeName)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }
            
            if ($this->model->updateEventType($eventTypeId, $typeName)) {
                header("Location: ../View/Eventtype/index.php");
                exit();
            } else {
                echo "<script>alert('Loại sự kiện đã tồn tại! Vui lòng nhập lại.'); window.history.back();</script>";
            }
        }
    }

    public function deleteEventType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventTypeId = $_POST['EventTypeId'] ?? null;
            if (!$eventTypeId) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }
            
            if ($this->model->deleteEventType($eventTypeId)) {
                header("Location: ../View/Eventtype/index.php");
                exit();
            } else {
                echo json_encode(["error" => "Xóa thất bại"]);
            }
        }
    }

    public function restoreEventType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventTypeId = $_POST['EventTypeId'] ?? null;
            if (!$eventTypeId) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }
            
            if ($this->model->restoreEventType($eventTypeId)) {
                header("Location: ../View/Eventtype/index.php");
                exit();
            } else {
                echo json_encode(["error" => "Khôi phục thất bại"]);
            }
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new EventTypeController();

    if ($_GET['action'] === 'getAll') {
        $controller->getAllEventTypes();
    } elseif ($_GET['action'] === 'getActive') {
        $controller->getActiveEventTypes();
    } elseif ($_GET['action'] === 'create') {
        $controller->createEventType();
    } elseif ($_GET['action'] === 'update') {
        $controller->updateEventType();
    } elseif ($_GET['action'] === 'delete') {
        $controller->deleteEventType();
    } elseif ($_GET['action'] === 'restore') {
        $controller->restoreEventType();
    }
}
