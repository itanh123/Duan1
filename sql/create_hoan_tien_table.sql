-- Tạo bảng hoan_tien để lưu thông tin hoàn tiền
CREATE TABLE IF NOT EXISTS `hoan_tien` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `id_thanh_toan` INT(11) NOT NULL COMMENT 'ID thanh toán gốc',
  `ma_hoan_tien` VARCHAR(100) NOT NULL COMMENT 'Mã đơn hàng hoàn tiền (REF...)',
  `ma_giao_dich_hoan_tien` VARCHAR(100) DEFAULT NULL COMMENT 'Mã giao dịch hoàn tiền từ VNPay',
  `so_tien_hoan` DECIMAL(15,2) NOT NULL COMMENT 'Số tiền hoàn (VND)',
  `ly_do` TEXT DEFAULT NULL COMMENT 'Lý do hoàn tiền',
  `trang_thai` VARCHAR(50) DEFAULT 'Đang xử lý' COMMENT 'Trạng thái: Đang xử lý, Thành công, Thất bại',
  `ngay_tao` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
  `ngay_cap_nhat` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ma_hoan_tien` (`ma_hoan_tien`),
  KEY `idx_id_thanh_toan` (`id_thanh_toan`),
  KEY `idx_ma_hoan_tien` (`ma_hoan_tien`),
  CONSTRAINT `fk_hoan_tien_thanh_toan` FOREIGN KEY (`id_thanh_toan`) REFERENCES `thanh_toan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu thông tin hoàn tiền';

