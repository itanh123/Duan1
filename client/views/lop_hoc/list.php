<?php
// views/lop_hoc/list.php
// Biến có sẵn: $lopHocs, $page, $totalPages
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách lớp học - Trang bán khóa học lập trình</title>
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
           5) PAGE HEADER
        ============================ */
        .page-header {
            padding: 40px 0 20px;
        }

        .page-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--muted);
        }

        /* ===========================
           6) GRID LỚP HỌC
        ============================ */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #eee;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: .2s;
        }

        .card:hover {
            box-shadow: 0 5px 20px #00000015;
        }

        .card-content {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .card h3 {
            font-size: 20px;
            color: var(--text);
            margin-bottom: 8px;
        }

        .card-meta {
            font-size: 14px;
            color: var(--muted);
        }

        .card-meta span {
            display: block;
            margin-bottom: 4px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }

        .status-badge.active {
            background: #d1fae5;
            color: #065f46;
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

        .btn-view {
            background: var(--primary);
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            margin-top: 12px;
            transition: .2s;
        }

        .btn-view:hover {
            opacity: 0.9;
        }

        /* ===========================
           7) PAGING
        ============================ */
        .paging {
            margin: 30px 0;
            text-align: center;
        }

        .paging a {
            display: inline-block;
            margin: 0 6px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-decoration: none;
            color: var(--text);
            transition: .2s;
        }

        .paging a:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .paging a.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* ===========================
           8) FOOTER
        ============================ */
        footer {
            text-align: center;
            padding: 30px 0;
            color: var(--muted);
            margin-top: 40px;
            background: var(--ast-global-color-4);
        }

        /* ===========================
           9) RESPONSIVE
        ============================ */
        @media (max-width: 768px) {
            nav ul {
                gap: 14px;
            }

            .grid {
                grid-template-columns: 1fr;
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
                <img src="https://websitedemos.net/be-bold-beauty-store-04/wp-content/uploads/sites/1117/2022/08/logo-regular.png" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Trang chủ</a></li>
                    <li><a href="index.php?act=client-khoa-hoc">Khóa học</a></li>
                    <li><a href="index.php?act=client-lop-hoc">Lớp học</a></li>
                    <li><a href="index.php?act=client-danh-muc">Danh mục</a></li>
                    <li><a href="#">Giảng viên</a></li>
                    <li><a href="#">Liên hệ</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- ===========================
         PAGE HEADER
    ============================ -->
    <div class="container page-header">
        <h1>Danh sách lớp học</h1>
        <p>Tìm lớp học phù hợp với lịch trình của bạn</p>
    </div>

    <!-- ===========================
         GRID LỚP HỌC
    ============================ -->
    <div class="container">
        <?php if (!empty($lopHocs)): ?>
            <div class="grid">
                <?php foreach ($lopHocs as $lop): ?>
                    <div class="card">
                        <div class="card-content">
                            <h3><?= htmlspecialchars($lop['ten_lop']) ?></h3>
                            <div class="card-meta">
                                <?php if ($lop['ten_khoa_hoc']): ?>
                                    <span><strong>Khóa học:</strong> <?= htmlspecialchars($lop['ten_khoa_hoc']) ?></span>
                                <?php endif; ?>
                                <?php if ($lop['ten_danh_muc']): ?>
                                    <span><strong>Danh mục:</strong> <?= htmlspecialchars($lop['ten_danh_muc']) ?></span>
                                <?php endif; ?>
                                <span><strong>Số lượng tối đa:</strong> <?= htmlspecialchars($lop['so_luong_toi_da']) ?> học viên</span>
                                <?php if ($lop['so_luong_dang_ky']): ?>
                                    <span><strong>Đã đăng ký:</strong> <?= htmlspecialchars($lop['so_luong_dang_ky']) ?> học viên</span>
                                <?php endif; ?>
                                <?php if ($lop['ngay_bat_dau']): ?>
                                    <span><strong>Ngày bắt đầu:</strong> <?= date('d/m/Y', strtotime($lop['ngay_bat_dau'])) ?></span>
                                <?php endif; ?>
                                <?php if ($lop['ngay_ket_thuc']): ?>
                                    <span><strong>Ngày kết thúc:</strong> <?= date('d/m/Y', strtotime($lop['ngay_ket_thuc'])) ?></span>
                                <?php endif; ?>
                                <?php if ($lop['gia']): ?>
                                    <span><strong>Giá:</strong> <?= number_format($lop['gia'], 0, ',', '.') ?>₫</span>
                                <?php endif; ?>
                            </div>
                            <?php if ($lop['trang_thai']): ?>
                                <?php 
                                $statusClass = strtolower(str_replace(' ', '-', $lop['trang_thai']));
                                ?>
                                <span class="status-badge <?= htmlspecialchars($statusClass) ?>">
                                    <?= htmlspecialchars($lop['trang_thai']) ?>
                                </span>
                            <?php endif; ?>
                            <a href="index.php?act=client-chi-tiet-lop-hoc&id=<?= $lop['id'] ?>" class="btn-view">Xem chi tiết</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="paging">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a class="<?= ($p == $page) ? 'active' : '' ?>" href="index.php?act=client-lop-hoc&page=<?= $p ?>"><?= $p ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--muted);">
                <p>Chưa có lớp học nào.</p>
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

