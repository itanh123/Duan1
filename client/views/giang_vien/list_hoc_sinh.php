<?php
// views/giang_vien/list_hoc_sinh.php
// Biến có sẵn: $hocSinh (danh sách học sinh), $page, $totalPages, $search
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Học sinh - Trang bán khóa học lập trình</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #10B981;
            --accent: #d4a6b6;
            --text: #1F2937;
            --muted: #6b7280;
            --bg: #ffffff;
            --container: 1200px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Inter, Arial, sans-serif;
            color: var(--text);
            background: #f5f5f5;
            line-height: 1.6;
        }

        .container {
            max-width: var(--container);
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: #fff;
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 0;
        }

        .logo img {
            height: 48px;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 24px;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            font-weight: 600;
            color: var(--text);
            transition: .2s;
        }

        nav a:hover {
            color: var(--primary);
        }

        .page-title {
            margin: 30px 0;
            font-size: 32px;
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-section {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .filter-form {
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }

        .filter-form .form-group {
            flex: 1;
        }

        .filter-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text);
        }

        .filter-form input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }

        .filter-form button {
            padding: 10px 24px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
        }

        .filter-form button:hover {
            background: #059669;
        }

        .table-wrapper {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
            color: white;
        }

        thead th {
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }

        tbody tr {
            border-bottom: 1px solid #eee;
            transition: .2s;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        tbody td {
            padding: 16px;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-view {
            padding: 6px 16px;
            background: #17a2b8;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: .2s;
            display: inline-block;
        }

        .btn-view:hover {
            background: #138496;
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 30px;
        }

        .pagination a {
            padding: 8px 16px;
            background: #fff;
            color: var(--text);
            text-decoration: none;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: .2s;
        }

        .pagination a:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination a.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
            color: var(--muted);
        }

        .empty-state p {
            font-size: 16px;
            color: var(--muted);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-wrap">
                <div class="logo">
                    <a href="?act=giang-vien-dashboard">
                        <img src="https://websitedemos.net/be-bold-beauty-store-04/wp-content/uploads/sites/1117/2022/08/logo-regular.png" alt="Logo">
                    </a>
                </div>
                <nav>
                    <ul>
                        <li><a href="?act=giang-vien-dashboard"><i class="bi bi-house-door"></i> Dashboard</a></li>
                        <li><a href="?act=giang-vien-lop-hoc"><i class="bi bi-book"></i> Lớp học của tôi</a></li>
                        <li><a href="?act=giang-vien-list-hoc-sinh" style="color: var(--primary);"><i class="bi bi-people"></i> Danh sách học sinh</a></li>
                        <li style="color: var(--primary); font-weight: 600;"><i class="bi bi-person-badge"></i> <?= htmlspecialchars($_SESSION['giang_vien_ho_ten'] ?? '') ?></li>
                        <li><a href="?act=giang-vien-logout" style="color: #dc3545;"><i class="bi bi-box-arrow-right"></i> Đăng xuất</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">
            <i class="bi bi-people"></i>
            Danh sách học sinh
        </h1>

        <!-- Form tìm kiếm -->
        <div class="filter-section">
            <form method="GET" action="" class="filter-form">
                <input type="hidden" name="act" value="giang-vien-list-hoc-sinh">
                <div class="form-group">
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Tìm theo mã, tên, email, số điện thoại...">
                </div>
                <div class="form-group">
                    <button type="submit"><i class="bi bi-search"></i> Tìm kiếm</button>
                </div>
                <?php if (!empty($search)): ?>
                <div class="form-group">
                    <a href="?act=giang-vien-list-hoc-sinh" style="padding: 10px 24px; background: #ffc107; color: #000; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">
                        <i class="bi bi-x-circle"></i> Xóa bộ lọc
                    </a>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Bảng danh sách -->
        <?php if (empty($hocSinh)): ?>
            <div class="empty-state">
                <i class="bi bi-person-x"></i>
                <p>Không tìm thấy học sinh nào.</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th>Trạng thái</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hocSinh as $hs): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($hs['ma_nguoi_dung'] ?? 'N/A') ?></strong></td>
                                <td><strong><?= htmlspecialchars($hs['ho_ten']) ?></strong></td>
                                <td><?= htmlspecialchars($hs['email']) ?></td>
                                <td><?= htmlspecialchars($hs['so_dien_thoai'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($hs['dia_chi'] ?? 'N/A') ?></td>
                                <td>
                                    <span class="status-badge <?= $hs['trang_thai'] == 1 ? 'status-active' : 'status-inactive' ?>">
                                        <?= $hs['trang_thai'] == 1 ? 'Hoạt động' : 'Khóa' ?>
                                    </span>
                                </td>
                                <td><?= isset($hs['ngay_tao']) ? date('d/m/Y', strtotime($hs['ngay_tao'])) : 'N/A' ?></td>
                                <td>
                                    <a href="?act=giang-vien-view-hoc-sinh-detail&id=<?= $hs['id_hoc_sinh'] ?>" class="btn-view">
                                        <i class="bi bi-eye"></i> Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?act=giang-vien-list-hoc-sinh&page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>">
                            <i class="bi bi-chevron-left"></i> Trước
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                            <a href="?act=giang-vien-list-hoc-sinh&page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>" 
                               class="<?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                            <span style="padding: 8px 16px;">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?act=giang-vien-list-hoc-sinh&page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>">
                            Sau <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

