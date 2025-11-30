-- Bảng yêu cầu đổi lịch
CREATE TABLE IF NOT EXISTS `yeu_cau_doi_lich` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_giang_vien` int(11) NOT NULL,
  `id_ca_hoc_cu` int(11) NOT NULL COMMENT 'ID ca học hiện tại cần đổi',
  `id_lop` int(11) NOT NULL,
  `thu_trong_tuan_moi` varchar(20) NOT NULL COMMENT 'Thứ trong tuần mới (Thứ 2, Thứ 3, ...)',
  `id_ca_moi` int(11) DEFAULT NULL COMMENT 'ID ca mặc định mới',
  `id_phong_moi` int(11) DEFAULT NULL COMMENT 'ID phòng học mới',
  `ngay_doi` date DEFAULT NULL COMMENT 'Ngày cụ thể cần đổi (nếu đổi một ngày cụ thể)',
  `ly_do` text DEFAULT NULL COMMENT 'Lý do đổi lịch',
  `trang_thai` enum('cho_duyet','da_duyet','tu_choi') DEFAULT 'cho_duyet',
  `ghi_chu_admin` text DEFAULT NULL COMMENT 'Ghi chú của admin khi duyệt/từ chối',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_giang_vien` (`id_giang_vien`),
  KEY `id_ca_hoc_cu` (`id_ca_hoc_cu`),
  KEY `id_lop` (`id_lop`),
  KEY `trang_thai` (`trang_thai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

