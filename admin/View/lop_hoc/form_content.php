<div class="page-container">
    <div class="page-header">
        <h2><?= isset($lopHoc) ? 'Sửa lớp học' : 'Thêm lớp học mới' ?></h2>
        <div class="page-actions">
            <a href="?act=admin-list-lop-hoc" class="btn btn-secondary">← Quay lại</a>
        </div>
    </div>

    <form method="POST" 
          action="?act=<?= isset($lopHoc) ? 'admin-update-lop-hoc' : 'admin-save-lop-hoc' ?>">
        
        <?php if (isset($lopHoc)): ?>
            <input type="hidden" name="id" value="<?= $lopHoc['id'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="id_khoa_hoc" class="required">Khóa học</label>
            <select name="id_khoa_hoc" id="id_khoa_hoc" class="form-control" required>
                <option value="">-- Chọn khóa học --</option>
                <?php foreach ($khoaHocList as $kh): ?>
                    <option value="<?= $kh['id'] ?>" 
                            <?= (isset($lopHoc) && $lopHoc['id_khoa_hoc'] == $kh['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kh['ten_khoa_hoc']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="ten_lop" class="required">Tên lớp</label>
            <input type="text" 
                   name="ten_lop" 
                   id="ten_lop" 
                   class="form-control" 
                   value="<?= htmlspecialchars($lopHoc['ten_lop'] ?? '') ?>" 
                   required 
                   maxlength="200"
                   placeholder="Nhập tên lớp học">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="so_luong_toi_da">Số lượng tối đa</label>
                <?php if (isset($lopHoc) && isset($soLuongDangKy)): ?>
                    <div style="margin-bottom: 5px; padding: 8px; background: #f8f9fa; border-radius: 4px; font-size: 13px;">
                        <strong>Thông tin hiện tại:</strong> 
                        Đã có <span style="color: #28a745; font-weight: bold;"><?= $soLuongDangKy ?></span> học sinh đăng ký (đã xác nhận)
                        <?php if (!empty($lopHoc['so_luong_toi_da'])): ?>
                            / <?= $lopHoc['so_luong_toi_da'] ?> tối đa
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <input type="number" 
                       name="so_luong_toi_da" 
                       id="so_luong_toi_da" 
                       class="form-control" 
                       value="<?= $lopHoc['so_luong_toi_da'] ?? '' ?>" 
                       min="<?= isset($lopHoc) && isset($soLuongDangKy) ? max(1, $soLuongDangKy) : 1 ?>"
                       placeholder="Để trống nếu không giới hạn">
                <?php if (isset($lopHoc) && isset($soLuongDangKy) && $soLuongDangKy > 0): ?>
                    <small style="color: #dc3545; display: block; margin-top: 5px;">
                        ⚠️ Số lượng tối đa phải >= <?= $soLuongDangKy ?> (số học sinh đã đăng ký)
                    </small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="trang_thai" class="required">Trạng thái</label>
                <select name="trang_thai" id="trang_thai" class="form-control" required>
                    <option value="Chưa khai giảng" <?= (!isset($lopHoc) || $lopHoc['trang_thai'] == 'Chưa khai giảng') ? 'selected' : '' ?>>
                        Chưa khai giảng
                    </option>
                    <option value="Đang học" <?= (isset($lopHoc) && $lopHoc['trang_thai'] == 'Đang học') ? 'selected' : '' ?>>
                        Đang học
                    </option>
                    <option value="Kết thúc" <?= (isset($lopHoc) && $lopHoc['trang_thai'] == 'Kết thúc') ? 'selected' : '' ?>>
                        Kết thúc
                    </option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="mo_ta">Mô tả</label>
            <textarea name="mo_ta" 
                      id="mo_ta" 
                      class="form-control" 
                      rows="4"
                      placeholder="Nhập mô tả lớp học (tùy chọn)"><?= htmlspecialchars($lopHoc['mo_ta'] ?? '') ?></textarea>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <?= isset($lopHoc) ? 'Cập nhật' : 'Thêm mới' ?>
            </button>
            <a href="?act=admin-list-lop-hoc" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>


