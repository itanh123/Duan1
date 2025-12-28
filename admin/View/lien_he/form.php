<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($lienHe) ? 'Sửa liên hệ' : 'Thêm liên hệ' ?> - Admin</title>
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
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .form-help {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span><?= isset($lienHe) ? 'Sửa liên hệ' : 'Thêm liên hệ mới' ?></span>
                <a href="?act=admin-dashboard" class="btn" style="background: #6c757d; color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px;">🏠 Trang chủ</a>
            </div>
        </h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" 
              action="?act=<?= isset($lienHe) ? 'admin-update-lien-he' : 'admin-save-lien-he' ?>">
            
            <?php if (isset($lienHe)): ?>
                <input type="hidden" name="id" value="<?= $lienHe['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="ten" class="required">Tên liên hệ</label>
                <input type="text" 
                       name="ten" 
                       id="ten" 
                       class="form-control" 
                       value="<?= htmlspecialchars($lienHe['ten'] ?? '') ?>" 
                       required 
                       maxlength="200"
                       placeholder="Ví dụ: Zalo, Messenger, Email, Điện thoại">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="loai" class="required">Loại liên hệ</label>
                    <select name="loai" id="loai" class="form-control" required>
                        <option value="">-- Chọn loại --</option>
                        <option value="zalo" <?= (isset($lienHe) && $lienHe['loai'] == 'zalo') ? 'selected' : '' ?>>Zalo</option>
                        <option value="messenger" <?= (isset($lienHe) && $lienHe['loai'] == 'messenger') ? 'selected' : '' ?>>Messenger</option>
                        <option value="phone" <?= (isset($lienHe) && $lienHe['loai'] == 'phone') ? 'selected' : '' ?>>Điện thoại</option>
                        <option value="email" <?= (isset($lienHe) && $lienHe['loai'] == 'email') ? 'selected' : '' ?>>Email</option>
                        <option value="facebook" <?= (isset($lienHe) && $lienHe['loai'] == 'facebook') ? 'selected' : '' ?>>Facebook</option>
                        <option value="other" <?= (isset($lienHe) && $lienHe['loai'] == 'other') ? 'selected' : '' ?>>Khác</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="thu_tu">Thứ tự hiển thị</label>
                    <input type="number" 
                           name="thu_tu" 
                           id="thu_tu" 
                           class="form-control" 
                           value="<?= $lienHe['thu_tu'] ?? 0 ?>" 
                           min="0"
                           placeholder="0">
                    <div class="form-help">Số nhỏ hơn sẽ hiển thị trước</div>
                </div>
            </div>

            <div class="form-group">
                <label for="gia_tri" class="required">Giá trị liên hệ</label>
                <input type="text" 
                       name="gia_tri" 
                       id="gia_tri" 
                       class="form-control" 
                       value="<?= htmlspecialchars($lienHe['gia_tri'] ?? '') ?>" 
                       required
                       placeholder="Ví dụ: https://zalo.me/868729743 hoặc 0868729743 hoặc email@example.com">
                <div class="form-help">Nhập số điện thoại, link, email hoặc giá trị liên hệ khác</div>
            </div>

            <div class="form-group">
                <label for="icon">Icon/Emoji</label>
                <input type="text" 
                       name="icon" 
                       id="icon" 
                       class="form-control" 
                       value="<?= htmlspecialchars($lienHe['icon'] ?? '') ?>" 
                       maxlength="100"
                       placeholder="Ví dụ: 📱, 💬, 📞, ✉️">
                <div class="form-help">Nhập emoji hoặc icon (tùy chọn)</div>
            </div>

            <div class="form-group">
                <label for="mo_ta">Mô tả</label>
                <textarea name="mo_ta" 
                          id="mo_ta" 
                          class="form-control" 
                          rows="3"
                          placeholder="Nhập mô tả (tùy chọn)"><?= htmlspecialchars($lienHe['mo_ta'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="trang_thai">Trạng thái</label>
                <select name="trang_thai" id="trang_thai" class="form-control">
                    <option value="1" <?= (!isset($lienHe) || $lienHe['trang_thai'] == 1) ? 'selected' : '' ?>>
                        Hiển thị
                    </option>
                    <option value="0" <?= (isset($lienHe) && $lienHe['trang_thai'] == 0) ? 'selected' : '' ?>>
                        Ẩn
                    </option>
                </select>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <?= isset($lienHe) ? 'Cập nhật' : 'Thêm mới' ?>
                </button>
                <a href="?act=admin-list-lien-he" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>

