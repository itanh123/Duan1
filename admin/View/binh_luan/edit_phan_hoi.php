<?php
$pageTitle = 'Chỉnh sửa phản hồi bình luận';
?>

<div class="page-container">
    <div class="page-header">
        <h2>Chỉnh sửa phản hồi bình luận</h2>
        <div class="page-actions">
            <a href="?act=admin-tra-loi-binh-luan&id=<?= $phanHoi['id_binh_luan'] ?>" class="btn btn-secondary">← Quay lại</a>
        </div>
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

    <!-- Thông tin bình luận gốc -->
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-header">
            <h3>Bình luận gốc</h3>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Khóa học:</label>
                    <span><?= htmlspecialchars($binhLuan['ten_khoa_hoc'] ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <label>Học sinh:</label>
                    <span><strong><?= htmlspecialchars($binhLuan['ten_hoc_sinh'] ?? 'N/A') ?></strong></span>
                </div>
                <div class="info-item">
                    <label>Ngày tạo:</label>
                    <span><?= isset($binhLuan['ngay_tao']) ? date('d/m/Y H:i', strtotime($binhLuan['ngay_tao'])) : 'N/A' ?></span>
                </div>
            </div>
            <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid #007bff;">
                <strong>Nội dung:</strong>
                <p style="margin: 10px 0 0 0; color: #333;"><?= nl2br(htmlspecialchars($binhLuan['noi_dung'] ?? '')) ?></p>
            </div>
        </div>
    </div>

    <!-- Form chỉnh sửa phản hồi -->
    <div class="card">
        <div class="card-header">
            <h3>Chỉnh sửa phản hồi</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="?act=admin-update-phan-hoi-binh-luan">
                <input type="hidden" name="id" value="<?= $phanHoi['id'] ?>">
                
                <div class="form-group">
                    <label for="noi_dung" class="required">Nội dung phản hồi</label>
                    <textarea name="noi_dung" 
                              id="noi_dung" 
                              class="form-control" 
                              rows="5" 
                              placeholder="Nhập nội dung phản hồi..." 
                              required><?= htmlspecialchars($phanHoi['noi_dung']) ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Cập nhật phản hồi</button>
                    <a href="?act=admin-tra-loi-binh-luan&id=<?= $phanHoi['id_binh_luan'] ?>" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-item label {
    font-weight: 600;
    color: #666;
    font-size: 14px;
}

.info-item span {
    color: #333;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    padding: 15px 20px;
    background: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    border-radius: 8px 8px 0 0;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
}

.card-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #333;
    font-weight: 500;
}

.form-group label.required::after {
    content: ' *';
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-warning {
    background: #ffc107;
    color: #000;
}

.btn-warning:hover {
    background: #e0a800;
}

.btn-sm {
    padding: 5px 12px;
    font-size: 12px;
}
</style>

