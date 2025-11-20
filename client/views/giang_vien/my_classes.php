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
    <title>Lớp học của tôi - Trang bán khóa học lập trình</title>
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
                        <img src="https://via.placeholder.com/150x48/10B981/ffffff?text=LOGO" alt="Logo">
                    </a>
                </div>
                <nav>
                    <ul>
                        <li><a href="?act=client-khoa-hoc">Khóa học</a></li>
                        <li><a href="?act=client-lop-hoc">Lớp học</a></li>
                        <li><a href="?act=client-danh-muc">Danh mục</a></li>
                        <li><a href="?act=client-giang-vien">Giảng viên</a></li>
                        <?php if (isset($_SESSION['client_id'])): ?>
                            <?php if (isset($_SESSION['client_vai_tro']) && $_SESSION['client_vai_tro'] === 'giang_vien'): ?>
                                <li><a href="?act=client-giang-vien-lop-hoc" style="color: var(--primary);">Lớp của tôi</a></li>
                            <?php endif; ?>
                            <li><a href="?act=client-logout">Đăng xuất</a></li>
                        <?php else: ?>
                            <li><a href="?act=client-login">Đăng nhập</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">Lớp học của tôi</h1>

        <?php if (empty($lopHocs)): ?>
            <div class="empty-state">
                <p>Bạn chưa được phân công lớp học nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($lopHocs as $lop): ?>
                <div class="class-card">
                    <div class="class-header">
                        <h2><?= htmlspecialchars($lop['ten_lop']) ?></h2>
                        <div class="course-name">Khóa học: <?= htmlspecialchars($lop['ten_khoa_hoc']) ?></div>
                    </div>
                    <div class="class-body">
                        <div class="class-info">
                            <div class="info-item">
                                <strong>Trạng thái:</strong>
                                <span class="status-badge <?= $lop['trang_thai_lop'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $lop['trang_thai_lop'] == 1 ? 'Đang hoạt động' : 'Ngừng hoạt động' ?>
                                </span>
                            </div>
                            <?php if (!empty($lop['so_luong_toi_da'])): ?>
                                <div class="info-item">
                                    <strong>Số lượng tối đa:</strong>
                                    <span><?= $lop['so_luong_toi_da'] ?> học sinh</span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($lop['mo_ta_lop'])): ?>
                                <div class="info-item" style="grid-column: 1 / -1;">
                                    <strong>Mô tả:</strong>
                                    <span><?= htmlspecialchars($lop['mo_ta_lop']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="schedule-section">
                            <h3>Lịch dạy</h3>
                            <?php if (!empty($lop['ca_hoc'])): ?>
                                <div class="schedule-list">
                                    <?php foreach ($lop['ca_hoc'] as $ca): ?>
                                        <div class="schedule-item">
                                            <div class="schedule-item-header">
                                                <strong>
                                                    <?php
                                                    $thuMap = [
                                                        'Thứ 2' => 'Thứ Hai',
                                                        'Thứ 3' => 'Thứ Ba',
                                                        'Thứ 4' => 'Thứ Tư',
                                                        'Thứ 5' => 'Thứ Năm',
                                                        'Thứ 6' => 'Thứ Sáu',
                                                        'Thứ 7' => 'Thứ Bảy',
                                                        'Chủ nhật' => 'Chủ Nhật'
                                                    ];
                                                    echo $thuMap[$ca['thu_trong_tuan']] ?? $ca['thu_trong_tuan'];
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
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="no-schedule">
                                    Lớp học này chưa có lịch dạy được phân công.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>

