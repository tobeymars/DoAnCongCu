<?php
class UserModel
{
    private $conn;
    private $table_name = "Users";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllUser()
    {
        $query = "SELECT u.Username, u.UserId,u.FullName, u.Email, u.IsDeleted, r.RoleId, r.RoleName 
              FROM " . $this->table_name . " u 
              JOIN roles r ON u.RoleId = r.RoleId";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function register($username, $password, $confirm_password, $fullname, $email, $role_id)
    {
        if (empty($username) || empty($password) || empty($confirm_password) || empty($fullname) || empty($email)) {
            return "Vui lòng điền đầy đủ thông tin.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Email không hợp lệ.";
        }

        if (strlen($password) < 6) {
            return "Mật khẩu phải có ít nhất 6 ký tự.";
        }

        if ($password !== $confirm_password) {
            return "Mật khẩu xác nhận không khớp.";
        }

        $username = htmlspecialchars($username);
        $fullname = htmlspecialchars($fullname);
        $email = htmlspecialchars($email);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $check_query = "SELECT * FROM " . $this->table_name . " WHERE Username = :username OR Email = :email";
        $stmt = $this->conn->prepare($check_query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return "Tên đăng nhập hoặc Email đã tồn tại.";
        } else {
            $insert_query = "INSERT INTO " . $this->table_name . " (Username, Password, FullName, Email, RoleId) VALUES (:username, :password, :fullname, :email, :role_id)";
            $stmt = $this->conn->prepare($insert_query);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":fullname", $fullname);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":role_id", $role_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return "Đăng ký thành công.";
            } else {
                return "Lỗi: " . $stmt->errorInfo()[2];
            }
        }
    }
    public function login($username, $password)
    {
        $query = "SELECT UserId, Username, FullName, IsDeleted, RoleId, Password FROM " . $this->table_name . " WHERE Username = :Username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':Username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!isset($user['Password']) || !password_verify($password, $user['Password'])) {
                return ["error" => "Sai tài khoản hoặc mật khẩu"];
            }
            if ($user['IsDeleted'] == 1) {
                return ["error" => "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên."];
            }
            return [
                "UserId" => $user["UserId"],
                "Username" => $user["Username"],
                "FullName" => $user["FullName"],
                "RoleId" => $user["RoleId"]
            ];
        }

        return ["error" => "Sai tài khoản hoặc mật khẩu"];
    }
    public function getUserInfo($userId)
    {
        $query = "SELECT UserId, Username, FullName, RoleId, Email, sdt, images, Password FROM " . $this->table_name . " WHERE UserId = :UserId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return null;
    }
    public function updatePassword($userId, $newHashedPassword) {
        $query = "UPDATE users SET Password = :password WHERE UserId = :userId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":password", $newHashedPassword);
        $stmt->bindParam(":userId", $userId);
        return $stmt->execute();
    }
    public function updateUserInfo($userId, $fullName, $email, $sdt, $images)
    {
        $query = "UPDATE " . $this->table_name . " 
              SET FullName = :FullName, Email = :Email, sdt = :sdt, images = :images 
              WHERE UserId = :UserId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':UserId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':FullName', $fullName, PDO::PARAM_STR);
        $stmt->bindParam(':Email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':sdt', $sdt, PDO::PARAM_STR);
        $stmt->bindParam(':images', $images, PDO::PARAM_STR);
        return $stmt->execute();
    }
    public function deleteUser($userId)
    {
        $query = "UPDATE " . $this->table_name . " SET IsDeleted = 1 WHERE UserId = :UserId";
        $stmt = $this->conn->prepare($query);
        $userId = intval($userId);
        $stmt->bindParam(":UserId", $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function restoreUser($userId)
    {
        $query = "UPDATE " . $this->table_name . " SET IsDeleted = 0 WHERE UserId = :UserId";
        $stmt = $this->conn->prepare($query);
        $userId = intval($userId);
        $stmt->bindParam(":UserId", $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
