<?php
/**
 * Script tạo bảng binh_luan tự động
 * Chạy file này một lần để tạo bảng binh_luan trong database
 */

require_once __DIR__ . '/../Commons/env.php';
require_once __DIR__ . '/../Commons/function.php';

try {
    $db = connectDB();
    
    // Kiểm tra xem bảng đã tồn tại chưa
    $checkTable = $db->query("SHOW TABLES LIKE 'binh_luan'");
    
    if ($checkTable->rowCount() > 0) {
        echo "Bảng 'binh_luan' đã tồn tại!\n";
        exit;
    }
    
    // Tạo bảng binh_luan
    $sql = "CREATE TABLE IF NOT EXISTS `binh_luan` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `id_khoa_hoc` int(11) NOT NULL,
      `id_hoc_sinh` int(11) NOT NULL,
      `noi_dung` text NOT NULL,
      `danh_gia` int(11) DEFAULT NULL COMMENT 'Đánh giá từ 1-5 sao',
      `trang_thai` varchar(50) DEFAULT 'Hiển thị' COMMENT 'Hiển thị hoặc Ẩn',
      `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_khoa_hoc` (`id_khoa_hoc`),
      KEY `idx_hoc_sinh` (`id_hoc_sinh`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql);
    
    echo "Đã tạo bảng 'binh_luan' thành công!\n";
    
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}

