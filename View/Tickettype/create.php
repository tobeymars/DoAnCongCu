<?php include '../shares/adminhd.php'; ?>
<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../../Model/TicketTypeModel.php';

$database = new Database();
$conn = $database->getConnection();
$eventModel = new Event($conn);
$events = $eventModel->getAllEvents()->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Loại Vé</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --success-color: #78e08f;
            --danger-color: #eb4d4b;
            --warning-color: #f6b93b;
            --light-color: #f5f6fa;
            --dark-color: #2f3640;
            
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark-text);
            padding-top: 80px;
            padding-left: 275px;
        }

        .page-header {
            background: linear-gradient(135deg, var(--accent-color), var(--secondary-color));
            color: white;
            padding: 25px 0;
            margin-bottom: 40px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-weight: 600;
            margin: 0;
            font-size: 2rem;
            text-align: center;
        }

        .form-container {
            max-width: 700px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .form-container:hover {
            box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.12);
        }

        .form-label {
            font-weight: 500;
            color: var(--dark-text);
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #dcdcdc;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            padding: 12px 20px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .form-section {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #eee;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 10px;
            padding-bottom: 0;
        }

        .input-group-text {
            background-color: #f8f9fa;
            color: var(--light-text);
            border-radius: 8px 0 0 8px;
            border: 1px solid #dcdcdc;
        }

        .input-with-icon {
            position: relative;
        }
        
        .input-with-icon i {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 15px;
            color: var(--light-text);
        }
        
        .input-with-icon input, .input-with-icon select {
            padding-left: 40px;
        }

        .error-message {
            color: var(--danger-color);
            font-size: 0.85rem;
            margin-top: 5px;
            display: none;
        }

        .card-header1 {
            background-color: rgba(52, 152, 219, 0.1);
            border-bottom: none;
            font-weight: 600;
            color: var(--accent-color);
            padding-left: 225px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--accent-color);
            text-decoration: none;
            margin-bottom: 15px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .back-link:hover {
            color: var(--secondary-color);
        }

        .back-link i {
            margin-right: 8px;
        }

        .help-text {
            color: var(--light-text);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-container {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div class="container">
            <h1 class="page-title"><i class="fas fa-ticket-alt me-2"></i> Thêm Loại Vé</h1>
        </div>
    </div>

    <div class="container">
        <a href="/Quanlysukien/View/Tickettype/index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách sự kiện
        </a>

        <div class="form-container">
            <div class="card-header1 mb-4 rounded">
                <i class="fas fa-plus-circle me-2"></i> Thông tin loại vé mới
            </div>

            <form action="../../Controller/TickettypeController.php?action=create" method="POST" id="ticketForm">
                <div class="form-section">
                    <div class="mb-4">
                        <label for="event_id" class="form-label">Sự Kiện</label>
                        <div class="input-with-icon">
                            <i class="fas fa-calendar-event"></i>
                            <select name="EventId" id="EventId" class="form-select" required>
                                <option value="">-- Chọn Sự Kiện --</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?= $event['EventId'] ?>"><?= htmlspecialchars($event['EventName']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="help-text">Chọn sự kiện mà loại vé này thuộc về</div>
                    </div>

                    <div class="mb-4">
                        <label for="TicketName" class="form-label">Tên Loại Vé</label>
                        <div class="input-with-icon">
                            <i class="fas fa-tag"></i>
                            <input type="text" name="TicketName" id="TicketName" class="form-control" 
                                   placeholder="Ví dụ: Vé VIP, Vé Thường, Vé Học Sinh..." required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="mb-4">
                        <label for="Price" class="form-label">Giá Vé (VNĐ)</label>
                        <div class="input-with-icon">
                            <i class="fas fa-money-bill"></i>
                            <input type="text" name="Price" id="Price" class="form-control" 
                                   placeholder="Nhập giá vé" required oninput="formatCurrency(this)">
                        </div>
                        <div id="priceError" class="error-message">Vui lòng chỉ nhập số!</div>
                        <div class="help-text">Giá vé sẽ được hiển thị dưới dạng VNĐ</div>
                    </div>

                    <div class="mb-4">
                        <label for="Quantity" class="form-label">Số Lượng Vé</label>
                        <div class="input-with-icon">
                            <i class="fas fa-sort-numeric-up"></i>
                            <input type="number" name="Quantity" id="Quantity" class="form-control" 
                                   placeholder="Số lượng vé phát hành" required min="1">
                        </div>
                        <div class="help-text">Số lượng vé tối đa được bán cho loại vé này</div>
                    </div>
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Lưu Loại Vé
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function formatCurrency(input) {
            // Lấy giá trị và loại bỏ tất cả ký tự không phải số
            let value = input.value.replace(/\D/g, '');
            
            if (!value) {
                document.getElementById("priceError").style.display = "block";
                input.value = '';
                return;
            } else {
                document.getElementById("priceError").style.display = "none";
            }
            
            // Định dạng số với dấu chấm ngăn cách phần nghìn
            input.value = new Intl.NumberFormat('vi-VN').format(value);
        }

        // Xóa dấu "." trong giá khi gửi form
        document.getElementById("ticketForm").addEventListener("submit", function(event) {
            let priceInput = document.getElementById("Price");
            priceInput.value = priceInput.value.replace(/\./g, '');
        });

        // Hiệu ứng focus cho input
        const inputs = document.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.boxShadow = '0 0 0 0.2rem rgba(52, 152, 219, 0.25)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.boxShadow = 'none';
            });
        });
    </script>
</body>
</html>