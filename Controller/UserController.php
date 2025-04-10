<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/UserModel.php';
require_once __DIR__ . "/../View/Users/JWTHelper.php";
class UserController
{
    private $model;
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->model = new UserModel($this->conn);
    }
    // Lấy tất cả user
    public function getAllUsers()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $users = $this->model->getAllUser()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
        }
    }
    public function getRoles()
    {
        $query = "SELECT RoleId, RoleName FROM roles";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function changePassword()
    {
        header("Content-Type: application/json");
        $data = json_decode(file_get_contents("php://input"), true);

        $token = $data["token"] ?? "";
        $currentPassword = $data["currentPassword"] ?? "";
        $newPassword = $data["newPassword"] ?? "";
        $confirmPassword = $data["confirmPassword"] ?? "";

        // 1. Xác thực token
        $payload = JWTHelper::verifyToken($token);
        if (!$payload) {
            echo json_encode(["error" => "Token không hợp lệ"]);
            exit();
        }

        $userId = $payload["user_id"];

        // 2. Lấy thông tin người dùng
        $user = $this->model->getUserInfo($userId);
        if (!$user) {
            echo json_encode(["error" => "Không tìm thấy người dùng"]);
            exit();
        }

        // 3. Kiểm tra mật khẩu hiện tại
        $hashedPassword = $user["Password"] ?? null;
        if (!$hashedPassword || !password_verify($currentPassword, $hashedPassword)) {
            echo json_encode(["error" => "Mật khẩu hiện tại không đúng"]);
            exit;
        }

        // 4. Kiểm tra xác nhận mật khẩu
        if ($newPassword !== $confirmPassword) {
            echo json_encode(["error" => "Mật khẩu xác nhận không khớp"]);
            exit();
        }

        // 5. Cập nhật mật khẩu
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $result = $this->model->updatePassword($userId, $hashedPassword);

        if ($result) {
            echo json_encode([
                "success" => true,
                "message" => "Đổi mật khẩu thành công"
            ]);
        } else {
            echo json_encode(["error" => "Đổi mật khẩu thất bại"]);
        }
    }
    public function registerUser()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $fullname = trim($_POST['fullname']);
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $role_id = $_POST['role'];
            $result = $this->model->register($username, $password, $confirm_password, $fullname, $email, $role_id);
            header('Content-Type: application/json');
            echo json_encode(["message" => $result, "success" => $result === "Đăng ký thành công."]);
            exit();
        }
    }
    public function registerUserad()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $fullname = trim($_POST['fullname']);
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $role_id = $_POST['role'];
            $result = $this->model->register($username, $password, $confirm_password, $fullname, $email, $role_id);
            if ($result) {
                header("Location: ../View/Users/index.php");
                exit();
            } else {
                echo "<script>alert('Lỗi! Không thể tạo loại vé.'); window.history.back();</script>";
            }
        }
    }
    public function loginUser()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $result = $this->model->login($username, $password);
            // Kiểm tra nếu tài khoản bị khóa hoặc không tồn tại
            if (isset($result['error'])) {
                echo json_encode($result);
                exit();
            }
            if ($result) {
                // Lưu thông tin người dùng vào session
                $_SESSION['user_id'] = $result['UserId'];
                $_SESSION['username'] = $result['Username'];
                $_SESSION['full_name'] = $result['FullName'];
                $_SESSION['role'] = $result['RoleId'];
                $payload = [
                    "user_id" => $result['UserId'],
                    "username" => $result['Username'],
                    "RoleId" => $result["RoleId"]
                ];
                $token = JWTHelper::createToken($payload);
                echo json_encode(["token" => $token]);
            } else {
                echo json_encode(["error" => "Sai tài khoản hoặc mật khẩu"]);
                exit();
            }
        }
    }
    public function getUserInfo()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $headers = getallheaders();
            if (!isset($headers['Authorization'])) {
                echo json_encode(["error" => "Thiếu token"]);
                exit();
            }

            $token = str_replace("Bearer ", "", $headers['Authorization']);
            $payload = JWTHelper::verifyToken($token);

            if (!$payload) {
                echo json_encode(["error" => "Token không hợp lệ"]);
                exit();
            }

            $userId = $payload['user_id'];
            $userInfo = $this->model->getUserInfo($userId);

            if ($userInfo) {
                echo json_encode($userInfo);
            } else {
                echo json_encode(["error" => "Không tìm thấy thông tin người dùng"]);
            }
        }
    }
    public function updateUseradmin()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $userId = $_POST['UserId'];
            $fullName = $_POST['FullName'];
            $email = $_POST['Email'];
            $sdt = $_POST['sdt'];
            $images   = $_POST["images"] ?? "";
            $sdt = empty($sdt) ? "Chưa có" : $sdt;
            $images = empty($images) ? "Chưa có" : $images;

            // Cập nhật thông tin người dùng
            $result = $this->model->updateUserInfo($userId, $fullName, $email, $sdt, $images);

            if ($result) {
                session_start();
                $_SESSION['success_message'] = "Cập nhật thông tin người dùng thành công!";
                header("Location: ../View/Users/index.php");
                exit();
            } else {
                echo "<script>alert('Cập nhật thất bại!'); window.location.href='edit.php?id=$userId';</script>";
            }
        } else {
            echo "<script>alert('Yêu cầu không hợp lệ!'); window.location.href='index.php';</script>";
        }
    }
    public function updateUser()
    {
        header("Content-Type: application/json");
        $token = $_POST["token"] ?? "";
        $user  = JWTHelper::verifyToken($token);
        if (!$user) {
            echo json_encode(["error" => "Token không hợp lệ"]);
            exit();
        }
        $userId   = $user["user_id"];
        $fullName = $_POST["fullName"] ?? "";
        $email    = $_POST["email"] ?? "";
        $sdt      = $_POST["sdt"] ?? "";
        $images   = $_POST["images"] ?? "";
        $sdt = empty($sdt) ? "Chưa có" : $sdt;
        $images = empty($images) ? "Chưa có" : $images;
        if ($this->model->updateUserInfo($userId, $fullName, $email, $sdt, $images)) {
            echo json_encode(["success" => "Cập nhật thành công"]);
        } else {
            echo json_encode(["error" => "Cập nhật thất bại"]);
        }
        exit();
    }
    public function deleteUser()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $userId = $_POST['UserId'];

            if ($this->model->deleteUser($userId)) {
                header("Location: ../View/Users/index.php");
                exit();
            } else {
                header("Location: ../views/Users/index.php?error=Lỗi khi xóa địa điểm");
                exit();
            }
        }
    }
    public function restoreUser()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $userId = $_POST['UserId'];

            if ($this->model->restoreUser($userId)) {
                header("Location: ../View/Users/index.php");
                exit();
            } else {
                header("Location: ../views/Users/index.php?error=Lỗi khi xóa địa điểm");
                exit();
            }
        }
    }
}
if (isset($_GET['action'])) {
    $controller = new UserController();

    if ($_GET['action'] === 'login') {
        $controller->loginUser();
    } elseif ($_GET['action'] === 'register') {
        $controller->registerUser();
    } elseif ($_GET['action'] === 'getUserInfo') {
        $controller->getUserInfo();
    } elseif ($_GET['action'] === 'updateUser') {
        $controller->updateUser();
    } elseif ($_GET['action'] === 'registerad') {
        $controller->registerUserad();
    } elseif ($_GET['action'] === 'edituser') {
        $controller->updateUseradmin();
    } elseif ($_GET['action'] === 'delete') {
        $controller->deleteUser();
    } elseif ($_GET['action'] === 'restore') {
        $controller->restoreUser();
    }elseif ($_GET['action'] === 'changePassword') {
        $controller->changePassword();
    }
}
