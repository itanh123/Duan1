<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Bình Luận - Admin</title>
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
            min-height: 120px;
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
        
        .stars {
            color: #ffc107;
            font-size: 20px;
        }
        
        .star-rating {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        
        .star-rating input[type="radio"] {
            display: none;
        }
        
        .star-rating label {
            cursor: pointer;
            font-size: 24px;
            color: #ddd;
            transition: color 0.2s;
        }
        
        .star-rating input[type="radio"]:checked ~ label,
        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <span>Sửa Bình Luận</span>
            <a href="?act=admin-list-binh-luan" class="btn btn-secondary">← Quay lại</a>
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

        <form method="POST" action="?act=admin-update-binh-luan">
            <input type="hidden" name="id" value="<?= $binhLuan['id'] ?>">

            <!-- Thông tin bình luận -->
            <div class="form-section">
                <h3>Thông tin bình luận</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Khóa học</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= htmlspecialchars($binhLuan['ten_khoa_hoc'] ?? '') ?>" 
                               readonly>
                    </div>

                    <div class="form-group">
                        <label>Học sinh</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= htmlspecialchars($binhLuan['ten_hoc_sinh'] ?? '') ?>" 
                               readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label for="noi_dung" class="required">Nội dung bình luận</label>
                    <textarea name="noi_dung" 
                              id="noi_dung" 
                              class="form-control" 
                              required><?= htmlspecialchars($binhLuan['noi_dung'] ?? '') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="danh_gia">Đánh giá (sao)</label>
                        <select name="danh_gia" id="danh_gia" class="form-control">
                            <option value="">Chưa đánh giá</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= (isset($binhLuan['danh_gia']) && $binhLuan['danh_gia'] == $i) ? 'selected' : '' ?>>
                                    <?= $i ?> sao
                                </option>
                            <?php endfor; ?>
                        </select>
                        <?php if (isset($binhLuan['danh_gia']) && $binhLuan['danh_gia']): ?>
                            <div style="margin-top: 10px;">
                                <span class="stars">
                                    <?= str_repeat('★', $binhLuan['danh_gia']) ?><?= str_repeat('☆', 5 - $binhLuan['danh_gia']) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Ngày tạo</label>
                        <input type="text" 
                               class="form-control" 
                               value="<?= isset($binhLuan['ngay_tao']) ? date('d/m/Y H:i:s', strtotime($binhLuan['ngay_tao'])) : 'N/A' ?>" 
                               readonly>
                    </div>
                </div>

                <div class="form-group">
                    <label for="trang_thai" class="required">Trạng thái</label>
                    <select name="trang_thai" id="trang_thai" class="form-control" required>
                        <option value="Hiển thị" <?= (isset($binhLuan) && $binhLuan['trang_thai'] == 'Hiển thị') ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="Ẩn" <?= (isset($binhLuan) && $binhLuan['trang_thai'] == 'Ẩn') ? 'selected' : '' ?>>Ẩn</option>
                        <option value="Đã xóa" <?= (isset($binhLuan) && $binhLuan['trang_thai'] == 'Đã xóa') ? 'selected' : '' ?>>Đã xóa</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <a href="?act=admin-list-binh-luan" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>

