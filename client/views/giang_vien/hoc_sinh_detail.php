<?php
// views/giang_vien/hoc_sinh_detail.php
// Biến có sẵn: $hocSinh, $lopHocs
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Học sinh - <?= htmlspecialchars($hocSinh['ho_ten']) ?></title>
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

        .info-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 24px;
            margin-bottom: 30px;
        }

        .info-card h3 {
            margin-bottom: 20px;
            color: var(--text);
            font-size: 20px;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-item strong {
            color: var(--text);
            font-size: 14px;
        }

        .info-item span {
            color: var(--muted);
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
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

        .class-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .class-header {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .class-header h3 {
            margin: 0 0 5px 0;
            font-size: 20px;
        }

        .class-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .class-body {
            padding: 20px;
        }

        .class-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .schedule-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .schedule-section h4 {
            margin-bottom: 15px;
            color: var(--text);
            font-size: 18px;
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
            margin-bottom: 10px;
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
        <a href="?act=giang-vien-list-hoc-sinh" class="btn-back">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>

        <h1 class="page-title">
            <i class="bi bi-person-circle"></i>
            Chi tiết học sinh: <?= htmlspecialchars($hocSinh['ho_ten']) ?>
        </h1>

        <!-- Thông tin học sinh -->
        <div class="info-card">
            <h3>Thông tin học sinh</h3>
            <div class="info-grid">
                <div class="info-item">
                    <strong>Họ tên:</strong>
                    <span><?= htmlspecialchars($hocSinh['ho_ten']) ?></span>
                </div>
                <div class="info-item">
                    <strong>Email:</strong>
                    <span><?= htmlspecialchars($hocSinh['email']) ?></span>
                </div>
                <div class="info-item">
                    <strong>Số điện thoại:</strong>
                    <span><?= htmlspecialchars($hocSinh['so_dien_thoai'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <strong>Địa chỉ:</strong>
                    <span><?= htmlspecialchars($hocSinh['dia_chi'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <strong>Trạng thái:</strong>
                    <span class="status-badge <?= $hocSinh['trang_thai'] == 1 ? 'status-active' : 'status-inactive' ?>">
                        <?= $hocSinh['trang_thai'] == 1 ? 'Hoạt động' : 'Khóa' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Danh sách lớp học -->
        <?php if (empty($lopHocs)): ?>
            <div class="info-card">
                <div class="empty-state">
                    <i class="bi bi-book-x"></i>
                    <p>Học sinh này chưa đăng ký lớp học nào.</p>
                </div>
            </div>
        <?php else: ?>
            <h2 style="margin-bottom: 20px; color: var(--text);">Danh sách lớp học đã đăng ký</h2>
            
            <?php foreach ($lopHocs as $lop): ?>
                <div class="class-card">
                    <div class="class-header">
                        <h3><?= htmlspecialchars($lop['ten_lop']) ?></h3>
                        <p>Khóa học: <?= htmlspecialchars($lop['ten_khoa_hoc']) ?></p>
                    </div>
                    <div class="class-body">
                        <div class="class-info">
                            <div class="info-item">
                                <strong>Trạng thái đăng ký:</strong>
                                <?php
                                $trangThaiClass = 'status-active';
                                if ($lop['trang_thai_dang_ky'] == 'Đã xác nhận') {
                                    $trangThaiClass = 'status-active';
                                } elseif ($lop['trang_thai_dang_ky'] == 'Chờ xác nhận') {
                                    $trangThaiClass = 'status-warning';
                                } elseif ($lop['trang_thai_dang_ky'] == 'Đã hủy') {
                                    $trangThaiClass = 'status-inactive';
                                }
                                ?>
                                <span class="status-badge <?= $trangThaiClass ?>" style="margin-top: 5px; display: inline-block;">
                                    <?= htmlspecialchars($lop['trang_thai_dang_ky']) ?>
                                </span>
                            </div>
                            <div class="info-item">
                                <strong>Ngày đăng ký:</strong>
                                <span><?= date('d/m/Y', strtotime($lop['ngay_dang_ky'])) ?></span>
                            </div>
                            <?php if (!empty($lop['so_luong_toi_da'])): ?>
                                <div class="info-item">
                                    <strong>Số lượng tối đa:</strong>
                                    <span><?= $lop['so_luong_toi_da'] ?> học sinh</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($lop['mo_ta_lop'])): ?>
                            <div style="margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                <strong>Mô tả lớp học:</strong>
                                <p style="margin: 5px 0 0 0; color: var(--muted);"><?= htmlspecialchars($lop['mo_ta_lop']) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="schedule-section">
                            <h4>Lịch học</h4>
                            
                            <?php if (!empty($lop['ca_hoc'])): ?>
                                <div class="schedule-list">
                                    <?php foreach ($lop['ca_hoc'] as $ca): ?>
                                        <div class="schedule-item">
                                            <div class="schedule-item-header">
                                                <strong>
                                                    <?php
                                                    $thuHienThi = tinhThuTuNgayHoc($ca['ngay_hoc'] ?? null, $ca['thu_trong_tuan'] ?? null);
                                                    $thuMap = [
                                                        'Thứ 2' => 'Thứ Hai',
                                                        'Thứ 3' => 'Thứ Ba',
                                                        'Thứ 4' => 'Thứ Tư',
                                                        'Thứ 5' => 'Thứ Năm',
                                                        'Thứ 6' => 'Thứ Sáu',
                                                        'Thứ 7' => 'Thứ Bảy',
                                                        'Chủ nhật' => 'Chủ Nhật'
                                                    ];
                                                    echo $thuMap[$thuHienThi] ?? $thuHienThi;
                                                    ?>
                                                </strong>
                                            </div>
                                            <div class="schedule-item-details">
                                                <span>
                                                    <strong>Ca học:</strong> 
                                                    <?= htmlspecialchars($ca['ten_ca'] ?? 'Chưa có') ?>
                                                    <?php if (!empty($ca['gio_bat_dau']) && !empty($ca['gio_ket_thuc'])): ?>
                                                        (<?= htmlspecialchars($ca['gio_bat_dau']) ?> - <?= htmlspecialchars($ca['gio_ket_thuc']) ?>)
                                                    <?php endif; ?>
                                                </span>
                                                <?php if (!empty($ca['ten_phong'])): ?>
                                                    <span>
                                                        <strong>Phòng học:</strong> <?= htmlspecialchars($ca['ten_phong']) ?>
                                                        <?php if (!empty($ca['suc_chua'])): ?>
                                                            (Sức chứa: <?= $ca['suc_chua'] ?>)
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($ca['ten_giang_vien'])): ?>
                                                    <span>
                                                        <strong>Giảng viên:</strong> <?= htmlspecialchars($ca['ten_giang_vien']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <p>Lớp học này chưa có lịch học được phân công.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

