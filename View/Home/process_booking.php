<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Controller/BookingController.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Bạn chưa đăng nhập"]);
    exit();
}

$database = new Database();
$conn = $database->getConnection();

$user_id = $_SESSION['user_id'];
$event_id = $_POST['event_id'] ?? null;
$ticket_types = $_POST['ticket_types'] ?? [];
$ticket_quantities = $_POST['ticket_quantities'] ?? [];

// Debug dữ liệu đầu vào
if (!$event_id || empty($ticket_types) || empty($ticket_quantities)) {
    echo json_encode([
        "error" => "Vui lòng chọn ít nhất một loại vé",
        "debug" => ["event_id" => $event_id, "ticket_types" => $ticket_types, "ticket_quantities" => $ticket_quantities]
    ]);
    exit();
}

try {
    $conn->beginTransaction();

    // Kiểm tra xem EventId có hợp lệ không
    $stmt = $conn->prepare("SELECT EventId FROM events WHERE EventId = ?");
    $stmt->execute([$event_id]);
    if ($stmt->rowCount() == 0) {
        echo json_encode(["error" => "Sự kiện không hợp lệ"]);
        exit();
    }

    // Thêm vào bảng bookings
    $stmt = $conn->prepare("INSERT INTO bookings (UserId, EventId, BookingDate, Status, IsDeleted) VALUES (?, ?, NOW(), 'pending', 0)");
    $stmt->execute([$user_id, $event_id]);
    $booking_id = $conn->lastInsertId(); // Get the inserted booking ID

    // Thêm vào bảng bookingdetails
    $stmt = $conn->prepare("INSERT INTO bookingdetails (BookingId, TicketTypeId, Quantity, IsDeleted) VALUES (?, ?, ?, 0)");

    foreach ($ticket_types as $ticket_type_id) {
        if (!isset($ticket_quantities[$ticket_type_id]) || $ticket_quantities[$ticket_type_id] <= 0) {
            continue;
        }

        // Kiểm tra TicketTypeId có hợp lệ không
        $stmt_check = $conn->prepare("SELECT TicketTypeId, Quantity FROM tickettypes WHERE TicketTypeId = ?");
        $stmt_check->execute([$ticket_type_id]);

        if ($stmt_check->rowCount() == 0) {
            echo json_encode(["error" => "Loại vé không hợp lệ: ID " . $ticket_type_id]);
            exit();
        }

        $ticket = $stmt_check->fetch(PDO::FETCH_ASSOC);
        if ($ticket['Quantity'] < $ticket_quantities[$ticket_type_id]) {
            echo json_encode(["error" => "Số lượng vé không đủ cho loại vé ID " . $ticket_type_id]);
            exit();
        }

        $quantity = (int) $ticket_quantities[$ticket_type_id];

        // Thêm dữ liệu vào bookingdetails
        $stmt->execute([$booking_id, $ticket_type_id, $quantity]);

        // Giảm số lượng vé sau khi đặt
        $new_quantity = $ticket['Quantity'] - $quantity;
        $stmt_update = $conn->prepare("UPDATE tickettypes SET Quantity = ? WHERE TicketTypeId = ?");
        $stmt_update->execute([$new_quantity, $ticket_type_id]);
    }

    $conn->commit();
    echo json_encode(["success" => "Đặt vé thành công"]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(["error" => "Lỗi khi đặt vé: " . $e->getMessage()]);
}
exit();
