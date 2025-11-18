<?php
$pageTitle = 'Quản lý Vai trò: ' . htmlspecialchars($nguoiDung['ho_ten']);
?>

<div class="content-header">
    <h1>Quản lý Vai trò: <?= htmlspecialchars($nguoiDung['ho_ten']) ?></h1>
    <a href="?act=admin-list-phan-quyen" class="btn btn-secondary">← Quay lại</a>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3>Thông tin người dùng</h3>
    </div>
    <div class="card-body">
        <table class="info-table">
            <tr>
                <th>Họ và tên:</th>
                <td><?= htmlspecialchars($nguoiDung['ho_ten']) ?></td>
            </tr>
            <tr>
                <th>Email:</th>
                <td><?= htmlspecialchars($nguoiDung['email']) ?></td>
            </tr>
            <tr>
                <th>Số điện thoại:</th>
                <td><?= htmlspecialchars($nguoiDung['so_dien_thoai'] ?? 'N/A') ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Vai trò</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="?act=admin-update-vai-tro-nguoi-dung">
            <input type="hidden" name="id_nguoi_dung" value="<?= $nguoiDung['id'] ?>">
            
            <div class="vai-tro-list">
                <div class="vai-tro-item">
                    <label>
                        <input type="checkbox" name="vai_tro[]" value="admin" 
                               <?= in_array('admin', $nguoiDung['vai_tro_list'] ?? []) ? 'checked' : '' ?>>
                        <span class="vai-tro-label">👨‍💼 Quản trị viên (Admin)</span>
                        <span class="vai-tro-desc">Quản lý toàn bộ hệ thống</span>
                    </label>
                </div>
                
                <div class="vai-tro-item">
                    <label>
                        <input type="checkbox" name="vai_tro[]" value="giang_vien" 
                               <?= in_array('giang_vien', $nguoiDung['vai_tro_list'] ?? []) ? 'checked' : '' ?>>
                        <span class="vai-tro-label">👨‍🏫 Giảng viên</span>
                        <span class="vai-tro-desc">Giảng dạy và quản lý lớp học</span>
                    </label>
                </div>
                
                <div class="vai-tro-item">
                    <label>
                        <input type="checkbox" name="vai_tro[]" value="hoc_sinh" 
                               <?= in_array('hoc_sinh', $nguoiDung['vai_tro_list'] ?? []) ? 'checked' : '' ?>>
                        <span class="vai-tro-label">👨‍🎓 Học sinh</span>
                        <span class="vai-tro-desc">Học tập và đăng ký khóa học</span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cập nhật Vai trò</button>
                <a href="?act=admin-list-phan-quyen" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<style>
.info-table {
    width: 100%;
    border-collapse: collapse;
}

.info-table th,
.info-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.info-table th {
    width: 200px;
    font-weight: 600;
    color: #333;
}

.vai-tro-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.vai-tro-item {
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    transition: all 0.3s;
}

.vai-tro-item:hover {
    border-color: #3498db;
    background: #f8f9fa;
}

.vai-tro-item label {
    display: flex;
    align-items: center;
    cursor: pointer;
    gap: 15px;
}

.vai-tro-item input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.vai-tro-label {
    font-weight: 600;
    font-size: 16px;
    color: #333;
    min-width: 200px;
}

.vai-tro-desc {
    color: #6c757d;
    font-size: 14px;
}

.form-actions {
    margin-top: 30px;
    display: flex;
    gap: 10px;
}
</style>

