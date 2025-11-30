<?php
$pageTitle = 'Quản lý Yêu cầu đổi lịch';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Quản lý yêu cầu đổi lịch</h2>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-yeu-cau-doi-lich">
            <div class="filter-group">
                <div class="form-group">
                    <label>Lọc theo trạng thái</label>
                    <select name="trang_thai" class="form-control">
                        <option value="">Tất cả</option>
                        <option value="cho_duyet" <?= ($trang_thai ?? '') == 'cho_duyet' ? 'selected' : '' ?>>Chờ duyệt</option>
                        <option value="da_duyet" <?= ($trang_thai ?? '') == 'da_duyet' ? 'selected' : '' ?>>Đã duyệt</option>
                        <option value="tu_choi" <?= ($trang_thai ?? '') == 'tu_choi' ? 'selected' : '' ?>>Từ chối</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Lọc</button>
                </div>
                <?php if (!empty($trang_thai)): ?>
                <div class="form-group">
                    <a href="?act=admin-list-yeu-cau-doi-lich" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($yeuCauList)): ?>
        <div class="empty-state">
            <p>Không tìm thấy yêu cầu đổi lịch nào.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Giảng viên</th>
                    <th>Lớp học</th>
                    <th>Lịch cũ</th>
                    <th>Lịch mới</th>
                    <th>Ngày đổi</th>
                    <th>Lý do</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($yeuCauList as $yc): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($yc['ten_giang_vien']) ?></strong></td>
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
                            $badgeClass = 'badge-warning';
                            $badgeText = 'Chờ duyệt';
                            if ($yc['trang_thai'] == 'da_duyet') {
                                $badgeClass = 'badge-success';
                                $badgeText = 'Đã duyệt';
                            } elseif ($yc['trang_thai'] == 'tu_choi') {
                                $badgeClass = 'badge-danger';
                                $badgeText = 'Từ chối';
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                        </td>
                        <td>
                            <small><?= date('d/m/Y H:i', strtotime($yc['ngay_tao'])) ?></small>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="?act=admin-detail-yeu-cau-doi-lich&id=<?= $yc['id'] ?>" 
                                   class="btn btn-primary btn-sm">Chi tiết</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?act=admin-list-yeu-cau-doi-lich&page=<?= $page - 1 ?><?= !empty($trang_thai) ? '&trang_thai=' . urlencode($trang_thai) : '' ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <?php if ($i == $page): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?act=admin-list-yeu-cau-doi-lich&page=<?= $i ?><?= !empty($trang_thai) ? '&trang_thai=' . urlencode($trang_thai) : '' ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-yeu-cau-doi-lich&page=<?= $page + 1 ?><?= !empty($trang_thai) ? '&trang_thai=' . urlencode($trang_thai) : '' ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

