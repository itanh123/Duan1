<?php
// views/danh_muc/detail.php
// Biến có sẵn: $danhMuc, $courses, $page, $totalPages
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($danhMuc['ten_danh_muc']) ?> - Trang bán khóa học lập trình</title>
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
           6) PAGE HEADER
        ============================ */
        .page-header {
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--muted);
        }

        /* ===========================
           7) GRID KHÓA HỌC
        ============================ */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
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

        .card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .card-content {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .card h3 {
            font-size: 18px;
            color: var(--text);
            margin-bottom: 4px;
        }

        .meta {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .desc {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 12px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .price {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .amount {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .btn-buy {
            background: var(--accent);
            color: #fff;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .btn-buy:hover {
            opacity: 0.9;
        }

        /* ===========================
           8) PAGING
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
           9) FOOTER
        ============================ */
        footer {
            text-align: center;
            padding: 30px 0;
            color: var(--muted);
            margin-top: 40px;
            background: var(--ast-global-color-4);
        }

        /* ===========================
           10) RESPONSIVE
        ============================ */
        @media (max-width: 768px) {
            nav ul {
                gap: 14px;
                flex-wrap: wrap;
            }

            .grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
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

    <div class="container">
        <!-- Back Link -->
        <a href="index.php?act=client-danh-muc" class="back-link">&larr; Quay về danh mục</a>

        <!-- Page Header -->
        <div class="page-header">
            <h1><?= htmlspecialchars($danhMuc['ten_danh_muc']) ?></h1>
            <?php if (!empty($danhMuc['mo_ta'])): ?>
                <p><?= htmlspecialchars($danhMuc['mo_ta']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Grid Khóa học -->
        <?php if (!empty($courses)): ?>
            <div class="grid">
                <?php foreach ($courses as $c): ?>
                    <div class="card">
                        <?php 
                        $img = $c['hinh_anh'] ? '/uploads/' . $c['hinh_anh'] : 'https://via.placeholder.com/600x400?text=Khóa+Học'; 
                        ?>
                        <a href="index.php?act=client-chi-tiet-khoa-hoc&id=<?= $c['id'] ?>">
                            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($c['ten_khoa_hoc']) ?>">
                        </a>
                        <div class="card-content">
                            <h3><?= htmlspecialchars($c['ten_khoa_hoc']) ?></h3>
                            <div class="meta">
                                <?php 
                                if (isset($c['so_buoi'])) {
                                    echo $c['so_buoi'] . ' buổi';
                                }
                                echo ' • Online';
                                ?>
                            </div>
                            <div class="desc">
                                <?= htmlspecialchars(mb_substr(strip_tags($c['mo_ta'] ?? ''), 0, 100, 'UTF-8')) ?>
                                <?= mb_strlen($c['mo_ta'] ?? '') > 100 ? '...' : '' ?>
                            </div>
                            <div class="price">
                                <div class="amount"><?= number_format($c['gia'], 0, ',', '.') ?>₫</div>
                                <a href="index.php?act=client-chi-tiet-khoa-hoc&id=<?= $c['id'] ?>" class="btn-buy">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="paging">
                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                        <a class="<?= ($p == $page) ? 'active' : '' ?>" href="index.php?act=client-chi-tiet-danh-muc&id=<?= $danhMuc['id'] ?>&page=<?= $p ?>"><?= $p ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--muted);">
                <p>Chưa có khóa học nào trong danh mục này.</p>
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

