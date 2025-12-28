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
                <label for="so_luong_toi_da" class="required">Số lượng tối đa</label>
                <?php if (isset($lopHoc) && isset($soLuongDangKy)): ?>
                    <div style="margin-bottom: 5px; padding: 8px; background: #f8f9fa; border-radius: 4px; font-size: 13px;">
                        <strong>Thông tin hiện tại:</strong> 
                        Đã có <span style="color: #28a745; font-weight: bold;"><?= $soLuongDangKy ?></span> học sinh đăng ký (đã xác nhận)
                        <?php if (!empty($lopHoc['so_luong_toi_da'])): ?>
                            / <?= $lopHoc['so_luong_toi_da'] ?> tối đa
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php
                $minValue = 1;
                if (isset($lopHoc) && isset($soLuongDangKy) && $soLuongDangKy > 0) {
                    $minValue = max(1, $soLuongDangKy);
                }
                ?>
                <input type="number" 
                       name="so_luong_toi_da" 
                       id="so_luong_toi_da" 
                       class="form-control" 
                       value="<?= $lopHoc['so_luong_toi_da'] ?? '' ?>" 
                       min="<?= $minValue ?>"
                       placeholder="Nhập số lượng tối đa học sinh"
                       required>
                <small id="so_luong_hint" style="color: #666; display: block; margin-top: 5px;">
                    <?php if (isset($lopHoc) && isset($soLuongDangKy) && $soLuongDangKy > 0): ?>
                        Số lượng tối đa phải ≥ <?= $soLuongDangKy ?> (số học sinh đã đăng ký)
                    <?php else: ?>
                        Nhập số lượng tối đa học sinh cho lớp học này
                    <?php endif; ?>
                </small>
                <?php if (isset($lopHoc)): ?>
                    <div style="margin-top: 8px; padding: 10px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; font-size: 13px;">
                        <strong style="color: #856404;">⚠️ Lưu ý quan trọng:</strong>
                        <div style="margin-top: 5px; color: #856404;">
                            <?php if (isset($soLuongDangKy) && $soLuongDangKy > 0): ?>
                                Lớp học này hiện có <strong><?= $soLuongDangKy ?></strong> học sinh đã đăng ký (đã xác nhận).
                                <br>
                                Số lượng tối đa phải <strong>≥ <?= $soLuongDangKy ?></strong> để đảm bảo không ảnh hưởng đến các học sinh đã đăng ký.
                                <?php if (!empty($lopHoc['so_luong_toi_da'])): ?>
                                    <?php 
                                    $conLai = $lopHoc['so_luong_toi_da'] - $soLuongDangKy;
                                    if ($conLai > 0):
                                    ?>
                                        <br>
                                        <span style="color: #28a745;">✓ Hiện tại còn <?= $conLai ?> chỗ trống.</span>
                                    <?php elseif ($conLai == 0): ?>
                                        <br>
                                        <span style="color: #dc3545;">⚠️ Lớp học đã đầy!</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php else: ?>
                                Bạn có thể đặt số lượng tối đa cho lớp học này.
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="trang_thai" class="required">Trạng thái</label>
                <select name="trang_thai" id="trang_thai" class="form-control" required>
                    <?php 
                    $trangThaiHienTai = $lopHoc['trang_thai'] ?? 'Chưa học';
                    // Nếu đã là "Đang học" hoặc "Kết thúc", không cho chuyển về "Chưa học"
                    $disableChuaHoc = in_array($trangThaiHienTai, ['Đang học', 'Kết thúc']);
                    ?>
                    <option value="Chưa học" 
                            <?= (!isset($lopHoc) || $trangThaiHienTai == 'Chưa học') ? 'selected' : '' ?>
                            <?= $disableChuaHoc ? 'disabled' : '' ?>>
                        Chưa học
                    </option>
                    <option value="Đang học" <?= (isset($lopHoc) && $trangThaiHienTai == 'Đang học') ? 'selected' : '' ?>>
                        Đang học
                    </option>
                    <option value="Kết thúc" <?= (isset($lopHoc) && $trangThaiHienTai == 'Kết thúc') ? 'selected' : '' ?>>
                        Kết thúc
                    </option>
                </select>
                <?php if ($disableChuaHoc): ?>
                    <small class="form-text text-muted" style="color: #856404;">
                        <i class="bi bi-info-circle"></i> Không thể chuyển từ "<?= htmlspecialchars($trangThaiHienTai) ?>" về "Chưa học"
                    </small>
                <?php endif; ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const soLuongToiDa = document.getElementById('so_luong_toi_da');
    const soLuongHint = document.getElementById('so_luong_hint');
    
    // Lấy số lượng đăng ký hiện tại (nếu có)
    const soLuongDangKy = <?= isset($soLuongDangKy) ? (int)$soLuongDangKy : 0 ?>;
    
    // Validate khi submit form
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const soLuong = parseInt(soLuongToiDa.value);
        
        if (isNaN(soLuong) || soLuong <= 0) {
            e.preventDefault();
            alert('Vui lòng nhập số lượng tối đa hợp lệ (lớn hơn 0)!');
            soLuongToiDa.focus();
            return false;
        }
        
        if (soLuong < soLuongDangKy) {
            e.preventDefault();
            alert(`Số lượng tối đa (${soLuong}) không được nhỏ hơn số lượng đã đăng ký (${soLuongDangKy})!`);
            soLuongToiDa.focus();
            return false;
        }
    });
});
</script>


