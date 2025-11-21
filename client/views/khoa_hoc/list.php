<?php
// views/khoa_hoc/list.php
// Biến có sẵn: $courses, $page, $totalPages
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang bán khóa học lập trình</title>
    <style>
        /* ===========================
           1) BIẾN MÀU TỪ THEME ASTRA
        ============================ */
        :root {
            --primary: #10B981;
            --accent: #d4a6b6;
            --text: #1F2937;
            --muted: #6b7280;
            --bg: #ffffff;
            --container: 1200px;
            --ast-global-color-0: #d4a6b6;
            --ast-global-color-4: #f6edf0;
            --moderncart-primary-color: #10B981;
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
           5) HERO SECTION
        ============================ */
        .hero {
            display: flex;
            align-items: center;
            gap: 40px;
            padding: 50px 0;
        }

        .hero .left {
            flex: 1.2;
        }

        .hero h1 {
            font-size: 42px;
            margin-bottom: 14px;
        }

        .hero p {
            color: var(--muted);
            margin-bottom: 20px;
        }

        .btn-primary {
            display: inline-block;
            padding: 14px 24px;
            background: var(--primary);
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
        }

        .hero .right {
            flex: 1;
        }

        .hero img {
            width: 100%;
            border-radius: 16px;
        }

        /* ===========================
           6) GRID KHÓA HỌC
        ============================ */
        #courses h2 {
            margin: 30px 0 20px;
            font-size: 26px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
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

        .card-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .card:hover img {
            transform: scale(1.05);
        }

        .card-image-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 48px;
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
            .hero {
                flex-direction: column;
                text-align: center;
            }

            nav ul {
                gap: 14px;
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
                    <li><a href="index.php?act=client-giang-vien">Giảng viên</a></li>
                    <li><a href="#">Liên hệ</a></li>
                    <li class="search-item">
                        <form class="search-form" method="get" action="index.php">
                            <input type="hidden" name="act" value="client-search-khoa-hoc">
                            <input type="search" name="q" placeholder="Tìm kiếm..." value="<?= isset($searchKeyword) ? htmlspecialchars($searchKeyword) : '' ?>" required>
                            <button type="submit">🔍</button>
                        </form>
                    </li>
                    <?php if (isset($_SESSION['client_id'])): ?>
                        <li><a href="?act=client-khoa-hoc-da-dang-ky" style="color: var(--primary); font-weight: 600;">📚 Khóa học của tôi</a></li>
                        <li><a href="?act=client-hoc-sinh-lop-hoc" style="color: var(--primary);">Lớp của tôi</a></li>
                        <li style="color: var(--primary); font-weight: 600;">👤 <?= htmlspecialchars($_SESSION['client_ho_ten'] ?? '') ?></li>
                        <li><a href="?act=client-logout" style="color: #dc3545;">🚪 Đăng xuất</a></li>
                    <?php else: ?>
                        <li><a href="?act=client-register" style="color: var(--primary); font-weight: 600;">📝 Đăng ký</a></li>
                        <li><a href="?act=client-login" style="color: var(--primary);">🔐 Đăng nhập</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- ===========================
         HERO SECTION
    ============================ -->
    <div class="container hero">
        <div class="left">
            <h1>Học Lập Trình Từ Zero Đến Làm Dự Án</h1>
            <p>Khóa học thực chiến, mentor hỗ trợ, bài tập theo dự án giúp bạn trở thành lập trình viên thực thụ.</p>
            <a class="btn-primary" href="#courses">Xem khóa học</a>
        </div>
        <div class="right">
            <img src="https://images.unsplash.com/photo-1555949963-aa79dcee981c" alt="Học lập trình">
        </div>
    </div>

    <!-- ===========================
         GRID KHÓA HỌC
    ============================ -->
    <div class="container" id="courses">
        <?php if (isset($isSearch) && $isSearch): ?>
            <h2>Kết quả tìm kiếm cho "<?= htmlspecialchars($searchKeyword) ?>" (<?= $total ?> kết quả)</h2>
        <?php else: ?>
            <h2>Khóa học nổi bật</h2>
        <?php endif; ?>
        <div class="grid">
            <?php if (empty($courses)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--muted);">
                    <p style="font-size: 18px; margin-bottom: 10px;">😔 Không tìm thấy khóa học nào</p>
                    <p>Vui lòng thử lại với từ khóa khác hoặc <a href="index.php?act=client-khoa-hoc" style="color: var(--primary);">xem tất cả khóa học</a></p>
                </div>
            <?php else: ?>
            <?php foreach ($courses as $c): ?>
                <div class="card">
                    <a href="index.php?act=client-chi-tiet-khoa-hoc&id=<?= $c['id'] ?>">
                        <div class="card-image">
                            <?php if (!empty($c['hinh_anh']) && file_exists('./uploads/' . $c['hinh_anh'])): ?>
                                <img src="./uploads/<?= htmlspecialchars($c['hinh_anh']) ?>" 
                                     alt="<?= htmlspecialchars($c['ten_khoa_hoc']) ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="card-image-placeholder">
                                    📚
                                </div>
                            <?php endif; ?>
                        </div>
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
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="paging">
                <?php 
                $baseUrl = isset($isSearch) && $isSearch 
                    ? 'index.php?act=client-search-khoa-hoc&q=' . urlencode($searchKeyword) . '&page='
                    : 'index.php?act=client-khoa-hoc&page=';
                ?>
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <a class="<?= ($p == $page) ? 'active' : '' ?>" href="<?= $baseUrl . $p ?>"><?= $p ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ===========================
         FOOTER
    ============================ -->
    <footer>
        © 2025 Bán Khóa Học Lập Trình — All rights reserved.
    </footer>
</body>
</html>
