<?php
$pageTitle = 'Quản lý Lớp học';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Quản lý lớp học</h2>
        <div class="page-actions">
            <a href="?act=admin-add-lop-hoc" class="btn btn-primary">+ Thêm lớp học</a>
        </div>
    </div>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-lop-hoc">
            <div class="filter-group">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Tìm theo tên lớp...">
                </div>
                <div class="form-group">
                    <label>Khóa học</label>
                    <select name="id_khoa_hoc" class="form-control">
                        <option value="">Tất cả khóa học</option>
                        <?php foreach ($khoaHocList as $kh): ?>
                            <option value="<?= $kh['id'] ?>" 
                                    <?= ($id_khoa_hoc ?? '') == $kh['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kh['ten_khoa_hoc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                </div>
                <?php if (!empty($search) || !empty($id_khoa_hoc)): ?>
                <div class="form-group">
                    <a href="?act=admin-list-lop-hoc" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($lopHoc)): ?>
        <div class="empty-state">
            <p>Không tìm thấy lớp học nào.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tên lớp</th>
                        <th>Khóa học</th>
                        <th>Số lượng tối đa</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lopHoc as $lh): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($lh['ten_lop']) ?></strong></td>
                            <td><?= htmlspecialchars($lh['ten_khoa_hoc'] ?? 'N/A') ?></td>
                            <td><?= $lh['so_luong_toi_da'] ? number_format($lh['so_luong_toi_da']) : 'Không giới hạn' ?></td>
                            <td>
                                <?php 
                                $trangThaiClass = 'status-active';
                                if ($lh['trang_thai'] == 'Kết thúc') {
                                    $trangThaiClass = 'status-inactive';
                                } elseif ($lh['trang_thai'] == 'Đang học') {
                                    $trangThaiClass = 'status-active';
                                } else {
                                    $trangThaiClass = 'status-warning';
                                }
                                ?>
                                <span class="<?= $trangThaiClass ?>">
                                    <?= htmlspecialchars($lh['trang_thai'] ?? 'Chưa học') ?>
                                </span>
                            </td>
                            <td><?= isset($lh['ngay_tao']) ? date('d/m/Y', strtotime($lh['ngay_tao'])) : 'N/A' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-edit-lop-hoc&id=<?= $lh['id'] ?>" 
                                       class="btn btn-warning btn-sm">Sửa</a>
                                    <?php if (($lh['trang_thai'] ?? 'Chưa học') == 'Kết thúc'): ?>
                                        <a href="?act=admin-delete-lop-hoc&id=<?= $lh['id'] ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa lớp học này?')">Xóa</a>
                                    <?php endif; ?>
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
                    <a href="?act=admin-list-lop-hoc&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>&id_khoa_hoc=<?= $id_khoa_hoc ?? '' ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?act=admin-list-lop-hoc&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&id_khoa_hoc=<?= $id_khoa_hoc ?? '' ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-lop-hoc&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>&id_khoa_hoc=<?= $id_khoa_hoc ?? '' ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>


