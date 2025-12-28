<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem thông tin học sinh - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
            background-color: #f8f9fa;
            color: #495057;
            cursor: not-allowed;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .info-value {
            padding: 10px 12px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: #495057;
            min-height: 42px;
        }
        
        .info-value.empty {
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span>Xem thông tin học sinh</span>
                <a href="?act=admin-dashboard" class="btn" style="background: #6c757d; color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px;">🏠 Trang chủ</a>
            </div>
        </h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($hocSinh)): ?>
            <div class="form-group">
                <label>Mã người dùng</label>
                <div class="info-value">
                    <?= htmlspecialchars($hocSinh['ma_nguoi_dung'] ?? 'N/A') ?>
                </div>
            </div>

            <div class="form-group">
                <label>Họ tên</label>
                <div class="info-value">
                    <?= htmlspecialchars($hocSinh['ho_ten'] ?? 'N/A') ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email</label>
                    <div class="info-value">
                        <?= htmlspecialchars($hocSinh['email'] ?? 'N/A') ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Số điện thoại</label>
                    <div class="info-value <?= empty($hocSinh['so_dien_thoai']) ? 'empty' : '' ?>">
                        <?= !empty($hocSinh['so_dien_thoai']) ? htmlspecialchars($hocSinh['so_dien_thoai']) : 'Chưa cập nhật' ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Địa chỉ</label>
                <div class="info-value <?= empty($hocSinh['dia_chi']) ? 'empty' : '' ?>">
                    <?= !empty($hocSinh['dia_chi']) ? nl2br(htmlspecialchars($hocSinh['dia_chi'])) : 'Chưa cập nhật' ?>
                </div>
            </div>

            <div class="form-group">
                <label>Trạng thái</label>
                <div class="info-value">
                    <span style="padding: 5px 10px; border-radius: 3px; background: <?= $hocSinh['trang_thai'] == 1 ? '#d4edda' : '#f8d7da' ?>; color: <?= $hocSinh['trang_thai'] == 1 ? '#155724' : '#721c24' ?>;">
                        <?= $hocSinh['trang_thai'] == 1 ? 'Hoạt động' : 'Khóa' ?>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label>Ngày tạo</label>
                <div class="info-value">
                    <?= isset($hocSinh['ngay_tao']) ? date('d/m/Y H:i:s', strtotime($hocSinh['ngay_tao'])) : 'N/A' ?>
                </div>
            </div>

            <div class="btn-group">
                <a href="?act=admin-list-hoc-sinh" class="btn btn-secondary">Quay lại</a>
            </div>
        <?php else: ?>
            <div class="alert alert-error">
                Không tìm thấy thông tin học sinh!
            </div>
            <div class="btn-group">
                <a href="?act=admin-list-hoc-sinh" class="btn btn-secondary">Quay lại</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
