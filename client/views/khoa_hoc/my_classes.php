<?php
// views/khoa_hoc/my_classes.php
// Biến có sẵn: $caHocs (danh sách ca học mà học sinh đã đăng ký)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$filter_ngay = $_GET['filter_ngay'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch học của tôi - Trang bán khóa học lập trình</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #10B981;
            --accent: #d4a6b6;
            --text: #1F2937;
            --muted: #6b7280;
        }

        body {
            font-family: Inter, Arial, sans-serif;
            background: #fafafa;
            color: var(--text);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-title {
            margin: 30px 0;
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
        }

        .filter-section {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .filter-section form {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }

        .filter-section .form-group {
            flex: 1;
            min-width: 200px;
        }

        .ca-hoc-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 24px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .ca-hoc-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .ca-hoc-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f0f0f0;
        }

        .ca-hoc-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }

        .ca-hoc-meta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            font-size: 14px;
            color: var(--muted);
        }

        .ca-hoc-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .ca-hoc-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-item strong {
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-item span {
            font-size: 14px;
            color: var(--text);
            font-weight: 600;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-primary {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
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

        .user-menu {
            position: relative;
        }
        .user-trigger {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            font-weight: 700;
        }
        .user-trigger:hover {
            background: #0ea271;
        }
        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #fff;
            border: 1px solid #eee;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            border-radius: 10px;
            min-width: 210px;
            padding: 6px 0;
            display: none;
            z-index: 1000;
        }
        .user-dropdown li {
            display: block;
        }
        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            color: var(--text);
            font-weight: 600;
        }
        .user-dropdown a:hover {
            background: #f6f6f6;
            color: var(--primary);
        }
        .user-menu:hover .user-dropdown,
        .user-menu:focus-within .user-dropdown {
            display: block;
        }
        .logout-link {
            color: #dc3545;
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
                        <li><a href="index.php">Trang chủ</a></li>
                        <li><a href="?act=client-khoa-hoc">Khóa học</a></li>
                        <li><a href="?act=client-lop-hoc">Lớp học</a></li>
                        <li><a href="?act=client-danh-muc">Danh mục</a></li>
                        <li><a href="?act=client-giang-vien">Giảng viên</a></li>
                        <?php if (isset($_SESSION['client_id']) && (!isset($_SESSION['client_vai_tro']) || $_SESSION['client_vai_tro'] === 'hoc_sinh')): ?>
                            <li class="user-menu">
                                <button type="button" class="user-trigger">
                                    <span><?= htmlspecialchars($_SESSION['client_ho_ten'] ?? '') ?></span>
                                    <span style="font-size: 12px;">▾</span>
                                </button>
                                <ul class="user-dropdown">
                                    <li><a href="?act=client-khoa-hoc-cua-toi"><i class="bi bi-book"></i> Khóa học của tôi</a></li>
                                    <li><a href="?act=client-hoc-sinh-lop-hoc"><i class="bi bi-calendar-week"></i> Lịch học của tôi</a></li>
                                    <li><a href="?act=client-profile"><i class="bi bi-person-circle"></i> Thông tin cá nhân</a></li>
                                    <li><a href="?act=client-logout" class="logout-link"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li><a href="?act=client-login">Đăng nhập</a></li>
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

        <!-- Bộ lọc -->
        <div class="filter-section">
            <form method="GET" action="">
                <input type="hidden" name="act" value="client-hoc-sinh-lop-hoc">
                <div class="form-group">
                    <label for="filter_ngay" class="form-label">Lọc theo ngày</label>
                    <input type="date" 
                           class="form-control" 
                           id="filter_ngay" 
                           name="filter_ngay" 
                           value="<?= htmlspecialchars($filter_ngay) ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>
                    <?php if (!empty($filter_ngay)): ?>
                        <a href="?act=client-hoc-sinh-lop-hoc" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Xóa bộ lọc
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if (empty($caHocs)): ?>
            <div class="empty-state">
                <p><?= !empty($filter_ngay) ? 'Không có ca học nào vào ngày đã chọn.' : 'Bạn chưa có ca học nào.' ?></p>
                <?php if (empty($filter_ngay)): ?>
                    <a href="?act=client-khoa-hoc" class="btn btn-primary">Xem khóa học</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($caHocs as $ca): ?>
                <div class="ca-hoc-card">
                    <div class="ca-hoc-header">
                        <div>
                            <div class="ca-hoc-title"><?= htmlspecialchars($ca['ten_khoa_hoc']) ?></div>
                            <div class="ca-hoc-meta">
                                <span><i class="bi bi-book"></i> <?= htmlspecialchars($ca['ten_lop']) ?></span>
                                <?php if (!empty($ca['ngay_hoc'])): ?>
                                    <span><i class="bi bi-calendar-date"></i> <?= date('d/m/Y', strtotime($ca['ngay_hoc'])) ?></span>
                                <?php endif; ?>
                                <span><i class="bi bi-calendar-week"></i> <?= htmlspecialchars(tinhThuTuNgayHoc($ca['ngay_hoc'] ?? null, $ca['thu_trong_tuan'] ?? null)) ?></span>
                            </div>
                        </div>
                        <span class="badge badge-success"><?= htmlspecialchars($ca['trang_thai_dang_ky'] ?? 'Đã xác nhận') ?></span>
                    </div>
                    <div class="ca-hoc-body">
                        <div class="info-item">
                            <strong>Ca học</strong>
                            <span><?= htmlspecialchars($ca['ten_ca'] ?? 'Chưa có') ?></span>
                        </div>
                        <?php if (!empty($ca['gio_bat_dau']) && !empty($ca['gio_ket_thuc'])): ?>
                            <div class="info-item">
                                <strong>Giờ học</strong>
                                <span><?= htmlspecialchars($ca['gio_bat_dau']) ?> - <?= htmlspecialchars($ca['gio_ket_thuc']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($ca['ten_phong'])): ?>
                            <div class="info-item">
                                <strong>Phòng học</strong>
                                <span><?= htmlspecialchars($ca['ten_phong']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($ca['ten_giang_vien'])): ?>
                            <div class="info-item">
                                <strong>Giảng viên</strong>
                                <span><?= htmlspecialchars($ca['ten_giang_vien']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($ca['ngay_bat_dau']) && !empty($ca['ngay_ket_thuc'])): ?>
                            <div class="info-item">
                                <strong>Thời gian lớp</strong>
                                <span><?= date('d/m/Y', strtotime($ca['ngay_bat_dau'])) ?> - <?= date('d/m/Y', strtotime($ca['ngay_ket_thuc'])) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
