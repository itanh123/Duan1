<?php
// views/giang_vien/hoc_sinh_trong_lop.php
// Biến có sẵn: $lopInfo, $hocSinh
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Học sinh trong lớp - <?= htmlspecialchars($lopInfo['ten_lop'] ?? '') ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #10B981;
            --accent: #d4a6b6;
            --text: #1F2937;
            --muted: #6b7280;
            --bg: #ffffff;
            --container: 1200px;
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
            max-width: var(--container);
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

        .section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .section-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
            color: #fff;
        }

        .section-header h2 {
            margin: 0;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-body {
            padding: 24px;
        }

        .class-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 24px;
        }

        .class-info h3 {
            margin: 0 0 10px 0;
            color: var(--text);
        }

        .class-info p {
            margin: 5px 0;
            color: var(--muted);
        }

        .student-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
        }

        .student-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            border-left: 4px solid var(--primary);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .student-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .student-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .student-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--text);
            margin: 0;
        }

        .student-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 14px;
            color: var(--muted);
        }

        .student-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .btn-back {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
            transition: .2s;
        }

        .btn-back:hover {
            background: #059669;
            color: white;
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
                        <li><a href="?act=giang-vien-lop-hoc"><i class="bi bi-book"></i> Lớp học của tôi</a></li>
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
        <a href="?act=giang-vien-dashboard" class="btn-back">
            <i class="bi bi-arrow-left"></i> Quay lại Dashboard
        </a>

        <h1 class="page-title">
            <i class="bi bi-people-fill"></i>
            Học sinh trong lớp
        </h1>

        <div class="section">
            <div class="section-header">
                <h2><i class="bi bi-book"></i> Thông tin lớp học</h2>
            </div>
            <div class="section-body">
                <div class="class-info">
                    <h3><?= htmlspecialchars($lopInfo['ten_lop'] ?? '') ?></h3>
                    <p><strong>Khóa học:</strong> <?= htmlspecialchars($lopInfo['ten_khoa_hoc'] ?? '') ?></p>
                    <?php if (!empty($lopInfo['so_luong_toi_da'])): ?>
                        <p><strong>Sức chứa:</strong> <?= htmlspecialchars($lopInfo['so_luong_toi_da']) ?> học sinh</p>
                    <?php endif; ?>
                    <p><strong>Số học sinh đã đăng ký:</strong> <?= count($hocSinh) ?> học sinh</p>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">
                <h2><i class="bi bi-people"></i> Danh sách học sinh (<?= count($hocSinh) ?>)</h2>
            </div>
            <div class="section-body">
                <?php if (empty($hocSinh)): ?>
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <p>Lớp học này chưa có học sinh nào đăng ký.</p>
                    </div>
                <?php else: ?>
                    <div class="student-list">
                        <?php foreach ($hocSinh as $hs): ?>
                            <div class="student-card">
                                <div class="student-header">
                                    <div class="student-avatar">
                                        <?= strtoupper(mb_substr($hs['ho_ten'], 0, 1, 'UTF-8')) ?>
                                    </div>
                                    <h4 class="student-name"><?= htmlspecialchars($hs['ho_ten']) ?></h4>
                                </div>
                                <div class="student-info">
                                    <?php if (!empty($hs['email'])): ?>
                                        <div class="student-info-item">
                                            <i class="bi bi-envelope"></i>
                                            <span><?= htmlspecialchars($hs['email']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($hs['so_dien_thoai'])): ?>
                                        <div class="student-info-item">
                                            <i class="bi bi-telephone"></i>
                                            <span><?= htmlspecialchars($hs['so_dien_thoai']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($hs['dia_chi'])): ?>
                                        <div class="student-info-item">
                                            <i class="bi bi-geo-alt"></i>
                                            <span><?= htmlspecialchars($hs['dia_chi']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="student-info-item">
                                        <i class="bi bi-calendar"></i>
                                        <span>Đăng ký: <?= date('d/m/Y', strtotime($hs['ngay_dang_ky'])) ?></span>
                                    </div>
                                    <div class="student-info-item">
                                        <i class="bi bi-check-circle"></i>
                                        <span class="badge bg-success"><?= htmlspecialchars($hs['trang_thai_dang_ky']) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

