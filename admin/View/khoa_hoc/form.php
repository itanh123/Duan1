<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($khoaHoc) ? 'Sửa khóa học' : 'Thêm khóa học' ?> - Admin</title>
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
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .img-preview {
            max-width: 300px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            display: block;
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
                <span><?= isset($khoaHoc) ? 'Sửa khóa học' : 'Thêm khóa học mới' ?></span>
                <a href="?act=admin-dashboard" class="btn" style="background: #6c757d; color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px;">🏠 Trang chủ</a>
            </div>
        </h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" 
              action="?act=<?= isset($khoaHoc) ? 'admin-update-khoa-hoc' : 'admin-save-khoa-hoc' ?>" 
              enctype="multipart/form-data">
            
            <?php if (isset($khoaHoc)): ?>
                <input type="hidden" name="id" value="<?= $khoaHoc['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="id_danh_muc" class="required">Danh mục</label>
                <select name="id_danh_muc" id="id_danh_muc" class="form-control" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($danhMuc as $dm): ?>
                        <option value="<?= $dm['id'] ?>" 
                                <?= (isset($khoaHoc) && $khoaHoc['id_danh_muc'] == $dm['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="ten_khoa_hoc" class="required">Tên khóa học</label>
                <input type="text" 
                       name="ten_khoa_hoc" 
                       id="ten_khoa_hoc" 
                       class="form-control" 
                       value="<?= htmlspecialchars($khoaHoc['ten_khoa_hoc'] ?? '') ?>" 
                       required 
                       maxlength="200">
            </div>

            <div class="form-group">
                <label for="mo_ta">Mô tả</label>
                <textarea name="mo_ta" 
                          id="mo_ta" 
                          class="form-control" 
                          rows="5"><?= htmlspecialchars($khoaHoc['mo_ta'] ?? '') ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gia" class="required">Giá (đồng)</label>
                    <input type="number" 
                           name="gia" 
                           id="gia" 
                           class="form-control" 
                           value="<?= $khoaHoc['gia'] ?? '0' ?>" 
                           required 
                           min="0" 
                           step="1000">
                    <div class="form-help">Nhập giá bằng số (ví dụ: 1000000)</div>
                </div>

                <div class="form-group">
                    <label for="trang_thai">Trạng thái</label>
                    <select name="trang_thai" id="trang_thai" class="form-control">
                        <option value="1" <?= (!isset($khoaHoc) || $khoaHoc['trang_thai'] == 1) ? 'selected' : '' ?>>
                            Hiển thị
                        </option>
                        <option value="0" <?= (isset($khoaHoc) && $khoaHoc['trang_thai'] == 0) ? 'selected' : '' ?>>
                            Ẩn
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="hinh_anh">Hình ảnh</label>
                <input type="file" 
                       name="hinh_anh" 
                       id="hinh_anh" 
                       class="form-control" 
                       accept="image/*"
                       onchange="previewImage(this)">
                <div class="form-help">Định dạng: JPG, PNG, GIF, WEBP. Kích thước tối đa: 5MB</div>
                
                <?php if (isset($khoaHoc) && $khoaHoc['hinh_anh']): ?>
                    <img src="./uploads/<?= htmlspecialchars($khoaHoc['hinh_anh']) ?>" 
                         alt="Hình ảnh hiện tại" 
                         class="img-preview" 
                         id="current-image">
                <?php endif; ?>
                
                <img id="image-preview" class="img-preview" style="display: none;">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <?= isset($khoaHoc) ? 'Cập nhật' : 'Thêm mới' ?>
                </button>
                <a href="?act=admin-list-khoa-hoc" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const currentImage = document.getElementById('current-image');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if (currentImage) {
                        currentImage.style.display = 'none';
                    }
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
                if (currentImage) {
                    currentImage.style.display = 'block';
                }
            }
        }
    </script>
</body>
</html>

