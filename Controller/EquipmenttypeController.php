<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/EquipmentTypeModel.php';

class EquipmentTypeController
{
    private $model;

    public function __construct()
    {
        $database = new Database();
        $conn = $database->getConnection();
        $this->model = new EquipmentType($conn);
    }

    public function getAllEquipmentTypes()
    {
        header('Content-Type: application/json');
        echo json_encode($this->model->getAllEquipmentTypes()->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getActiveEquipmentTypes()
    {
        if (!isset($_GET['id'])) {
            echo json_encode(["error" => "Thiếu ID"]);
            exit();
        }

        $equipmentType = $this->model->getActiveEquipmentTypes($_GET['id']);
        header('Content-Type: application/json');
        echo json_encode($equipmentType ?: ["error" => "Loại thiết bị không tồn tại"]);
    }

    public function createEquipmentType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipmentTypeName = $_POST['EquipmentTypeName'] ?? null;

            if (!$equipmentTypeName) {
                echo json_encode(["error" => "Tên loại thiết bị không được để trống"]);
                exit();
            }

            if ($this->model->createEquipmentType($equipmentTypeName)) {
                header("Location: ../view/EquipmentType/index.php");
                exit();
            } else {
                echo "<script>alert('Loại thiết bị đã tồn tại! Vui lòng nhập lại.'); window.history.back();</script>";
            }
        }
    }

    public function updateEquipmentType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipmentTypeId = $_POST['EquipmentTypeId'] ?? null;
            $equipmentTypeName = $_POST['EquipmentTypeName'] ?? null;

            if (!$equipmentTypeId || !$equipmentTypeName) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            if ($this->model->updateEquipmentType($equipmentTypeId, $equipmentTypeName)) {
                header("Location: ../view/EquipmentType/index.php");
                exit();
            } else {
                echo "<script>alert('Loại thiết bị đã tồn tại! Vui lòng nhập lại.'); window.history.back();</script>";
            }
        }
    }

    public function deleteEquipmentType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipmentTypeId = $_POST['EquipmentTypeId'] ?? null;
            if (!$equipmentTypeId) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            if ($this->model->deleteEquipmentType($equipmentTypeId)) {
                header("Location: ../view/EquipmentType/index.php");
                exit();
            } else {
                echo json_encode(["error" => "Xóa thất bại"]);
            }
        }
    }

    public function restoreEquipmentType()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $equipmentTypeId = $_POST['EquipmentTypeId'] ?? null;
            if (!$equipmentTypeId) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            if ($this->model->restoreEquipmentType($equipmentTypeId)) {
                header("Location: ../view/EquipmentType/index.php");
                exit();
            } else {
                echo json_encode(["error" => "Khôi phục thất bại"]);
            }
        }
    }
}

// Xử lý action từ request
if (isset($_GET['action'])) {
    $controller = new EquipmentTypeController();
    switch ($_GET['action']) {
        case 'getAll':
            $controller->getAllEquipmentTypes();
            break;
        case 'getActive':
            $controller->getActiveEquipmentTypes();
            break;
        case 'create':
            $controller->createEquipmentType();
            break;
        case 'update':
            $controller->updateEquipmentType();
            break;
        case 'delete':
            $controller->deleteEquipmentType();
            break;
        case 'restore':
            $controller->restoreEquipmentType();
            break;
        default:
            echo json_encode(["error" => "Hành động không hợp lệ"]);
            break;
    }
}
?>
