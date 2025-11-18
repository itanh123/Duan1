-- Tạo bảng lưu nhiều vai trò cho một người dùng
CREATE TABLE IF NOT EXISTS `nguoi_dung_vai_tro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_nguoi_dung` int(11) NOT NULL,
  `vai_tro` enum('admin','giang_vien','hoc_sinh') NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_nguoi_dung_vai_tro` (`id_nguoi_dung`, `vai_tro`),
  KEY `idx_id_nguoi_dung` (`id_nguoi_dung`),
  CONSTRAINT `fk_nguoi_dung_vai_tro_nguoi_dung` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Di chuyển dữ liệu từ cột vai_tro cũ sang bảng mới
INSERT INTO `nguoi_dung_vai_tro` (`id_nguoi_dung`, `vai_tro`)
SELECT `id`, `vai_tro` FROM `nguoi_dung` WHERE `vai_tro` IS NOT NULL;

