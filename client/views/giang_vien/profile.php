<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - Giảng viên</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #10B981;
            --text: #1F2937;
            --muted: #6b7280;
            --bg: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Inter, Arial, sans-serif;
            color: var(--text);
            background: #f5f5f5;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: #fff;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
        }

        .logo img {
            height: 48px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 24px;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            font-weight: 600;
            color: var(--text);
            transition: .2s;
        }

        nav a:hover {
            color: var(--primary);
        }

        .page-title {
            margin: 30px 0;
            font-size: 32px;
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .profile-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 30px;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
            font-weight: 700;
        }

        .profile-info h2 {
            font-size: 28px;
            margin-bottom: 8px;
            color: var(--text);
        }

        .profile-info .badge {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background: #dbeafe;
            color: #1e40af;
            margin-top: 8px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .info-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }

        .info-card h3 {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-card p {
            font-size: 16px;
            color: var(--text);
            font-weight: 500;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
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

        .empty-value {
            color: var(--muted);
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-wrap">
                <div class="logo">
                    <a href="?act=giang-vien-dashboard">
                        <img src="./uploads/logo.png" alt="Logo">
                    </a>
                </div>
                <nav>
                    <ul>
                        <li><a href="?act=giang-vien-dashboard"><i class="bi bi-house-door"></i> Dashboard</a></li>
                        <li><a href="?act=giang-vien-lop-hoc"><i class="bi bi-calendar-week"></i> Lịch học của tôi</a></li>
                        <li><a href="?act=giang-vien-list-hoc-sinh"><i class="bi bi-people"></i> Danh sách học sinh</a></li>
                        <li><a href="?act=giang-vien-profile"><i class="bi bi-person-circle"></i> Thông tin cá nhân</a></li>
                        <li style="color: var(--primary); font-weight: 600;"><i class="bi bi-person-badge"></i> <?= htmlspecialchars($_SESSION['giang_vien_ho_ten'] ?? '') ?></li>
                        <li><a href="?act=giang-vien-logout" style="color: #dc3545;"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">
            <i class="bi bi-person-circle"></i>
            Thông tin cá nhân
        </h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="bi bi-exclamation-triangle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?= strtoupper(mb_substr($user['ho_ten'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($user['ho_ten'] ?? 'N/A') ?></h2>
                    <span class="badge">Giảng viên</span>
                </div>
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h3>Mã người dùng</h3>
                    <p><?= htmlspecialchars($user['ma_nguoi_dung'] ?? 'N/A') ?></p>
                </div>

                <div class="info-card">
                    <h3>Email</h3>
                    <p><?= htmlspecialchars($user['email'] ?? 'N/A') ?></p>
                </div>

                <div class="info-card">
                    <h3>Số điện thoại</h3>
                    <p><?= !empty($user['so_dien_thoai']) ? htmlspecialchars($user['so_dien_thoai']) : '<span class="empty-value">Chưa cập nhật</span>' ?></p>
                </div>

                <div class="info-card">
                    <h3>Địa chỉ</h3>
                    <p><?= !empty($user['dia_chi']) ? htmlspecialchars($user['dia_chi']) : '<span class="empty-value">Chưa cập nhật</span>' ?></p>
                </div>

                <div class="info-card">
                    <h3>Trạng thái</h3>
                    <p>
                        <span style="color: <?= ($user['trang_thai'] ?? 0) == 1 ? '#28a745' : '#dc3545' ?>; font-weight: 600;">
                            <?= ($user['trang_thai'] ?? 0) == 1 ? 'Hoạt động' : 'Đã khóa' ?>
                        </span>
                    </p>
                </div>

                <div class="info-card">
                    <h3>Ngày tạo tài khoản</h3>
                    <p><?= isset($user['ngay_tao']) ? date('d/m/Y H:i', strtotime($user['ngay_tao'])) : 'N/A' ?></p>
                </div>
            </div>

            <div style="margin-top: 30px; text-align: center;">
                <a href="?act=giang-vien-dashboard" class="btn btn-secondary">Quay lại Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

