<?php
// views/khoa_hoc/my_courses.php
// Biến có sẵn: $khoaHocs (danh sách khóa học mà học sinh đã đăng ký)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khóa học của tôi - Trang bán khóa học lập trình</title>
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

        .btn-primary {
            display: inline-block;
            padding: 12px 24px;
            background: var(--primary);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: .2s;
        }

        .btn-primary:hover {
            background: #059669;
            color: #fff;
        }

        .course-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: .2s;
        }

        .course-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        .course-header {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
            color: #fff;
        }

        .course-image {
            flex: 0 0 150px;
            height: 150px;
            border-radius: 8px;
            overflow: hidden;
            background: rgba(255,255,255,0.2);
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .course-info {
            flex: 1;
        }

        .course-info h2 {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .course-info .category {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .course-info .price {
            font-size: 20px;
            font-weight: 700;
            margin-top: 10px;
        }

        .course-body {
            padding: 20px;
        }

        .course-description {
            color: var(--muted);
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .classes-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .classes-section h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--text);
        }

        .class-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #f0fdf4;
            border: 1px solid var(--primary);
            border-radius: 20px;
            margin: 5px;
            font-size: 14px;
            color: var(--text);
        }

        .class-badge .badge-text {
            font-weight: 600;
            color: var(--primary);
        }

        .no-classes {
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
                        <img src="https://websitedemos.net/be-bold-beauty-store-04/wp-content/uploads/sites/1117/2022/08/logo-regular.png" alt="Logo">
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
                            <li><a href="?act=client-khoa-hoc-da-dang-ky" style="color: var(--primary); font-weight: 600;">📚 Khóa học của tôi</a></li>
                            <li><a href="?act=client-hoc-sinh-lop-hoc" style="color: var(--primary);">Lớp của tôi</a></li>
                            <li style="color: var(--primary); font-weight: 600;">👤 <?= htmlspecialchars($_SESSION['client_ho_ten'] ?? '') ?></li>
                            <li><a href="?act=client-logout" style="color: #dc3545;">🚪 Đăng xuất</a></li>
                        <?php else: ?>
                            <li><a href="?act=client-login">Đăng nhập</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">📚 Khóa học của tôi</h1>

        <?php if (empty($khoaHocs)): ?>
            <div class="empty-state">
                <i class="bi bi-book" style="font-size: 64px; color: var(--muted); margin-bottom: 20px;"></i>
                <p>Bạn chưa đăng ký khóa học nào.</p>
                <a href="?act=client-khoa-hoc" class="btn-primary">Xem khóa học</a>
            </div>
        <?php else: ?>
            <?php foreach ($khoaHocs as $khoaHoc): ?>
                <div class="course-card">
                    <div class="course-header">
                        <div class="course-image">
                            <?php if (!empty($khoaHoc['hinh_anh']) && file_exists('./uploads/' . $khoaHoc['hinh_anh'])): ?>
                                <img src="./uploads/<?= htmlspecialchars($khoaHoc['hinh_anh']) ?>" 
                                     alt="<?= htmlspecialchars($khoaHoc['ten_khoa_hoc']) ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                                    📚
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="course-info">
                            <h2><?= htmlspecialchars($khoaHoc['ten_khoa_hoc']) ?></h2>
                            <?php if (!empty($khoaHoc['ten_danh_muc'])): ?>
                                <div class="category">Danh mục: <?= htmlspecialchars($khoaHoc['ten_danh_muc']) ?></div>
                            <?php endif; ?>
                            <div class="price"><?= number_format($khoaHoc['gia'], 0, ',', '.') ?>₫</div>
                            <div style="margin-top: 10px; font-size: 14px; opacity: 0.9;">
                                Đã đăng ký: <?= $khoaHoc['so_lop_da_dang_ky'] ?> lớp học
                            </div>
                        </div>
                    </div>
                    <div class="course-body">
                        <?php if (!empty($khoaHoc['mo_ta'])): ?>
                            <div class="course-description">
                                <?= nl2br(htmlspecialchars($khoaHoc['mo_ta'])) ?>
                            </div>
                        <?php endif; ?>

                        <div class="classes-section">
                            <h3><i class="bi bi-calendar-check"></i> Lớp học đã đăng ký</h3>
                            <?php if (!empty($khoaHoc['lop_hoc'])): ?>
                                <div>
                                    <?php foreach ($khoaHoc['lop_hoc'] as $lop): ?>
                                        <div class="class-badge">
                                            <span class="badge-text"><?= htmlspecialchars($lop['ten_lop']) ?></span>
                                            <span style="font-size: 12px; color: var(--muted); margin-left: 8px;">
                                                (Đăng ký: <?= date('d/m/Y', strtotime($lop['ngay_dang_ky'])) ?>)
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div style="margin-top: 15px;">
                                    <a href="?act=client-chi-tiet-khoa-hoc&id=<?= $khoaHoc['id_khoa_hoc'] ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Xem chi tiết khóa học
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="no-classes">
                                    Chưa có lớp học nào được đăng ký.
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

