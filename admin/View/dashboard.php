<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ Admin - Quản lý Hệ thống</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .stat-card.blue { border-left-color: #007bff; }
        .stat-card.green { border-left-color: #28a745; }
        .stat-card.orange { border-left-color: #ffc107; }
        .stat-card.purple { border-left-color: #6f42c1; }
        .stat-card.red { border-left-color: #dc3545; }
        .stat-card.teal { border-left-color: #20c997; }
        .stat-card.pink { border-left-color: #e83e8c; }
        .stat-card.indigo { border-left-color: #6610f2; }
        
        .stat-card-icon {
            font-size: 36px;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .stat-card-title {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .stat-card-value {
            font-size: 32px;
            font-weight: 700;
            color: #333;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .card-header h2 {
            font-size: 20px;
            color: #333;
            margin: 0;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        
        table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #495057;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        table tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .menu-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
            border: 2px solid transparent;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border-color: #007bff;
            color: #007bff;
        }
        
        .menu-card-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .menu-card-title {
            font-size: 18px;
            font-weight: 600;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1>📊 Trang Quản Trị Hệ Thống</h1>
                    <p>Chào mừng, <?= htmlspecialchars($_SESSION['admin_ho_ten'] ?? 'Admin') ?> (<?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?>)</p>
                </div>
                <a href="?act=admin-logout" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; transition: all 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">🚪 Đăng xuất</a>
            </div>
        </div>

        <!-- Thống kê tổng quan -->
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-card-icon">📚</div>
                <div class="stat-card-title">Tổng Khóa Học</div>
                <div class="stat-card-value"><?= number_format($thongKe['tong_khoa_hoc'] ?? 0) ?></div>
            </div>
            
            <div class="stat-card green">
                <div class="stat-card-icon">👥</div>
                <div class="stat-card-title">Tổng Học Sinh</div>
                <div class="stat-card-value"><?= number_format($thongKe['tong_hoc_sinh'] ?? 0) ?></div>
            </div>
            
            <div class="stat-card orange">
                <div class="stat-card-icon">👨‍🏫</div>
                <div class="stat-card-title">Tổng Giảng Viên</div>
                <div class="stat-card-value"><?= number_format($thongKe['tong_giang_vien'] ?? 0) ?></div>
            </div>
            
            <div class="stat-card purple">
                <div class="stat-card-icon">📝</div>
                <div class="stat-card-title">Tổng Đăng Ký</div>
                <div class="stat-card-value"><?= number_format($thongKe['tong_dang_ky'] ?? 0) ?></div>
            </div>
            
            <div class="stat-card teal">
                <div class="stat-card-icon">✅</div>
                <div class="stat-card-title">Đã Xác Nhận</div>
                <div class="stat-card-value"><?= number_format($thongKe['dang_ky_da_xac_nhan'] ?? 0) ?></div>
            </div>
            
            <div class="stat-card red">
                <div class="stat-card-icon">💰</div>
                <div class="stat-card-title">Tổng Doanh Thu</div>
                <div class="stat-card-value"><?= number_format($thongKe['tong_doanh_thu'] ?? 0, 0, ',', '.') ?> đ</div>
            </div>
            
            <div class="stat-card pink">
                <div class="stat-card-icon">🏫</div>
                <div class="stat-card-title">Tổng Lớp Học</div>
                <div class="stat-card-value"><?= number_format($thongKe['tong_lop_hoc'] ?? 0) ?></div>
            </div>
            
            <div class="stat-card indigo">
                <div class="stat-card-icon">📁</div>
                <div class="stat-card-title">Tổng Danh Mục</div>
                <div class="stat-card-value"><?= number_format($thongKe['tong_danh_muc'] ?? 0) ?></div>
            </div>
        </div>

        <!-- Danh sách mới nhất -->
        <div class="content-grid">
            <!-- Đăng ký mới nhất -->
            <div class="card">
                <div class="card-header">
                    <h2>📋 Đăng Ký Mới Nhất</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($dangKyMoiNhat)): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Học Sinh</th>
                                        <th>Lớp Học</th>
                                        <th>Ngày Đăng Ký</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dangKyMoiNhat as $dk): ?>
                                        <tr>
                                            <td>
                                                <div><strong><?= htmlspecialchars($dk['ho_ten'] ?? 'N/A') ?></strong></div>
                                                <div style="font-size: 12px; color: #6c757d;"><?= htmlspecialchars($dk['email'] ?? '') ?></div>
                                            </td>
                                            <td>
                                                <div><?= htmlspecialchars($dk['ten_lop'] ?? 'N/A') ?></div>
                                                <div style="font-size: 12px; color: #6c757d;"><?= htmlspecialchars($dk['ten_khoa_hoc'] ?? '') ?></div>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($dk['ngay_dang_ky'])) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = match($dk['trang_thai']) {
                                                    'Đã xác nhận' => 'badge-success',
                                                    'Chờ xác nhận' => 'badge-warning',
                                                    'Đã hủy' => 'badge-danger',
                                                    default => 'badge-info'
                                                };
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($dk['trang_thai']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Chưa có đăng ký nào</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Thanh toán mới nhất -->
            <div class="card">
                <div class="card-header">
                    <h2>💳 Thanh Toán Mới Nhất</h2>
                </div>
                <div class="card-body">
                    <?php if (!empty($thanhToanMoiNhat)): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Học Sinh</th>
                                        <th>Số Tiền</th>
                                        <th>Phương Thức</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($thanhToanMoiNhat as $tt): ?>
                                        <tr>
                                            <td>
                                                <div><strong><?= htmlspecialchars($tt['ho_ten'] ?? 'N/A') ?></strong></div>
                                                <div style="font-size: 12px; color: #6c757d;"><?= htmlspecialchars($tt['email'] ?? '') ?></div>
                                            </td>
                                            <td><strong><?= number_format($tt['so_tien'], 0, ',', '.') ?> đ</strong></td>
                                            <td><?= htmlspecialchars($tt['phuong_thuc']) ?></td>
                                            <td>
                                                <?php
                                                $statusClass = match($tt['trang_thai']) {
                                                    'Thành công' => 'badge-success',
                                                    'Chờ xác nhận' => 'badge-warning',
                                                    'Thất bại' => 'badge-danger',
                                                    'Hoàn tiền' => 'badge-info',
                                                    default => 'badge-info'
                                                };
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($tt['trang_thai']) ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <p>Chưa có thanh toán nào</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Menu Quản Lý -->
        <div class="menu-grid">
            <a href="?act=admin-list-khoa-hoc" class="menu-card">
                <div class="menu-card-icon">📚</div>
                <div class="menu-card-title">Quản Lý Khóa Học</div>
            </a>
            
            <a href="?act=admin-list-lop-hoc" class="menu-card">
                <div class="menu-card-icon">🏫</div>
                <div class="menu-card-title">Quản Lý Lớp Học</div>
            </a>
            
            <a href="?act=admin-list-ca-hoc" class="menu-card">
                <div class="menu-card-icon">⏰</div>
                <div class="menu-card-title">Quản Lý Ca Học</div>
            </a>
            
            <a href="?act=admin-list-hoc-sinh" class="menu-card">
                <div class="menu-card-icon">👥</div>
                <div class="menu-card-title">Quản Lý Học Sinh</div>
            </a>
            
            <a href="?act=admin-list-giang-vien" class="menu-card">
                <div class="menu-card-icon">👨‍🏫</div>
                <div class="menu-card-title">Quản Lý Giảng Viên</div>
            </a>
            
            <a href="#" class="menu-card">
                <div class="menu-card-icon">📝</div>
                <div class="menu-card-title">Quản Lý Đăng Ký</div>
            </a>
            
            <a href="#" class="menu-card">
                <div class="menu-card-icon">💳</div>
                <div class="menu-card-title">Quản Lý Thanh Toán</div>
            </a>
            
            <a href="?act=admin-list-danh-muc" class="menu-card">
                <div class="menu-card-icon">📁</div>
                <div class="menu-card-title">Quản Lý Danh Mục</div>
            </a>
            
            <a href="#" class="menu-card">
                <div class="menu-card-icon">💬</div>
                <div class="menu-card-title">Quản Lý Bình Luận</div>
            </a>
            
            <a href="#" class="menu-card">
                <div class="menu-card-icon">📊</div>
                <div class="menu-card-title">Thống Kê & Báo Cáo</div>
            </a>
            
            <a href="#" class="menu-card">
                <div class="menu-card-icon">⚙️</div>
                <div class="menu-card-title">Cài Đặt Hệ Thống</div>
            </a>
        </div>
    </div>
</body>
</html>

