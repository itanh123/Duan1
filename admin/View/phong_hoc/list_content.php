<div class="page-container">
    <div class="page-header">
        <h2>Quản lý phòng học</h2>
        <div class="page-actions">
            <a href="?act=admin-add-phong-hoc" class="btn btn-primary">+ Thêm phòng học</a>
        </div>
    </div>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-phong-hoc">
            <div class="filter-group">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Tìm theo tên phòng, mô tả...">
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="trang_thai" class="form-control">
                        <option value="">Tất cả</option>
                        <option value="Sử dụng" <?= (isset($trang_thai) && $trang_thai == 'Sử dụng') ? 'selected' : '' ?>>Sử dụng</option>
                        <option value="Bảo trì" <?= (isset($trang_thai) && $trang_thai == 'Bảo trì') ? 'selected' : '' ?>>Bảo trì</option>
                        <option value="Khóa" <?= (isset($trang_thai) && $trang_thai == 'Khóa') ? 'selected' : '' ?>>Khóa</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                </div>
                <?php if (!empty($search) || !empty($trang_thai)): ?>
                <div class="form-group">
                    <a href="?act=admin-list-phong-hoc" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($phongHoc)): ?>
        <div class="empty-state">
            <p>Không tìm thấy phòng học nào.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tên phòng</th>
                        <th>Sức chứa</th>
                        <th>Mô tả</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($phongHoc as $ph): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ph['ten_phong']) ?></strong></td>
                            <td><?= $ph['suc_chua'] ?> người</td>
                            <td><?= htmlspecialchars($ph['mo_ta'] ?? 'N/A') ?></td>
                            <td>
                                <?php
                                $statusClass = 'status-su-dung';
                                if ($ph['trang_thai'] == 'Bảo trì') $statusClass = 'status-bao-tri';
                                if ($ph['trang_thai'] == 'Khóa') $statusClass = 'status-khoa';
                                ?>
                                <span class="<?= $statusClass ?>">
                                    <?= htmlspecialchars($ph['trang_thai']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-edit-phong-hoc&id=<?= $ph['id'] ?>" 
                                       class="btn btn-warning btn-sm">Sửa</a>
                                    <a href="?act=admin-delete-phong-hoc&id=<?= $ph['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa phòng học này?')">Xóa</a>
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
                    <a href="?act=admin-list-phong-hoc&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?act=admin-list-phong-hoc&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-phong-hoc&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

