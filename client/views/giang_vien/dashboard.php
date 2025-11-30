<?php
// views/giang_vien/dashboard.php
// Biến có sẵn: $scheduleItems (danh sách lịch dạy đã tính toán ngày), $hocSinhList (danh sách học sinh đã đăng ký)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Giảng viên - Trang bán khóa học lập trình</title>
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
            background: #f5f5f5;
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
            transition: .2s;
        }

        nav a:hover {
            color: var(--primary);
        }

        .page-title {
            margin: 30px 0;
            font-size: 32px;
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
            color: #fff;
        }

        .stat-info h3 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }

        .stat-info p {
            color: var(--muted);
            margin: 0;
            font-size: 14px;
        }

        .section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .section-header {
            padding: 20px 24px;
            background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
            color: #fff;
        }

        .section-header h2 {
            margin: 0;
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-body {
            padding: 24px;
        }

        .schedule-item {
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 16px;
            border-left: 4px solid var(--primary);
        }

        .schedule-item:last-child {
            margin-bottom: 0;
        }

        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .schedule-header h4 {
            margin: 0;
            color: var(--text);
            font-size: 18px;
        }

        .schedule-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            font-size: 14px;
            color: var(--muted);
        }

        .schedule-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .schedule-detail-item strong {
            color: var(--text);
            min-width: 80px;
        }

        .student-list {
            display: grid;
            gap: 12px;
        }

        .student-item {
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .student-info h5 {
            margin: 0 0 8px 0;
            color: var(--text);
            font-size: 16px;
        }

        .student-info p {
            margin: 4px 0;
            font-size: 14px;
            color: var(--muted);
        }

        .student-meta {
            text-align: right;
        }

        .student-meta .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #d1fae5;
            color: #065f46;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
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
                        <li><a href="?act=giang-vien-lop-hoc"><i class="bi bi-book"></i> Lớp học của tôi</a></li>
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
            <i class="bi bi-speedometer2"></i>
            Dashboard Giảng viên
        </h1>

        <!-- Thống kê -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-book"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count(array_unique(array_column($scheduleItems ?? [], 'id_lop'))) ?></h3>
                    <p>Lớp học đang dạy</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?= count($scheduleItems ?? []) ?></h3>
                    <p>Ca học trong lịch</p>
                </div>
            </div>
        </div>

        <!-- Lịch dạy -->
        <div class="section">
            <div class="section-header">
                <h2><i class="bi bi-calendar3"></i> Lịch dạy của tôi</h2>
            </div>
            <div class="section-body">
                <!-- Bộ lọc theo khoảng thời gian -->
                <form method="GET" action="?act=giang-vien-dashboard" class="mb-4">
                    <input type="hidden" name="act" value="giang-vien-dashboard">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="tu_ngay" class="form-label"><i class="bi bi-calendar"></i> Từ ngày:</label>
                            <input type="date" class="form-control" id="tu_ngay" name="tu_ngay" 
                                   value="<?= htmlspecialchars($tuNgay ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="den_ngay" class="form-label"><i class="bi bi-calendar-check"></i> Đến ngày:</label>
                            <input type="date" class="form-control" id="den_ngay" name="den_ngay" 
                                   value="<?= htmlspecialchars($denNgay ?? '') ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2"><i class="bi bi-search"></i> Lọc</button>
                            <a href="?act=giang-vien-dashboard" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Xóa bộ lọc</a>
                        </div>
                    </div>
                </form>

                <?php if (empty($scheduleItems)): ?>
                    <div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <p>
                            <?php if (!empty($tuNgay) || !empty($denNgay)): ?>
                                Không tìm thấy lịch dạy trong khoảng thời gian đã chọn.
                            <?php else: ?>
                                Bạn chưa có lịch dạy nào. Lịch chỉ hiển thị khi lớp học có thời điểm bắt đầu và kết thúc.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($scheduleItems as $item): 
                        // Lấy học sinh trong lớp này
                        $hocSinhTrongLop = array_filter($hocSinhList ?? [], function($hs) use ($item) {
                            return isset($hs['id_lop']) && $hs['id_lop'] == $item['id_lop'];
                        });
                        $hocSinhTrongLop = array_values($hocSinhTrongLop);
                    ?>
                        <div class="schedule-item">
                            <div class="schedule-header">
                                <h4><?= htmlspecialchars($item['ten_lop']) ?></h4>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <span class="badge bg-primary"><?= htmlspecialchars($item['ten_khoa_hoc']) ?></span>
                                    <a href="?act=giang-vien-yeu-cau-doi-lich&id_ca_hoc=<?= $item['id_ca_hoc'] ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="bi bi-calendar-event"></i> Yêu cầu đổi lịch
                                    </a>
                                </div>
                            </div>
                            <div class="schedule-details">
                                <?php if (!empty($item['ngay_hoc_formatted'])): ?>
                                    <div class="schedule-detail-item">
                                        <strong><i class="bi bi-calendar-date"></i> Ngày học:</strong>
                                        <span class="text-primary fw-bold" style="font-size: 16px;"><?= htmlspecialchars($item['ngay_hoc_formatted']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="schedule-detail-item">
                                    <strong><i class="bi bi-calendar-week"></i> Thứ:</strong>
                                    <span><?= htmlspecialchars($item['thu_trong_tuan']) ?></span>
                                </div>
                                <div class="schedule-detail-item">
                                    <strong><i class="bi bi-calendar-range"></i> Khoảng thời gian:</strong>
                                    <span><?= htmlspecialchars($item['ngay_bat_dau_formatted']) ?> - <?= htmlspecialchars($item['ngay_ket_thuc_formatted']) ?></span>
                                </div>
                                <div class="schedule-detail-item">
                                    <strong><i class="bi bi-clock"></i> Ca học:</strong>
                                    <span><?= htmlspecialchars($item['ten_ca'] ?? 'Chưa có') ?></span>
                                </div>
                                <?php if (!empty($item['gio_bat_dau']) && !empty($item['gio_ket_thuc'])): ?>
                                    <div class="schedule-detail-item">
                                        <strong><i class="bi bi-clock-history"></i> Giờ:</strong>
                                        <span><?= htmlspecialchars($item['gio_bat_dau']) ?> - <?= htmlspecialchars($item['gio_ket_thuc']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($item['ten_phong'])): ?>
                                    <div class="schedule-detail-item">
                                        <strong><i class="bi bi-door-open"></i> Phòng:</strong>
                                        <span><?= htmlspecialchars($item['ten_phong']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($item['suc_chua'])): ?>
                                    <div class="schedule-detail-item">
                                        <strong><i class="bi bi-people"></i> Sức chứa:</strong>
                                        <span><?= htmlspecialchars($item['suc_chua']) ?> người</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mt-3 pt-3 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0"><i class="bi bi-people-fill"></i> Học sinh trong lớp (<?= count($hocSinhTrongLop) ?>)</h6>
                                    <a href="?act=giang-vien-view-hoc-sinh-trong-lop&id_lop=<?= $item['id_lop'] ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Xem tất cả
                                    </a>
                                </div>
                                <?php if (!empty($hocSinhTrongLop)): ?>
                                    <div class="row g-2">
                                        <?php 
                                        // Chỉ hiển thị 3 học sinh đầu tiên
                                        $hocSinhHienThi = array_slice($hocSinhTrongLop, 0, 3);
                                        foreach ($hocSinhHienThi as $hs): 
                                        ?>
                                            <div class="col-md-6">
                                                <div class="student-item">
                                                    <div class="student-info">
                                                        <h6 class="mb-1"><?= htmlspecialchars($hs['ho_ten']) ?></h6>
                                                        <p class="mb-0 text-muted small">
                                                            <?php if (!empty($hs['email'])): ?>
                                                                <i class="bi bi-envelope"></i> <?= htmlspecialchars($hs['email']) ?>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if (count($hocSinhTrongLop) > 3): ?>
                                            <div class="col-12">
                                                <p class="text-muted small mb-0">
                                                    <i class="bi bi-three-dots"></i> 
                                                    Và <?= count($hocSinhTrongLop) - 3 ?> học sinh khác...
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted small mb-0">Lớp này chưa có học sinh đăng ký.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

