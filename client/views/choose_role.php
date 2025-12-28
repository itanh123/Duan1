<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn vai trò đăng nhập</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .choose-role-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            padding: 40px;
        }
        
        .choose-role-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .choose-role-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .choose-role-header p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .user-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .user-info strong {
            color: #333;
            font-size: 16px;
        }
        
        .user-info .email {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .role-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .role-item {
            padding: 20px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        
        .role-item:hover {
            border-color: #667eea;
            background: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .role-item input[type="radio"] {
            display: none;
        }
        
        .role-item input[type="radio"]:checked + .role-content {
            border-color: #667eea;
            background: #667eea;
            color: white;
        }
        
        .role-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .role-icon {
            font-size: 48px;
        }
        
        .role-name {
            font-size: 18px;
            font-weight: 600;
        }
        
        .role-desc {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .form-actions {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }
        
        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="choose-role-container">
        <div class="choose-role-header">
            <h1>Chọn vai trò đăng nhập</h1>
            <p>Bạn có nhiều vai trò, vui lòng chọn vai trò muốn sử dụng</p>
        </div>
        
        <div class="user-info">
            <strong><?= htmlspecialchars($_SESSION['temp_user_ho_ten'] ?? '') ?></strong>
            <div class="email"><?= htmlspecialchars($_SESSION['temp_user_email'] ?? '') ?></div>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="?act=client-process-choose-role">
            <div class="role-list">
                <?php 
                $vaiTroLabels = [
                    'admin' => ['name' => 'Quản trị viên', 'icon' => '👨‍💼', 'desc' => 'Quản lý hệ thống'],
                    'giang_vien' => ['name' => 'Giảng viên', 'icon' => '👨‍🏫', 'desc' => 'Giảng dạy và quản lý lớp học'],
                    'hoc_sinh' => ['name' => 'Học sinh', 'icon' => '👨‍🎓', 'desc' => 'Học tập và đăng ký khóa học']
                ];
                
                foreach ($_SESSION['temp_vai_tro_list'] ?? [] as $vaiTro): 
                    $label = $vaiTroLabels[$vaiTro] ?? ['name' => ucfirst($vaiTro), 'icon' => '👤', 'desc' => ''];
                ?>
                    <label class="role-item">
                        <input type="radio" name="vai_tro" value="<?= $vaiTro ?>" required>
                        <div class="role-content">
                            <div class="role-icon"><?= $label['icon'] ?></div>
                            <div class="role-name"><?= $label['name'] ?></div>
                            <?php if (!empty($label['desc'])): ?>
                                <div class="role-desc"><?= $label['desc'] ?></div>
                            <?php endif; ?>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Đăng nhập</button>
                <a href="?act=client-login" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
    
    <script>
        // Auto-select first role if only one
        const roleItems = document.querySelectorAll('.role-item');
        if (roleItems.length === 1) {
            roleItems[0].querySelector('input[type="radio"]').checked = true;
        }
        
        // Add click handler for role items
        roleItems.forEach(item => {
            item.addEventListener('click', function() {
                this.querySelector('input[type="radio"]').checked = true;
            });
        });
    </script>
</body>
</html>

