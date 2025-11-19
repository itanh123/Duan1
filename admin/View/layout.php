<?php
// Kiểm tra quyền để hiển thị menu
require_once('./admin/Model/adminmodel.php');
$adminModel = new adminmodel();
$adminId = $_SESSION['admin_id'] ?? 0;
$hasQuanTri = $adminModel->hasPermission($adminId, 'quan_tri');
$hasXem = $hasQuanTri || $adminModel->hasPermission($adminId, 'xem');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Panel' ?> - Quản lý Hệ thống</title>
    <link rel="stylesheet" href="./admin/View/css/admin.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background: rgba(0,0,0,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .sidebar-header p {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-section {
            margin-bottom: 30px;
        }
        
        .menu-section-title {
            padding: 10px 20px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.6;
            font-weight: 600;
        }
        
        .menu-item {
            display: block;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
            font-size: 14px;
        }
        
        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #3498db;
        }
        
        .menu-item.active {
            background: rgba(255,255,255,0.15);
            color: white;
            border-left-color: #3498db;
            font-weight: 600;
        }
        
        .menu-item-icon {
            display: inline-block;
            width: 20px;
            margin-right: 10px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            min-height: 100vh;
        }
        
        /* Top Header */
        .top-header {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .top-header h1 {
            font-size: 24px;
            color: #333;
            font-weight: 600;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info span {
            color: #666;
            font-size: 14px;
        }
        
        .logout-btn {
            padding: 8px 16px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: #c82333;
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s;
            }
            
            .sidebar.open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: block;
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1001;
                background: #2c3e50;
                color: white;
                border: none;
                padding: 10px;
                border-radius: 5px;
                cursor: pointer;
            }
        }
        
        .menu-toggle {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2>🏢 Admin Panel</h2>
            <p><?= htmlspecialchars($_SESSION['admin_ho_ten'] ?? 'Administrator') ?></p>
        </div>
        
        <nav class="sidebar-menu">
            <?php if ($hasXem): ?>
            <div class="menu-section">
                <div class="menu-section-title">Tổng quan</div>
                <a href="?act=admin-dashboard" class="menu-item <?= (isset($_GET['act']) && $_GET['act'] == 'admin-dashboard') ? 'active' : '' ?>">
                    <span class="menu-item-icon">📊</span>
                    Dashboard
                </a>
            </div>
            
            <div class="menu-section">
                <div class="menu-section-title">Quản lý nội dung</div>
                <?php if ($hasXem): ?>
                <a href="?act=admin-list-khoa-hoc" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'khoa-hoc') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">📚</span>
                    Khóa Học
                </a>
                <a href="?act=admin-list-danh-muc" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'danh-muc') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">📁</span>
                    Danh Mục
                </a>
                <a href="?act=admin-list-binh-luan" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'binh-luan') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">💬</span>
                    Bình Luận
                </a>
                <?php endif; ?>
            </div>
            
            <div class="menu-section">
                <div class="menu-section-title">Quản lý lớp học</div>
                <?php if ($hasXem): ?>
                <a href="?act=admin-list-lop-hoc" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'lop-hoc') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">🏫</span>
                    Lớp Học
                </a>
                <a href="?act=admin-list-ca-hoc" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'ca-hoc') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">⏰</span>
                    Ca Học
                </a>
                <a href="?act=admin-list-phong-hoc" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'phong-hoc') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">🏢</span>
                    Phòng Học
                </a>
                <?php endif; ?>
            </div>
            
            <?php if ($hasQuanTri): ?>
            <div class="menu-section">
                <div class="menu-section-title">Quản lý hệ thống</div>
                <a href="?act=admin-list-phan-quyen" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'phan-quyen') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">🔐</span>
                    Phân Quyền
                </a>
            </div>
            <?php endif; ?>
            
            <div class="menu-section">
                <div class="menu-section-title">Quản lý người dùng</div>
                <?php if ($hasXem): ?>
                <a href="?act=admin-list-tai-khoan" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'tai-khoan') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">👤</span>
                    Tài Khoản
                </a>
                <a href="?act=admin-list-hoc-sinh" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'hoc-sinh') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">👥</span>
                    Học Sinh
                </a>
                <a href="?act=admin-list-giang-vien" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'giang-vien') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">👨‍🏫</span>
                    Giảng Viên
                </a>
                <?php endif; ?>
            </div>
            
            <div class="menu-section">
                <div class="menu-section-title">Đăng ký & Thanh toán</div>
                <?php if ($hasXem): ?>
                <a href="?act=admin-list-dang-ky" class="menu-item <?= (isset($_GET['act']) && strpos($_GET['act'], 'dang-ky') !== false) ? 'active' : '' ?>">
                    <span class="menu-item-icon">📝</span>
                    Đăng Ký
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div>
                <button class="menu-toggle" onclick="toggleSidebar()">☰</button>
                <h1><?= $pageTitle ?? 'Admin Panel' ?></h1>
            </div>
            <div class="user-info">
                <span>Xin chào, <strong><?= htmlspecialchars($_SESSION['admin_ho_ten'] ?? 'Admin') ?></strong></span>
                <a href="?act=admin-logout" class="logout-btn">🚪 Đăng xuất</a>
            </div>
        </header>
        
        <!-- Content Area -->
        <div class="content-area">
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
            
            <?= $content ?? '' ?>
        </div>
    </main>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('open');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.querySelector('.menu-toggle');
            
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target) && sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                }
            }
        });
    </script>
</body>
</html>

