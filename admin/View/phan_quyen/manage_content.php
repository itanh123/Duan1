<?php
$pageTitle = 'Quản lý Phân quyền: ' . htmlspecialchars($nguoiDung['ho_ten']);
?>

<div class="content-header">
    <h1>Quản lý Phân quyền: <?= htmlspecialchars($nguoiDung['ho_ten']) ?></h1>
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
                <th>Vai trò:</th>
                <td>
                    <span class="badge badge-<?= $nguoiDung['vai_tro'] == 'admin' ? 'primary' : 'info' ?>">
                        <?= ucfirst($nguoiDung['vai_tro']) ?>
                    </span>
                </td>
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
        <h3>Phân quyền</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="?act=admin-update-quyen-nguoi-dung">
            <input type="hidden" name="id_nguoi_dung" value="<?= $nguoiDung['id'] ?>">
            
            <div class="quyen-list">
                <div class="quyen-item">
                    <label>
                        <input type="checkbox" name="quyen[]" value="xem" 
                               <?= in_array('xem', $nguoiDung['quyen'] ?? []) ? 'checked' : '' ?>>
                        <span class="quyen-label">Xem</span>
                        <span class="quyen-desc">Quyền xem dữ liệu</span>
                    </label>
                </div>
                
                <div class="quyen-item">
                    <label>
                        <input type="checkbox" name="quyen[]" value="them" 
                               <?= in_array('them', $nguoiDung['quyen'] ?? []) ? 'checked' : '' ?>>
                        <span class="quyen-label">Thêm</span>
                        <span class="quyen-desc">Quyền thêm dữ liệu mới</span>
                    </label>
                </div>
                
                <div class="quyen-item">
                    <label>
                        <input type="checkbox" name="quyen[]" value="sua" 
                               <?= in_array('sua', $nguoiDung['quyen'] ?? []) ? 'checked' : '' ?>>
                        <span class="quyen-label">Sửa</span>
                        <span class="quyen-desc">Quyền chỉnh sửa dữ liệu</span>
                    </label>
                </div>
                
                <div class="quyen-item">
                    <label>
                        <input type="checkbox" name="quyen[]" value="xoa" 
                               <?= in_array('xoa', $nguoiDung['quyen'] ?? []) ? 'checked' : '' ?>>
                        <span class="quyen-label">Xóa</span>
                        <span class="quyen-desc">Quyền xóa dữ liệu</span>
                    </label>
                </div>
                
                <div class="quyen-item">
                    <label>
                        <input type="checkbox" name="quyen[]" value="quan_tri" 
                               <?= in_array('quan_tri', $nguoiDung['quyen'] ?? []) ? 'checked' : '' ?>>
                        <span class="quyen-label">Quản trị</span>
                        <span class="quyen-desc">Quyền quản trị hệ thống (có tất cả quyền khác)</span>
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cập nhật Phân quyền</button>
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

.quyen-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.quyen-item {
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    transition: all 0.3s;
}

.quyen-item:hover {
    border-color: #3498db;
    background: #f8f9fa;
}

.quyen-item label {
    display: flex;
    align-items: center;
    cursor: pointer;
    gap: 15px;
}

.quyen-item input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.quyen-label {
    font-weight: 600;
    font-size: 16px;
    color: #333;
    min-width: 100px;
}

.quyen-desc {
    color: #6c757d;
    font-size: 14px;
}

.form-actions {
    margin-top: 30px;
    display: flex;
    gap: 10px;
}
</style>

