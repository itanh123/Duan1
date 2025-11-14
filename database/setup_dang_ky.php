<?php
/**
 * Script tạo bảng dang_ky tự động
 * Chạy file này một lần để tạo bảng dang_ky trong database
 */

require_once __DIR__ . '/../Commons/env.php';
require_once __DIR__ . '/../Commons/function.php';

try {
    $db = connectDB();
    
    // Kiểm tra xem bảng đã tồn tại chưa
    $checkTable = $db->query("SHOW TABLES LIKE 'dang_ky'");
    
    if ($checkTable->rowCount() > 0) {
        echo "Bảng 'dang_ky' đã tồn tại!\n";
        exit;
    }
    
    // Tạo bảng dang_ky
    $sql = "CREATE TABLE IF NOT EXISTS `dang_ky` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `id_khoa_hoc` int(11) NOT NULL,
      `id_lop` int(11) DEFAULT NULL,
      `ho_ten` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `sdt` varchar(20) NOT NULL,
      `ghi_chu` text DEFAULT NULL,
      `trang_thai` varchar(50) DEFAULT 'Chờ xử lý' COMMENT 'Chờ xử lý, Đã xác nhận, Đã hủy',
      `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_khoa_hoc` (`id_khoa_hoc`),
      KEY `idx_lop` (`id_lop`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    
    echo "Đã tạo bảng 'dang_ky' thành công!\n";
    
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}

