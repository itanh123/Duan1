<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản</title>
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
        
        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            padding: 40px;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .register-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .register-header p {
            color: #6c757d;
            font-size: 14px;
        }
        
        .register-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
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
            font-size: 14px;
        }
        
        .form-group label.required::after {
            content: ' *';
            color: #dc3545;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-control.error {
            border-color: #dc3545;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
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
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .register-footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .register-footer a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .register-footer a:hover {
            text-decoration: underline;
        }
        
        .password-strength {
            font-size: 12px;
            margin-top: 5px;
            color: #6c757d;
        }
        
        .password-strength.weak {
            color: #dc3545;
        }
        
        .password-strength.medium {
            color: #ffc107;
        }
        
        .password-strength.strong {
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="register-icon">👤</div>
            <h1>Đăng ký tài khoản</h1>
            <p>Tạo tài khoản học sinh mới</p>
        </div>

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

        <form method="POST" action="?act=client-process-register" id="registerForm">
            <div class="form-group">
                <label for="ho_ten" class="required">Họ và tên</label>
                <input type="text" 
                       id="ho_ten" 
                       name="ho_ten" 
                       class="form-control" 
                       placeholder="Nhập họ và tên của bạn"
                       value="<?= htmlspecialchars($_POST['ho_ten'] ?? '') ?>"
                       required 
                       autofocus>
            </div>

            <div class="form-group">
                <label for="email" class="required">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       placeholder="Nhập email của bạn"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       required>
                <div class="error-message" id="email-error"></div>
            </div>

            <div class="form-group">
                <label for="so_dien_thoai">Số điện thoại</label>
                <input type="tel" 
                       id="so_dien_thoai" 
                       name="so_dien_thoai" 
                       class="form-control" 
                       placeholder="Nhập số điện thoại (tùy chọn)"
                       value="<?= htmlspecialchars($_POST['so_dien_thoai'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="dia_chi">Địa chỉ</label>
                <input type="text" 
                       id="dia_chi" 
                       name="dia_chi" 
                       class="form-control" 
                       placeholder="Nhập địa chỉ (tùy chọn)"
                       value="<?= htmlspecialchars($_POST['dia_chi'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="mat_khau" class="required">Mật khẩu</label>
                <input type="password" 
                       id="mat_khau" 
                       name="mat_khau" 
                       class="form-control" 
                       placeholder="Nhập mật khẩu (tối thiểu 6 ký tự)"
                       required
                       minlength="6">
                <div class="password-strength" id="password-strength"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password" class="required">Xác nhận mật khẩu</label>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       class="form-control" 
                       placeholder="Nhập lại mật khẩu"
                       required>
                <div class="error-message" id="confirm-password-error"></div>
            </div>

            <button type="submit" class="btn btn-primary">Đăng ký</button>
        </form>

        <div class="register-footer">
            <p>Đã có tài khoản? <a href="?act=client-login">Đăng nhập ngay</a></p>
            <p style="margin-top: 10px;"><a href="index.php">← Về trang chủ</a></p>
        </div>
    </div>

    <script>
        // Kiểm tra mật khẩu khớp
        const password = document.getElementById('mat_khau');
        const confirmPassword = document.getElementById('confirm_password');
        const confirmPasswordError = document.getElementById('confirm-password-error');
        const passwordStrength = document.getElementById('password-strength');

        function checkPasswordMatch() {
            if (confirmPassword.value && password.value !== confirmPassword.value) {
                confirmPassword.classList.add('error');
                confirmPasswordError.textContent = 'Mật khẩu không khớp!';
                return false;
            } else {
                confirmPassword.classList.remove('error');
                confirmPasswordError.textContent = '';
                return true;
            }
        }

        function checkPasswordStrength() {
            const pwd = password.value;
            if (pwd.length === 0) {
                passwordStrength.textContent = '';
                return;
            }
            
            let strength = 0;
            if (pwd.length >= 6) strength++;
            if (pwd.length >= 8) strength++;
            if (/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength++;
            if (/\d/.test(pwd)) strength++;
            if (/[^a-zA-Z\d]/.test(pwd)) strength++;

            if (strength <= 2) {
                passwordStrength.textContent = 'Mật khẩu yếu';
                passwordStrength.className = 'password-strength weak';
            } else if (strength <= 3) {
                passwordStrength.textContent = 'Mật khẩu trung bình';
                passwordStrength.className = 'password-strength medium';
            } else {
                passwordStrength.textContent = 'Mật khẩu mạnh';
                passwordStrength.className = 'password-strength strong';
            }
        }

        password.addEventListener('input', checkPasswordStrength);
        confirmPassword.addEventListener('input', checkPasswordMatch);
        password.addEventListener('input', checkPasswordMatch);

        // Validate form trước khi submit
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            if (!checkPasswordMatch()) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>

