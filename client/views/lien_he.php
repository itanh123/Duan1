<?php
// views/lien_he.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - Trang bán khóa học lập trình</title>
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
            text-align: center;
        }

        .page-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--muted);
        }

        /* ===========================
           6) CONTACT SECTION
        ============================ */
        .contact-section {
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            margin: 30px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .contact-item {
            text-align: center;
            padding: 30px;
            background: var(--ast-global-color-4);
            border-radius: 12px;
            transition: transform 0.2s;
        }

        .contact-item:hover {
            transform: translateY(-5px);
        }

        .contact-item h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: var(--text);
        }

        .contact-item p {
            color: var(--muted);
            margin-bottom: 15px;
        }

        .contact-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .contact-link:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }

        .contact-link.zalo {
            background: #0068FF;
        }

        .contact-link.zalo:hover {
            background: #0052CC;
        }

        .contact-link.messenger {
            background: #0084FF;
        }

        .contact-link.messenger:hover {
            background: #0066CC;
        }

        .contact-link.phone {
            background: #10B981;
        }

        .contact-link.phone:hover {
            background: #059669;
        }

        .contact-link.email {
            background: #6366F1;
        }

        .contact-link.email:hover {
            background: #4F46E5;
        }

        .contact-link.facebook {
            background: #1877F2;
        }

        .contact-link.facebook:hover {
            background: #166FE5;
        }

        .contact-link.other {
            background: #6B7280;
        }

        .contact-link.other:hover {
            background: #4B5563;
        }

        .contact-icon {
            width: 24px;
            height: 24px;
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

            .contact-info {
                grid-template-columns: 1fr;
            }

            .contact-section {
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
                    <li><a href="index.php?act=client-giang-vien">Giảng viên</a></li>
                    <li><a href="index.php?act=client-lien-he" style="color: var(--primary);">Liên hệ</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- ===========================
         PAGE HEADER
    ============================ -->
    <div class="container page-header">
        <h1>Liên hệ với chúng tôi</h1>
        <p>Chúng tôi luôn sẵn sàng hỗ trợ bạn</p>
    </div>

    <!-- ===========================
         CONTACT SECTION
    ============================ -->
    <div class="container">
        <div class="contact-section">
            <?php if (empty($lienHeList)): ?>
                <div style="text-align: center; padding: 40px;">
                    <p style="color: var(--muted); font-size: 18px;">Chưa có thông tin liên hệ nào.</p>
                </div>
            <?php else: ?>
                <div class="contact-info">
                    <?php foreach ($lienHeList as $lh): ?>
                        <div class="contact-item">
                            <h3><?= htmlspecialchars($lh['icon'] ?? '📱') ?> <?= htmlspecialchars($lh['ten']) ?></h3>
                            <p><?= htmlspecialchars($lh['mo_ta'] ?? 'Liên hệ với chúng tôi để được hỗ trợ nhanh chóng') ?></p>
                            <?php
                            // Xác định link và text hiển thị
                            $link = $lh['gia_tri'];
                            $displayText = $lh['gia_tri'];
                            
                            // Xử lý link theo loại
                            if ($lh['loai'] == 'zalo') {
                                // Nếu là số điện thoại, chuyển thành link zalo.me
                                if (preg_match('/^0\d{9,10}$/', $link)) {
                                    $link = 'https://zalo.me/' . substr($link, 1);
                                } elseif (strpos($link, 'http') !== 0) {
                                    $link = 'https://zalo.me/' . preg_replace('/[^0-9]/', '', $link);
                                }
                            } elseif ($lh['loai'] == 'messenger') {
                                // Nếu chưa có http, thêm https://
                                if (strpos($link, 'http') !== 0) {
                                    $link = 'https://' . $link;
                                }
                            } elseif ($lh['loai'] == 'phone') {
                                // Tạo link tel:
                                $link = 'tel:' . preg_replace('/[^0-9+]/', '', $link);
                            } elseif ($lh['loai'] == 'email') {
                                // Tạo link mailto:
                                $link = 'mailto:' . $link;
                            } elseif ($lh['loai'] == 'facebook') {
                                // Nếu chưa có http, thêm https://
                                if (strpos($link, 'http') !== 0) {
                                    $link = 'https://' . $link;
                                }
                            } else {
                                // Loại khác, kiểm tra xem có phải link không
                                if (strpos($link, 'http') !== 0 && strpos($link, 'mailto:') !== 0 && strpos($link, 'tel:') !== 0) {
                                    // Nếu không phải link, không tạo link
                                    $link = null;
                                }
                            }
                            
                            // Xác định class CSS theo loại
                            $linkClass = 'contact-link ' . htmlspecialchars($lh['loai']);
                            
                            // Icon SVG theo loại
                            $iconSvg = '';
                            if ($lh['loai'] == 'zalo') {
                                $iconSvg = '<svg class="contact-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.15 12.04 20.15C10.56 20.15 9.11 19.76 7.85 19L7.55 18.83L4.43 19.65L5.26 16.61L5.06 16.29C4.24 15 3.8 13.47 3.8 11.91C3.81 7.37 7.5 3.67 12.05 3.67M8.53 7.33C8.37 7.33 8.1 7.39 7.87 7.64C7.65 7.89 7 8.5 7 9.71C7 10.93 7.89 12.1 8 12.27C8.14 12.44 9.76 14.94 12.25 15.9C13.1 16.23 13.77 16.33 14.27 16.36C14.77 16.38 15.25 16.28 15.64 16.17C16.08 16.04 16.93 15.59 17.19 15.08C17.45 14.57 17.45 14.12 17.37 14.05C17.28 13.97 17.09 13.9 16.95 13.85C16.81 13.8 15.09 13.12 14.87 13.05C14.64 12.97 14.5 12.93 14.35 13.1C14.21 13.27 13.68 13.83 13.53 14C13.38 14.19 13.24 14.21 13.04 14.12C12.84 14.03 11.94 13.7 10.83 12.68C9.93 11.86 9.32 10.97 9.18 10.76C9.03 10.56 9.13 10.41 9.24 10.3C9.37 10.18 9.54 9.97 9.66 9.8C9.78 9.64 9.85 9.53 9.95 9.35C10.05 9.17 10 9.05 9.93 8.95C9.86 8.85 9.19 7.47 8.95 7.05C8.71 6.63 8.5 6.71 8.35 6.71C8.19 6.71 8 6.65 7.84 6.65C7.68 6.65 7.43 6.73 7.23 6.97C7.03 7.2 6.5 7.74 6.5 8.82C6.5 9.9 7.29 10.93 7.38 11.06C7.47 11.19 8.53 12.8 10.16 13.78C11.25 14.46 12.11 14.8 12.73 15C13.31 15.19 13.8 15.26 14.19 15.29C14.62 15.33 15.3 15.25 15.77 14.88C16.25 14.5 16.6 13.97 16.73 13.81C16.86 13.65 17.05 13.5 17.13 13.36C17.22 13.22 17.28 13.11 17.19 12.97C17.11 12.83 16.11 10.75 15.87 10.2C15.64 9.65 15.4 9.72 15.27 9.72C15.15 9.72 15.05 9.67 8.53 7.33Z"/></svg>';
                            } elseif ($lh['loai'] == 'messenger') {
                                $iconSvg = '<svg class="contact-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.373 0 0 4.925 0 11c0 2.134.694 4.11 1.87 5.73L0 24l7.803-2.104c1.063.29 2.188.44 3.197.44 6.627 0 12-4.925 12-11S18.627 0 12 0zm.797 18.305c-1.062 0-2.1-.15-3.048-.44l-.22-.062-2.28.614.61-2.223-.07-.22c-.93-.94-1.424-2.15-1.424-3.45 0-3.893 3.582-7.05 8-7.05s8 3.157 8 7.05-3.582 7.05-8 7.05z"/></svg>';
                            } elseif ($lh['loai'] == 'phone') {
                                $iconSvg = '<svg class="contact-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>';
                            } elseif ($lh['loai'] == 'email') {
                                $iconSvg = '<svg class="contact-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>';
                            } elseif ($lh['loai'] == 'facebook') {
                                $iconSvg = '<svg class="contact-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
                            }
                            
                            // Text hiển thị
                            if ($lh['loai'] == 'phone') {
                                $displayText = $lh['gia_tri'];
                            } elseif ($lh['loai'] == 'email') {
                                $displayText = $lh['gia_tri'];
                            } elseif ($lh['loai'] == 'zalo' && preg_match('/^0\d{9,10}$/', $lh['gia_tri'])) {
                                $displayText = $lh['gia_tri'];
                            } else {
                                $displayText = 'Liên hệ ngay';
                            }
                            ?>
                            
                            <?php if ($link): ?>
                                <a href="<?= htmlspecialchars($link) ?>" 
                                   target="_blank" 
                                   class="<?= $linkClass ?>" 
                                   title="<?= htmlspecialchars($lh['ten']) ?>">
                                    <?= $iconSvg ?>
                                    <span><?= htmlspecialchars($displayText) ?></span>
                                </a>
                            <?php else: ?>
                                <div class="<?= $linkClass ?>" style="cursor: default;">
                                    <?= $iconSvg ?>
                                    <span><?= htmlspecialchars($displayText) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
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

