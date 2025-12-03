<?php
$pageTitle = 'Quản lý Giảng viên';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Quản lý giảng viên</h2>
        <div class="page-actions">
            <a href="?act=admin-add-giang-vien" class="btn btn-primary">+ Thêm giảng viên</a>
        </div>
    </div>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-giang-vien">
            <div class="filter-group">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Tìm theo mã người dùng, tên, email, số điện thoại...">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                </div>
                <?php if (!empty($search)): ?>
                <div class="form-group">
                    <a href="?act=admin-list-giang-vien" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($giangVien)): ?>
        <div class="empty-state">
            <p>Không tìm thấy giảng viên nào.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Mã người dùng</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Địa chỉ</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($giangVien as $gv): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($gv['ma_nguoi_dung'] ?? 'N/A') ?></strong></td>
                            <td><strong><?= htmlspecialchars($gv['ho_ten']) ?></strong></td>
                            <td><?= htmlspecialchars($gv['email']) ?></td>
                            <td><?= htmlspecialchars($gv['so_dien_thoai'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($gv['dia_chi'] ?? 'N/A') ?></td>
                            <td>
                                <span class="<?= $gv['trang_thai'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $gv['trang_thai'] == 1 ? 'Hoạt động' : 'Khóa' ?>
                                </span>
                            </td>
                            <td><?= isset($gv['ngay_tao']) ? date('d/m/Y', strtotime($gv['ngay_tao'])) : 'N/A' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-view-lop-hoc-giang-vien&id=<?= $gv['id'] ?>" 
                                       class="btn btn-info btn-sm" 
                                       style="background: #17a2b8; color: white; margin-right: 5px;">Lớp học</a>
                                    <a href="?act=admin-edit-giang-vien&id=<?= $gv['id'] ?>" 
                                       class="btn btn-warning btn-sm">Sửa</a>
                                    <a href="?act=admin-delete-giang-vien&id=<?= $gv['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa giảng viên này?')">Xóa</a>
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
                    <a href="?act=admin-list-giang-vien&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?act=admin-list-giang-vien&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-giang-vien&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

