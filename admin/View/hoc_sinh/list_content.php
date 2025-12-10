<?php
$pageTitle = 'Quản lý Học sinh';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Quản lý học sinh</h2>
    </div>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-hoc-sinh">
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
                    <a href="?act=admin-list-hoc-sinh" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($hocSinh)): ?>
        <div class="empty-state">
            <p>Không tìm thấy học sinh nào.</p>
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
                    <?php foreach ($hocSinh as $hs): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($hs['ma_nguoi_dung'] ?? 'N/A') ?></strong></td>
                            <td><strong><?= htmlspecialchars($hs['ho_ten']) ?></strong></td>
                            <td><?= htmlspecialchars($hs['email']) ?></td>
                            <td><?= htmlspecialchars($hs['so_dien_thoai'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($hs['dia_chi'] ?? 'N/A') ?></td>
                            <td>
                                <span class="<?= $hs['trang_thai'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $hs['trang_thai'] == 1 ? 'Hoạt động' : 'Khóa' ?>
                                </span>
                            </td>
                            <td><?= isset($hs['ngay_tao']) ? date('d/m/Y', strtotime($hs['ngay_tao'])) : 'N/A' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-view-lop-hoc-hoc-sinh&id=<?= $hs['id'] ?>" 
                                       class="btn btn-info btn-sm" 
                                       style="background: #17a2b8; color: white; margin-right: 5px;">Lớp học</a>
                                    <a href="?act=admin-view-hoc-sinh&id=<?= $hs['id'] ?>" 
                                       class="btn btn-info btn-sm" 
                                       style="background: #17a2b8; color: white;">Xem</a>
                                    <?php if ($hs['trang_thai'] == 1): ?>
                                        <a href="?act=admin-delete-hoc-sinh&id=<?= $hs['id'] ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Bạn có chắc chắn muốn ẩn học sinh này?')">Ẩn</a>
                                    <?php else: ?>
                                        <a href="?act=admin-toggle-hoc-sinh-status&id=<?= $hs['id'] ?>" 
                                           class="btn btn-success btn-sm" 
                                           style="background: #28a745; color: white;"
                                           onclick="return confirm('Bạn có chắc chắn muốn hiện học sinh này?')">Hiện</a>
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
                    <a href="?act=admin-list-hoc-sinh&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?act=admin-list-hoc-sinh&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-hoc-sinh&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

