<?php
$pageTitle = 'Lớp học của ' . htmlspecialchars($giangVien['ho_ten']);
?>

<div class="page-container">
    <div class="page-header">
        <h2>Lớp học của: <?= htmlspecialchars($giangVien['ho_ten']) ?></h2>
        <div class="page-actions">
            <a href="?act=admin-list-giang-vien" class="btn btn-secondary">← Quay lại</a>
        </div>
    </div>

    <div style="background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="margin-bottom: 15px; color: #333;">Thông tin giảng viên</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
            <div>
                <strong>Họ tên:</strong> <?= htmlspecialchars($giangVien['ho_ten']) ?>
            </div>
            <div>
                <strong>Email:</strong> <?= htmlspecialchars($giangVien['email']) ?>
            </div>
            <div>
                <strong>Số điện thoại:</strong> <?= htmlspecialchars($giangVien['so_dien_thoai'] ?? 'N/A') ?>
            </div>
            <div>
                <strong>Trạng thái:</strong> 
                <span class="<?= $giangVien['trang_thai'] == 1 ? 'status-active' : 'status-inactive' ?>">
                    <?= $giangVien['trang_thai'] == 1 ? 'Hoạt động' : 'Khóa' ?>
                </span>
            </div>
        </div>
    </div>

    <?php if (empty($lopHocs)): ?>
        <div class="empty-state">
            <p>Giảng viên này chưa được phân công lớp học nào.</p>
        </div>
    <?php else: ?>
        <div style="margin-top: 20px;">
            <h3 style="margin-bottom: 20px; color: #333;">Danh sách lớp học đang dạy</h3>
            
            <?php foreach ($lopHocs as $lop): ?>
                <div style="background: #fff; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <h3 style="margin: 0 0 5px 0; font-size: 20px;"><?= htmlspecialchars($lop['ten_lop']) ?></h3>
                        <p style="margin: 0; opacity: 0.9;">Khóa học: <?= htmlspecialchars($lop['ten_khoa_hoc']) ?></p>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                        <div>
                            <strong>Trạng thái lớp:</strong>
                            <span class="<?= $lop['trang_thai_lop'] == 1 ? 'status-active' : 'status-inactive' ?>" style="display: inline-block; margin-left: 5px;">
                                <?= $lop['trang_thai_lop'] == 1 ? 'Đang hoạt động' : 'Ngừng hoạt động' ?>
                            </span>
                        </div>
                        <?php if (!empty($lop['so_luong_toi_da'])): ?>
                            <div>
                                <strong>Số lượng tối đa:</strong> <?= $lop['so_luong_toi_da'] ?> học sinh
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($lop['mo_ta_lop'])): ?>
                        <div style="margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                            <strong>Mô tả lớp học:</strong>
                            <p style="margin: 5px 0 0 0; color: #666;"><?= htmlspecialchars($lop['mo_ta_lop']) ?></p>
                        </div>
                    <?php endif; ?>

                    <div style="margin-top: 20px;">
                        <h4 style="margin-bottom: 15px; color: #333; border-bottom: 2px solid #10B981; padding-bottom: 5px;">Lịch dạy</h4>
                        
                        <?php if (!empty($lop['ca_hoc'])): ?>
                            <div style="display: grid; gap: 12px;">
                                <?php foreach ($lop['ca_hoc'] as $ca): ?>
                                    <div style="padding: 15px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #10B981;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <strong style="font-size: 16px; color: #333;">
                                                <?php
                                                $thuMap = [
                                                    'Thứ 2' => 'Thứ Hai',
                                                    'Thứ 3' => 'Thứ Ba',
                                                    'Thứ 4' => 'Thứ Tư',
                                                    'Thứ 5' => 'Thứ Năm',
                                                    'Thứ 6' => 'Thứ Sáu',
                                                    'Thứ 7' => 'Thứ Bảy',
                                                    'Chủ nhật' => 'Chủ Nhật'
                                                ];
                                                echo $thuMap[$ca['thu_trong_tuan']] ?? $ca['thu_trong_tuan'];
                                                ?>
                                            </strong>
                                        </div>
                                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; font-size: 14px; color: #666;">
                                            <div>
                                                <strong>Ca học:</strong> 
                                                <?= htmlspecialchars($ca['ten_ca'] ?? 'Chưa có') ?>
                                                <?php if (!empty($ca['gio_bat_dau']) && !empty($ca['gio_ket_thuc'])): ?>
                                                    <br><span style="color: #999;">(<?= htmlspecialchars($ca['gio_bat_dau']) ?> - <?= htmlspecialchars($ca['gio_ket_thuc']) ?>)</span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($ca['ten_phong'])): ?>
                                                <div>
                                                    <strong>Phòng học:</strong> <?= htmlspecialchars($ca['ten_phong']) ?>
                                                    <?php if (!empty($ca['suc_chua'])): ?>
                                                        <br><span style="color: #999;">(Sức chứa: <?= $ca['suc_chua'] ?>)</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="padding: 20px; text-align: center; background: #f8f9fa; border-radius: 8px; color: #999;">
                                Lớp học này chưa có lịch dạy được phân công.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

