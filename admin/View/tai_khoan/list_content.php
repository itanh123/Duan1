<?php
$pageTitle = 'Quản lý Tài khoản';
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

.search-form {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead {
    background: #34495e;
    color: white;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

tbody tr:hover {
    background: #f5f5f5;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge-success {
    background: #28a745;
    color: white;
}

.badge-danger {
    background: #dc3545;
    color: white;
}

.badge-primary {
    background: #3498db;
    color: white;
}

.badge-info {
    background: #17a2b8;
    color: white;
}

.badge-secondary {
    background: #6c757d;
    color: white;
}

.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 12px;
    transition: all 0.3s;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
}

.btn-warning {
    background: #f39c12;
    color: white;
}

.btn-warning:hover {
    background: #e67e22;
}

.btn-danger {
    background: #e74c3c;
    color: white;
}

.btn-danger:hover {
    background: #c0392b;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 20px;
}

.pagination a, .pagination span {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
}

.pagination a:hover {
    background: #f0f0f0;
}

.pagination .active {
    background: #3498db;
    color: white;
    border-color: #3498db;
}
</style>

<div class="content-header">
    <h1>Quản lý Tài khoản</h1>
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

<form method="GET" action="?act=admin-list-tai-khoan" class="search-form">
    <input type="hidden" name="act" value="admin-list-tai-khoan">
    <input type="text" name="search" placeholder="Tìm kiếm (Họ tên, Email, SĐT)" 
           value="<?= htmlspecialchars($search ?? '') ?>" 
           class="form-control" style="width: 250px;">
    <select name="trang_thai" class="form-control" style="width: 150px;">
        <option value="">Tất cả trạng thái</option>
        <option value="1" <?= ($trang_thai ?? '') == '1' ? 'selected' : '' ?>>Hoạt động</option>
        <option value="0" <?= ($trang_thai ?? '') == '0' ? 'selected' : '' ?>>Tạm khóa</option>
    </select>
    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
    <a href="?act=admin-list-tai-khoan" class="btn btn-secondary">Reset</a>
</form>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Địa chỉ</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($taiKhoan)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        Không có dữ liệu
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($taiKhoan as $tk): ?>
                    <tr>
                        <td><?= htmlspecialchars($tk['ho_ten']) ?></td>
                        <td><?= htmlspecialchars($tk['email']) ?></td>
                        <td><?= htmlspecialchars($tk['so_dien_thoai'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($tk['dia_chi'] ?? '-') ?></td>
                        <td>
                            <?php 
                            $vaiTro = $tk['vai_tro'] ?? '';
                            if (!empty($vaiTro)): 
                                $badgeClass = $vaiTro == 'admin' ? 'badge-danger' : ($vaiTro == 'giang_vien' ? 'badge-primary' : 'badge-secondary');
                            ?>
                                <span class="badge <?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $vaiTro)) ?></span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Chưa phân vai trò</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($tk['trang_thai'] == 1): ?>
                                <span class="badge badge-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Tạm khóa</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?act=admin-edit-tai-khoan&id=<?= $tk['id'] ?>" class="btn btn-primary btn-sm">Sửa</a>
                            <?php if ($tk['trang_thai'] == 1): ?>
                                <a href="?act=admin-toggle-tai-khoan-status&id=<?= $tk['id'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Bạn có chắc muốn tạm khóa tài khoản này?')">Ban</a>
                            <?php else: ?>
                                <a href="?act=admin-toggle-tai-khoan-status&id=<?= $tk['id'] ?>" 
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Bạn có chắc muốn mở ban tài khoản này?')">Mở ban</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?act=admin-list-tai-khoan&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>">« Trước</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $page): ?>
                <span class="active"><?= $i ?></span>
            <?php else: ?>
                <a href="?act=admin-list-tai-khoan&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($page < $totalPages): ?>
            <a href="?act=admin-list-tai-khoan&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>">Sau »</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

