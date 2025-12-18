<?php
// views/khoa_hoc/detail.php
// Biến có sẵn: $course, $lops, $lopCa, $binh_luan
// Session đã được khởi động ở index.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dang_ky_success = isset($_SESSION['dang_ky_success']) ? $_SESSION['dang_ky_success'] : false;
$dang_ky_error = isset($_SESSION['dang_ky_error']) ? $_SESSION['dang_ky_error'] : '';
$dang_ky_message = isset($_SESSION['dang_ky_message']) ? $_SESSION['dang_ky_message'] : '';
$dang_ky_info = isset($_SESSION['dang_ky_info']) ? $_SESSION['dang_ky_info'] : '';
unset($_SESSION['dang_ky_success']);
unset($_SESSION['dang_ky_error']);
unset($_SESSION['dang_ky_message']);
unset($_SESSION['dang_ky_info']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['ten_khoa_hoc']) ?> - Trang bán khóa học lập trình</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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

        /* User dropdown */
        .user-menu {
            position: relative;
        }
        .user-trigger {
            display: flex;
            align-items: center;
            gap: 6px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            font-weight: 700;
        }
        .user-trigger:hover {
            background: #0ea271;
        }
        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #fff;
            border: 1px solid #eee;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            border-radius: 10px;
            min-width: 210px;
            padding: 6px 0;
            display: none;
            z-index: 1000;
        }
        .user-dropdown li {
            display: block;
        }
        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            color: var(--text);
            font-weight: 600;
        }
        .user-dropdown a:hover {
            background: #f6f6f6;
            color: var(--primary);
        }
        .user-menu:hover .user-dropdown,
        .user-menu:focus-within .user-dropdown {
            display: block;
        }
        .logout-link {
            color: #dc3545;
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
           6) COURSE DETAIL
        ============================ */
        .course-detail {
            display: flex;
            gap: 40px;
            margin-bottom: 40px;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .course-image {
            flex: 0 0 400px;
            border-radius: 12px;
            overflow: hidden;
            background: #f0f0f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .course-image img {
            width: 100%;
            height: 100%;
            min-height: 400px;
            object-fit: cover;
            display: block;
        }

        .course-image-placeholder {
            width: 100%;
            min-height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 80px;
        }

        .course-info {
            flex: 1;
        }

        .course-info h1 {
            font-size: 32px;
            margin-bottom: 16px;
            color: var(--text);
        }

        .course-price {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 16px;
        }

        .course-desc {
            color: var(--muted);
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .course-meta {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: var(--muted);
        }

        /* ===========================
           7) REGISTRATION FORM
        ============================ */
        .registration-section {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }

        .registration-section h2 {
            font-size: 24px;
            margin-bottom: 24px;
            color: var(--text);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: .2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-submit {
            background: var(--primary);
            color: #fff;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            width: 100%;
        }

        .btn-submit:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-register {
            background: var(--primary);
            color: #fff;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            width: 100%;
        }

        .btn-register:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        /* ===========================
           8) MODAL
        ============================ */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s;
            position: relative;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #eee;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 24px;
            color: var(--text);
        }

        .close {
            color: var(--muted);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: .2s;
            line-height: 1;
        }

        .close:hover,
        .close:focus {
            color: var(--text);
        }

        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #3b82f6;
        }

        /* ===========================
           8) CLASSES SECTION
        ============================ */
        .classes-section {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            margin-bottom: 40px;
        }

        .classes-section h2 {
            font-size: 24px;
            margin-bottom: 24px;
            color: var(--text);
        }

        .class-item {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
        }

        .class-item:last-child {
            border-bottom: none;
        }

        .class-name {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 8px;
            color: var(--text);
        }

        .class-meta {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 12px;
        }

        .schedule-list {
            margin-left: 20px;
            color: var(--muted);
            font-size: 14px;
        }

        .schedule-item {
            padding: 6px 0;
        }

        /* ===========================
           9) COMMENTS SECTION
        ============================ */
        .comments-section {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .comments-section h2 {
            font-size: 24px;
            margin-bottom: 24px;
            color: var(--text);
        }

        .comment-item {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
        }

        .comment-item:last-child {
            border-bottom: none;
        }

        .comment-author {
            font-weight: 600;
            color: var(--text);
            margin-bottom: 8px;
        }

        .comment-date {
            font-size: 12px;
            color: var(--muted);
        }

        .comment-content {
            color: var(--muted);
            margin: 8px 0;
        }

        .comment-rating {
            color: #f59e0b;
            font-weight: 600;
        }

        .comment-form {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }

        /* ===========================
           10) FOOTER
        ============================ */
        footer {
            text-align: center;
            padding: 30px 0;
            color: var(--muted);
            margin-top: 40px;
            background: var(--ast-global-color-4);
        }

        /* ===========================
           11) RESPONSIVE
        ============================ */
        @media (max-width: 768px) {
            .course-detail {
                flex-direction: column;
            }

            .course-image {
                flex: 1;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            nav ul {
                gap: 14px;
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
                    <li><a href="index.php?act=client-giang-vien">Giảng viên</a></li>
                    <li><a href="index.php?act=client-lien-he">Liên hệ</a></li>
                    <li class="search-item">
                        <form class="search-form" method="get" action="index.php">
                            <input type="hidden" name="act" value="client-search-khoa-hoc">
                            <input type="search" name="q" placeholder="Tìm kiếm..." required>
                            <button type="submit">🔍</button>
                        </form>
                    </li>
                    <?php if (isset($_SESSION['client_id']) && (!isset($_SESSION['client_vai_tro']) || $_SESSION['client_vai_tro'] === 'hoc_sinh')): ?>
                        <li class="user-menu">
                            <button type="button" class="user-trigger">
                                <span><?= htmlspecialchars($_SESSION['client_ho_ten'] ?? '') ?></span>
                                <span style="font-size: 12px;">▾</span>
                            </button>
                            <ul class="user-dropdown">
                                <li><a href="?act=client-khoa-hoc-da-dang-ky">📚 Khóa học của tôi</a></li>
                                <li><a href="?act=client-hoc-sinh-lop-hoc">🏫 Lớp của tôi</a></li>
                                <li><a href="?act=client-profile">👤 Thông tin cá nhân</a></li>
                                <li><a href="?act=client-logout" class="logout-link">🚪 Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a href="?act=client-register" style="color: var(--primary); font-weight: 600;">📝 Đăng ký</a></li>
                        <li><a href="?act=client-login" style="color: var(--primary);">🔐 Đăng nhập</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <!-- Back Link -->
        <a href="index.php?act=client-khoa-hoc" class="back-link">&larr; Quay về danh sách khóa học</a>

        <!-- Course Detail -->
        <div class="course-detail">
            <div class="course-image">
                <?php if (!empty($course['hinh_anh']) && file_exists('./uploads/' . $course['hinh_anh'])): ?>
                    <img src="./uploads/<?= htmlspecialchars($course['hinh_anh']) ?>" 
                         alt="<?= htmlspecialchars($course['ten_khoa_hoc']) ?>"
                         loading="lazy">
                <?php else: ?>
                    <div class="course-image-placeholder">
                        📚
                    </div>
                <?php endif; ?>
            </div>
            <div class="course-info">
                <h1><?= htmlspecialchars($course['ten_khoa_hoc']) ?></h1>
                <div class="course-price"><?= number_format($course['gia'], 0, ',', '.') ?>₫</div>
                <div class="course-desc"><?= nl2br(htmlspecialchars($course['mo_ta'])) ?></div>
                <div class="course-meta">
                    <?php if (!empty($course['ten_danh_muc'])): ?>
                        <span><strong>Danh mục:</strong> <?= htmlspecialchars($course['ten_danh_muc']) ?></span>
                    <?php endif; ?>
                    <span><strong>Trạng thái:</strong> <?= $course['trang_thai'] ? 'Đang mở' : 'Đã đóng' ?></span>
                </div>
            </div>
        </div>

        <!-- Registration Section -->
        <div class="registration-section" id="registration-section">
            <h2>Đăng ký khóa học</h2>
            
            <?php if (!isset($_SESSION['client_id'])): ?>
                <div class="alert alert-error">
                    <strong>Yêu cầu đăng nhập!</strong> Vui lòng <a href="?act=client-login" style="color: var(--primary); font-weight: 600;">đăng nhập</a> hoặc <a href="?act=client-register" style="color: var(--primary); font-weight: 600;">đăng ký tài khoản</a> để đăng ký khóa học.
                </div>
            <?php else: ?>
                <?php if ($dang_ky_success): ?>
                    <div class="alert alert-success">
                        <strong>Thành công!</strong> 
                        <?php if ($dang_ky_message): ?>
                            <?= htmlspecialchars($dang_ky_message) ?>
                        <?php else: ?>
                            Đăng ký của bạn đã được ghi nhận. Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($dang_ky_error): ?>
                    <div class="alert alert-error">
                        <strong>Lỗi!</strong> <?= htmlspecialchars($dang_ky_error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($dang_ky_info): ?>
                    <div class="alert alert-info">
                        <strong>Thông báo!</strong> <?= htmlspecialchars($dang_ky_info) ?>
                    </div>
                <?php endif; ?>

                <button type="button" class="btn-register" id="btnOpenModal">Đăng ký ngay</button>
            <?php endif; ?>
        </div>

        <!-- Modal Form Đăng ký -->
        <div id="registrationModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Đăng ký khóa học</h2>
                    <span class="close" id="closeModal">&times;</span>
                </div>
                
                <form method="post" action="index.php?act=client-dang-ky-khoa-hoc" id="registrationForm">
                    <input type="hidden" name="id_khoa_hoc" value="<?= (int)$course['id'] ?>">
                    
                    <div class="form-group">
                        <label for="modal_id_lop">Chọn lớp học <span style="color:red">*</span></label>
                        <select id="modal_id_lop" name="id_lop" required>
                            <option value="">-- Chọn lớp học --</option>
                            <?php foreach ($lops as $lop): 
                                $soLuongDangKy = $lopSoLuong[$lop['id']] ?? 0;
                                $soLuongToiDa = $lop['so_luong_toi_da'] ?? null;
                                $isFull = $soLuongToiDa !== null && $soLuongDangKy >= $soLuongToiDa;
                                $conLai = $soLuongToiDa !== null ? ($soLuongToiDa - $soLuongDangKy) : null;
                            ?>
                                <option value="<?= $lop['id'] ?>" 
                                        <?= $isFull ? 'disabled' : '' ?>
                                        data-full="<?= $isFull ? '1' : '0' ?>"
                                        data-so-luong="<?= $soLuongDangKy ?>"
                                        data-toi-da="<?= $soLuongToiDa ?? 'Không giới hạn' ?>">
                                    <?= htmlspecialchars($lop['ten_lop']) ?>
                                    <?php if ($isFull): ?>
                                        - [ĐÃ ĐẦY] (<?= $soLuongDangKy ?>/<?= $soLuongToiDa ?>)
                                    <?php elseif ($soLuongToiDa !== null): ?>
                                        - Còn <?= $conLai ?> chỗ (<?= $soLuongDangKy ?>/<?= $soLuongToiDa ?>)
                                    <?php else: ?>
                                        - Không giới hạn
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="modal-lop-info" style="margin-top: 8px; padding: 10px; background: #f8f9fa; border-radius: 4px; font-size: 13px; display: none;">
                            <span id="modal-lop-info-text"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="mb-3">Phương thức thanh toán <span style="color:red">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="phuong_thuc_thanh_toan" id="payment-direct" value="truc_tiep" required>
                                <label class="btn btn-outline-primary w-100 h-100 p-3 d-flex flex-column align-items-center justify-content-center" for="payment-direct" id="modal-payment-direct-label" style="min-height: 120px; cursor: pointer;">
                                    <i class="bi bi-cash-coin fs-1 mb-2"></i>
                                    <strong class="mb-1">Thanh toán trực tiếp</strong>
                                    <small class="text-muted text-center">Thanh toán tại trung tâm</small>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <input type="radio" class="btn-check" name="phuong_thuc_thanh_toan" id="payment-online" value="online" required>
                                <label class="btn btn-outline-success w-100 h-100 p-3 d-flex flex-column align-items-center justify-content-center" for="payment-online" id="modal-payment-online-label" style="min-height: 120px; cursor: pointer;">
                                    <i class="bi bi-credit-card fs-1 mb-2"></i>
                                    <strong class="mb-1">Thanh toán online</strong>
                                    <small class="text-muted text-center">Thanh toán qua ví điện tử</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="modal_ghi_chu">Ghi chú (tùy chọn)</label>
                        <textarea id="modal_ghi_chu" name="ghi_chu" placeholder="Bạn có câu hỏi hoặc yêu cầu đặc biệt nào không?"></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Xác nhận đăng ký</button>
                </form>
            </div>
        </div>

        <script>
        // Mở modal
        const btnOpenModal = document.getElementById('btnOpenModal');
        const modal = document.getElementById('registrationModal');
        const closeModal = document.getElementById('closeModal');
        
        if (btnOpenModal) {
            btnOpenModal.addEventListener('click', function() {
                modal.classList.add('show');
            });
        }

        // Đóng modal khi click vào X
        if (closeModal) {
            closeModal.addEventListener('click', function() {
                modal.classList.remove('show');
            });
        }

        // Đóng modal khi click bên ngoài
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.remove('show');
            }
        });

        // Bootstrap tự động xử lý style cho radio buttons, không cần code thêm

        // Hiển thị thông tin lớp học khi chọn
        const selectLop = document.getElementById('modal_id_lop');
        const lopInfo = document.getElementById('modal-lop-info');
        const lopInfoText = document.getElementById('modal-lop-info-text');
        const form = document.getElementById('registrationForm');

        if (selectLop && lopInfo && lopInfoText) {
            selectLop.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const isFull = selectedOption.getAttribute('data-full') === '1';
                const soLuong = selectedOption.getAttribute('data-so-luong');
                const toiDa = selectedOption.getAttribute('data-toi-da');

                if (this.value && !isFull) {
                    lopInfo.style.display = 'block';
                    if (toiDa && toiDa !== 'Không giới hạn') {
                        const conLai = parseInt(toiDa) - parseInt(soLuong);
                        lopInfo.style.background = conLai <= 3 ? '#fff3cd' : '#d1ecf1';
                        lopInfo.style.border = '1px solid ' + (conLai <= 3 ? '#ffc107' : '#0c5460');
                        lopInfoText.innerHTML = `<strong>Thông tin lớp:</strong> Đã có ${soLuong}/${toiDa} học sinh đăng ký. Còn lại ${conLai} chỗ trống.`;
                        if (conLai <= 3) {
                            lopInfoText.innerHTML += ' <span style="color: #856404;">⚠️ Sắp đầy!</span>';
                        }
                    } else {
                        lopInfo.style.background = '#d1ecf1';
                        lopInfo.style.border = '1px solid #0c5460';
                        lopInfoText.innerHTML = `<strong>Thông tin lớp:</strong> Đã có ${soLuong} học sinh đăng ký. Lớp học không giới hạn số lượng.`;
                    }
                } else if (isFull) {
                    lopInfo.style.display = 'block';
                    lopInfo.style.background = '#f8d7da';
                    lopInfo.style.border = '1px solid #dc3545';
                    lopInfoText.innerHTML = `<strong style="color: #721c24;">⚠️ Lớp học này đã đầy!</strong> (${soLuong}/${toiDa}) Vui lòng chọn lớp học khác.`;
                } else {
                    lopInfo.style.display = 'none';
                }
            });
        }

        // Ngăn submit nếu lớp đã đầy
        if (form && selectLop) {
            form.addEventListener('submit', function(e) {
                const selectedOption = selectLop.options[selectLop.selectedIndex];
                const isFull = selectedOption.getAttribute('data-full') === '1';
                
                if (isFull) {
                    e.preventDefault();
                    alert('Lớp học này đã đầy! Vui lòng chọn lớp học khác.');
                    return false;
                }
            });
        }
        </script>

        <!-- Comments Section -->
        <div class="comments-section">
            <h2>Bình luận (<?= count($binh_luan) ?>)</h2>
            
            <?php if (!empty($binh_luan)): ?>
                <?php foreach ($binh_luan as $b): ?>
                    <div class="comment-item">
                        <div class="comment-author">
                            <?= htmlspecialchars($b['ho_ten'] ?? 'Người dùng') ?>
                            <span class="comment-date">— <?= htmlspecialchars($b['ngay_tao']) ?></span>
                            <?php if ($b['danh_gia'] !== null): ?>
                                <span class="comment-rating">⭐ <?= (int)$b['danh_gia'] ?>/5</span>
                            <?php endif; ?>
                        </div>
                        <div class="comment-content"><?= nl2br(htmlspecialchars($b['noi_dung'])) ?></div>
                        
                        <!-- Phản hồi của admin -->
                        <?php if (!empty($phanHoiList[$b['id']])): ?>
                            <div class="admin-replies" style="margin-top: 15px; padding-left: 20px; border-left: 3px solid #007bff;">
                                <?php foreach ($phanHoiList[$b['id']] as $ph): ?>
                                    <div class="admin-reply" style="margin-bottom: 10px; padding: 10px; background: #e7f3ff; border-radius: 5px;">
                                        <div style="display: flex; align-items: center; margin-bottom: 5px;">
                                            <strong style="color: #007bff; margin-right: 10px;">👨‍💼 Admin:</strong>
                                            <span style="color: #666; font-size: 12px;">
                                                <?= date('d/m/Y H:i', strtotime($ph['ngay_tao'])) ?>
                                            </span>
                                        </div>
                                        <div style="color: #333;"><?= nl2br(htmlspecialchars($ph['noi_dung'])) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--muted);">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
            <?php endif; ?>

            <div class="comment-form">
                <h3>Gửi bình luận</h3>
                <?php if (!isset($_SESSION['client_id'])): ?>
                    <div class="alert alert-error">
                        <strong>Yêu cầu đăng nhập!</strong> Vui lòng <a href="?act=client-login" style="color: var(--primary); font-weight: 600;">đăng nhập</a> hoặc <a href="?act=client-register" style="color: var(--primary); font-weight: 600;">đăng ký tài khoản</a> để bình luận.
                    </div>
                <?php elseif (!isset($daDangKy) || !$daDangKy): ?>
                    <div class="alert alert-warning" style="background: #fff3cd; color: #856404; border: 1px solid #ffc107;">
                        <strong>⚠️ Yêu cầu đăng ký khóa học!</strong> Chỉ những học viên đã đăng ký khóa học (trạng thái "Đã xác nhận") mới có thể bình luận. 
                        <a href="#registration-section" style="color: var(--primary); font-weight: 600;">Đăng ký ngay</a> để tham gia thảo luận!
                    </div>
                <?php else: ?>
                    <form method="post" action="index.php?act=client-binh-luan-khoa-hoc">
                        <input type="hidden" name="id_khoa_hoc" value="<?= (int)$course['id'] ?>">
                        <div class="form-group">
                            <label for="noi_dung">Nội dung bình luận:</label>
                            <textarea id="noi_dung" name="noi_dung" required placeholder="Nhập bình luận của bạn..."></textarea>
                        </div>
                        <div class="form-group">
                            <label for="danh_gia">Đánh giá (1-5 sao):</label>
                            <input type="number" id="danh_gia" name="danh_gia" min="1" max="5" placeholder="Chọn số sao (tùy chọn)">
                        </div>
                        <button type="submit" class="btn-submit">Gửi bình luận</button>
                    </form>
                <?php endif; ?>
            </div>
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
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
