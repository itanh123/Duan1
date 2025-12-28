<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($dangKy) ? 'Sửa Đăng Ký' : 'Thêm Đăng Ký' ?> - Admin</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        
        .alert-error, .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .form-section h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
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
        
        .form-group label.required::after {
            content: ' *';
            color: #dc3545;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        .form-control[readonly] {
            background: #e9ecef;
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
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <span><?= isset($dangKy) ? 'Sửa Đăng Ký' : 'Thêm Đăng Ký' ?></span>
            <a href="?act=admin-list-dang-ky" class="btn btn-secondary">← Quay lại</a>
        </h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="?act=<?= isset($dangKy) ? 'admin-update-dang-ky' : 'admin-save-dang-ky' ?>">
            <?php if (isset($dangKy)): ?>
                <input type="hidden" name="id" value="<?= $dangKy['id'] ?>">
            <?php endif; ?>

            <!-- Thông tin học sinh -->
            <div class="form-section">
                <h3>Thông tin học sinh</h3>
                <div class="form-group">
                    <label>Họ tên</label>
                    <input type="text" 
                           class="form-control" 
                           value="<?= htmlspecialchars($dangKy['ten_hoc_sinh'] ?? '') ?>" 
                           readonly>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" 
                               class="form-control" 
                               value="<?= htmlspecialchars($dangKy['email_hoc_sinh'] ?? '') ?>" 
                               readonly>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= htmlspecialchars($dangKy['so_dien_thoai'] ?? '') ?>" 
                               readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label>Địa chỉ</label>
                    <input type="text" 
                           class="form-control" 
                           value="<?= htmlspecialchars($dangKy['dia_chi'] ?? '') ?>" 
                           readonly>
                </div>
            </div>

            <!-- Thông tin lớp học -->
            <div class="form-section">
                <h3>Thông tin lớp học</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Lớp học</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= htmlspecialchars($dangKy['ten_lop'] ?? '') ?>" 
                               readonly>
                    </div>

                    <div class="form-group">
                        <label>Khóa học</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= htmlspecialchars($dangKy['ten_khoa_hoc'] ?? '') ?>" 
                               readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Giá khóa học</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= number_format($dangKy['gia'] ?? 0, 0, ',', '.') ?> đ" 
                               readonly>
                    </div>

                    <div class="form-group">
                        <label>Số lượng tối đa</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= $dangKy['so_luong_toi_da'] ?? 'N/A' ?>" 
                               readonly>
                    </div>
                </div>
            </div>

            <!-- Thông tin đăng ký -->
            <div class="form-section">
                <h3>Thông tin đăng ký</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày đăng ký</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= date('d/m/Y H:i:s', strtotime($dangKy['ngay_dang_ky'])) ?>" 
                               readonly>
                    </div>

                    <div class="form-group">
                        <label for="trang_thai" class="required">Trạng thái</label>
                        <select name="trang_thai" id="trang_thai" class="form-control" required>
                            <option value="Chờ xác nhận" <?= (isset($dangKy) && $dangKy['trang_thai'] == 'Chờ xác nhận') ? 'selected' : '' ?>>Chờ xác nhận</option>
                            <option value="Đã xác nhận" <?= (isset($dangKy) && $dangKy['trang_thai'] == 'Đã xác nhận') ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="Đã hủy" <?= (isset($dangKy) && $dangKy['trang_thai'] == 'Đã hủy') ? 'selected' : '' ?>>Đã hủy</option>
                            <option value="Hoàn tiền" <?= (isset($dangKy) && $dangKy['trang_thai'] == 'Hoàn tiền') ? 'selected' : '' ?>>Hoàn tiền</option>
                        </select>
                    </div>
                </div>
            </div>

            <?php
            // Lấy thông tin thanh toán nếu có
            if (isset($dangKy['id'])) {
                require_once('./admin/Model/adminmodel.php');
                $adminModel = new adminmodel();
                $thanhToan = $adminModel->getThanhToanByIdDangKy($dangKy['id']);
                
                // Kiểm tra đã hoàn tiền chưa
                $daHoanTien = false;
                $thongTinHoanTien = null;
                if ($thanhToan) {
                    $checkHoanTien = $adminModel->conn->prepare("
                        SELECT * FROM hoan_tien 
                        WHERE id_thanh_toan = :id_thanh_toan 
                        ORDER BY ngay_tao DESC
                        LIMIT 1
                    ");
                    $checkHoanTien->execute([':id_thanh_toan' => $thanhToan['id']]);
                    $thongTinHoanTien = $checkHoanTien->fetch();
                    $daHoanTien = $thongTinHoanTien !== false;
                }
            ?>
            <!-- Thông tin thanh toán -->
            <?php if ($thanhToan): ?>
            <div class="form-section">
                <h3>Thông tin thanh toán</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phương thức thanh toán</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= htmlspecialchars($thanhToan['phuong_thuc']) ?>" 
                               readonly>
                    </div>
                    <div class="form-group">
                        <label>Số tiền</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= number_format($thanhToan['so_tien'], 0, ',', '.') ?> đ" 
                               readonly>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày thanh toán</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= date('d/m/Y H:i:s', strtotime($thanhToan['ngay_thanh_toan'])) ?>" 
                               readonly>
                    </div>
                    <div class="form-group">
                        <label>Mã giao dịch</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= htmlspecialchars($thanhToan['ma_giao_dich'] ?? 'N/A') ?>" 
                               readonly>
                    </div>
                </div>
                
                <!-- Thông tin hoàn tiền -->
                <?php if ($daHoanTien && $thongTinHoanTien): ?>
                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px;">
                    <h4 style="color: #856404; margin-bottom: 10px;">Thông tin hoàn tiền</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Mã hoàn tiền</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($thongTinHoanTien['ma_hoan_tien']) ?>" 
                                   readonly>
                        </div>
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($thongTinHoanTien['trang_thai']) ?>" 
                                   readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Lý do hoàn tiền</label>
                        <textarea class="form-control" readonly><?= htmlspecialchars($thongTinHoanTien['ly_do'] ?? '') ?></textarea>
                    </div>
                </div>
                <?php elseif ($thanhToan['phuong_thuc'] === 'VNPAY' && $dangKy['trang_thai'] === 'Đã xác nhận'): ?>
                <!-- Form hoàn tiền -->
                <div style="margin-top: 20px; padding: 15px; background: #e7f3ff; border: 1px solid #007bff; border-radius: 5px;">
                    <h4 style="color: #004085; margin-bottom: 15px;">Hoàn tiền tự động</h4>
                    <form method="POST" action="?act=admin-hoan-tien" onsubmit="return confirm('Bạn có chắc chắn muốn hoàn tiền cho đăng ký này? Hành động này không thể hoàn tác!');">
                        <input type="hidden" name="id_dang_ky" value="<?= $dangKy['id'] ?>">
                        <div class="form-group">
                            <label for="ly_do_hoan_tien">Lý do hoàn tiền</label>
                            <textarea name="ly_do" 
                                      id="ly_do_hoan_tien" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Nhập lý do hoàn tiền..." 
                                      required><?= htmlspecialchars($_POST['ly_do'] ?? 'Hoàn tiền theo yêu cầu') ?></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn" style="background: #28a745; color: white;">
                                💰 Hoàn tiền tự động
                            </button>
                            <small style="display: block; margin-top: 5px; color: #666;">
                                Số tiền sẽ được hoàn: <strong><?= number_format($thanhToan['so_tien'], 0, ',', '.') ?> đ</strong>
                            </small>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php } ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= isset($dangKy) ? 'Cập nhật' : 'Thêm mới' ?>
                </button>
                <a href="?act=admin-list-dang-ky" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>
