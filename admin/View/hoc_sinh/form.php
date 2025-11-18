<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($hocSinh) ? 'Sửa học sinh' : 'Thêm học sinh' ?> - Admin</title>
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
                <span><?= isset($hocSinh) ? 'Sửa học sinh' : 'Thêm học sinh mới' ?></span>
                <a href="?act=admin-dashboard" class="btn" style="background: #6c757d; color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px;">🏠 Trang chủ</a>
            </div>
        </h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" 
              action="?act=<?= isset($hocSinh) ? 'admin-update-hoc-sinh' : 'admin-save-hoc-sinh' ?>">
            
            <?php if (isset($hocSinh)): ?>
                <input type="hidden" name="id" value="<?= $hocSinh['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="ho_ten" class="required">Họ tên</label>
                <input type="text" 
                       name="ho_ten" 
                       id="ho_ten" 
                       class="form-control" 
                       value="<?= htmlspecialchars($hocSinh['ho_ten'] ?? '') ?>" 
                       maxlength="200"
                       placeholder="Nhập họ tên học sinh">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           class="form-control" 
                           value="<?= htmlspecialchars($hocSinh['email'] ?? '') ?>" 
                           maxlength="200"
                           placeholder="example@email.com">
                </div>

                <div class="form-group">
                    <label for="so_dien_thoai">Số điện thoại</label>
                    <input type="text" 
                           name="so_dien_thoai" 
                           id="so_dien_thoai" 
                           class="form-control" 
                           value="<?= htmlspecialchars($hocSinh['so_dien_thoai'] ?? '') ?>" 
                           maxlength="20"
                           placeholder="0123456789">
                </div>
            </div>

            <div class="form-group">
                <label for="mat_khau" class="<?= !isset($hocSinh) ? 'required' : '' ?>">Mật khẩu</label>
                <input type="password" 
                       name="mat_khau" 
                       id="mat_khau" 
                       class="form-control" 
                       placeholder="<?= isset($hocSinh) ? 'Để trống nếu không đổi mật khẩu' : 'Nhập mật khẩu' ?>">
                <?php if (isset($hocSinh)): ?>
                    <div class="form-help">Để trống nếu không muốn thay đổi mật khẩu</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="dia_chi">Địa chỉ</label>
                <textarea name="dia_chi" 
                          id="dia_chi" 
                          class="form-control" 
                          rows="3"
                          placeholder="Nhập địa chỉ"><?= htmlspecialchars($hocSinh['dia_chi'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="trang_thai">Trạng thái</label>
                <select name="trang_thai" id="trang_thai" class="form-control">
                    <option value="1" <?= (!isset($hocSinh) || $hocSinh['trang_thai'] == 1) ? 'selected' : '' ?>>
                        Hoạt động
                    </option>
                    <option value="0" <?= (isset($hocSinh) && $hocSinh['trang_thai'] == 0) ? 'selected' : '' ?>>
                        Khóa
                    </option>
                </select>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <?= isset($hocSinh) ? 'Cập nhật' : 'Thêm mới' ?>
                </button>
                <a href="?act=admin-list-hoc-sinh" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    
    <script src="admin/View/js/validation.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isEditMode = <?= isset($hocSinh) ? 'true' : 'false' ?>;
            
            const validationRules = {
                ho_ten: {
                    required: true,
                    label: 'Họ tên',
                    requiredMessage: 'Vui lòng nhập họ tên học sinh',
                    minLength: 2,
                    minLengthMessage: 'Họ tên phải có ít nhất 2 ký tự',
                    maxLength: 200
                },
                email: {
                    required: true,
                    label: 'Email',
                    requiredMessage: 'Vui lòng nhập email',
                    email: true,
                    emailMessage: 'Email không hợp lệ',
                    maxLength: 200
                },
                so_dien_thoai: {
                    required: false,
                    label: 'Số điện thoại',
                    phone: true,
                    phoneMessage: 'Số điện thoại không hợp lệ (định dạng: 0xxxxxxxxx hoặc +84xxxxxxxxx)'
                },
                mat_khau: {
                    required: !isEditMode,
                    label: 'Mật khẩu',
                    requiredMessage: 'Vui lòng nhập mật khẩu',
                    minLength: isEditMode ? 0 : 6,
                    minLengthMessage: 'Mật khẩu phải có ít nhất 6 ký tự',
                    custom: function(value) {
                        if (isEditMode && !value) {
                            return true; // Optional when editing
                        }
                        if (!isEditMode && value.length < 6) {
                            return 'Mật khẩu phải có ít nhất 6 ký tự';
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

