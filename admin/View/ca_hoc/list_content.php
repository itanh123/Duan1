<?php
$pageTitle = 'Quản lý Ca học';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Quản lý ca học</h2>
        <div class="page-actions">
            <a href="?act=admin-add-ca-hoc" class="btn btn-primary">+ Thêm ca học</a>
        </div>
    </div>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-ca-hoc">
            <div class="filter-group">
                <div class="form-group">
                    <label>Tìm kiếm (Phòng học/Ghi chú)</label>
                    <input type="text" name="search" class="form-control" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Nhập từ khóa...">
                </div>
                <div class="form-group">
                    <label>Lọc theo lớp học</label>
                    <select name="id_lop" class="form-control">
                        <option value="">Tất cả lớp học</option>
                        <?php foreach ($lopHocList as $lh): ?>
                            <option value="<?= $lh['id'] ?>" 
                                    <?= ($id_lop ?? '') == $lh['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($lh['ten_lop']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                </div>
                <?php if (!empty($search) || !empty($id_lop)): ?>
                <div class="form-group">
                    <a href="?act=admin-list-ca-hoc" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($caHoc)): ?>
        <div class="empty-state">
            <p>Không tìm thấy ca học nào.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Lớp học</th>
                        <th>Khóa học</th>
                        <th>Ca học</th>
                        <th>Thứ</th>
                        <th>Giờ học</th>
                        <th>Phòng học</th>
                        <th>Giảng viên</th>
                        <th>Ghi chú</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($caHoc as $ch): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ch['ten_lop'] ?? 'N/A') ?></strong></td>
                            <td><?= htmlspecialchars($ch['ten_khoa_hoc'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($ch['ten_ca'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($ch['thu_trong_tuan']) ?></td>
                            <td><?= isset($ch['gio_bat_dau']) && isset($ch['gio_ket_thuc']) ? date('H:i', strtotime($ch['gio_bat_dau'])) . ' - ' . date('H:i', strtotime($ch['gio_ket_thuc'])) : 'N/A' ?></td>
                            <td><?= htmlspecialchars($ch['ten_phong'] ?? 'Chưa có') ?></td>
                            <td><?= htmlspecialchars($ch['ten_giang_vien'] ?? 'Chưa phân công') ?></td>
                            <td><?= htmlspecialchars($ch['ghi_chu'] ?? '') ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-edit-ca-hoc&id=<?= $ch['id'] ?>" 
                                       class="btn btn-warning btn-sm">Sửa</a>
                                    <a href="?act=admin-delete-ca-hoc&id=<?= $ch['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa ca học này?')">Xóa</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?act=admin-list-ca-hoc&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>&id_lop=<?= $id_lop ?? '' ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?act=admin-list-ca-hoc&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&id_lop=<?= $id_lop ?? '' ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-ca-hoc&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>&id_lop=<?= $id_lop ?? '' ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>


