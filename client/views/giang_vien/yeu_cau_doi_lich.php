<?php
// views/giang_vien/yeu_cau_doi_lich.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu cầu đổi lịch - Trang bán khóa học lập trình</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #10B981;
            --accent: #d4a6b6;
            --text: #1F2937;
            --muted: #6b7280;
            --bg: #ffffff;
        }

        body {
            font-family: Inter, Arial, sans-serif;
            color: var(--text);
            background: #f5f5f5;
            line-height: 1.6;
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

        .info-box {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--text);
        }

        .info-value {
            color: var(--muted);
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
            <i class="bi bi-calendar-event"></i> Yêu cầu đổi lịch
        </h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="bi bi-info-circle"></i> Thông tin lịch hiện tại</h3>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <div class="info-item">
                        <span class="info-label">Lớp học:</span>
                        <span class="info-value"><?= htmlspecialchars($caHoc['ten_lop'] ?? '') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Khóa học:</span>
                        <span class="info-value"><?= htmlspecialchars($caHoc['ten_khoa_hoc'] ?? '') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Thứ:</span>
                        <span class="info-value"><?= htmlspecialchars($caHoc['thu_trong_tuan'] ?? '') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ca học:</span>
                        <span class="info-value"><?= htmlspecialchars($caHoc['ten_ca'] ?? '') ?> (<?= htmlspecialchars($caHoc['gio_bat_dau'] ?? '') ?> - <?= htmlspecialchars($caHoc['gio_ket_thuc'] ?? '') ?>)</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phòng học:</span>
                        <span class="info-value"><?= htmlspecialchars($caHoc['ten_phong'] ?? '') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="mb-0"><i class="bi bi-pencil-square"></i> Thông tin lịch mới</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="?act=giang-vien-process-yeu-cau-doi-lich">
                    <input type="hidden" name="id_ca_hoc_cu" value="<?= htmlspecialchars($caHoc['id'] ?? '') ?>">
                    <input type="hidden" name="id_lop" value="<?= htmlspecialchars($caHoc['id_lop'] ?? '') ?>">
                    
                    <div class="mb-3">
                        <label for="ngay_doi" class="form-label">Ngày đổi (tùy chọn - để trống nếu đổi toàn bộ lịch)</label>
                        <input type="date" 
                               class="form-control" 
                               id="ngay_doi" 
                               name="ngay_doi"
                               value="<?= !empty($caHoc['ngay_hoc']) ? htmlspecialchars($caHoc['ngay_hoc']) : '' ?>">
                        <small class="form-text text-muted">Nếu chọn ngày, chỉ đổi lịch cho ngày đó. Nếu để trống, sẽ đổi toàn bộ lịch trong khoảng thời gian của lớp.</small>
                    </div>

                    <div class="mb-3" id="thu_trong_tuan_moi_group">
                        <label for="thu_trong_tuan_moi" class="form-label">Thứ trong tuần mới <span class="text-danger">*</span></label>
                        <select class="form-select" id="thu_trong_tuan_moi" name="thu_trong_tuan_moi" required>
                            <option value="">-- Chọn thứ --</option>
                            <option value="Thứ 2" <?= (isset($caHoc['thu_trong_tuan']) && $caHoc['thu_trong_tuan'] == 'Thứ 2') ? 'selected' : '' ?>>Thứ 2</option>
                            <option value="Thứ 3" <?= (isset($caHoc['thu_trong_tuan']) && $caHoc['thu_trong_tuan'] == 'Thứ 3') ? 'selected' : '' ?>>Thứ 3</option>
                            <option value="Thứ 4" <?= (isset($caHoc['thu_trong_tuan']) && $caHoc['thu_trong_tuan'] == 'Thứ 4') ? 'selected' : '' ?>>Thứ 4</option>
                            <option value="Thứ 5" <?= (isset($caHoc['thu_trong_tuan']) && $caHoc['thu_trong_tuan'] == 'Thứ 5') ? 'selected' : '' ?>>Thứ 5</option>
                            <option value="Thứ 6" <?= (isset($caHoc['thu_trong_tuan']) && $caHoc['thu_trong_tuan'] == 'Thứ 6') ? 'selected' : '' ?>>Thứ 6</option>
                            <option value="Thứ 7" <?= (isset($caHoc['thu_trong_tuan']) && $caHoc['thu_trong_tuan'] == 'Thứ 7') ? 'selected' : '' ?>>Thứ 7</option>
                            <option value="Chủ nhật" <?= (isset($caHoc['thu_trong_tuan']) && $caHoc['thu_trong_tuan'] == 'Chủ nhật') ? 'selected' : '' ?>>Chủ nhật</option>
                        </select>
                        <small id="thu_auto_info" class="form-text text-muted" style="display: none; color: #28a745;">Thứ được tự động tính từ ngày đã chọn</small>
                        <small class="form-text text-muted">Nếu chọn ngày đổi, thứ sẽ tự động tính từ ngày. Nếu không chọn ngày, vui lòng chọn thứ để đổi toàn bộ lịch.</small>
                    </div>

                    <div class="mb-3">
                        <label for="id_ca_moi" class="form-label">Ca học mới <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_ca_moi" name="id_ca_moi" required>
                            <option value="">-- Chọn ca học --</option>
                            <?php foreach ($caMacDinhList as $ca): ?>
                                <option value="<?= $ca['id'] ?>" 
                                        data-gio-bat-dau="<?= htmlspecialchars($ca['gio_bat_dau']) ?>" 
                                        data-gio-ket-thuc="<?= htmlspecialchars($ca['gio_ket_thuc']) ?>" 
                                        <?= (isset($caHoc['id_ca']) && $caHoc['id_ca'] == $ca['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ca['ten_ca']) ?> (<?= htmlspecialchars($ca['gio_bat_dau']) ?> - <?= htmlspecialchars($ca['gio_ket_thuc']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_phong_moi" class="form-label">Phòng học mới <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_phong_moi" name="id_phong_moi" required>
                            <option value="">-- Chọn phòng học --</option>
                            <?php foreach ($phongHocList as $phong): ?>
                                <option value="<?= $phong['id'] ?>" <?= (isset($caHoc['id_phong']) && $caHoc['id_phong'] == $phong['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($phong['ten_phong']) ?> (Sức chứa: <?= $phong['suc_chua'] ?> người)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ly_do" class="form-label">Lý do đổi lịch <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="ly_do" name="ly_do" rows="4" placeholder="Nhập lý do đổi lịch..." required></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Gửi yêu cầu
                        </button>
                        <a href="?act=giang-vien-dashboard" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        const ngayDoi = document.getElementById('ngay_doi');
        const thuTrongTuanMoi = document.getElementById('thu_trong_tuan_moi');
        const thuTrongTuanMoiGroup = document.getElementById('thu_trong_tuan_moi_group');
        const thuAutoInfo = document.getElementById('thu_auto_info');
        
        // Hàm tính thứ từ ngày
        function tinhThuTuNgay(ngay) {
            if (!ngay) return null;
            const date = new Date(ngay);
            const thu = date.getDay(); // 0 = Chủ nhật, 1 = Thứ 2, ..., 6 = Thứ 7
            const thuMap = {
                0: 'Chủ nhật',
                1: 'Thứ 2',
                2: 'Thứ 3',
                3: 'Thứ 4',
                4: 'Thứ 5',
                5: 'Thứ 6',
                6: 'Thứ 7'
            };
            return thuMap[thu] || null;
        }
        
        // Xử lý khi thay đổi ngày đổi
        function handleNgayDoiChange() {
            const ngay = ngayDoi.value;
            if (ngay) {
                // Có ngày: tự động tính thứ và disable field thứ
                const thuTuNgay = tinhThuTuNgay(ngay);
                if (thuTuNgay) {
                    thuTrongTuanMoi.value = thuTuNgay;
                    thuTrongTuanMoi.disabled = true;
                    thuTrongTuanMoi.style.backgroundColor = '#e9ecef';
                    thuAutoInfo.style.display = 'block';
                }
            } else {
                // Không có ngày: enable field thứ để chọn thủ công (đổi toàn bộ lịch)
                thuTrongTuanMoi.disabled = false;
                thuTrongTuanMoi.style.backgroundColor = '';
                thuAutoInfo.style.display = 'none';
            }
        }
        
        // Lắng nghe sự kiện thay đổi ngày đổi
        ngayDoi.addEventListener('change', handleNgayDoiChange);
        
        // Xử lý khi load trang nếu đã có ngày đổi
        if (ngayDoi.value) {
            handleNgayDoiChange();
        }
    })();
    </script>
</body>
</html>

