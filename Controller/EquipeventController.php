<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/EquipEventModel.php';
require_once __DIR__ . '/../Model/EquipmentModel.php';

class EventEquipmentController {
    private $conn;
    private $eventEquipmentModel;
    private $model;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->eventEquipmentModel = new EventEquipment($this->conn);
        $this->model = new Equipment($this->conn);
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eventId = $_POST['EventId'] ?? null;
            $equipmentId = $_POST['EquipmentId'] ?? null;
            $date = $_POST['Date'] ?? null;
            $token = $_POST['token'] ?? null;
            $quantity = $_POST['quantity'] ?? 1;

            if (!$eventId || !$equipmentId || !$date) {
                $this->redirectWithError("Thiếu dữ liệu đầu vào", $eventId, $token);
                return;
            }

            $result = $this->eventEquipmentModel->create($eventId, $equipmentId, $date, $quantity);

            if ($result) {
                $this->redirectWithSuccess("Thêm thiết bị thành công", $eventId, $token);
            } else {
                $this->redirectWithError("Lỗi khi thêm thiết bị", $eventId, $token);
            }
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['Id'] ?? null;
            $eventId = $_POST['EventId'] ?? null;
            $equipmentId = $_POST['EquipmentId'] ?? null;
            $date = $_POST['Date'] ?? null;
            $token = $_POST['token'] ?? null;
            $quantity = $_POST['quantity'] ?? 1;

            if (!$id || !$eventId || !$equipmentId || !$date) {
                $this->redirectWithError("Thiếu dữ liệu đầu vào", $eventId, $token);
                return;
            }

            $result = $this->eventEquipmentModel->update($id, $eventId, $equipmentId, $date, $quantity);

            if ($result) {
                $this->redirectWithSuccess("Cập nhật thiết bị thành công", $eventId, $token);
            } else {
                $this->redirectWithError("Lỗi khi cập nhật thiết bị", $eventId, $token);
            }
        }
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['Id'] ?? null;
            $eventId = $_POST['EventId'] ?? null;
            $token = $_POST['token'] ?? null;

            if (!$id) {
                $this->redirectWithError("Thiếu ID thiết bị", $eventId, $token);
                return;
            }

            $result = $this->eventEquipmentModel->delete($id);

            if ($result) {
                $this->redirectWithSuccess("Xóa thiết bị thành công", $eventId, $token);
            } else {
                $this->redirectWithError("Lỗi khi xóa thiết bị", $eventId, $token);
            }
        }
    }

    public function restore() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['Id'] ?? null;
            $eventId = $_POST['EventId'] ?? null;
            $token = $_POST['token'] ?? null;

            if (!$id) {
                $this->redirectWithError("Thiếu ID thiết bị", $eventId, $token);
                return;
            }

            $result = $this->eventEquipmentModel->restore($id);

            if ($result) {
                $this->redirectWithSuccess("Khôi phục thiết bị thành công", $eventId, $token);
            } else {
                $this->redirectWithError("Lỗi khi khôi phục thiết bị", $eventId, $token);
            }
        }
    }

    private function redirectWithSuccess($message, $eventId, $token) {
        header("Location: ../View/EquipEvent/indexU.php?eventId=" . urlencode($eventId) . "&token=" . urlencode($token) . "&success=" . urlencode($message));
        exit();
    }

    private function redirectWithError($message, $eventId, $token) {
        header("Location: ../View/EquipEvent/create.php?eventId=" . urlencode($eventId) . "&token=" . urlencode($token) . "&error=" . urlencode($message));
        exit();
    }
}

// Xử lý yêu cầu từ client
$controller = new EventEquipmentController();
$controller->create();
