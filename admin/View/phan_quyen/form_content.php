<?php
$isEdit = isset($phanQuyen) && $phanQuyen;
$pageTitle = $isEdit ? 'Sửa Phân quyền' : 'Thêm Phân quyền';
?>

<div class="content-header">
    <h1><?= $isEdit ? 'Sửa Phân quyền' : 'Thêm Phân quyền' ?></h1>
    <a href="?act=admin-list-phan-quyen" class="btn btn-secondary">← Quay lại</a>
</div>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="?act=<?= $isEdit ? 'admin-update-phan-quyen' : 'admin-save-phan-quyen' ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $phanQuyen['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="id_nguoi_dung" class="required">Người dùng</label>
                <select name="id_nguoi_dung" id="id_nguoi_dung" class="form-control" required>
                    <option value="">-- Chọn người dùng --</option>
                    <?php foreach ($nguoiDungList as $nd): ?>
                        <option value="<?= $nd['id'] ?>" 
                                <?= ($isEdit && $phanQuyen['id_nguoi_dung'] == $nd['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($nd['ho_ten']) ?> - <?= htmlspecialchars($nd['email']) ?> 
                            (<?= ucfirst($nd['vai_tro']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="ten_quyen" class="required">Quyền</label>
                <select name="ten_quyen" id="ten_quyen" class="form-control" required>
                    <option value="">-- Chọn quyền --</option>
                    <option value="xem" <?= ($isEdit && $phanQuyen['ten_quyen'] == 'xem') ? 'selected' : '' ?>>Xem</option>
                    <option value="them" <?= ($isEdit && $phanQuyen['ten_quyen'] == 'them') ? 'selected' : '' ?>>Thêm</option>
                    <option value="sua" <?= ($isEdit && $phanQuyen['ten_quyen'] == 'sua') ? 'selected' : '' ?>>Sửa</option>
                    <option value="xoa" <?= ($isEdit && $phanQuyen['ten_quyen'] == 'xoa') ? 'selected' : '' ?>>Xóa</option>
                    <option value="quan_tri" <?= ($isEdit && $phanQuyen['ten_quyen'] == 'quan_tri') ? 'selected' : '' ?>>Quản trị</option>
                </select>
                <small class="form-text text-muted">
                    <strong>Lưu ý:</strong> Quyền "Quản trị" sẽ có tất cả các quyền khác (xem, thêm, sửa, xóa).
                </small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Cập nhật' : 'Thêm' ?></button>
                <a href="?act=admin-list-phan-quyen" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<style>
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.form-group label.required::after {
    content: ' *';
    color: #dc3545;
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

.form-text {
    display: block;
    margin-top: 5px;
    font-size: 13px;
}

.form-actions {
    margin-top: 30px;
    display: flex;
    gap: 10px;
}
</style>

