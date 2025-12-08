<?php
// views/lop_hoc/chi_tiet.php
// Biến có sẵn: $lopHoc, $danhSachCaHoc
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
    <title>Chi tiết lớp học - <?= htmlspecialchars($lopHoc['ten_lop']) ?></title>
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

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
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

        .ca-hoc-item {
            background: #f9fafb;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 4px solid var(--primary);
        }

        .ca-hoc-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .ca-hoc-time {
            font-weight: 600;
            color: var(--text);
            font-size: 16px;
        }

        .ca-hoc-day {
            background: var(--primary);
            color: #fff;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .ca-hoc-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            font-size: 14px;
            color: var(--muted);
        }

        .ca-hoc-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ca-hoc-detail-item strong {
            color: var(--text);
            min-width: 80px;
        }

        .link-ca-hoc {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .link-ca-hoc:hover {
            text-decoration: underline;
        }

        .progress-bar {
            width: 100%;
            height: 24px;
            background: #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 8px;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            transition: width 0.3s ease;
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

            .ca-hoc-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
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
                    <li><a href="index.php?act=client-lien-he">Liên hệ</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <!-- Back Link -->
        <?php if ($lopHoc['id_khoa_hoc']): ?>
            <a href="index.php?act=client-chi-tiet-khoa-hoc&id=<?= (int)$lopHoc['id_khoa_hoc'] ?>" class="back-link">&larr; Quay về chi tiết khóa học</a>
        <?php else: ?>
            <a href="index.php?act=client-khoa-hoc" class="back-link">&larr; Quay về danh sách khóa học</a>
        <?php endif; ?>

        <!-- Thông tin lớp học -->
        <div class="detail-section">
            <h2><?= htmlspecialchars($lopHoc['ten_lop']) ?></h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Trạng thái</div>
                    <div class="info-value">
                        <?php 
                        $statusClass = strtolower(str_replace(' ', '-', $lopHoc['trang_thai'] ?? ''));
                        ?>
                        <span class="status-badge <?= htmlspecialchars($statusClass) ?>">
                            <?= htmlspecialchars($lopHoc['trang_thai'] ?? 'Chưa xác định') ?>
                        </span>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">Số lượng tối đa</div>
                    <div class="info-value"><?= htmlspecialchars($lopHoc['so_luong_toi_da']) ?> học viên</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Số lượng đã đăng ký</div>
                    <div class="info-value"><?= (int)$lopHoc['so_luong_dang_ky'] ?> học viên</div>
                </div>

                <div class="info-item">
                    <div class="info-label">Đã xác nhận</div>
                    <div class="info-value"><?= (int)$lopHoc['so_luong_da_xac_nhan'] ?> học viên</div>
                </div>

                <?php if ($lopHoc['ngay_bat_dau']): ?>
                <div class="info-item">
                    <div class="info-label">Ngày bắt đầu</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($lopHoc['ngay_bat_dau'])) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($lopHoc['ngay_ket_thuc']): ?>
                <div class="info-item">
                    <div class="info-label">Ngày kết thúc</div>
                    <div class="info-value"><?= date('d/m/Y', strtotime($lopHoc['ngay_ket_thuc'])) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($lopHoc['so_luong_toi_da'] > 0): ?>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Tỷ lệ đăng ký</div>
                    <div class="info-value">
                        <?php 
                        $tyLe = ($lopHoc['so_luong_dang_ky'] / $lopHoc['so_luong_toi_da']) * 100;
                        $tyLe = min(100, max(0, $tyLe)); // Đảm bảo trong khoảng 0-100
                        ?>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $tyLe ?>%">
                                <?= number_format($tyLe, 1) ?>%
                            </div>
                        </div>
                        <div style="margin-top: 4px; font-size: 14px; color: var(--muted);">
                            <?= (int)$lopHoc['so_luong_dang_ky'] ?> / <?= (int)$lopHoc['so_luong_toi_da'] ?> học viên
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($lopHoc['mo_ta']): ?>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Mô tả lớp học</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($lopHoc['mo_ta'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Thông tin khóa học -->
        <?php if ($lopHoc['ten_khoa_hoc']): ?>
        <div class="detail-section">
            <h2>Thông tin khóa học</h2>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Tên khóa học</div>
                    <div class="info-value">
                        <a href="index.php?act=client-chi-tiet-khoa-hoc&id=<?= (int)$lopHoc['id_khoa_hoc'] ?>" class="course-link">
                            <?= htmlspecialchars($lopHoc['ten_khoa_hoc']) ?>
                        </a>
                    </div>
                </div>

                <?php if ($lopHoc['ten_danh_muc']): ?>
                <div class="info-item">
                    <div class="info-label">Danh mục</div>
                    <div class="info-value"><?= htmlspecialchars($lopHoc['ten_danh_muc']) ?></div>
                </div>
                <?php endif; ?>

                <?php if ($lopHoc['gia']): ?>
                <div class="info-item">
                    <div class="info-label">Giá khóa học</div>
                    <div class="info-value" style="color: var(--primary); font-weight: 600;">
                        <?= number_format($lopHoc['gia'], 0, ',', '.') ?>₫
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($lopHoc['mo_ta_khoa_hoc']): ?>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Mô tả khóa học</div>
                    <div class="info-value"><?= nl2br(htmlspecialchars($lopHoc['mo_ta_khoa_hoc'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Danh sách ca học -->
        <div class="detail-section">
            <h2>Lịch học (<?= count($danhSachCaHoc) ?> ca)</h2>
            
            <?php if (!empty($danhSachCaHoc)): ?>
                <?php foreach ($danhSachCaHoc as $ca): ?>
                    <div class="ca-hoc-item">
                        <div class="ca-hoc-header">
                            <div>
                                <span class="ca-hoc-day"><?= htmlspecialchars($ca['thu_trong_tuan']) ?></span>
                                <?php if ($ca['ten_ca']): ?>
                                    <span class="ca-hoc-time" style="margin-left: 12px;">
                                        <?= htmlspecialchars($ca['ten_ca']) ?>
                                        <?php if ($ca['gio_bat_dau'] && $ca['gio_ket_thuc']): ?>
                                            - <?= date('H:i', strtotime($ca['gio_bat_dau'])) ?> - <?= date('H:i', strtotime($ca['gio_ket_thuc'])) ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if ($ca['id']): ?>
                                <a href="index.php?act=client-chi-tiet-ca-hoc&id=<?= (int)$ca['id'] ?>" class="link-ca-hoc">
                                    Xem chi tiết ca học →
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="ca-hoc-details">
                            <?php if ($ca['ten_giang_vien']): ?>
                            <div class="ca-hoc-detail-item">
                                <strong>Giảng viên:</strong>
                                <span><?= htmlspecialchars($ca['ten_giang_vien']) ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if ($ca['ten_phong']): ?>
                            <div class="ca-hoc-detail-item">
                                <strong>Phòng học:</strong>
                                <span><?= htmlspecialchars($ca['ten_phong']) ?></span>
                                <?php if ($ca['suc_chua']): ?>
                                    <span>(<?= (int)$ca['suc_chua'] ?> chỗ)</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($ca['ghi_chu']): ?>
                            <div class="ca-hoc-detail-item" style="grid-column: 1 / -1;">
                                <strong>Ghi chú:</strong>
                                <span><?= htmlspecialchars($ca['ghi_chu']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="info-value empty">Lớp học này chưa có lịch học.</div>
            <?php endif; ?>
        </div>
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

