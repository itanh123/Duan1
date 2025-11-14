<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($lopHoc) ? 'Sửa lớp học' : 'Thêm lớp học' ?> - Admin</title>
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
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span><?= isset($lopHoc) ? 'Sửa lớp học' : 'Thêm lớp học mới' ?></span>
                <a href="?act=admin-dashboard" class="btn" style="background: #6c757d; color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px;">🏠 Trang chủ</a>
            </div>
        </h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" 
              action="?act=<?= isset($lopHoc) ? 'admin-update-lop-hoc' : 'admin-save-lop-hoc' ?>">
            
            <?php if (isset($lopHoc)): ?>
                <input type="hidden" name="id" value="<?= $lopHoc['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="id_khoa_hoc" class="required">Khóa học</label>
                <select name="id_khoa_hoc" id="id_khoa_hoc" class="form-control" required>
                    <option value="">-- Chọn khóa học --</option>
                    <?php foreach ($khoaHocList as $kh): ?>
                        <option value="<?= $kh['id'] ?>" 
                                <?= (isset($lopHoc) && $lopHoc['id_khoa_hoc'] == $kh['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kh['ten_khoa_hoc']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="ten_lop" class="required">Tên lớp</label>
                <input type="text" 
                       name="ten_lop" 
                       id="ten_lop" 
                       class="form-control" 
                       value="<?= htmlspecialchars($lopHoc['ten_lop'] ?? '') ?>" 
                       required 
                       maxlength="200"
                       placeholder="Nhập tên lớp học">
            </div>

            <div class="form-group">
                <label for="so_luong_toi_da">Số lượng tối đa</label>
                <input type="number" 
                       name="so_luong_toi_da" 
                       id="so_luong_toi_da" 
                       class="form-control" 
                       value="<?= $lopHoc['so_luong_toi_da'] ?? '' ?>" 
                       min="1"
                       placeholder="Để trống nếu không giới hạn">
            </div>

            <div class="form-group">
                <label for="mo_ta">Mô tả</label>
                <textarea name="mo_ta" 
                          id="mo_ta" 
                          class="form-control" 
                          rows="4"
                          placeholder="Nhập mô tả lớp học (tùy chọn)"><?= htmlspecialchars($lopHoc['mo_ta'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="trang_thai" class="required">Trạng thái</label>
                <select name="trang_thai" id="trang_thai" class="form-control" required>
                    <option value="Chưa khai giảng" <?= (!isset($lopHoc) || $lopHoc['trang_thai'] == 'Chưa khai giảng') ? 'selected' : '' ?>>
                        Chưa khai giảng
                    </option>
                    <option value="Đang học" <?= (isset($lopHoc) && $lopHoc['trang_thai'] == 'Đang học') ? 'selected' : '' ?>>
                        Đang học
                    </option>
                    <option value="Kết thúc" <?= (isset($lopHoc) && $lopHoc['trang_thai'] == 'Kết thúc') ? 'selected' : '' ?>>
                        Kết thúc
                    </option>
                </select>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <?= isset($lopHoc) ? 'Cập nhật' : 'Thêm mới' ?>
                </button>
                <a href="?act=admin-list-lop-hoc" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>

