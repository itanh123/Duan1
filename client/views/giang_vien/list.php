<?php
// views/giang_vien/list.php
// Biến có sẵn: $giangVien, $page, $totalPages, $search
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách giảng viên - Trang bán khóa học lập trình</title>
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
            font-size: 16px;
        }

        /* ===========================
           6) SEARCH SECTION
        ============================ */
        .search-section {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .search-form {
            display: flex;
            gap: 10px;
        }

        .search-form input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .search-form input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .search-form button {
            padding: 12px 24px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .search-form button:hover {
            background: #059669;
        }

        /* ===========================
           7) GRID GIẢNG VIÊN
        ============================ */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: .3s;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .card-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #fff;
            margin: 0 auto 16px;
            font-weight: 700;
        }

        .card h3 {
            font-size: 20px;
            margin-bottom: 8px;
            text-align: center;
            color: var(--text);
        }

        .card-info {
            text-align: center;
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 12px;
        }

        .card-info-item {
            margin: 6px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .card-info-item strong {
            color: var(--text);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .empty-state p {
            color: var(--muted);
            font-size: 16px;
        }

        /* ===========================
           8) PAGING
        ============================ */
        .paging {
            margin: 30px 0;
            text-align: center;
        }

        .paging a,
        .paging span {
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

        .paging a.active,
        .paging span.active {
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
                grid-template-columns: 1fr;
            }

            .search-form {
                flex-direction: column;
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
                    <li><a href="index.php?act=client-khoa-hoc">Trang chủ</a></li>
                    <li><a href="index.php?act=client-khoa-hoc">Khóa học</a></li>
                    <li><a href="index.php?act=client-lop-hoc">Lớp học</a></li>
                    <li><a href="index.php?act=client-danh-muc">Danh mục</a></li>
                    <li><a href="index.php?act=client-giang-vien">Giảng viên</a></li>
                    <li><a href="index.php?act=client-lien-he">Liên hệ</a></li>
                    <?php if (isset($_SESSION['client_id']) && (!isset($_SESSION['client_vai_tro']) || $_SESSION['client_vai_tro'] === 'hoc_sinh')): ?>
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

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>👨‍🏫 Danh sách giảng viên</h1>
            <p>Đội ngũ giảng viên giàu kinh nghiệm và tận tâm</p>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="index.php" class="search-form">
                <input type="hidden" name="act" value="client-giang-vien">
                <input type="text" 
                       name="search" 
                       placeholder="Tìm kiếm giảng viên theo tên hoặc email..." 
                       value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit">🔍 Tìm kiếm</button>
            </form>
        </div>

        <!-- Grid Giảng viên -->
        <?php if (empty($giangVien)): ?>
            <div class="empty-state">
                <p>Không tìm thấy giảng viên nào.</p>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($giangVien as $gv): ?>
                    <div class="card">
                        <div class="card-avatar">
                            <?= strtoupper(mb_substr($gv['ho_ten'], 0, 1)) ?>
                        </div>
                        <h3><?= htmlspecialchars($gv['ho_ten']) ?></h3>
                        <div class="card-info">
                            <?php if (!empty($gv['email'])): ?>
                                <div class="card-info-item">
                                    <span>📧</span>
                                    <span><?= htmlspecialchars($gv['email']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($gv['so_dien_thoai'])): ?>
                                <div class="card-info-item">
                                    <span>📞</span>
                                    <span><?= htmlspecialchars($gv['so_dien_thoai']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($gv['dia_chi'])): ?>
                                <div class="card-info-item">
                                    <span>📍</span>
                                    <span><?= htmlspecialchars($gv['dia_chi']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Paging -->
            <?php if ($totalPages > 1): ?>
                <div class="paging">
                    <?php if ($page > 1): ?>
                        <a href="?act=client-giang-vien&page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">« Trước</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                            <a href="?act=client-giang-vien&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                               class="<?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?act=client-giang-vien&page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">Sau »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
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

