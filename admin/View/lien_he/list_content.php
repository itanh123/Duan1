<div class="page-container">
    <div class="page-header">
        <h2>Quản lý liên hệ</h2>
        <div class="page-actions">
            <a href="?act=admin-add-lien-he" class="btn btn-primary">+ Thêm liên hệ</a>
        </div>
    </div>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-lien-he">
            <div class="filter-group">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Tìm theo tên, loại, giá trị...">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                </div>
                <?php if (!empty($search)): ?>
                <div class="form-group">
                    <a href="?act=admin-list-lien-he" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($lienHe)): ?>
        <div class="empty-state">
            <p>Không tìm thấy liên hệ nào.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Icon</th>
                        <th>Tên</th>
                        <th>Loại</th>
                        <th>Giá trị</th>
                        <th>Mô tả</th>
                        <th>Thứ tự</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lienHe as $lh): ?>
                        <tr>
                            <td><?= htmlspecialchars($lh['icon'] ?? '📱') ?></td>
                            <td><strong><?= htmlspecialchars($lh['ten']) ?></strong></td>
                            <td>
                                <span class="badge badge-info"><?= htmlspecialchars($lh['loai']) ?></span>
                            </td>
                            <td>
                                <?php if (strpos($lh['gia_tri'], 'http') === 0): ?>
                                    <a href="<?= htmlspecialchars($lh['gia_tri']) ?>" target="_blank" style="color: #007bff;">
                                        <?= htmlspecialchars($lh['gia_tri']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($lh['gia_tri']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($lh['mo_ta'] ?? 'N/A') ?></td>
                            <td><?= $lh['thu_tu'] ?></td>
                            <td>
                                <span class="<?= $lh['trang_thai'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $lh['trang_thai'] == 1 ? 'Hiển thị' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-edit-lien-he&id=<?= $lh['id'] ?>" 
                                       class="btn btn-warning btn-sm">Sửa</a>
                                    <?php if ($lh['trang_thai'] == 1): ?>
                                        <a href="?act=admin-delete-lien-he&id=<?= $lh['id'] ?>" 
                                           class="btn btn-danger btn-sm" 
                                           onclick="return confirm('Bạn có chắc chắn muốn ẩn liên hệ này?')">Ẩn</a>
                                    <?php else: ?>
                                        <a href="?act=admin-toggle-lien-he-status&id=<?= $lh['id'] ?>" 
                                           class="btn btn-success btn-sm" 
                                           style="background: #28a745; color: white;"
                                           onclick="return confirm('Bạn có chắc chắn muốn hiện liên hệ này?')">Hiện</a>
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
                    <a href="?act=admin-list-lien-he&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?act=admin-list-lien-he&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-lien-he&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

