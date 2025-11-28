<?php
// views/ca_hoc/chi_tiet.php
// Biến có sẵn: $caHoc
// Session đã được khởi động ở index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết ca học - Trang bán khóa học lập trình</title>
    <style>
        /* ===========================
           1) BIẾN MÀU
        ============================ */
        :root {
            --primary: #10B981;
            --accent: #d4a6b6;
            --text: #1F2937;
            --muted: #6b7280;
            --bg: #ffffff;
            --container: 1200px;
            --ast-global-color-4: #f6edf0;
        }

        /* ===========================
           2) RESET
        ============================ */
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

        /* ===========================
           3) CONTAINER
        ============================ */
        .container {
            max-width: var(--container);
            margin: 0 auto;
            padding: 20px;
        }

        /* ===========================
           4) HEADER
        ============================ */
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

        /* Search Form */
        nav ul li.search-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-form input[type="search"] {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            width: 180px;
            transition: .2s;
        }

        .search-form input[type="search"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .search-form button {
            padding: 8px 12px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: .2s;
            font-size: 14px;
        }

        .search-form button:hover {
            background: #059669;
        }

        /* ===========================
           5) BACK BUTTON
        ============================ */
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: .2s;
        }

        .back-link:hover {
            opacity: 0.8;
        }

        /* ===========================
           6) DETAIL SECTIONS
        ============================ */
        .detail-section {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        .detail-section h2 {
            font-size: 24px;
            margin-bottom: 24px;
            color: var(--text);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
        }

        .detail-section h3 {
            font-size: 20px;
            margin-bottom: 16px;
            color: var(--text);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .info-item {
            padding: 16px;
            background: #f9fafb;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
        }

        .info-label {
            font-weight: 600;
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 16px;
            color: var(--text);
            font-weight: 500;
        }

        .info-value.empty {
            color: var(--muted);
            font-style: italic;
        }

        .time-badge {
            display: inline-block;
            background: var(--primary);
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            margin: 4px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-badge.active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.maintenance {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.locked {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge.chua-khai-giang {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-badge.dang-hoc {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.ket-thuc {
            background: #f3f4f6;
            color: #374151;
        }

        .section-divider {
            height: 1px;
            background: #eee;
            margin: 30px 0;
        }

        .course-link {
            display: inline-block;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: .2s;
        }

        .course-link:hover {
            text-decoration: underline;
        }

        /* ===========================
           7) FOOTER
        ============================ */
        footer {
            text-align: center;
            padding: 30px 0;
            color: var(--muted);
            margin-top: 40px;
            background: var(--ast-global-color-4);
        }

        /* ===========================
           8) RESPONSIVE
        ============================ */
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            nav ul {
                gap: 14px;
            }

            .detail-section {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- ===========================
         HEADER
    ============================ -->
    <header>
        <div class="container header-wrap">
            <div class="logo">
                <img src="./uploads/logo.png" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Trang chủ</a></li>
                    <li><a href="index.php?act=client-khoa-hoc">Khóa học</a></li>
                    <li><a href="index.php?act=client-lop-hoc">Lớp học</a></li>
                    <li><a href="index.php?act=client-danh-muc">Danh mục</a></li>
                    <li><a href="#">Giảng viên</a></li>
                    <li><a href="#">Liên hệ</a></li>
                    <li class="search-item">
                        <form class="search-form" method="get" action="index.php">
                            <input type="hidden" name="act" value="client-search-khoa-hoc">
                            <input type="search" name="q" placeholder="Tìm kiếm..." required>
                            <button type="submit">🔍</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <!-- Back Link -->
        <a href="index.php?act=client-khoa-hoc" class="back-link">&larr; Quay về danh sách khóa học</a>

        <!-- Chi tiết ca học -->
        <div class="detail-section">
            <h2>Chi tiết ca học #<?= htmlspecialchars($caHoc['id']) ?></h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Thứ trong tuần</div>
                    <div class="info-value"><?= htmlspecialchars($caHoc['thu_trong_tuan']) ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Ca học</div>
                    <div class="info-value">
                        <?php if ($caHoc['ten_ca']): ?>
                            <span class="time-badge">
                                <?= htmlspecialchars($caHoc['ten_ca']) ?> 
                                <?php if ($caHoc['gio_bat_dau'] && $caHoc['gio_ket_thuc']): ?>
                                    (<?= date('H:i', strtotime($caHoc['gio_bat_dau'])) ?> - <?= date('H:i', strtotime($caHoc['gio_ket_thuc'])) ?>)
                                <?php endif; ?>
                            </span>
                        <?php else: ?>
                            <span class="info-value empty">Chưa có thông tin</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($caHoc['ghi_chu']): ?>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Ghi chú</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($caHoc['ghi_chu'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thông tin lớp học -->
        <?php if ($caHoc['ten_lop']): ?>
        <div class="detail-section">
            <h2>Thông tin lớp học</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Tên lớp</div>
                    <div class="info-value"><?= htmlspecialchars($caHoc['ten_lop']) ?></div>
                </div>

                <div class="info-item">
                    <div class="info-label">Số lượng tối đa</div>
                    <div class="info-value"><?= htmlspecialchars($caHoc['so_luong_toi_da']) ?> học viên</div>
                </div>

                <?php if ($caHoc['ngay_bat_dau']): ?>
                <div class="info-item">
                    <div class="info-label">Ngày bắt đầu</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($caHoc['ngay_bat_dau'])) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($caHoc['ngay_ket_thuc']): ?>
                <div class="info-item">
                    <div class="info-label">Ngày kết thúc</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($caHoc['ngay_ket_thuc'])) ?></div>
                </div>
                <?php endif; ?>

                <div class="info-item">
                    <div class="info-label">Trạng thái lớp</div>
                    <div class="info-value">
                        <?php 
                        $statusClass = strtolower(str_replace(' ', '-', $caHoc['trang_thai_lop'] ?? ''));
                        ?>
                        <span class="status-badge <?= htmlspecialchars($statusClass) ?>">
                            <?= htmlspecialchars($caHoc['trang_thai_lop'] ?? 'Chưa xác định') ?>
                        </span>
                    </div>
                </div>

                <?php if ($caHoc['mo_ta_lop']): ?>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Mô tả lớp học</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($caHoc['mo_ta_lop'])) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($caHoc['ten_khoa_hoc']): ?>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Khóa học</div>
                    <div class="info-value">
                        <a href="index.php?act=client-chi-tiet-khoa-hoc&id=<?= (int)$caHoc['id_khoa_hoc'] ?>" class="course-link">
                            <?= htmlspecialchars($caHoc['ten_khoa_hoc']) ?>
                        </a>
                        <?php if ($caHoc['gia']): ?>
                            <span style="color: var(--primary); font-weight: 600; margin-left: 10px;">
                                - <?= number_format($caHoc['gia'], 0, ',', '.') ?>₫
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Thông tin giảng viên -->
        <?php if ($caHoc['ten_giang_vien']): ?>
        <div class="detail-section">
            <h2>Thông tin giảng viên</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Họ tên</div>
                    <div class="info-value"><?= htmlspecialchars($caHoc['ten_giang_vien']) ?></div>
                </div>

                <?php if ($caHoc['email_giang_vien']): ?>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?= htmlspecialchars($caHoc['email_giang_vien']) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($caHoc['sdt_giang_vien']): ?>
                <div class="info-item">
                    <div class="info-label">Số điện thoại</div>
                    <div class="info-value"><?= htmlspecialchars($caHoc['sdt_giang_vien']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="detail-section">
            <h2>Thông tin giảng viên</h2>
            <div class="info-value empty">Chưa phân công giảng viên</div>
        </div>
        <?php endif; ?>

        <!-- Thông tin phòng học -->
        <?php if ($caHoc['ten_phong']): ?>
        <div class="detail-section">
            <h2>Thông tin phòng học</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Tên phòng</div>
                    <div class="info-value"><?= htmlspecialchars($caHoc['ten_phong']) ?></div>
                </div>

                <?php if ($caHoc['suc_chua']): ?>
                <div class="info-item">
                    <div class="info-label">Sức chứa</div>
                    <div class="info-value"><?= htmlspecialchars($caHoc['suc_chua']) ?> người</div>
                </div>
                <?php endif; ?>

                <div class="info-item">
                    <div class="info-label">Trạng thái phòng</div>
                    <div class="info-value">
                        <?php 
                        $phongStatus = strtolower(str_replace(' ', '-', $caHoc['trang_thai_phong'] ?? ''));
                        ?>
                        <span class="status-badge <?= htmlspecialchars($phongStatus) ?>">
                            <?= htmlspecialchars($caHoc['trang_thai_phong'] ?? 'Chưa xác định') ?>
                        </span>
                    </div>
                </div>

                <?php if ($caHoc['mo_ta_phong']): ?>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Mô tả phòng</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($caHoc['mo_ta_phong'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="detail-section">
            <h2>Thông tin phòng học</h2>
            <div class="info-value empty">Chưa phân phòng học</div>
        </div>
        <?php endif; ?>
    </div>

    <!-- ===========================
         FOOTER
    ============================ -->
    <footer>
        <div class="container">
            © 2025 Bán Khóa Học Lập Trình — All rights reserved.
        </div>
    </footer>
</body>
</html>

