<?php include '../shares/adminhd.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Loại Thiết Bị</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
        }

        .btn-custom {
            width: 100%;
            font-size: 16px;
        }

        .form-label {
            font-weight: bold;
        }

        .back-link {
            text-align: center;
            display: block;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Thêm Loại Thiết Bị</h1>
        <form action="../../controller/EquipmentTypeController.php?action=create" method="POST">
            
            <div class="mb-3">
                <label class="form-label">Tên Loại Thiết Bị:</label>
                <input type="text" class="form-control" name="EquipmentTypeName" required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-custom">Thêm</button>
        </form>

        <a href="index.php" class="back-link text-primary">← Quay lại danh sách</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
