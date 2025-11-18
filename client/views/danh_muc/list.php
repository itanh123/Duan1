<?php
// views/danh_muc/list.php
// Biến có sẵn: $danhMucs
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh mục khóa học - Trang bán khóa học lập trình</title>
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
           6) GRID DANH MỤC
        ============================ */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
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
            padding: 30px;
            text-align: center;
        }

        .card:hover {
            box-shadow: 0 5px 20px #00000015;
            transform: translateY(-4px);
        }

        .card-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .card h3 {
            font-size: 22px;
            color: var(--text);
            margin-bottom: 12px;
        }

        .card p {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 20px;
            min-height: 40px;
        }

        .btn-view {
            background: var(--primary);
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
            transition: .2s;
        }

        .btn-view:hover {
            opacity: 0.9;
            background: var(--accent);
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
            nav ul {
                gap: 14px;
                flex-wrap: wrap;
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
        <h1>Danh mục khóa học</h1>
        <p>Khám phá các danh mục khóa học đa dạng</p>
    </div>

    <!-- ===========================
         GRID DANH MỤC
    ============================ -->
    <div class="container">
        <?php if (!empty($danhMucs)): ?>
            <div class="grid">
                <?php foreach ($danhMucs as $dm): ?>
                    <div class="card">
                        <div class="card-icon">📚</div>
                        <h3><?= htmlspecialchars($dm['ten_danh_muc']) ?></h3>
                        <?php if (!empty($dm['mo_ta'])): ?>
                            <p><?= htmlspecialchars(mb_substr($dm['mo_ta'], 0, 100, 'UTF-8')) ?><?= mb_strlen($dm['mo_ta']) > 100 ? '...' : '' ?></p>
                        <?php else: ?>
                            <p>Khám phá các khóa học trong danh mục này</p>
                        <?php endif; ?>
                        <a href="index.php?act=client-chi-tiet-danh-muc&id=<?= $dm['id'] ?>" class="btn-view">Xem khóa học</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; color: var(--muted);">
                <p>Chưa có danh mục nào.</p>
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

