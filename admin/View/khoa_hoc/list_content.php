<div class="page-container">
    <div class="page-header">
        <h2>Quản lý khóa học</h2>
        <div class="page-actions">
            <a href="?act=admin-add-khoa-hoc" class="btn btn-primary">+ Thêm khóa học</a>
        </div>
    </div>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-khoa-hoc">
            <div class="filter-group">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Nhập tên khóa học...">
                </div>
                <div class="form-group">
                    <label>Danh mục</label>
                    <select name="id_danh_muc" class="form-control">
                        <option value="">Tất cả danh mục</option>
                        <?php foreach ($danhMuc as $dm): ?>
                            <option value="<?= $dm['id'] ?>" 
                                    <?= ($id_danh_muc ?? '') == $dm['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($dm['ten_danh_muc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                </div>
                <?php if (!empty($search) || !empty($id_danh_muc)): ?>
                <div class="form-group">
                    <a href="?act=admin-list-khoa-hoc" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($khoaHoc)): ?>
        <div class="empty-state">
            <p>Không tìm thấy khóa học nào.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Hình ảnh</th>
                        <th>Tên khóa học</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($khoaHoc as $kh): ?>
                        <tr>
                            <td>
                                <?php if ($kh['hinh_anh']): ?>
                                    <img src="./uploads/<?= htmlspecialchars($kh['hinh_anh']) ?>" 
                                         alt="<?= htmlspecialchars($kh['ten_khoa_hoc']) ?>" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <span style="color: #999;">Chưa có ảnh</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($kh['ten_khoa_hoc']) ?></td>
                            <td><?= htmlspecialchars($kh['ten_danh_muc'] ?? 'N/A') ?></td>
                            <td><?= number_format($kh['gia'], 0, ',', '.') ?> đ</td>
                            <td>
                                <span class="<?= $kh['trang_thai'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                    <?= $kh['trang_thai'] == 1 ? 'Hiển thị' : 'Ẩn' ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($kh['ngay_tao'])) ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-edit-khoa-hoc&id=<?= $kh['id'] ?>" 
                                       class="btn btn-warning btn-sm">Sửa</a>
                                    <a href="?act=admin-delete-khoa-hoc&id=<?= $kh['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa khóa học này?')">Xóa</a>
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
                    <a href="?act=admin-list-khoa-hoc&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>&id_danh_muc=<?= $id_danh_muc ?? '' ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?act=admin-list-khoa-hoc&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&id_danh_muc=<?= $id_danh_muc ?? '' ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-khoa-hoc&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>&id_danh_muc=<?= $id_danh_muc ?? '' ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

