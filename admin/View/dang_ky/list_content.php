<?php
$pageTitle = 'Quản lý Đăng ký';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Quản lý đăng ký</h2>
    </div>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-dang-ky">
            <div class="filter-group">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Tên học sinh, email, lớp học...">
                </div>
                <div class="form-group">
                    <label>Lọc theo lớp học</label>
                    <select name="id_lop" class="form-control">
                        <option value="">Tất cả lớp học</option>
                        <?php if (isset($lopHocList) && !empty($lopHocList)): ?>
                            <?php foreach ($lopHocList as $lh): ?>
                                <option value="<?= $lh['id'] ?>" 
                                        <?= (isset($id_lop) && $id_lop == $lh['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($lh['ten_lop']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lọc theo trạng thái</label>
                    <select name="trang_thai" class="form-control">
                        <option value="">Tất cả trạng thái</option>
                        <option value="Chờ xác nhận" <?= (isset($trang_thai) && $trang_thai == 'Chờ xác nhận') ? 'selected' : '' ?>>Chờ xác nhận</option>
                        <option value="Đã xác nhận" <?= (isset($trang_thai) && $trang_thai == 'Đã xác nhận') ? 'selected' : '' ?>>Đã xác nhận</option>
                        <option value="Đã hủy" <?= (isset($trang_thai) && $trang_thai == 'Đã hủy') ? 'selected' : '' ?>>Đã hủy</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                </div>
                <?php if (!empty($search) || !empty($id_lop) || !empty($trang_thai)): ?>
                <div class="form-group">
                    <a href="?act=admin-list-dang-ky" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($dangKy)): ?>
        <div class="empty-state">
            <p>Không tìm thấy đăng ký nào.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Học sinh</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Lớp học</th>
                        <th>Khóa học</th>
                        <th>Ngày đăng ký</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dangKy as $dk): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($dk['ten_hoc_sinh'] ?? 'N/A') ?></strong></td>
                            <td><?= htmlspecialchars($dk['email_hoc_sinh'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($dk['so_dien_thoai'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($dk['ten_lop'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($dk['ten_khoa_hoc'] ?? 'N/A') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($dk['ngay_dang_ky'])) ?></td>
                            <td>
                                <?php 
                                $trangThaiClass = 'status-warning';
                                if ($dk['trang_thai'] == 'Đã xác nhận') {
                                    $trangThaiClass = 'status-active';
                                } elseif ($dk['trang_thai'] == 'Đã hủy') {
                                    $trangThaiClass = 'status-inactive';
                                }
                                ?>
                                <span class="<?= $trangThaiClass ?>">
                                    <?= htmlspecialchars($dk['trang_thai'] ?? 'Chờ xác nhận') ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-edit-dang-ky&id=<?= $dk['id'] ?>" 
                                       class="btn btn-warning btn-sm">Sửa</a>
                                    <a href="?act=admin-delete-dang-ky&id=<?= $dk['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa đăng ký này?')">Xóa</a>
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
                    <a href="?act=admin-list-dang-ky&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>&id_lop=<?= $id_lop ?? '' ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?act=admin-list-dang-ky&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&id_lop=<?= $id_lop ?? '' ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-dang-ky&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>&id_lop=<?= $id_lop ?? '' ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

