<div class="page-container">
    <div class="page-header">
        <h2>Quản lý danh mục</h2>
        <div class="page-actions">
            <a href="?act=admin-add-danh-muc" class="btn btn-primary">+ Thêm danh mục</a>
        </div>
    </div>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-danh-muc">
            <div class="filter-group">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Tìm theo tên danh mục...">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                </div>
                <?php if (!empty($search)): ?>
                <div class="form-group">
                    <a href="?act=admin-list-danh-muc" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($danhMuc)): ?>
        <div class="empty-state">
            <p>Không tìm thấy danh mục nào.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tên danh mục</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($danhMuc as $dm): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($dm['ten_danh_muc']) ?></strong></td>
                            <td><?= htmlspecialchars($dm['mo_ta'] ?? 'N/A') ?></td>
                            <td>
                                <span class="<?= $dm['trang_thai'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $dm['trang_thai'] == 1 ? 'Hiển thị' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td><?= isset($dm['ngay_tao']) ? date('d/m/Y', strtotime($dm['ngay_tao'])) : 'N/A' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-edit-danh-muc&id=<?= $dm['id'] ?>" 
                                       class="btn btn-warning btn-sm">Sửa</a>
                                    <?php if ($dm['trang_thai'] == 1): ?>
                                        <a href="?act=admin-delete-danh-muc&id=<?= $dm['id'] ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Bạn có chắc chắn muốn ẩn danh mục này?')">Ẩn</a>
                                    <?php else: ?>
                                        <a href="?act=admin-toggle-danh-muc-status&id=<?= $dm['id'] ?>" 
                                           class="btn btn-success btn-sm" 
                                           style="background: #28a745; color: white;"
                                           onclick="return confirm('Bạn có chắc chắn muốn hiện danh mục này?')">Hiện</a>
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
                    <a href="?act=admin-list-danh-muc&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?act=admin-list-danh-muc&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-danh-muc&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

