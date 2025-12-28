<?php
// views/giang_vien/danh_sach_yeu_cau_doi_lich.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách yêu cầu đổi lịch - Trang bán khóa học lập trình</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #10B981;
            --text: #1F2937;
            --muted: #6b7280;
        }

        body {
            font-family: Inter, Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: #fff;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            align-items: center;
        }

        nav a {
            text-decoration: none;
            font-weight: 600;
            color: var(--text);
        }

        .page-title {
            margin: 30px 0;
            font-size: 32px;
            font-weight: 700;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .card-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
            color: #fff;
            border-radius: 12px 12px 0 0;
        }

        .card-body {
            padding: 24px;
        }

        .badge-cho-duyet {
            background: #ffc107;
            color: #000;
        }

        .badge-da-duyet {
            background: #28a745;
            color: #fff;
        }

        .badge-tu-choi {
            background: #dc3545;
            color: #fff;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-wrap">
                <div class="logo">
                    <a href="?act=giang-vien-dashboard">
                        <img src="./uploads/logo.png" alt="Logo">
                    </a>
                </div>
                <nav>
                    <ul>
                        <li><a href="?act=giang-vien-dashboard"><i class="bi bi-house-door"></i> Dashboard</a></li>
                        <li><a href="?act=giang-vien-lop-hoc"><i class="bi bi-calendar-week"></i> Lịch học của tôi</a></li>
                        <li><a href="?act=giang-vien-list-hoc-sinh"><i class="bi bi-people"></i> Danh sách học sinh</a></li>
                        <li><a href="?act=giang-vien-profile"><i class="bi bi-person-circle"></i> Thông tin cá nhân</a></li>
                        <li style="color: var(--primary); font-weight: 600;"><i class="bi bi-person-badge"></i> <?= htmlspecialchars($_SESSION['giang_vien_ho_ten'] ?? '') ?></li>
                        <li><a href="?act=giang-vien-logout" style="color: #dc3545;"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">
            <i class="bi bi-list-check"></i> Danh sách yêu cầu đổi lịch
        </h1>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="bi bi-calendar-event"></i> Yêu cầu của tôi</h3>
            </div>
            <div class="card-body">
                <?php if (empty($yeuCauList)): ?>
                    <p class="text-muted">Bạn chưa có yêu cầu đổi lịch nào.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Lớp học</th>
                                    <th>Lịch cũ</th>
                                    <th>Lịch mới</th>
                                    <th>Ngày đổi</th>
                                    <th>Lý do</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($yeuCauList as $yc): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($yc['ten_lop']) ?></strong><br>
                                            <small class="text-muted"><?= htmlspecialchars($yc['ten_khoa_hoc']) ?></small>
                                        </td>
                                        <td>
                                            <small>
                                                <?= htmlspecialchars($yc['thu_cu']) ?><br>
                                                <?= htmlspecialchars($yc['ten_ca_cu']) ?> (<?= htmlspecialchars($yc['gio_bat_dau_cu']) ?> - <?= htmlspecialchars($yc['gio_ket_thuc_cu']) ?>)<br>
                                                Phòng: <?= htmlspecialchars($yc['ten_phong_cu']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <?= htmlspecialchars($yc['thu_trong_tuan_moi']) ?><br>
                                                <?= htmlspecialchars($yc['ten_ca_moi']) ?> (<?= htmlspecialchars($yc['gio_bat_dau_moi']) ?> - <?= htmlspecialchars($yc['gio_ket_thuc_moi']) ?>)<br>
                                                Phòng: <?= htmlspecialchars($yc['ten_phong_moi']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?= $yc['ngay_doi'] ? date('d/m/Y', strtotime($yc['ngay_doi'])) : 'Toàn bộ lịch' ?>
                                        </td>
                                        <td>
                                            <small><?= htmlspecialchars($yc['ly_do'] ?? '') ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = 'badge-cho-duyet';
                                            $badgeText = 'Chờ duyệt';
                                            if ($yc['trang_thai'] == 'da_duyet') {
                                                $badgeClass = 'badge-da-duyet';
                                                $badgeText = 'Đã duyệt';
                                            } elseif ($yc['trang_thai'] == 'tu_choi') {
                                                $badgeClass = 'badge-tu-choi';
                                                $badgeText = 'Từ chối';
                                            }
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                                            <?php if ($yc['ghi_chu_admin']): ?>
                                                <br><small class="text-muted"><?= htmlspecialchars($yc['ghi_chu_admin']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small><?= date('d/m/Y H:i', strtotime($yc['ngay_tao'])) ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

