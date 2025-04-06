<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../Model/BookingModel.php';
require_once __DIR__ . '/../Model/Bookingdetail.php';
require_once __DIR__ . '/../Model/TickettypeModel.php';
require_once __DIR__ . '/../phpqrcode/qrlib.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class BookingController
{
    private $model;
    private $bookingDetailModel;
    private $ticketModel;
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->model = new Booking($this->conn);
        $this->bookingDetailModel = new BookingDetails($this->conn);
        $this->ticketModel = new TicketType($this->conn);
    }

    // Lấy tất cả đơn đặt vé (admin)
    public function getAllBookings()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET") {
            $bookings = $this->model->getAllBookings()->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($bookings);
        }
    }

    // Lấy danh sách đặt vé của user
    public function getUserBookings()
    {
        if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['userId'])) {
            $userId = $_GET['userId'];
            $bookings = $this->model->getUserBookings($userId);
            echo json_encode($bookings);
        }
    }
    public function cancelBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['BookingId']; // Lấy BookingId từ POST

            // Kiểm tra xem BookingId có trống không
            if (empty($bookingId)) {
                echo json_encode(["error" => "Vui lòng nhập đầy đủ thông tin"]);
                exit();
            }
            // Gọi phương thức cancelBooking để cập nhật trạng thái đơn
            $result = $this->model->cancelBooking($bookingId);

            // Nếu hủy đơn thành công
            if ($result) {

                echo json_encode(["success" => "Đơn đã được hủy thành công"]);
                exit();
            } else {
                echo json_encode(["error" => "Lỗi! Không thể hủy đơn"]);
                exit();
            }
        }
    }

    public function updateBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['BookingId'];
            
            if (empty($bookingId)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }
        // Đảm bảo thư mục QR tồn tại và có quyền ghi
        $qrDirectory = __DIR__ . '/QR/';
        if (!file_exists($qrDirectory)) {
            mkdir($qrDirectory, 0777, true); // Tạo thư mục nếu chưa tồn tại
        }

        // Kiểm tra nếu mã QR đã được tạo trước đó
        $qrFilePath = $qrDirectory . 'Booking_' . $bookingId . '.png';

        // Nếu chưa có file QR, tạo mới
        if (!file_exists($qrFilePath)) {
            // Tạo URL cho mã QR
            $url = "http://192.168.88.50/Quanlysukien/View/Booking/QR.php?id=" . $bookingId;  
            // Tạo mã QR và lưu vào file
            QRcode::png($url, $qrFilePath);
        }
        // Lấy email và tên người dùng từ bảng users
        $userData = $this->model->getUserEmailAndFullNameByBookingId($bookingId);
            if ($this->model->updateBooking($bookingId)) {
                $email = $userData['Email'];
                $fullName = $userData['FullName'];
                // Thêm thông tin bookingdetails và tickettypes vào email
                $bookingDetails = $userData['Quantity'];
                $ticketName = $userData['TicketName'];
                $ticketPrice = $userData['Price'];
                $total = $userData['Price'] * $userData['Quantity'];
                $this->sendEmailNotification($bookingId, $qrFilePath, $email, $fullName, $bookingDetails, $ticketName, $ticketPrice, $total);
                echo json_encode(["success" => "Cập nhật thành công", "qrFilePath" => $qrFilePath]);
            } else {
                echo json_encode(["error" => "Cập nhật thất bại"]);
            }
        }
    }
    //Gửi email thông báo
    public function sendEmailNotification($bookingId, $qrFilePath, $email, $fullName, $bookingDetails, $ticketName, $ticketPrice, $total)
    {
        $mail = new PHPMailer(true);  // Khởi tạo đối tượng PHPMailer
        try {
            // Cấu hình SMTP của Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noheart135246@gmail.com';  // Thay bằng địa chỉ email của bạn
            $mail->Password = 'vyil sjgy ywsn rmap';  // Thay bằng mật khẩu ứng dụng Gmail của bạn
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            // Thiết lập charset là UTF-8
            $mail->CharSet = 'UTF-8';
        
            // Người gửi và người nhận
            $mail->setFrom('noheart135246@gmail.com', 'Website sự kiện');
            $mail->addAddress($email);

            // Tiêu đề email (Mã hóa tiêu đề để hỗ trợ các ký tự đặc biệt)
            $mail->Subject = 'Thông báo đặt vé thành công';
            $mail->Subject = $mail->encodeHeader($mail->Subject, 'UTF-8', 'Q'); // Mã hóa tiêu đề

            $mail->isHTML(true);  // Đảm bảo nội dung email hỗ trợ HTML
            // Nội dung email
            $mail->Body = "Kính gửi <strong>$fullName</strong>,<br><br>";
            $mail->Body .= "Chúng tôi xin thông báo rằng <strong>Mã Vé: $bookingId</strong> của bạn đã được xác nhận thành công.<br><br>";
            $mail->Body .= "Dưới đây là thông tin chi tiết về vé của bạn:<br>";
            $mail->Body .= "------------------------------------------------<br>";
            $mail->Body .= "<strong>Số lượng vé:</strong> $bookingDetails<br>";
            $mail->Body .= "<strong>Tên vé:</strong> $ticketName<br>";
            $mail->Body .= "<strong>Giá vé mỗi vé:</strong> " . number_format($ticketPrice, 0, ',', '.') . " VND<br>";
            $mail->Body .= "<strong>Tổng tiền:</strong> " . number_format($total, 0, ',', '.') . " VND<br>";
            $mail->Body .= "------------------------------------------------<br><br>";
            $mail->Body .= "Để hoàn tất quá trình đăng ký, vui lòng tải xuống mã QR dưới đây để sử dụng khi tham gia sự kiện (<strong>Mã QR sẽ thay cho vé</strong>):<br><br>";
            $mail->Body .= "Vui lòng giữ mã QR này để kiểm tra tại quầy sự kiện.<br><br>";
            $mail->Body .= "Chúng tôi rất mong được chào đón bạn tại sự kiện.<br><br>";
            $mail->Body .= "<strong>Trân trọng,</strong><br>";
            $mail->Body .= "<strong>Đội ngũ hỗ trợ sự kiện</strong><br>";
            $mail->Body .= "Website sự kiện<br>";
            $mail->Body .= "Liên hệ: support@eventwebsite.com<br><br>";
            $mail->Body .= "------------------------------------------------<br>";
            $mail->Body .= "Lưu ý: Nếu bạn không thực hiện đăng ký này, vui lòng bỏ qua email này.<br>";            
            
            // Đính kèm tệp QR (nếu cần)
            $mail->addAttachment($qrFilePath);
    
            // Gửi email
            $mail->send();
        } catch (Exception $e) {
            echo json_encode(["error" => "Không thể gửi email. Lỗi: {$mail->ErrorInfo}"]);
        }
    }    
    // Xóa đơn đặt vé (admin)
    public function deleteBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['BookingId'];

            if (empty($bookingId)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            if ($this->model->deleteBooking($bookingId)) {
                echo json_encode(["success" => "Xóa thành công"]);
            } else {
                echo json_encode(["error" => "Xóa thất bại"]);
            }
        }
    }

    // Khôi phục đơn đặt vé (admin)
    public function restoreBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $bookingId = $_POST['BookingId'];

            if (empty($bookingId)) {
                echo json_encode(["error" => "Dữ liệu không hợp lệ"]);
                exit();
            }

            if ($this->model->restoreBooking($bookingId)) {
                echo json_encode(["success" => "Khôi phục thành công"]);
            } else {
                echo json_encode(["error" => "Khôi phục thất bại"]);
            }
        }
    }
}

// Xử lý request từ URL
if (isset($_GET['action'])) {
    $controller = new BookingController();

    if ($_GET['action'] === 'getAll') {
        $controller->getAllBookings();
    } elseif ($_GET['action'] === 'getUserBookings') {
        $controller->getUserBookings();
    } elseif ($_GET['action'] === 'update') {
        $controller->updateBooking();
    } elseif ($_GET['action'] === 'delete') {
        $controller->deleteBooking();
    } elseif ($_GET['action'] === 'restore') {
        $controller->restoreBooking();
    }elseif ($_GET['action'] === 'cancel') {
        $controller->cancelBooking();
    }
}
