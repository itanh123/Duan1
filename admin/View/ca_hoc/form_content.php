<?php
// Load helper function
require_once __DIR__ . '/../../../Commons/function.php';
?>
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
                            data-so-luong-toi-da="<?= $lh['so_luong_toi_da'] ?? 30 ?>"
                            <?= $selectedLop == $lh['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lh['ten_lop']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div id="so_luong_toi_da_info" style="margin-top: 8px; padding: 10px; background: #e7f3ff; border-left: 3px solid #007bff; border-radius: 4px; display: none;">
                <div style="margin-bottom: 5px;">
                    <strong>Số lượng tối đa:</strong> <span id="so_luong_toi_da_value"></span> học sinh
                </div>
                <div>
                    <strong>Số lượng đã đăng ký:</strong> <span id="so_luong_dang_ky_value"></span> học sinh
                </div>
                <div style="margin-top: 5px; font-size: 12px; color: #666;">
                    <span id="so_luong_con_lai_text"></span>
                </div>
            </div>
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
                <label for="thu_trong_tuan_display">Thứ trong tuần</label>
                <input type="text" 
                       id="thu_trong_tuan_display" 
                       class="form-control" 
                       readonly
                       style="background-color: #e9ecef; cursor: not-allowed;"
                       value="<?= !empty($selectedNgayHoc) ? htmlspecialchars(tinhThuTuNgayHoc($selectedNgayHoc, $selectedThu)) : htmlspecialchars($selectedThu) ?>">
                <input type="hidden" 
                       name="thu_trong_tuan" 
                       id="thu_trong_tuan" 
                       value="<?= !empty($selectedNgayHoc) ? htmlspecialchars(tinhThuTuNgayHoc($selectedNgayHoc, $selectedThu)) : htmlspecialchars($selectedThu) ?>">
                <small class="form-text text-muted">Thứ được tự động tính từ ngày học</small>
            </div>
        </div>

        <div class="form-group">
            <label for="ngay_hoc" class="required">Ngày học</label>
            <input type="date" 
                   name="ngay_hoc" 
                   id="ngay_hoc" 
                   class="form-control"
                   required
                   value="<?= !empty($selectedNgayHoc) ? htmlspecialchars($selectedNgayHoc) : '' ?>">
            <small class="form-text text-muted">Thứ trong tuần sẽ tự động được tính từ ngày học</small>
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
                                data-suc-chua="<?= $ph['suc_chua'] ?>"
                                <?= !empty($selectedPhong) && (int)$selectedPhong == (int)$ph['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ph['ten_phong']) ?> (Sức chứa: <?= $ph['suc_chua'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="phong_warning" class="warning-message" style="display: none; color: #f39c12; font-size: 12px; margin-top: 5px;"></div>
                <div id="phong_info" style="margin-top: 8px; padding: 8px; background: #fff3cd; border-left: 3px solid #ffc107; border-radius: 4px; display: none; font-size: 12px;">
                    <strong>Lưu ý:</strong> Chỉ hiển thị các phòng có sức chứa >= số lượng tối đa của lớp học
                </div>
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
    const idLop = document.getElementById('id_lop');
    const idCa = document.getElementById('id_ca');
    const thuTrongTuan = document.getElementById('thu_trong_tuan');
    const thuTrongTuanDisplay = document.getElementById('thu_trong_tuan_display');
    const ngayHoc = document.getElementById('ngay_hoc');
    const idGiangVien = document.getElementById('id_giang_vien');
    const idPhong = document.getElementById('id_phong');
    const giangVienWarning = document.getElementById('giang_vien_warning');
    const phongWarning = document.getElementById('phong_warning');
    const soLuongToiDaInfo = document.getElementById('so_luong_toi_da_info');
    const soLuongToiDaValue = document.getElementById('so_luong_toi_da_value');
    const soLuongDangKyValue = document.getElementById('so_luong_dang_ky_value');
    const soLuongConLaiText = document.getElementById('so_luong_con_lai_text');
    const phongInfo = document.getElementById('phong_info');
    
    let checkTimeout = null;
    let currentGiangVienTrung = [];
    let currentPhongTrung = [];
    let currentSoLuongToiDa = null;
    let allPhongHoc = []; // Lưu tất cả phòng học ban đầu
    
    // Lưu tất cả phòng học ban đầu
    Array.from(idPhong.options).forEach(opt => {
        if (opt.value) {
            allPhongHoc.push({
                value: opt.value,
                text: opt.text,
                sucChua: opt.getAttribute('data-suc-chua') || 0
            });
        }
    });
    
    // Xử lý khi chọn lớp học
    function handleLopChange() {
        const selectedLopId = idLop.value;
        if (!selectedLopId) {
            // Ẩn thông tin số lượng tối đa
            soLuongToiDaInfo.style.display = 'none';
            phongInfo.style.display = 'none';
            // Khôi phục tất cả phòng học
            restoreAllPhongHoc();
            return;
        }
        
        // Lấy số lượng tối đa từ option được chọn (tạm thời để lọc phòng)
        const selectedOption = idLop.options[idLop.selectedIndex];
        const tempSoLuongToiDa = parseInt(selectedOption.getAttribute('data-so-luong-toi-da') || 30);
        
        // Gọi API để lấy thông tin đầy đủ (bao gồm số lượng đã đăng ký)
        fetch(`?act=admin-get-lop-hoc-info&id_lop=${selectedLopId}`)
            .then(response => response.json())
            .then(data => {
                let soLuongToiDa = tempSoLuongToiDa;
                let soLuongDangKy = 0;
                
                if (data.error) {
                    console.error('Lỗi:', data.error);
                    // Vẫn hiển thị số lượng tối đa từ attribute
                    soLuongDangKy = 0;
                } else {
                    // Lấy thông tin từ API
                    soLuongToiDa = data.so_luong_toi_da || tempSoLuongToiDa;
                    soLuongDangKy = data.so_luong_dang_ky || 0;
                }
                
                // Cập nhật currentSoLuongToiDa
                currentSoLuongToiDa = soLuongToiDa;
                
                // Hiển thị thông tin
                soLuongToiDaValue.textContent = soLuongToiDa;
                soLuongDangKyValue.textContent = soLuongDangKy;
                
                // Tính và hiển thị số lượng còn lại
                const soLuongConLai = soLuongToiDa - soLuongDangKy;
                if (soLuongConLai > 0) {
                    soLuongConLaiText.textContent = `Còn lại: ${soLuongConLai} chỗ trống`;
                    soLuongConLaiText.style.color = '#28a745';
                } else {
                    soLuongConLaiText.textContent = 'Lớp đã đầy';
                    soLuongConLaiText.style.color = '#dc3545';
                }
                
                soLuongToiDaInfo.style.display = 'block';
                
                // Lọc phòng học theo sức chứa
                filterPhongHocBySucChua(soLuongToiDa);
            })
            .catch(error => {
                console.error('Lỗi khi lấy thông tin lớp học:', error);
                // Vẫn hiển thị số lượng tối đa từ attribute
                currentSoLuongToiDa = tempSoLuongToiDa;
                soLuongToiDaValue.textContent = tempSoLuongToiDa;
                soLuongDangKyValue.textContent = '0';
                soLuongConLaiText.textContent = '';
                soLuongToiDaInfo.style.display = 'block';
                // Lọc phòng học theo sức chứa
                filterPhongHocBySucChua(tempSoLuongToiDa);
            });
    }
    
    // Lọc phòng học theo sức chứa
    function filterPhongHocBySucChua(soLuongToiDa) {
        // Lưu giá trị phòng đã chọn trước khi lọc
        const currentSelectedPhong = idPhong.value;
        
        // Xóa tất cả options (trừ option đầu tiên)
        while (idPhong.options.length > 1) {
            idPhong.remove(1);
        }
        
        // Thêm lại các phòng học có sức chứa >= số lượng tối đa
        let hasValidPhong = false;
        let foundSelectedPhong = false;
        allPhongHoc.forEach(ph => {
            const sucChua = parseInt(ph.sucChua) || 0;
            if (sucChua >= soLuongToiDa) {
                const option = document.createElement('option');
                option.value = ph.value;
                option.textContent = ph.text;
                option.setAttribute('data-suc-chua', ph.sucChua);
                idPhong.appendChild(option);
                hasValidPhong = true;
                
                // Kiểm tra xem phòng đã chọn có trong danh sách không
                if (currentSelectedPhong && ph.value === currentSelectedPhong) {
                    foundSelectedPhong = true;
                }
            }
        });
        
        // Hiển thị thông báo nếu có phòng phù hợp
        if (hasValidPhong) {
            phongInfo.style.display = 'block';
        } else {
            phongInfo.style.display = 'none';
            // Thêm option thông báo không có phòng phù hợp
            const option = document.createElement('option');
            option.value = '';
            option.textContent = '-- Không có phòng phù hợp (sức chứa >= ' + soLuongToiDa + ') --';
            option.disabled = true;
            idPhong.appendChild(option);
        }
        
        // Khôi phục giá trị đã chọn nếu còn trong danh sách
        if (currentSelectedPhong && foundSelectedPhong) {
            idPhong.value = currentSelectedPhong;
        } else if (currentSelectedPhong && !foundSelectedPhong) {
            // Nếu phòng đã chọn không còn phù hợp, giữ lại nhưng cảnh báo
            // Thêm lại phòng đó vào danh sách với cảnh báo
            const selectedPhongData = allPhongHoc.find(ph => ph.value === currentSelectedPhong);
            if (selectedPhongData) {
                const option = document.createElement('option');
                option.value = selectedPhongData.value;
                option.textContent = selectedPhongData.text + ' (⚠️ Sức chứa không đủ)';
                option.setAttribute('data-suc-chua', selectedPhongData.sucChua);
                option.style.color = '#dc3545';
                idPhong.appendChild(option);
                idPhong.value = currentSelectedPhong;
            }
        }
    }
    
    // Khôi phục tất cả phòng học
    function restoreAllPhongHoc() {
        // Xóa tất cả options (trừ option đầu tiên)
        while (idPhong.options.length > 1) {
            idPhong.remove(1);
        }
        
        // Thêm lại tất cả phòng học
        allPhongHoc.forEach(ph => {
            const option = document.createElement('option');
            option.value = ph.value;
            option.textContent = ph.text;
            option.setAttribute('data-suc-chua', ph.sucChua);
            idPhong.appendChild(option);
        });
    }
    
    // Lắng nghe sự kiện thay đổi lớp học
    idLop.addEventListener('change', handleLopChange);
    
    // Xử lý khi load trang nếu đã có lớp được chọn
    // Đảm bảo phòng học hiện tại được giữ lại
    if (idLop.value) {
        // Lưu giá trị phòng hiện tại trước khi xử lý
        const currentPhongValue = idPhong.value;
        handleLopChange();
        
        // Sau khi xử lý, khôi phục giá trị phòng nếu có
        if (currentPhongValue) {
            // Đợi một chút để đảm bảo filterPhongHocBySucChua đã chạy xong
            setTimeout(() => {
                // Kiểm tra xem phòng hiện tại có trong danh sách không
                let found = false;
                Array.from(idPhong.options).forEach(opt => {
                    if (opt.value === currentPhongValue) {
                        found = true;
                    }
                });
                
                // Nếu không tìm thấy, thêm lại phòng đó vào danh sách
                if (!found && currentPhongValue) {
                    const selectedPhongData = allPhongHoc.find(ph => ph.value === currentPhongValue);
                    if (selectedPhongData) {
                        const option = document.createElement('option');
                        option.value = selectedPhongData.value;
                        option.textContent = selectedPhongData.text + ' (⚠️ Phòng hiện tại)';
                        option.setAttribute('data-suc-chua', selectedPhongData.sucChua);
                        option.style.color = '#856404';
                        // Thêm vào đầu danh sách (sau option đầu tiên)
                        idPhong.insertBefore(option, idPhong.options[1]);
                        idPhong.value = currentPhongValue;
                    }
                } else if (found) {
                    // Nếu tìm thấy, đặt lại giá trị
                    idPhong.value = currentPhongValue;
                }
            }, 100);
        }
    }
    
    function checkTrung() {
        const ca = idCa.value;
        const ngay = ngayHoc.value;
        const excludeId = <?= isset($caHoc['id']) ? $caHoc['id'] : 'null' ?>;
        
        // Luôn tính thứ từ ngày học
        let thu = '';
        if (ngay) {
            const thuTuNgay = tinhThuTuNgay(ngay);
            if (thuTuNgay) {
                thu = thuTuNgay;
                thuTrongTuan.value = thu;
                thuTrongTuanDisplay.value = thu;
            }
        }
        
        if (!ca || !ngay || !thu) {
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
    
    // Hàm tính thứ từ ngày
    function tinhThuTuNgay(ngay) {
        if (!ngay) return null;
        const date = new Date(ngay);
        const thu = date.getDay(); // 0 = Chủ nhật, 1 = Thứ 2, ..., 6 = Thứ 7
        const thuMap = {
            0: 'Chủ nhật',
            1: 'Thứ 2',
            2: 'Thứ 3',
            3: 'Thứ 4',
            4: 'Thứ 5',
            5: 'Thứ 6',
            6: 'Thứ 7'
        };
        return thuMap[thu] || null;
    }
    
    // Xử lý khi thay đổi ngày học
    function handleNgayHocChange() {
        const ngay = ngayHoc.value;
        if (ngay) {
            // Có ngày: tự động tính thứ từ ngày
            const thuTuNgay = tinhThuTuNgay(ngay);
            if (thuTuNgay) {
                thuTrongTuan.value = thuTuNgay;
                thuTrongTuanDisplay.value = thuTuNgay;
            }
        } else {
            // Không có ngày: xóa thứ
            thuTrongTuan.value = '';
            thuTrongTuanDisplay.value = '';
        }
        
        // Kiểm tra trùng
        if (checkTimeout) {
            clearTimeout(checkTimeout);
        }
        checkTimeout = setTimeout(() => {
            checkTrung();
        }, 300);
    }
    
    // Lắng nghe sự kiện thay đổi ngày học
    ngayHoc.addEventListener('change', handleNgayHocChange);
    
    // Xử lý khi load trang nếu đã có ngày học
    if (ngayHoc.value) {
        handleNgayHocChange();
    }
    
    // Lắng nghe sự kiện thay đổi ca
    idCa.addEventListener('change', function() {
        // Clear timeout cũ
        if (checkTimeout) {
            clearTimeout(checkTimeout);
        }
        
        // Đợi 300ms sau khi người dùng ngừng nhập
        checkTimeout = setTimeout(() => {
            checkTrung();
        }, 300);
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
    if (idCa.value && ngayHoc.value) {
        setTimeout(checkTrung, 500);
    }
})();
</script>

