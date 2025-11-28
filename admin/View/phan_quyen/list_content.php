<?php
$pageTitle = 'Quản lý Phân quyền';
?>

<style>
.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.badge-primary {
    background: #3498db;
    color: white;
}

.badge-info {
    background: #17a2b8;
    color: white;
}

.badge-success {
    background: #28a745;
    color: white;
}

.badge-secondary {
    background: #6c757d;
    color: white;
}

.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.content-header h1 {
    margin: 0;
}

.content-header .btn {
    padding: 8px 16px;
    background: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    transition: all 0.3s;
}

.content-header .btn:hover {
    background: #2980b9;
}

.search-form {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.card-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}

.card-header h3 {
    margin: 0;
}

.card-body {
    padding: 20px;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.data-table th {
    background: #f8f9fa;
    font-weight: 600;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.btn {
    padding: 8px 16px;
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

.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
}

.pagination {
    display: flex;
    gap: 5px;
    margin-top: 20px;
    justify-content: center;
}

.pagination .btn {
    min-width: 40px;
    text-align: center;
}

.text-muted {
    color: #6c757d;
    text-align: center;
    padding: 20px;
}
</style>

<div class="content-header">
    <h1>Quản lý Phân quyền</h1>
    <a href="?act=admin-add-phan-quyen" class="btn btn-primary">+ Thêm Phân quyền</a>
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
        <h3>Danh sách Phân quyền</h3>
        <form method="GET" action="" class="search-form">
            <input type="hidden" name="act" value="admin-list-phan-quyen">
            
            <!-- Filter theo vai trò -->
            <select name="vai_tro" class="form-control" style="width: 180px; display: inline-block;">
                <option value="">Tất cả</option>
                <option value="admin" <?= ($vai_tro ?? '') == 'admin' ? 'selected' : '' ?>>Tất cả Admin</option>
                <option value="giang_vien" <?= ($vai_tro ?? '') == 'giang_vien' ? 'selected' : '' ?>>Tất cả Giảng viên</option>
                <option value="hoc_sinh" <?= ($vai_tro ?? '') == 'hoc_sinh' ? 'selected' : '' ?>>Tất cả Học sinh</option>
            </select>
            
            <!-- Filter theo người dùng cụ thể -->
            <select name="id_nguoi_dung" class="form-control" style="width: 200px; display: inline-block;">
                <option value="">Chọn người dùng</option>
                <?php foreach ($nguoiDungList as $nd): ?>
                    <option value="<?= $nd['id'] ?>" <?= ($id_nguoi_dung ?? '') == $nd['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($nd['ho_ten']) ?> (<?= htmlspecialchars($nd['vai_tro']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" name="search" placeholder="Tìm kiếm theo tên, email..." 
                   value="<?= htmlspecialchars($search ?? '') ?>" class="form-control" style="width: 250px; display: inline-block;">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            <?php if (!empty($search) || !empty($id_nguoi_dung) || !empty($vai_tro)): ?>
                <a href="?act=admin-list-phan-quyen" class="btn btn-secondary">Xóa bộ lọc</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card-body">
        <?php if (empty($phanQuyen)): ?>
            <p class="text-muted">Không có phân quyền nào.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Người dùng</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Quyền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Nhóm theo người dùng
                    $groupedUsers = [];
                    foreach ($phanQuyen as $pq) {
                        $userId = $pq['id_nguoi_dung'];
                        if (!isset($groupedUsers[$userId])) {
                            $groupedUsers[$userId] = [
                                'id_nguoi_dung' => $pq['id_nguoi_dung'],
                                'ho_ten' => $pq['ho_ten'],
                                'email' => $pq['email'],
                                'vai_tro' => $pq['vai_tro'],
                                'quyen' => []
                            ];
                        }
                        if (!empty($pq['ten_quyen'])) {
                            $groupedUsers[$userId]['quyen'][] = $pq['ten_quyen'];
                        }
                    }
                    
                    foreach ($groupedUsers as $user):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($user['ho_ten']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge badge-<?= $user['vai_tro'] == 'admin' ? 'primary' : ($user['vai_tro'] == 'giang_vien' ? 'info' : 'secondary') ?>">
                                    <?= ucfirst($user['vai_tro']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($user['quyen'])): ?>
                                    <?php 
                                    $quyenLabels = [
                                        'xem' => 'Xem',
                                        'them' => 'Thêm',
                                        'sua' => 'Sửa',
                                        'xoa' => 'Xóa',
                                        'quan_tri' => 'Quản trị'
                                    ];
                                    foreach ($user['quyen'] as $quyen): 
                                    ?>
                                        <span class="badge badge-success" style="display: inline-block; margin: 2px;">
                                            <?= $quyenLabels[$quyen] ?? $quyen ?>
                                        </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="color: #999;">Chưa có quyền</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?act=admin-manage-quyen-nguoi-dung&id_nguoi_dung=<?= $user['id_nguoi_dung'] ?>" 
                                   class="btn btn-sm btn-primary">Quản lý Quyền</a>
                                <a href="?act=admin-manage-vai-tro-nguoi-dung&id_nguoi_dung=<?= $user['id_nguoi_dung'] ?>" 
                                   class="btn btn-sm btn-info" 
                                   style="background: #17a2b8; color: white;">Quản lý Vai trò</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?act=admin-list-phan-quyen&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>&id_nguoi_dung=<?= urlencode($id_nguoi_dung ?? '') ?>&vai_tro=<?= urlencode($vai_tro ?? '') ?>" class="btn">« Trước</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="btn btn-primary"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?act=admin-list-phan-quyen&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&id_nguoi_dung=<?= urlencode($id_nguoi_dung ?? '') ?>&vai_tro=<?= urlencode($vai_tro ?? '') ?>" class="btn"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?act=admin-list-phan-quyen&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>&id_nguoi_dung=<?= urlencode($id_nguoi_dung ?? '') ?>&vai_tro=<?= urlencode($vai_tro ?? '') ?>" class="btn">Sau »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

