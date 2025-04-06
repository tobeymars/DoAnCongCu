<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/TickettypeModel.php';

class TicketTypeController
{
    private $model;
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->model = new TicketType($this->conn);
    }

    // Lấy tất cả các loại vé
    public function getAllTicketTypes()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $tickets = $this->model->getAllTicketTypes()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($tickets);
        }
    }

    // Lấy loại vé chưa bị xóa
    public function getActiveTicketTypes()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $tickets = $this->model->getActiveTicketTypes()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($tickets);
        }
    }

    // Lấy loại vé theo ID
    public function getTicketTypeById()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
            $ticket = $this->model->getTicketTypeById($_GET['id']);
            if ($ticket) {
                echo json_encode($ticket);
            } else {
                echo json_encode(["error" => "Loại vé không tồn tại"]);
            }
        }
    }

    // Thêm loại vé mới
    public function createTicketType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventId = $_POST['EventId'];
            $ticketName = trim($_POST['TicketName']);
            $price = $_POST['Price'];
            $quantity = $_POST['Quantity'];

            if (empty($eventId) || empty($ticketName) || empty($price) || empty($quantity)) {
                echo json_encode(["error" => "Vui lòng nhập đầy đủ thông tin"]);
                exit();          
            }

            if ($this->model->createTicketType($eventId, $ticketName, $price, $quantity)) {
                header("Location: ../View/TicketType/index.php");
                exit();
            } else {
                echo "<script>alert('Lỗi! Không thể tạo loại vé.'); window.history.back();</script>";
            }
        }
    }

    // Cập nhật loại vé
    public function updateTicketType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketTypeId = $_POST['TicketTypeId'];
            $ticketName = trim($_POST['TicketName']);
            $price = $_POST['Price'];
            $quantity = $_POST['Quantity'];

            if (empty($ticketTypeId) || empty($ticketName) || empty($price) || empty($quantity)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            if ($this->model->updateTicketType($ticketTypeId, $ticketName, $price, $quantity)) {
                header("Location: ../View/Tickettype/index.php");
                exit();
            } else {
                echo "<script>alert('Cập nhật thất bại!'); window.history.back();</script>";
            }
        }
    }

    // Xóa loại vé (Chuyển IsDeleted = 1)
    public function deleteTicketType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketTypeId = $_POST['TicketTypeId'];
            if (empty($ticketTypeId)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            if ($this->model->deleteTicketType($ticketTypeId)) {
                header("Location: ../View/Tickettype/index.php");
                exit();
            } else {
                echo json_encode(["error" => "Xóa thất bại"]);
            }
        }
    }

    // Khôi phục loại vé (Chuyển IsDeleted = 0)
    public function restoreTicketType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ticketTypeId = $_POST['TicketTypeId'];
            if (empty($ticketTypeId)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            if ($this->model->restoreTicketType($ticketTypeId)) {
                header("Location: ../View/Tickettype/index.php");
                exit();
            } else {
                echo json_encode(["error" => "Khôi phục thất bại"]);
            }
        }
    }
}

// Xử lý request từ URL
if (isset($_GET['action'])) {
    $controller = new TicketTypeController();

    if ($_GET['action'] === 'getAll') {
        $controller->getAllTicketTypes();
    } elseif ($_GET['action'] === 'getActive') {
        $controller->getActiveTicketTypes();
    } elseif ($_GET['action'] === 'getById') {
        $controller->getTicketTypeById();
    } elseif ($_GET['action'] === 'create') {
        $controller->createTicketType();
    } elseif ($_GET['action'] === 'update') {
        $controller->updateTicketType();
    } elseif ($_GET['action'] === 'delete') {
        $controller->deleteTicketType();
    } elseif ($_GET['action'] === 'restore') {
        $controller->restoreTicketType();
    }
}
?>
