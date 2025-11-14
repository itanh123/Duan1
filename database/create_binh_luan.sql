-- Tạo bảng binh_luan (Bình luận)
-- Chạy file này trong phpMyAdmin hoặc MySQL để tạo bảng

CREATE TABLE IF NOT EXISTS `binh_luan` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

