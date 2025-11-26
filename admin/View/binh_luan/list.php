<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý bình luận - Admin</title>
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
        
        .alert-error {
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
            background: #007bff;
            color: white;
            font-weight: 600;
        }
        
        table tr:hover {
            background: #f8f9fa;
        }
        
        .status-hien-thi {
            color: #28a745;
            font-weight: 600;
        }
        
        .status-an {
            color: #ffc107;
            font-weight: 600;
        }
        
        .status-da-xoa {
            color: #dc3545;
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .pagination .active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .noi-dung {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .stars {
            color: #ffc107;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <span>Quản lý bình luận</span>
            <a href="?act=admin-dashboard" class="btn btn-secondary">🏠 Trang chủ</a>
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

        <div class="filter-section">
            <form method="GET" action="">
                <input type="hidden" name="act" value="admin-list-binh-luan">
                <div class="filter-group">
                    <div class="form-group">
                        <label>Tìm kiếm</label>
                        <input type="text" name="search" class="form-control" 
                               value="<?= htmlspecialchars($search ?? '') ?>" 
                               placeholder="Tìm theo nội dung, học sinh, khóa học...">
                    </div>
                    <div class="form-group">
                        <label>Khóa học</label>
                        <select name="id_khoa_hoc" class="form-control">
                            <option value="">Tất cả khóa học</option>
                            <?php foreach ($khoaHocList ?? [] as $kh): ?>
                                <option value="<?= $kh['id'] ?>" <?= (isset($id_khoa_hoc) && $id_khoa_hoc == $kh['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kh['ten_khoa_hoc']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <select name="trang_thai" class="form-control">
                            <option value="">Tất cả</option>
                            <option value="Hiển thị" <?= (isset($trang_thai) && $trang_thai == 'Hiển thị') ? 'selected' : '' ?>>Hiển thị</option>
                            <option value="Ẩn" <?= (isset($trang_thai) && $trang_thai == 'Ẩn') ? 'selected' : '' ?>>Ẩn</option>
                            <option value="Đã xóa" <?= (isset($trang_thai) && $trang_thai == 'Đã xóa') ? 'selected' : '' ?>>Đã xóa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Tìm kiếm</button>
                    </div>
                    <?php if (!empty($search) || !empty($id_khoa_hoc) || !empty($trang_thai)): ?>
                    <div class="form-group">
                        <a href="?act=admin-list-binh-luan" class="btn btn-warning" style="width: 100%;">Xóa bộ lọc</a>
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <?php if (empty($binhLuan)): ?>
            <div class="empty-state">
                <p>Không tìm thấy bình luận nào.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Khóa học</th>
                        <th>Học sinh</th>
                        <th>Nội dung</th>
                        <th>Đánh giá</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($binhLuan as $bl): ?>
                        <tr>
                            <td><?= htmlspecialchars($bl['ten_khoa_hoc'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($bl['ten_hoc_sinh'] ?? 'N/A') ?></td>
                            <td class="noi-dung" title="<?= htmlspecialchars($bl['noi_dung']) ?>">
                                <?= htmlspecialchars($bl['noi_dung']) ?>
                            </td>
                            <td>
                                <?php if ($bl['danh_gia']): ?>
                                    <span class="stars">
                                        <?= str_repeat('★', $bl['danh_gia']) ?><?= str_repeat('☆', 5 - $bl['danh_gia']) ?>
                                    </span>
                                    (<?= $bl['danh_gia'] ?>/5)
                                <?php else: ?>
                                    <span style="color: #999;">Chưa đánh giá</span>
                                <?php endif; ?>
                            </td>
                            <td><?= isset($bl['ngay_tao']) ? date('d/m/Y H:i', strtotime($bl['ngay_tao'])) : 'N/A' ?></td>
                            <td>
                                <?php
                                $statusClass = 'status-hien-thi';
                                if ($bl['trang_thai'] == 'Ẩn') $statusClass = 'status-an';
                                if ($bl['trang_thai'] == 'Đã xóa') $statusClass = 'status-da-xoa';
                                ?>
                                <span class="<?= $statusClass ?>">
                                    <?= htmlspecialchars($bl['trang_thai']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="?act=admin-edit-binh-luan&id=<?= $bl['id'] ?>" 
                                       class="btn btn-warning btn-sm">Sửa</a>
                                    <a href="?act=admin-delete-binh-luan&id=<?= $bl['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">Xóa</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?act=admin-list-binh-luan&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>&id_khoa_hoc=<?= urlencode($id_khoa_hoc ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>">« Trước</a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                            <a href="?act=admin-list-binh-luan&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&id_khoa_hoc=<?= urlencode($id_khoa_hoc ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>" 
                               class="<?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?act=admin-list-binh-luan&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>&id_khoa_hoc=<?= urlencode($id_khoa_hoc ?? '') ?>&trang_thai=<?= urlencode($trang_thai ?? '') ?>">Sau »</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>

