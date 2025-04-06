<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/EquipmentModel.php';

class EquipmentController
{
    private $model;
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->model = new Equipment($this->conn);
    }

    // Lấy tất cả thiết bị (kể cả thiết bị đã xóa)
    public function getAllEquipments()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $equipments = $this->model->getAllEquipments()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($equipments);
        }
    }

    // Lấy thiết bị chưa bị xóa
    public function getActiveEquipments()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $equipments = $this->model->getActiveEquipments()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($equipments);
        }
    }

    // Lấy thiết bị theo ID
    public function getEquipmentById()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
            $equipment = $this->model->getEquipmentById($_GET['id']);
            if ($equipment) {
                echo json_encode($equipment);
            } else {
                echo json_encode(["error" => "Thiết bị không tồn tại"]);
            }
        }
    }

    // Thêm thiết bị mới
    public function createEquipment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipmentTypeId = $_POST['EquipmentTypeId'] ?? null;
            $equipmentName = trim($_POST['EquipmentName'] ?? '');
            $quantity = $_POST['Quantity'] ?? 0;
            $status = $_POST['Status'] ?? '';

            if (!$equipmentTypeId || empty($equipmentName)) {
                echo json_encode(["error" => "Vui lòng nhập đầy đủ thông tin"]);
                exit();
            }

            if ($this->model->createEquipment($equipmentTypeId, $equipmentName, $quantity, $status)) {
                header("Location: ../view/Equipment/index.php");
            } else {
                echo "<script>alert('Lỗi! Không thể thêm thiết bị.'); window.history.back();</script>";
            }
        }
    }

    // Cập nhật thiết bị
    public function updateEquipment()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!isset($_POST['EquipmentId']) || !isset($_POST['EquipmentName']) || !isset($_POST['Quantity']) || !isset($_POST['EquipmentTypeId'])) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            $id = $_POST['EquipmentId'];
            $name = trim($_POST['EquipmentName']);
            $quantity = intval($_POST['Quantity']);
            $equipmentTypeId = $_POST['EquipmentTypeId'];
            if ($quantity < 0) {
                echo json_encode(["error" => "Số lượng không hợp lệ"]);
                exit();
            }
            $result = $this->model->updateEquipment($id, $name, $quantity, $equipmentTypeId);
            if ($result) {
                header("Location: ../View/Equipment/index.php");
                exit();
            } else {
                echo json_encode(["error" => "Cập nhật thất bại"]);
                exit();
            }
        }
    }


    // Xóa thiết bị (soft delete)
    public function deleteEquipment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['EquipmentId'])) {
            $equipmentId = $_POST['EquipmentId'];

            if ($this->model->deleteEquipment($equipmentId)) {
                header("Location: ../view/Equipment/index.php");
            } else {
                echo json_encode(["error" => "Xóa thất bại"]);
            }
        }
    }

    // Khôi phục thiết bị
    public function restoreEquipment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['EquipmentId'])) {
            $equipmentId = $_POST['EquipmentId'];

            if ($this->model->restoreEquipment($equipmentId)) {
                header("Location: ../view/Equipment/index.php");
            } else {
                echo json_encode(["error" => "Khôi phục thất bại"]);
            }
        }
    }
}

// Xử lý request từ URL
if (isset($_GET['action'])) {
    $controller = new EquipmentController();

    if ($_GET['action'] === 'getAll') {
        $controller->getAllEquipments();
    } elseif ($_GET['action'] === 'getActive') {
        $controller->getActiveEquipments();
    } elseif ($_GET['action'] === 'getById') {
        $controller->getEquipmentById();
    } elseif ($_GET['action'] === 'create') {
        $controller->createEquipment();
    } elseif ($_GET['action'] === 'update') {
        $controller->updateEquipment();
    } elseif ($_GET['action'] === 'delete') {
        $controller->deleteEquipment();
    } elseif ($_GET['action'] === 'restore') {
        $controller->restoreEquipment();
    }
}
?>
