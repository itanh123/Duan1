<div class="page-container">
    <div class="page-header">
        <h2>Quản lý bình luận</h2>
    </div>

    <div class="filter-section">
        <form method="GET" action="">
            <input type="hidden" name="act" value="admin-list-binh-luan">
            <div class="filter-group">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Tìm theo nội dung, học sinh, khóa học...">
                </div>
                <div class="form-group">
                    <label>Khóa học</label>
                    <select name="id_khoa_hoc" class="form-control">
                        <option value="">Tất cả khóa học</option>
                        <?php foreach ($khoaHocList ?? [] as $kh): ?>
                            <option value="<?= $kh['id'] ?>" <?= (isset($id_khoa_hoc) && $id_khoa_hoc == $kh['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kh['ten_khoa_hoc']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="trang_thai" class="form-control">
                        <option value="">Tất cả</option>
                        <option value="Hiển thị" <?= (isset($trang_thai) && $trang_thai == 'Hiển thị') ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="Ẩn" <?= (isset($trang_thai) && $trang_thai == 'Ẩn') ? 'selected' : '' ?>>Ẩn</option>
                        <option value="Đã xóa" <?= (isset($trang_thai) && $trang_thai == 'Đã xóa') ? 'selected' : '' ?>>Đã xóa</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                </div>
                <?php if (!empty($search) || !empty($id_khoa_hoc) || !empty($trang_thai)): ?>
                <div class="form-group">
                    <a href="?act=admin-list-binh-luan" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if (empty($binhLuan)): ?>
        <div class="empty-state">
            <p>Không tìm thấy bình luận nào.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Khóa học</th>
                        <th>Học sinh</th>
                        <th>Nội dung</th>
                        <th>Đánh giá</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($binhLuan as $bl): ?>
                        <tr>
                            <td><?= htmlspecialchars($bl['ten_khoa_hoc'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($bl['ten_hoc_sinh'] ?? 'N/A') ?></td>
                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($bl['noi_dung']) ?>">
                                <?= htmlspecialchars($bl['noi_dung']) ?>
                            </td>
                            <td>
                                <?php if ($bl['danh_gia']): ?>
                                    <span style="color: #ffc107;">
                                        <?= str_repeat('★', $bl['danh_gia']) ?><?= str_repeat('☆', 5 - $bl['danh_gia']) ?>
                                    </span>
                                    (<?= $bl['danh_gia'] ?>/5)
                                <?php else: ?>
                                    <span style="color: #999;">Chưa đánh giá</span>
                                <?php endif; ?>
                            </td>
                            <td><?= isset($bl['ngay_tao']) ? date('d/m/Y H:i', strtotime($bl['ngay_tao'])) : 'N/A' ?></td>
                            <td>
                                <?php
                                $statusClass = 'status-hien-thi';
                                if ($bl['trang_thai'] == 'Ẩn') $statusClass = 'status-an';
                                if ($bl['trang_thai'] == 'Đã xóa') $statusClass = 'status-da-xoa';
                                ?>
                                <span class="<?= $statusClass ?>">
                                    <?= htmlspecialchars($bl['trang_thai']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-tra-loi-binh-luan&id=<?= $bl['id'] ?>" 
                                       class="btn btn-primary btn-sm">💬 Trả lời</a>
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
                    <a href="?act=admin-list-binh-luan&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>&id_khoa_hoc=<?= urlencode($id_khoa_hoc ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>">« Trước</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                        <a href="?act=admin-list-binh-luan&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&id_khoa_hoc=<?= urlencode($id_khoa_hoc ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>" 
                           class="<?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                        <span>...</span>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?act=admin-list-binh-luan&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>&id_khoa_hoc=<?= urlencode($id_khoa_hoc ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>">Sau »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

