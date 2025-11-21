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
                <?php
                $minValue = 1;
                $maxValue = null;
                if (isset($lopHoc) && isset($soLuongDangKy) && $soLuongDangKy > 0) {
                    $minValue = max(1, $soLuongDangKy);
                }
                if (isset($phongHocInfo) && $phongHocInfo) {
                    $maxValue = $phongHocInfo['suc_chua'];
                    if ($minValue > $maxValue) {
                        $minValue = $maxValue; // Đảm bảo min không lớn hơn max
                    }
                }
                ?>
                <input type="number" 
                       name="so_luong_toi_da" 
                       id="so_luong_toi_da" 
                       class="form-control" 
                       value="<?= $lopHoc['so_luong_toi_da'] ?? '' ?>" 
                       min="<?= $minValue ?>"
                       <?= $maxValue ? 'max="' . $maxValue . '"' : '' ?>
                       placeholder="Để trống nếu không giới hạn">
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
                            <?php endif; ?>
                            
                            <?php if (isset($phongHocInfo) && $phongHocInfo): ?>
                                <?php if (isset($soLuongDangKy) && $soLuongDangKy > 0): ?>
                                    <br><br>
                                <?php endif; ?>
                                <strong>Giới hạn phòng học:</strong>
                                <br>
                                Lớp học này đang sử dụng phòng học có sức chứa tối đa là <strong><?= $phongHocInfo['suc_chua'] ?></strong> người.
                                <br>
                                Phòng: <strong><?= htmlspecialchars($phongHocInfo['danh_sach_phong']) ?></strong>
                                <br>
                                Số lượng tối đa phải <strong>≤ <?= $phongHocInfo['suc_chua'] ?></strong> để phù hợp với sức chứa phòng học.
                            <?php else: ?>
                                <?php if (isset($soLuongDangKy) && $soLuongDangKy > 0): ?>
                                    <br><br>
                                <?php endif; ?>
                                <span style="color: #6c757d;">ℹ️ Lớp học này chưa có phòng học được phân công trong ca học.</span>
                            <?php endif; ?>
                        </div>
                    </div>
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


