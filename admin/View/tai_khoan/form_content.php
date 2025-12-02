<?php
$pageTitle = 'Sửa Tài khoản: ' . htmlspecialchars($taiKhoan['ho_ten']);
?>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.content-header h1 {
    margin: 0;
}

.card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-secondary {
    background: #95a5a6;
    color: white;
}

.btn-secondary:hover {
    background: #7f8c8d;
}

.alert {
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.help-text {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.checkbox-group input[type="checkbox"] {
    width: auto;
    margin: 0;
}

.vai-tro-list {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
    margin-top: 5px;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge-danger {
    background: #dc3545;
    color: white;
}

.badge-primary {
    background: #3498db;
    color: white;
}

.badge-secondary {
    background: #6c757d;
    color: white;
}
</style>

<div class="content-header">
    <h1>Sửa Tài khoản: <?= htmlspecialchars($taiKhoan['ho_ten']) ?></h1>
    <a href="?act=admin-list-tai-khoan" class="btn btn-secondary">← Quay lại</a>
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
    <form method="POST" action="?act=admin-update-tai-khoan">
        <input type="hidden" name="id" value="<?= $taiKhoan['id'] ?>">

        <div class="form-group">
            <label for="ho_ten">Họ tên *</label>
            <input type="text" id="ho_ten" name="ho_ten" class="form-control" 
                   value="<?= htmlspecialchars($taiKhoan['ho_ten']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?= htmlspecialchars($taiKhoan['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="mat_khau">Mật khẩu mới</label>
            <input type="password" id="mat_khau" name="mat_khau" class="form-control" 
                   placeholder="Để trống nếu không đổi mật khẩu">
            <div class="help-text">Chỉ nhập nếu muốn thay đổi mật khẩu</div>
        </div>

        <div class="form-group">
            <label for="so_dien_thoai">Số điện thoại</label>
            <input type="text" id="so_dien_thoai" name="so_dien_thoai" class="form-control" 
                   value="<?= htmlspecialchars($taiKhoan['so_dien_thoai'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="dia_chi">Địa chỉ</label>
            <textarea id="dia_chi" name="dia_chi" class="form-control" rows="3"><?= htmlspecialchars($taiKhoan['dia_chi'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Vai trò hiện tại</label>
            <div class="vai-tro-list">
                <?php 
                $vaiTro = $taiKhoan['vai_tro'] ?? '';
                if (!empty($vaiTro)): 
                    $badgeClass = $vaiTro == 'admin' ? 'badge-danger' : ($vaiTro == 'giang_vien' ? 'badge-primary' : 'badge-secondary');
                ?>
                    <span class="badge <?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $vaiTro)) ?></span>
                <?php else: ?>
                    <span class="badge badge-secondary">Chưa phân vai trò</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Trạng thái tài khoản</label>
            <div class="checkbox-group">
                <input type="radio" id="trang_thai_1" name="trang_thai" value="1" 
                       <?= $taiKhoan['trang_thai'] == 1 ? 'checked' : '' ?>>
                <label for="trang_thai_1" style="font-weight: normal; margin: 0;">Hoạt động</label>
            </div>
            <div class="checkbox-group">
                <input type="radio" id="trang_thai_0" name="trang_thai" value="0" 
                       <?= $taiKhoan['trang_thai'] == 0 ? 'checked' : '' ?>>
                <label for="trang_thai_0" style="font-weight: normal; margin: 0;">Tạm khóa</label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="?act=admin-list-tai-khoan" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>

