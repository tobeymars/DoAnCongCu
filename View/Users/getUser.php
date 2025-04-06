<?php
require_once __DIR__ . "/../../config/db.php";
require_once __DIR__ . "/JWTHelper.php";
require_once __DIR__ . "/../../Model/UserModel.php";
header("Content-Type: application/json");
$token = $_GET["token"] ?? "";
$user = JWTHelper::verifyToken($token);
if (!$user) {
    echo json_encode(["error" => "Invalid token"]);
    exit();
}
$database = new Database();
$conn = $database->getConnection();
$model = new UserModel($conn);
$userInfo = $model->getUserInfo($user["user_id"]);
if (!$userInfo) {
    echo json_encode(["error" => "User not found"]);
    exit();
}
echo json_encode([
    "user_id"  => $user["user_id"],
    "username" => $user["username"], 
    "fullName" => $userInfo["FullName"], 
    "email"    => $userInfo["Email"],
    "RoleId"    => $userInfo["RoleId"],
    "sdt"      => isset($userInfo["sdt"]) ? $userInfo["sdt"] : "",
    "images"   => isset($userInfo["images"]) ? $userInfo["images"] : ""
]);
exit();
