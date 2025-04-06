<?php include '../shares/header.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../../Model/TickettypeModel.php';
require_once __DIR__ . '/../Users/JWTHelper.php';
$database = new Database();
$conn = $database->getConnection();

// Lấy danh sách sự kiện
$eventModel = new Event($conn);
$events = $eventModel->getActiveEvents()->fetchAll(PDO::FETCH_ASSOC);
$ticketModel = new TicketType($conn);
$tickets = $ticketModel->getActiveTicketTypes()->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đặt Vé Sự Kiện</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>
    body{
        padding-top: 60px;
    }
</style>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Đặt Vé Sự Kiện</h2>
        <form action="../../Controller/BookingController.php?action=create" method="POST">
            <div class="mb-3">
                <label class="form-label">Sự kiện:</label>
                <select class="form-select" name="EventId" required>
                    <option value="">-- Chọn sự kiện --</option>
                    <?php foreach ($events as $event): ?>
                        <option value="<?= htmlspecialchars($event['EventId']) ?>">
                            <?= htmlspecialchars($event['EventName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="TicketTypeId">Chọn loại vé:</label>
                <select class="form-select" name="TicketTypeId" id="TicketTypeId" required onchange="updateMaxQuantity()">
                    <option value="" data-quantity="0">-- Chọn loại vé --</option>
                    <?php foreach ($tickets as $ticket) : ?>
                        <option value="<?= htmlspecialchars($ticket['TicketTypeId']) ?>"
                            data-event-id="<?= htmlspecialchars($ticket['EventId']) ?>"
                            data-quantity="<?= htmlspecialchars($ticket['Quantity']) ?>">
                            <?= htmlspecialchars($ticket['TicketName']) ?> - <?= number_format($ticket['Price'], 0, ',', '.') ?> VND - Số lượng: <?= htmlspecialchars($ticket['Quantity']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" id="userIdInput" name="UserId">
            <input type="hidden" id="UpdatedQuantity" name="UpdatedQuantity">
            <div class="mb-3">
                <label for="Quantity">Số lượng:</label>
                <input type="number" class="form-control" id="Quantity" name="Quantity" min="1" required oninput="validateQuantity(this)">
                <p id="quantity-error" style="color: red; display: none;">Số lượng vé không đủ!</p>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đặt Vé</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", async function() {
            const token = localStorage.getItem("token");

            fetch("/Quanlysukien/Controller/UserController.php?action=getUserInfo", {
                    method: "GET",
                    headers: {
                        "Authorization": "Bearer " + token
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert("Phiên đăng nhập hết hạn, vui lòng đăng nhập lại!");
                        window.location.href = "/Quanlysukien/View/Users/register.php";
                    } else {
                        console.log("Thông tin người dùng:", data);

                        // Gán UserId vào input ẩn
                        document.getElementById("userIdInput").value = data.UserId;
                    }
                })
                .catch(error => console.error("Lỗi khi lấy thông tin user:", error));
        });

        function updateMaxQuantity() {
            let ticketSelect = document.getElementById("TicketTypeId");
            let selectedOption = ticketSelect.options[ticketSelect.selectedIndex];
            let maxQuantity = parseInt(selectedOption.getAttribute("data-quantity")) || 0;

            let quantityInput = document.getElementById("Quantity");
            quantityInput.setAttribute("max", maxQuantity);

            // Khi chọn vé, cập nhật giá trị mới vào input hidden
            quantityInput.addEventListener("input", function() {
                let selectedQuantity = parseInt(quantityInput.value) || 0;
                let updatedQuantity = maxQuantity - selectedQuantity;
                document.getElementById("UpdatedQuantity").value = updatedQuantity;
            });
        }

        function validateQuantity(input) {
            let maxQuantity = parseInt(input.getAttribute("max")); // Lấy số lượng tối đa
            let quantity = parseInt(input.value); // Lấy số lượng nhập vào
            let errorMessage = document.getElementById("quantity-error");

            if (quantity > maxQuantity) {
                errorMessage.style.display = "block"; // Hiện thông báo lỗi
                input.value = maxQuantity; // Giới hạn số lượng tối đa
            } else {
                errorMessage.style.display = "none"; // Ẩn thông báo lỗi
            }
        }
        document.addEventListener("DOMContentLoaded", function() {
            const eventSelect = document.querySelector("select[name='EventId']");
            const ticketSelect = document.querySelector("select[name='TicketTypeId']");

            function filterTickets() {
                const selectedEventId = eventSelect.value;

                // Ẩn tất cả vé trước
                Array.from(ticketSelect.options).forEach(option => {
                    if (option.value !== "") {
                        option.hidden = true;
                    }
                });

                // Hiển thị vé thuộc sự kiện đã chọn
                Array.from(ticketSelect.options).forEach(option => {
                    if (option.dataset.eventId === selectedEventId) {
                        option.hidden = false;
                    }
                });

                // Reset giá trị dropdown vé khi chọn sự kiện mới
                ticketSelect.value = "";
            }

            // Gọi filterTickets khi chọn sự kiện mới
            eventSelect.addEventListener("change", filterTickets);

            // Ẩn vé ngay khi trang load
            filterTickets();
        });
    </script>

</body>

</html>