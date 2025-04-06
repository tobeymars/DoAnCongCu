<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/VenuesModel.php';

class VenueController
{
    private $model;
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->model = new Venue($this->conn);
    }

    public function getAllVenues()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $venues = $this->model->getAllVenues()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($venues);
        }
    }

    public function getVenueById()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
            $venue = $this->model->getVenueById($_GET['id']);
            if ($venue) {
                echo json_encode($venue);
            } else {
                echo json_encode(["error" => "Địa điểm không tồn tại"]);
            }
        }
    }

    public function createVenue()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $venueName = $_POST['VenueName'] ?? '';
            $address = $_POST['Address'] ?? '';
            $capacity = $_POST['Capacity'] ?? '';
            $description = $_POST['Description'] ?? '';
            $status = $_POST['Status'] ?? 'Available';
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
            if ($this->model->createVenue($venueName, $address, $capacity, $description, $status, $imagePath)) {
                header("Location: ../View/Venues/index.php");
                exit();
            } else {
                echo "<script>alert('Địa điểm đã tồn tại! Vui lòng nhập lại.'); window.history.back();</script>";
            }
        }
    }

    public function updateVenue()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['VenueId'])) {
            $venueId = $_POST['VenueId'];
            $venueName = $_POST['VenueName'] ?? '';
            $address = $_POST['Address'] ?? '';
            $capacity = $_POST['Capacity'] ?? '';
            $description = $_POST['Description'] ?? '';
            $status = $_POST['Status'] ?? 'Available';
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

            if ($this->model->updateVenue($venueId, $venueName, $address, $capacity, $description, $status, $imagePath)) {
                header("Location: ../View/Venues/index.php");
                exit();
            } else {
                echo "<script>alert('Địa điểm đã tồn tại! Vui lòng nhập lại.'); window.history.back();</script>";
            }
        }
    }
    public function deleteVenue()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['VenueId'])) {
            $venueId = $_POST['VenueId'];

            if ($this->model->deleteVenue($venueId)) {
                header("Location: ../View/Venues/index.php");
                exit();
            } else {
                header("Location: ../views/Venues/index.php?error=Lỗi khi xóa địa điểm");
                exit();
            }
        }
    }
    public function restoreVenue()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['VenueId'])) {
            $venueId = $_POST['VenueId'];

            if ($this->model->restoreVenue($venueId)) {
                header("Location: ../View/Venues/index.php");
                exit();
            } else {
                header("Location: ../views/venues/index.php?error=Lỗi khi xóa địa điểm");
                exit();
            }
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new VenueController();
    if ($_GET['action'] === 'getAllVenues') {
        $controller->getAllVenues();
    } elseif ($_GET['action'] === 'getVenueById') {
        $controller->getVenueById();
    } elseif ($_GET['action'] === 'createVenue') {
        $controller->createVenue();
    } elseif ($_GET['action'] === 'updateVenue') {
        $controller->updateVenue();
    } elseif ($_GET['action'] === 'deleteVenue') {
        $controller->deleteVenue();
    }
    elseif ($_GET['action'] === 'restoreVenue') {
        $controller->restoreVenue();
    }
}
