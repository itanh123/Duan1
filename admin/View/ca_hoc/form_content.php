<div class="page-container">
    <div class="page-header">
        <h2><?= isset($caHoc) ? 'Sửa ca học' : 'Thêm ca học mới' ?></h2>
        <div class="page-actions">
            <a href="?act=admin-list-ca-hoc" class="btn btn-secondary">← Quay lại</a>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error" style="background: #fee; color: #c33; padding: 12px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #c33;">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" 
          action="?act=<?= isset($caHoc) ? 'admin-update-ca-hoc' : 'admin-save-ca-hoc' ?>"
          id="caHocForm">
        
        <?php if (isset($caHoc)): ?>
            <input type="hidden" name="id" value="<?= $caHoc['id'] ?>">
        <?php endif; ?>

        <?php
        // Lấy giá trị từ formData nếu có (khi có lỗi), nếu không thì dùng từ caHoc
        $selectedLop = $formData['id_lop'] ?? ($caHoc['id_lop'] ?? '');
        $selectedCa = $formData['id_ca'] ?? ($caHoc['id_ca'] ?? '');
        $selectedThu = $formData['thu_trong_tuan'] ?? ($caHoc['thu_trong_tuan'] ?? '');
        $selectedNgayHoc = $formData['ngay_hoc'] ?? ($caHoc['ngay_hoc'] ?? '');
        $selectedGiangVien = $formData['id_giang_vien'] ?? ($caHoc['id_giang_vien'] ?? '');
        $selectedPhong = $formData['id_phong'] ?? ($caHoc['id_phong'] ?? '');
        $selectedGhiChu = $formData['ghi_chu'] ?? ($caHoc['ghi_chu'] ?? '');
        ?>

        <div class="form-group">
            <label for="id_lop" class="required">Lớp học</label>
            <select name="id_lop" id="id_lop" class="form-control" required>
                <option value="">-- Chọn lớp học --</option>
                <?php foreach ($lopHocList as $lh): ?>
                    <option value="<?= $lh['id'] ?>" 
                            <?= $selectedLop == $lh['id'] ? 'selected' : '' ?>>
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
                                    data-gio-bat-dau="<?= $ca['gio_bat_dau'] ?>"
                                    data-gio-ket-thuc="<?= $ca['gio_ket_thuc'] ?>"
                                    <?= $selectedCa == $ca['id'] ? 'selected' : '' ?>>
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
                    <option value="Thứ 2" <?= $selectedThu == 'Thứ 2' ? 'selected' : '' ?>>Thứ 2</option>
                    <option value="Thứ 3" <?= $selectedThu == 'Thứ 3' ? 'selected' : '' ?>>Thứ 3</option>
                    <option value="Thứ 4" <?= $selectedThu == 'Thứ 4' ? 'selected' : '' ?>>Thứ 4</option>
                    <option value="Thứ 5" <?= $selectedThu == 'Thứ 5' ? 'selected' : '' ?>>Thứ 5</option>
                    <option value="Thứ 6" <?= $selectedThu == 'Thứ 6' ? 'selected' : '' ?>>Thứ 6</option>
                    <option value="Thứ 7" <?= $selectedThu == 'Thứ 7' ? 'selected' : '' ?>>Thứ 7</option>
                    <option value="Chủ nhật" <?= $selectedThu == 'Chủ nhật' ? 'selected' : '' ?>>Chủ nhật</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="ngay_hoc">Ngày học (tùy chọn - nếu để trống sẽ dùng thứ trong tuần)</label>
            <input type="date" 
                   name="ngay_hoc" 
                   id="ngay_hoc" 
                   class="form-control"
                   value="<?= !empty($selectedNgayHoc) ? htmlspecialchars($selectedNgayHoc) : '' ?>">
            <small class="form-text text-muted">Nếu nhập ngày học, hệ thống sẽ tìm kiếm theo ngày này thay vì thứ trong tuần</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="id_giang_vien">Giảng viên</label>
                <select name="id_giang_vien" id="id_giang_vien" class="form-control <?= ($errorField ?? '') == 'id_giang_vien' ? 'error-field' : '' ?>">
                    <option value="">-- Chọn giảng viên (tùy chọn) --</option>
                    <?php if (isset($giangVienList) && !empty($giangVienList)): ?>
                        <?php foreach ($giangVienList as $gv): ?>
                            <option value="<?= $gv['id'] ?>" 
                                    data-gv-id="<?= $gv['id'] ?>"
                                    <?= !empty($selectedGiangVien) && (int)$selectedGiangVien == (int)$gv['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($gv['ho_ten']) ?> (<?= htmlspecialchars($gv['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <div id="giang_vien_warning" class="warning-message" style="display: none; color: #f39c12; font-size: 12px; margin-top: 5px;"></div>
            </div>

            <div class="form-group">
                <label for="id_phong">Phòng học</label>
                <select name="id_phong" id="id_phong" class="form-control <?= ($errorField ?? '') == 'id_phong' ? 'error-field' : '' ?>">
                    <option value="">-- Chọn phòng học (tùy chọn) --</option>
                    <?php foreach ($phongHocList ?? [] as $ph): ?>
                        <option value="<?= $ph['id'] ?>" 
                                data-phong-id="<?= $ph['id'] ?>"
                                <?= !empty($selectedPhong) && (int)$selectedPhong == (int)$ph['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ph['ten_phong']) ?> (Sức chứa: <?= $ph['suc_chua'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="phong_warning" class="warning-message" style="display: none; color: #f39c12; font-size: 12px; margin-top: 5px;"></div>
            </div>
        </div>

        <div class="form-group">
            <label for="ghi_chu">Ghi chú</label>
            <textarea name="ghi_chu" 
                      id="ghi_chu" 
                      class="form-control" 
                      rows="3"
                      maxlength="255"
                      placeholder="Nhập ghi chú (tùy chọn)"><?= htmlspecialchars($selectedGhiChu) ?></textarea>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn btn-primary">
                <?= isset($caHoc) ? 'Cập nhật' : 'Thêm mới' ?>
            </button>
            <a href="?act=admin-list-ca-hoc" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

<style>
.error-field {
    border: 2px solid #e74c3c !important;
    background-color: #fee !important;
}
.warning-message {
    color: #f39c12;
    font-size: 12px;
    margin-top: 5px;
}
.option-disabled {
    color: #999;
    background-color: #f5f5f5;
    font-style: italic;
}
</style>

<script>
(function() {
    const form = document.getElementById('caHocForm');
    const idCa = document.getElementById('id_ca');
    const thuTrongTuan = document.getElementById('thu_trong_tuan');
    const ngayHoc = document.getElementById('ngay_hoc');
    const idGiangVien = document.getElementById('id_giang_vien');
    const idPhong = document.getElementById('id_phong');
    const giangVienWarning = document.getElementById('giang_vien_warning');
    const phongWarning = document.getElementById('phong_warning');
    
    let checkTimeout = null;
    let currentGiangVienTrung = [];
    let currentPhongTrung = [];
    
    function checkTrung() {
        const ca = idCa.value;
        const thu = thuTrongTuan.value;
        const ngay = ngayHoc.value;
        const excludeId = <?= isset($caHoc['id']) ? $caHoc['id'] : 'null' ?>;
        
        if (!ca || (!thu && !ngay)) {
            // Reset về trạng thái ban đầu
            resetOptions();
            return;
        }
        
        // Gọi API kiểm tra trùng
        const url = `?act=admin-check-ca-hoc-trung&id_ca=${ca}&thu_trong_tuan=${encodeURIComponent(thu || '')}&ngay_hoc=${ngay || ''}&exclude_id=${excludeId || ''}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                currentGiangVienTrung = data.giang_vien_trung || [];
                currentPhongTrung = data.phong_hoc_trung || [];
                
                updateOptions();
            })
            .catch(error => {
                console.error('Lỗi khi kiểm tra trùng:', error);
            });
    }
    
    function resetOptions() {
        // Bỏ disable tất cả options
        Array.from(idGiangVien.options).forEach(opt => {
            opt.disabled = false;
            opt.style.color = '';
            opt.style.backgroundColor = '';
            opt.style.fontStyle = '';
        });
        
        Array.from(idPhong.options).forEach(opt => {
            opt.disabled = false;
            opt.style.color = '';
            opt.style.backgroundColor = '';
            opt.style.fontStyle = '';
        });
        
        giangVienWarning.style.display = 'none';
        phongWarning.style.display = 'none';
        idGiangVien.classList.remove('error-field');
        idPhong.classList.remove('error-field');
    }
    
    function updateOptions() {
        // Cập nhật giảng viên
        const selectedGiangVien = idGiangVien.value;
        let hasSelectedGiangVienTrung = false;
        
        Array.from(idGiangVien.options).forEach(opt => {
            if (opt.value && currentGiangVienTrung.includes(parseInt(opt.value))) {
                opt.disabled = true;
                opt.style.color = '#999';
                opt.style.backgroundColor = '#f5f5f5';
                opt.style.fontStyle = 'italic';
                if (opt.value == selectedGiangVien) {
                    hasSelectedGiangVienTrung = true;
                }
            } else {
                opt.disabled = false;
                opt.style.color = '';
                opt.style.backgroundColor = '';
                opt.style.fontStyle = '';
            }
        });
        
        if (hasSelectedGiangVienTrung) {
            idGiangVien.value = '';
            giangVienWarning.textContent = 'Giảng viên này đã có ca học vào thời gian này!';
            giangVienWarning.style.display = 'block';
            idGiangVien.classList.add('error-field');
        } else {
            giangVienWarning.style.display = 'none';
            idGiangVien.classList.remove('error-field');
        }
        
        // Cập nhật phòng học
        const selectedPhong = idPhong.value;
        let hasSelectedPhongTrung = false;
        
        Array.from(idPhong.options).forEach(opt => {
            if (opt.value && currentPhongTrung.includes(parseInt(opt.value))) {
                opt.disabled = true;
                opt.style.color = '#999';
                opt.style.backgroundColor = '#f5f5f5';
                opt.style.fontStyle = 'italic';
                if (opt.value == selectedPhong) {
                    hasSelectedPhongTrung = true;
                }
            } else {
                opt.disabled = false;
                opt.style.color = '';
                opt.style.backgroundColor = '';
                opt.style.fontStyle = '';
            }
        });
        
        if (hasSelectedPhongTrung) {
            idPhong.value = '';
            phongWarning.textContent = 'Phòng học này đã được sử dụng vào thời gian này!';
            phongWarning.style.display = 'block';
            idPhong.classList.add('error-field');
        } else {
            phongWarning.style.display = 'none';
            idPhong.classList.remove('error-field');
        }
    }
    
    // Lắng nghe sự kiện thay đổi
    [idCa, thuTrongTuan, ngayHoc].forEach(element => {
        element.addEventListener('change', function() {
            // Clear timeout cũ
            if (checkTimeout) {
                clearTimeout(checkTimeout);
            }
            
            // Đợi 300ms sau khi người dùng ngừng nhập
            checkTimeout = setTimeout(() => {
                checkTrung();
            }, 300);
        });
    });
    
    // Kiểm tra khi chọn giảng viên hoặc phòng
    idGiangVien.addEventListener('change', function() {
        if (this.value && currentGiangVienTrung.includes(parseInt(this.value))) {
            this.value = '';
            giangVienWarning.textContent = 'Giảng viên này đã có ca học vào thời gian này!';
            giangVienWarning.style.display = 'block';
            this.classList.add('error-field');
        } else {
            giangVienWarning.style.display = 'none';
            this.classList.remove('error-field');
        }
    });
    
    idPhong.addEventListener('change', function() {
        if (this.value && currentPhongTrung.includes(parseInt(this.value))) {
            this.value = '';
            phongWarning.textContent = 'Phòng học này đã được sử dụng vào thời gian này!';
            phongWarning.style.display = 'block';
            this.classList.add('error-field');
        } else {
            phongWarning.style.display = 'none';
            this.classList.remove('error-field');
        }
    });
    
    // Kiểm tra ngay khi load trang nếu đã có giá trị
    if (idCa.value && (thuTrongTuan.value || ngayHoc.value)) {
        setTimeout(checkTrung, 500);
    }
})();
</script>

