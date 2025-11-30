-- Bảng phản hồi bình luận (admin trả lời bình luận của học sinh)
CREATE TABLE IF NOT EXISTS `phan_hoi_binh_luan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_binh_luan` int(11) NOT NULL COMMENT 'ID bình luận được trả lời',
  `id_admin` int(11) NOT NULL COMMENT 'ID admin trả lời',
  `noi_dung` text NOT NULL COMMENT 'Nội dung phản hồi',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_binh_luan` (`id_binh_luan`),
  KEY `id_admin` (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

