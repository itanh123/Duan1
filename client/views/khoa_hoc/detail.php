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
                    <?php if (isset($_SESSION['client_id'])): ?>
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

        <!-- Registration Form -->
        <div class="registration-section">
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

                <form method="post" action="index.php?act=client-dang-ky-khoa-hoc" id="registrationForm">
                <input type="hidden" name="id_khoa_hoc" value="<?= (int)$course['id'] ?>">
                
                <div class="form-group">
                    <label for="id_lop">Chọn lớp học <span style="color:red">*</span></label>
                    <select id="id_lop" name="id_lop" required>
                        <option value="">-- Chọn lớp học --</option>
                        <?php foreach ($lops as $lop): ?>
                            <option value="<?= $lop['id'] ?>"><?= htmlspecialchars($lop['ten_lop']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Phương thức thanh toán <span style="color:red">*</span></label>
                    <div style="display: flex; gap: 20px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 12px; border: 2px solid #ddd; border-radius: 8px; flex: 1; transition: .2s;" id="payment-direct-label">
                            <input type="radio" name="phuong_thuc_thanh_toan" value="truc_tiep" required style="margin-right: 8px; cursor: pointer;">
                            <div>
                                <strong>Thanh toán trực tiếp</strong>
                                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">Thanh toán tại trung tâm</div>
                            </div>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 12px; border: 2px solid #ddd; border-radius: 8px; flex: 1; transition: .2s;" id="payment-online-label">
                            <input type="radio" name="phuong_thuc_thanh_toan" value="online" required style="margin-right: 8px; cursor: pointer;">
                            <div>
                                <strong>Thanh toán online</strong>
                                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">Thanh toán qua ví điện tử</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ghi_chu">Ghi chú (tùy chọn)</label>
                    <textarea id="ghi_chu" name="ghi_chu" placeholder="Bạn có câu hỏi hoặc yêu cầu đặc biệt nào không?"></textarea>
                </div>

                <button type="submit" class="btn-submit">Đăng ký ngay</button>
                </form>

                <script>
                // Thêm style cho radio button khi được chọn
                document.querySelectorAll('input[name="phuong_thuc_thanh_toan"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        document.querySelectorAll('label[id$="-label"]').forEach(label => {
                            label.style.borderColor = '#ddd';
                            label.style.backgroundColor = '#fff';
                        });
                        if (this.checked) {
                            const label = document.getElementById(this.value === 'truc_tiep' ? 'payment-direct-label' : 'payment-online-label');
                            label.style.borderColor = 'var(--primary)';
                            label.style.backgroundColor = '#f0fdf4';
                        }
                    });
                });
                </script>
            <?php endif; ?>
        </div>

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
</body>
</html>
