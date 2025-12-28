<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đăng ký - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            color: #333;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error, .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .filter-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .stats-section {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            flex: 1;
        }
        
        .stat-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .stat-value {
            color: #333;
            font-size: 24px;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        table tr:hover {
            background: #f8f9fa;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .status-warning {
            background: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
        }
        
        .pagination {
            display: flex;
            gap: 5px;
            margin-top: 20px;
            justify-content: center;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination .active, .pagination .current {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <span>Quản lý đăng ký</span>
            <div style="display: flex; gap: 10px;">
                <a href="?act=admin-dashboard" class="btn btn-secondary">🏠 Trang chủ</a>
            </div>
        </h1>

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

        <!-- Bộ lọc và tìm kiếm -->
        <div class="filter-section">
            <form method="GET" action="?act=admin-list-dang-ky">
                <input type="hidden" name="act" value="admin-list-dang-ky">
                
                <div class="filter-row">
                    <div class="form-group">
                        <label for="search">Tìm kiếm</label>
                        <input type="text" 
                               name="search" 
                               id="search"
                               class="form-control" 
                               placeholder="Tên học sinh, email, lớp học..." 
                               value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="id_lop">Lọc theo lớp học</label>
                        <select name="id_lop" id="id_lop" class="form-control">
                            <option value="">-- Tất cả lớp học --</option>
                            <?php if (isset($lopHocList) && !empty($lopHocList)): ?>
                                <?php foreach ($lopHocList as $lh): ?>
                                    <option value="<?= $lh['id'] ?>" 
                                            <?= (isset($id_lop) && $id_lop == $lh['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($lh['ten_lop']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="trang_thai">Lọc theo trạng thái</label>
                        <select name="trang_thai" id="trang_thai" class="form-control">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="Chờ xác nhận" <?= (isset($trang_thai) && $trang_thai == 'Chờ xác nhận') ? 'selected' : '' ?>>Chờ xác nhận</option>
                            <option value="Đã xác nhận" <?= (isset($trang_thai) && $trang_thai == 'Đã xác nhận') ? 'selected' : '' ?>>Đã xác nhận</option>
                            <option value="Đã hủy" <?= (isset($trang_thai) && $trang_thai == 'Đã hủy') ? 'selected' : '' ?>>Đã hủy</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                    </div>
                    
                    <?php if (!empty($search) || !empty($id_lop) || !empty($trang_thai)): ?>
                    <div class="form-group">
                        <a href="?act=admin-list-dang-ky" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Thống kê -->
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-label">Tổng số đăng ký</div>
                <div class="stat-value"><?= number_format($total ?? 0) ?></div>
            </div>
        </div>

        <!-- Danh sách đăng ký -->
        <?php if (empty($dangKy)): ?>
            <div class="empty-state">
                <p>Không tìm thấy đăng ký nào.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Học sinh</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Lớp học</th>
                        <th>Khóa học</th>
                        <th>Ngày đăng ký</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dangKy as $dk): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($dk['ten_hoc_sinh'] ?? 'N/A') ?></strong></td>
                            <td><?= htmlspecialchars($dk['email_hoc_sinh'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($dk['so_dien_thoai'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($dk['ten_lop'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($dk['ten_khoa_hoc'] ?? 'N/A') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($dk['ngay_dang_ky'])) ?></td>
                            <td>
                                <?php 
                                $trangThaiClass = 'status-warning';
                                if ($dk['trang_thai'] == 'Đã xác nhận') {
                                    $trangThaiClass = 'status-active';
                                } elseif ($dk['trang_thai'] == 'Đã hủy') {
                                    $trangThaiClass = 'status-inactive';
                                }
                                ?>
                                <span class="<?= $trangThaiClass ?>">
                                    <?= htmlspecialchars($dk['trang_thai'] ?? 'Chờ xác nhận') ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-edit-dang-ky&id=<?= $dk['id'] ?>" 
                                       class="btn btn-warning btn-sm">Sửa</a>
                                    <a href="?act=admin-delete-dang-ky&id=<?= $dk['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa đăng ký này?')">Xóa</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?act=admin-list-dang-ky&page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($id_lop) ? '&id_lop=' . $id_lop : '' ?><?= !empty($trang_thai) ? '&trang_thai=' . urlencode($trang_thai) : '' ?>">« Trước</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                            <?php if ($i == $page): ?>
                                <span class="active"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?act=admin-list-dang-ky&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($id_lop) ? '&id_lop=' . $id_lop : '' ?><?= !empty($trang_thai) ? '&trang_thai=' . urlencode($trang_thai) : '' ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?act=admin-list-dang-ky&page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?><?= !empty($id_lop) ? '&id_lop=' . $id_lop : '' ?><?= !empty($trang_thai) ? '&trang_thai=' . urlencode($trang_thai) : '' ?>">Sau »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
