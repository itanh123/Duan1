<?php
$pageTitle = 'Chi tiết yêu cầu đổi lịch';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Chi tiết yêu cầu đổi lịch</h2>
        <div class="page-actions">
            <a href="?act=admin-list-yeu-cau-doi-lich" class="btn btn-secondary">← Quay lại</a>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h3>Thông tin yêu cầu</h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Giảng viên:</label>
                    <span><strong><?= htmlspecialchars($yeuCau['ten_giang_vien']) ?></strong></span>
                </div>
                <div class="info-item">
                    <label>Lớp học:</label>
                    <span><?= htmlspecialchars($yeuCau['ten_lop']) ?></span>
                </div>
                <div class="info-item">
                    <label>Khóa học:</label>
                    <span><?= htmlspecialchars($yeuCau['ten_khoa_hoc']) ?></span>
                </div>
                <div class="info-item">
                    <label>Trạng thái:</label>
                    <span>
                        <?php
                        $badgeClass = 'badge-warning';
                        $badgeText = 'Chờ duyệt';
                        if ($yeuCau['trang_thai'] == 'da_duyet') {
                            $badgeClass = 'badge-success';
                            $badgeText = 'Đã duyệt';
                        } elseif ($yeuCau['trang_thai'] == 'tu_choi') {
                            $badgeClass = 'badge-danger';
                            $badgeText = 'Từ chối';
                        }
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                    </span>
                </div>
                <div class="info-item">
                    <label>Ngày tạo:</label>
                    <span><?= date('d/m/Y H:i', strtotime($yeuCau['ngay_tao'])) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h3>Lịch hiện tại</h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Thứ:</label>
                    <span><?= htmlspecialchars($yeuCau['thu_cu']) ?></span>
                </div>
                <div class="info-item">
                    <label>Ca học:</label>
                    <span><?= htmlspecialchars($yeuCau['ten_ca_cu']) ?></span>
                </div>
                <div class="info-item">
                    <label>Giờ học:</label>
                    <span><?= htmlspecialchars($yeuCau['gio_bat_dau_cu']) ?> - <?= htmlspecialchars($yeuCau['gio_ket_thuc_cu']) ?></span>
                </div>
                <div class="info-item">
                    <label>Phòng học:</label>
                    <span><?= htmlspecialchars($yeuCau['ten_phong_cu']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h3>Lịch mới yêu cầu</h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Thứ:</label>
                    <span><strong><?= htmlspecialchars($yeuCau['thu_trong_tuan_moi']) ?></strong></span>
                </div>
                <div class="info-item">
                    <label>Ca học:</label>
                    <span><strong><?= htmlspecialchars($yeuCau['ten_ca_moi']) ?></strong></span>
                </div>
                <div class="info-item">
                    <label>Giờ học:</label>
                    <span><strong><?= htmlspecialchars($yeuCau['gio_bat_dau_moi']) ?> - <?= htmlspecialchars($yeuCau['gio_ket_thuc_moi']) ?></strong></span>
                </div>
                <div class="info-item">
                    <label>Phòng học:</label>
                    <span><strong><?= htmlspecialchars($yeuCau['ten_phong_moi']) ?></strong></span>
                </div>
                <div class="info-item">
                    <label>Ngày đổi:</label>
                    <span><?= $yeuCau['ngay_doi'] ? date('d/m/Y', strtotime($yeuCau['ngay_doi'])) : 'Toàn bộ lịch' ?></span>
                </div>
                <div class="info-item">
                    <label>Lý do:</label>
                    <span><?= htmlspecialchars($yeuCau['ly_do'] ?? '') ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($trungLich)): ?>
        <div class="alert alert-error">
            <strong>⚠️ Cảnh báo trùng lịch!</strong><br>
            Lịch mới bị trùng với các lịch sau:
            <ul style="margin-top: 10px;">
                <?php foreach ($trungLich as $tl): ?>
                    <li>
                        Lớp: <?= htmlspecialchars($tl['ten_lop'] ?? 'N/A') ?> - 
                        Thứ: <?= htmlspecialchars($tl['thu_trong_tuan']) ?> - 
                        Phòng: <?= htmlspecialchars($tl['ten_phong'] ?? 'N/A') ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($yeuCau['trang_thai'] == 'cho_duyet'): ?>
        <div class="card">
            <div class="card-header">
                <h3>Xử lý yêu cầu</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="?act=admin-duyet-yeu-cau-doi-lich" style="margin-bottom: 20px;">
                    <input type="hidden" name="id" value="<?= $yeuCau['id'] ?>">
                    <div class="form-group">
                        <label>Ghi chú (tùy chọn)</label>
                        <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Nhập ghi chú..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-success" 
                            <?= !empty($trungLich) ? 'onclick="return confirm(\'Lịch mới bị trùng! Bạn có chắc chắn muốn duyệt?\')"' : '' ?>>
                        ✓ Duyệt yêu cầu
                    </button>
                </form>

                <form method="POST" action="?act=admin-tu-choi-yeu-cau-doi-lich">
                    <input type="hidden" name="id" value="<?= $yeuCau['id'] ?>">
                    <div class="form-group">
                        <label>Lý do từ chối</label>
                        <textarea name="ghi_chu" class="form-control" rows="3" placeholder="Nhập lý do từ chối..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">✗ Từ chối yêu cầu</button>
                </form>
            </div>
        </div>
    <?php elseif ($yeuCau['trang_thai'] == 'da_duyet'): ?>
        <div class="card">
            <div class="card-header">
                <h3>Quản lý yêu cầu đã duyệt</h3>
            </div>
            <div class="card-body">
                <?php if ($yeuCau['ghi_chu_admin']): ?>
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Ghi chú của admin:</label>
                        <p style="background: #f8f9fa; padding: 10px; border-radius: 5px;"><?= htmlspecialchars($yeuCau['ghi_chu_admin']) ?></p>
                    </div>
                <?php endif; ?>
                
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <form method="POST" action="?act=admin-xac-nhan-thay-doi-lich" style="flex: 1; min-width: 200px;">
                        <input type="hidden" name="id" value="<?= $yeuCau['id'] ?>">
                        <div class="form-group">
                            <label>Ghi chú xác nhận (tùy chọn)</label>
                            <textarea name="ghi_chu" class="form-control" rows="2" placeholder="Nhập ghi chú xác nhận..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success" style="width: 100%;">
                            ✓ Xác nhận thay đổi
                        </button>
                    </form>
                    
                    <form method="POST" action="?act=admin-hoan-nguyen-lich" style="flex: 1; min-width: 200px;">
                        <input type="hidden" name="id" value="<?= $yeuCau['id'] ?>">
                        <div class="form-group">
                            <label>Ghi chú hoàn nguyên (tùy chọn)</label>
                            <textarea name="ghi_chu" class="form-control" rows="2" placeholder="Nhập ghi chú hoàn nguyên..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning" style="width: 100%;"
                                onclick="return confirm('Bạn có chắc chắn muốn hoàn nguyên lịch về trạng thái cũ?')">
                            ↺ Hoàn nguyên lịch
                        </button>
                    </form>
                    
                    <form method="POST" action="?act=admin-huy-yeu-cau-doi-lich" style="flex: 1; min-width: 200px;">
                        <input type="hidden" name="id" value="<?= $yeuCau['id'] ?>">
                        <div class="form-group">
                            <label>Lý do hủy</label>
                            <textarea name="ghi_chu" class="form-control" rows="2" placeholder="Nhập lý do hủy..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger" style="width: 100%;"
                                onclick="return confirm('Bạn có chắc chắn muốn hủy yêu cầu này?')">
                            ✗ Hủy yêu cầu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php elseif ($yeuCau['trang_thai'] == 'tu_choi'): ?>
        <div class="card">
            <div class="card-header">
                <h3>Yêu cầu đã bị từ chối/hủy</h3>
            </div>
            <div class="card-body">
                <?php if ($yeuCau['ghi_chu_admin']): ?>
                    <div class="form-group">
                        <label>Ghi chú:</label>
                        <p style="background: #f8f9fa; padding: 10px; border-radius: 5px;"><?= htmlspecialchars($yeuCau['ghi_chu_admin']) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (strpos($yeuCau['ghi_chu_admin'] ?? '', '[Đã hủy bởi admin]') !== false || 
                          strpos($yeuCau['ghi_chu_admin'] ?? '', '[Đã hoàn nguyên bởi admin]') !== false): ?>
                    <p class="text-muted">Yêu cầu này đã bị hủy hoặc hoàn nguyên bởi admin.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-item label {
    font-weight: 600;
    color: #666;
}

.info-item span {
    color: #333;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 8px 8px 0 0;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
}

.card-body {
    padding: 20px;
}
</style>

