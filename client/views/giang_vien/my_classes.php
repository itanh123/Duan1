<?php
// views/giang_vien/my_classes.php
// Biến có sẵn: $lopHocs (danh sách lớp học mà giảng viên đang dạy)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch học của tôi - Trang bán khóa học lập trình</title>
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
            background: #fafafa;
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
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .empty-state p {
            font-size: 16px;
            color: var(--muted);
            margin-bottom: 20px;
        }

        .class-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 24px;
            overflow: hidden;
            transition: .2s;
        }

        .class-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .class-header {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .class-header h2 {
            font-size: 22px;
            margin-bottom: 8px;
        }

        .class-header .course-name {
            font-size: 14px;
            opacity: 0.9;
        }

        .class-body {
            padding: 20px;
        }

        .class-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item strong {
            color: var(--text);
            min-width: 100px;
        }

        .info-item span {
            color: var(--muted);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .status-warning {
            background: #fff3cd;
            color: #856404;
        }

        .schedule-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .schedule-section h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--text);
        }

        .schedule-list {
            display: grid;
            gap: 12px;
        }

        .schedule-item {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .schedule-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .schedule-item-header strong {
            font-size: 16px;
            color: var(--text);
        }

        .schedule-item-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            font-size: 14px;
            color: var(--muted);
        }

        .schedule-item-details span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .no-schedule {
            padding: 20px;
            text-align: center;
            color: var(--muted);
            background: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-wrap">
                <div class="logo">
                    <a href="?act=client-khoa-hoc">
                        <img src="./uploads/logo.png" alt="Logo">
                    </a>
                </div>
                <nav>
                    <ul>
                        <?php if (isset($_SESSION['giang_vien_id'])): ?>
                            <li><a href="?act=giang-vien-dashboard" style="color: var(--primary);">Dashboard</a></li>
                            <li><a href="?act=giang-vien-lop-hoc" style="color: var(--primary);">Lịch học của tôi</a></li>
                            <li><a href="?act=giang-vien-list-hoc-sinh" style="color: var(--primary);">Danh sách học sinh</a></li>
                            <li><a href="?act=giang-vien-profile" style="color: var(--primary);">👤 Thông tin cá nhân</a></li>
                            <li style="color: var(--primary); font-weight: 600;"><?= htmlspecialchars($_SESSION['giang_vien_ho_ten'] ?? '') ?></li>
                            <li><a href="?act=giang-vien-logout" style="color: #dc3545;">Đăng xuất</a></li>
                        <?php else: ?>
                            <li><a href="?act=giang-vien-login" style="color: var(--primary);">Đăng nhập</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">
            <i class="bi bi-calendar-week"></i> Lịch học của tôi
        </h1>

        <?php
        $filter_ngay = $_GET['filter_ngay'] ?? '';
        ?>
        
        <!-- Bộ lọc -->
        <div class="filter-section" style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
            <form method="GET" action="">
                <input type="hidden" name="act" value="giang-vien-lop-hoc">
                <div style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <label for="filter_ngay" class="form-label">Lọc theo ngày</label>
                        <input type="date" 
                               class="form-control" 
                               id="filter_ngay" 
                               name="filter_ngay" 
                               value="<?= htmlspecialchars($filter_ngay) ?>">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel"></i> Lọc
                        </button>
                        <?php if (!empty($filter_ngay)): ?>
                            <a href="?act=giang-vien-lop-hoc" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Xóa bộ lọc
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($caHocs)): ?>
            <div class="empty-state">
                <p><?= !empty($filter_ngay) ? 'Không có ca học nào vào ngày đã chọn.' : 'Bạn chưa có ca học nào.' ?></p>
            </div>
        <?php else: ?>
            <?php foreach ($caHocs as $ca): ?>
                <div class="class-card" style="background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 20px; padding: 24px;">
                    <div class="class-header" style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 2px solid #f0f0f0;">
                        <div>
                            <h2 style="font-size: 20px; font-weight: 700; color: var(--text); margin-bottom: 8px;"><?= htmlspecialchars($ca['ten_khoa_hoc']) ?></h2>
                            <div style="display: flex; gap: 15px; flex-wrap: wrap; font-size: 14px; color: var(--muted);">
                                <span><i class="bi bi-book"></i> <?= htmlspecialchars($ca['ten_lop']) ?></span>
                                <?php if (!empty($ca['ngay_hoc'])): ?>
                                    <span><i class="bi bi-calendar-date"></i> <?= date('d/m/Y', strtotime($ca['ngay_hoc'])) ?></span>
                                <?php endif; ?>
                                <span><i class="bi bi-calendar-week"></i> <?= htmlspecialchars(tinhThuTuNgayHoc($ca['ngay_hoc'] ?? null, $ca['thu_trong_tuan'] ?? null)) ?></span>
                            </div>
                        </div>
                        <?php 
                        $trangThai = $ca['trang_thai_lop'] ?? 'Chưa học';
                        $trangThaiClass = 'status-warning';
                        if ($trangThai == 'Kết thúc') {
                            $trangThaiClass = 'status-inactive';
                        } elseif ($trangThai == 'Đang học') {
                            $trangThaiClass = 'status-active';
                        }
                        ?>
                        <span class="status-badge <?= $trangThaiClass ?>" style="display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                            <?= htmlspecialchars($trangThai) ?>
                        </span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <strong style="font-size: 12px; color: var(--muted); text-transform: uppercase;">Ca học</strong>
                            <span style="font-size: 14px; color: var(--text); font-weight: 600;"><?= htmlspecialchars($ca['ten_ca'] ?? 'Chưa có') ?></span>
                        </div>
                        <?php if (!empty($ca['gio_bat_dau']) && !empty($ca['gio_ket_thuc'])): ?>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <strong style="font-size: 12px; color: var(--muted); text-transform: uppercase;">Giờ học</strong>
                                <span style="font-size: 14px; color: var(--text); font-weight: 600;"><?= htmlspecialchars($ca['gio_bat_dau']) ?> - <?= htmlspecialchars($ca['gio_ket_thuc']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($ca['ten_phong'])): ?>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <strong style="font-size: 12px; color: var(--muted); text-transform: uppercase;">Phòng học</strong>
                                <span style="font-size: 14px; color: var(--text); font-weight: 600;"><?= htmlspecialchars($ca['ten_phong']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($ca['ngay_bat_dau']) && !empty($ca['ngay_ket_thuc'])): ?>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <strong style="font-size: 12px; color: var(--muted); text-transform: uppercase;">Thời gian lớp</strong>
                                <span style="font-size: 14px; color: var(--text); font-weight: 600;"><?= date('d/m/Y', strtotime($ca['ngay_bat_dau'])) ?> - <?= date('d/m/Y', strtotime($ca['ngay_ket_thuc'])) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>

