<div class="page-container">
    <div class="page-header">
        <h2><?= isset($caHoc) ? 'Sửa ca học' : 'Thêm ca học mới' ?></h2>
        <div class="page-actions">
            <a href="?act=admin-list-ca-hoc" class="btn btn-secondary">← Quay lại</a>
        </div>
    </div>

    <form method="POST" 
          action="?act=<?= isset($caHoc) ? 'admin-update-ca-hoc' : 'admin-save-ca-hoc' ?>">
        
        <?php if (isset($caHoc)): ?>
            <input type="hidden" name="id" value="<?= $caHoc['id'] ?>">
        <?php endif; ?>

        <div class="form-group">
            <label for="id_lop" class="required">Lớp học</label>
            <select name="id_lop" id="id_lop" class="form-control" required>
                <option value="">-- Chọn lớp học --</option>
                <?php foreach ($lopHocList as $lh): ?>
                    <option value="<?= $lh['id'] ?>" 
                            <?= (isset($caHoc) && $caHoc['id_lop'] == $lh['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lh['ten_lop']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_ca" class="required">Ca học</label>
                <select name="id_ca" id="id_ca" class="form-control" required>
                    <option value="">-- Chọn ca học --</option>
                    <?php if (isset($caMacDinhList) && !empty($caMacDinhList)): ?>
                        <?php foreach ($caMacDinhList as $ca): ?>
                            <option value="<?= $ca['id'] ?>" 
                                    <?= (isset($caHoc) && $caHoc['id_ca'] == $ca['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ca['ten_ca']) ?> 
                                (<?= date('H:i', strtotime($ca['gio_bat_dau'])) ?> - <?= date('H:i', strtotime($ca['gio_ket_thuc'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="thu_trong_tuan" class="required">Thứ trong tuần</label>
                <select name="thu_trong_tuan" id="thu_trong_tuan" class="form-control" required>
                    <option value="">-- Chọn thứ --</option>
                    <option value="Thứ 2" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 2') ? 'selected' : '' ?>>Thứ 2</option>
                    <option value="Thứ 3" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 3') ? 'selected' : '' ?>>Thứ 3</option>
                    <option value="Thứ 4" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 4') ? 'selected' : '' ?>>Thứ 4</option>
                    <option value="Thứ 5" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 5') ? 'selected' : '' ?>>Thứ 5</option>
                    <option value="Thứ 6" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 6') ? 'selected' : '' ?>>Thứ 6</option>
                    <option value="Thứ 7" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 7') ? 'selected' : '' ?>>Thứ 7</option>
                    <option value="Chủ nhật" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Chủ nhật') ? 'selected' : '' ?>>Chủ nhật</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_giang_vien">Giảng viên</label>
                <select name="id_giang_vien" id="id_giang_vien" class="form-control">
                    <option value="">-- Chọn giảng viên (tùy chọn) --</option>
                    <?php if (isset($giangVienList) && !empty($giangVienList)): ?>
                        <?php foreach ($giangVienList as $gv): ?>
                            <option value="<?= $gv['id'] ?>" 
                                    <?= (isset($caHoc) && !empty($caHoc['id_giang_vien']) && (int)$caHoc['id_giang_vien'] == (int)$gv['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($gv['ho_ten']) ?> (<?= htmlspecialchars($gv['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_phong">Phòng học</label>
                <select name="id_phong" id="id_phong" class="form-control">
                    <option value="">-- Chọn phòng học (tùy chọn) --</option>
                    <?php foreach ($phongHocList ?? [] as $ph): ?>
                        <option value="<?= $ph['id'] ?>" <?= (isset($caHoc['id_phong']) && $caHoc['id_phong'] == $ph['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ph['ten_phong']) ?> (Sức chứa: <?= $ph['suc_chua'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="ghi_chu">Ghi chú</label>
            <textarea name="ghi_chu" 
                      id="ghi_chu" 
                      class="form-control" 
                      rows="3"
                      maxlength="255"
                      placeholder="Nhập ghi chú (tùy chọn)"><?= htmlspecialchars($caHoc['ghi_chu'] ?? '') ?></textarea>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <?= isset($caHoc) ? 'Cập nhật' : 'Thêm mới' ?>
            </button>
            <a href="?act=admin-list-ca-hoc" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>


