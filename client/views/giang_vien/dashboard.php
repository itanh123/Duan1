<?php
// views/giang_vien/dashboard.php
// Biến có sẵn: $lopHocs (danh sách lớp học mà giảng viên đang dạy), $hocSinhList (danh sách học sinh đã đăng ký)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Giảng viên - Trang bán khóa học lập trình</title>
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

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
            color: #fff;
        }

        .stat-info h3 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }

        .stat-info p {
            color: var(--muted);
            margin: 0;
            font-size: 14px;
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

        .schedule-item {
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 16px;
            border-left: 4px solid var(--primary);
        }

        .schedule-item:last-child {
            margin-bottom: 0;
        }

        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .schedule-header h4 {
            margin: 0;
            color: var(--text);
            font-size: 18px;
        }

        .schedule-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            font-size: 14px;
            color: var(--muted);
        }

        .schedule-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .schedule-detail-item strong {
            color: var(--text);
            min-width: 80px;
        }

        .student-list {
            display: grid;
            gap: 12px;
        }

        .student-item {
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .student-info h5 {
            margin: 0 0 8px 0;
            color: var(--text);
            font-size: 16px;
        }

        .student-info p {
            margin: 4px 0;
            font-size: 14px;
            color: var(--muted);
        }

        .student-meta {
            text-align: right;
        }

        .student-meta .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #d1fae5;
            color: #065f46;
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
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-wrap">
                <div class="logo">
                    <a href="?act=giang-vien-dashboard">
                        <img src="https://websitedemos.net/be-bold-beauty-store-04/wp-content/uploads/sites/1117/2022/08/logo-regular.png" alt="Logo">
                    </a>
                </div>
                <nav>
                    <ul>
                        <li><a href="?act=giang-vien-dashboard"><i class="bi bi-house-door"></i> Dashboard</a></li>
                        <li><a href="?act=giang-vien-lop-hoc"><i class="bi bi-book"></i> Lớp học của tôi</a></li>
                        <li><a href="?act=giang-vien-list-hoc-sinh"><i class="bi bi-people"></i> Danh sách học sinh</a></li>
                        <li style="color: var(--primary); font-weight: 600;"><i class="bi bi-person-badge"></i> <?= htmlspecialchars($_SESSION['giang_vien_ho_ten'] ?? '') ?></li>
                        <li><a href="?act=giang-vien-logout" style="color: #dc3545;"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">
            <i class="bi bi-speedometer2"></i>
            Dashboard Giảng viên
        </h1>

        <!-- Thống kê -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-book"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($lopHocs) ?></h3>
                    <p>Lớp học đang dạy</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?= array_sum(array_map(function($lop) { return count($lop['ca_hoc']); }, $lopHocs)) ?></h3>
                    <p>Ca học trong tuần</p>
                </div>
            </div>
        </div>

        <!-- Lịch dạy -->
        <div class="section">
            <div class="section-header">
                <h2><i class="bi bi-calendar3"></i> Lịch dạy của tôi</h2>
            </div>
            <div class="section-body">
                <?php if (empty($lopHocs)): ?>
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <p>Bạn chưa có lịch dạy nào.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($lopHocs as $lop): ?>
                        <?php if (!empty($lop['ca_hoc'])): ?>
                            <?php foreach ($lop['ca_hoc'] as $ca): ?>
                                <div class="schedule-item">
                                    <div class="schedule-header">
                                        <h4><?= htmlspecialchars($lop['ten_lop']) ?></h4>
                                        <span class="badge bg-primary"><?= htmlspecialchars($lop['ten_khoa_hoc']) ?></span>
                                    </div>
                                    <div class="schedule-details">
                                        <div class="schedule-detail-item">
                                            <strong><i class="bi bi-calendar-week"></i> Thứ:</strong>
                                            <span><?= htmlspecialchars($ca['thu_trong_tuan']) ?></span>
                                        </div>
                                        <div class="schedule-detail-item">
                                            <strong><i class="bi bi-clock"></i> Ca học:</strong>
                                            <span><?= htmlspecialchars($ca['ten_ca'] ?? 'Chưa có') ?></span>
                                        </div>
                                        <?php if (!empty($ca['gio_bat_dau']) && !empty($ca['gio_ket_thuc'])): ?>
                                            <div class="schedule-detail-item">
                                                <strong><i class="bi bi-clock-history"></i> Giờ:</strong>
                                                <span><?= htmlspecialchars($ca['gio_bat_dau']) ?> - <?= htmlspecialchars($ca['gio_ket_thuc']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($ca['ten_phong'])): ?>
                                            <div class="schedule-detail-item">
                                                <strong><i class="bi bi-door-open"></i> Phòng:</strong>
                                                <span><?= htmlspecialchars($ca['ten_phong']) ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($ca['suc_chua'])): ?>
                                            <div class="schedule-detail-item">
                                                <strong><i class="bi bi-people"></i> Sức chứa:</strong>
                                                <span><?= htmlspecialchars($ca['suc_chua']) ?> người</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

