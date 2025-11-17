<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($phongHoc) ? 'Sửa phòng học' : 'Thêm phòng học' ?> - Admin</title>
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
            <span><?= isset($phongHoc) ? 'Sửa phòng học' : 'Thêm phòng học mới' ?></span>
            <a href="?act=admin-list-phong-hoc" class="btn btn-secondary">← Quay lại</a>
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

        <form method="POST" 
              action="?act=<?= isset($phongHoc) ? 'admin-update-phong-hoc' : 'admin-save-phong-hoc' ?>">
            
            <?php if (isset($phongHoc)): ?>
                <input type="hidden" name="id" value="<?= $phongHoc['id'] ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="ten_phong" class="required">Tên phòng học</label>
                    <input type="text" 
                           name="ten_phong" 
                           id="ten_phong" 
                           class="form-control" 
                           value="<?= htmlspecialchars($phongHoc['ten_phong'] ?? '') ?>" 
                           maxlength="50"
                           placeholder="Ví dụ: P101, P203...">
                </div>

                <div class="form-group">
                    <label for="suc_chua" class="required">Sức chứa</label>
                    <input type="number" 
                           name="suc_chua" 
                           id="suc_chua" 
                           class="form-control" 
                           value="<?= $phongHoc['suc_chua'] ?? 30 ?>" 
                           min="1"
                           max="1000"
                           placeholder="Số lượng người">
                </div>
            </div>

            <div class="form-group">
                <label for="mo_ta">Mô tả</label>
                <textarea name="mo_ta" 
                          id="mo_ta" 
                          class="form-control" 
                          rows="4"
                          maxlength="255"
                          placeholder="Nhập mô tả phòng học (tùy chọn)"><?= htmlspecialchars($phongHoc['mo_ta'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="trang_thai" class="required">Trạng thái</label>
                <select name="trang_thai" id="trang_thai" class="form-control">
                    <option value="Sử dụng" <?= (!isset($phongHoc) || $phongHoc['trang_thai'] == 'Sử dụng') ? 'selected' : '' ?>>
                        Sử dụng
                    </option>
                    <option value="Bảo trì" <?= (isset($phongHoc) && $phongHoc['trang_thai'] == 'Bảo trì') ? 'selected' : '' ?>>
                        Bảo trì
                    </option>
                    <option value="Khóa" <?= (isset($phongHoc) && $phongHoc['trang_thai'] == 'Khóa') ? 'selected' : '' ?>>
                        Khóa
                    </option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= isset($phongHoc) ? 'Cập nhật' : 'Thêm mới' ?>
                </button>
                <a href="?act=admin-list-phong-hoc" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    
    <script src="admin/View/js/validation.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const validationRules = {
                ten_phong: {
                    required: true,
                    label: 'Tên phòng học',
                    requiredMessage: 'Vui lòng nhập tên phòng học',
                    minLength: 2,
                    minLengthMessage: 'Tên phòng học phải có ít nhất 2 ký tự',
                    maxLength: 50
                },
                suc_chua: {
                    required: true,
                    label: 'Sức chứa',
                    requiredMessage: 'Vui lòng nhập sức chứa',
                    min: 1,
                    max: 1000,
                    rangeMessage: 'Sức chứa phải từ 1 đến 1000 người',
                    custom: function(value) {
                        const num = parseInt(value);
                        if (isNaN(num) || num < 1 || num > 1000) {
                            return 'Sức chứa phải là số từ 1 đến 1000';
                        }
                        return true;
                    }
                },
                trang_thai: {
                    required: true,
                    label: 'Trạng thái',
                    requiredMessage: 'Vui lòng chọn trạng thái',
                    custom: function(value) {
                        const validStatuses = ['Sử dụng', 'Bảo trì', 'Khóa'];
                        if (!validStatuses.includes(value)) {
                            return 'Trạng thái không hợp lệ';
                        }
                        return true;
                    }
                }
            };
            
            FormValidator.init('form', validationRules);
        });
    </script>
</body>
</html>

