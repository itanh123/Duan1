<?php
/**
 * Script cài đặt bảng yeu_cau_doi_lich
 * Chạy file này một lần để tạo bảng trong database
 */

require_once('./Commons/env.php');
require_once('./Commons/function.php');

try {
    $db = connectDB();
    
    $sql = "CREATE TABLE IF NOT EXISTS `yeu_cau_doi_lich` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->exec($sql);
    
    echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Cài đặt thành công</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #c3e6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border: 1px solid #bee5eb;
        }
    </style>
</head>
<body>
    <div class='success'>
        <h2>✓ Cài đặt thành công!</h2>
        <p>Bảng <strong>yeu_cau_doi_lich</strong> đã được tạo thành công trong database.</p>
    </div>
    <div class='info'>
        <p><strong>Lưu ý:</strong> Bạn có thể xóa file <code>install_yeu_cau_doi_lich.php</code> sau khi cài đặt xong.</p>
        <p><a href='?act=giang-vien-dashboard'>Quay lại Dashboard</a></p>
    </div>
</body>
</html>";
    
} catch (PDOException $e) {
    echo "<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Lỗi cài đặt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class='error'>
        <h2>✗ Lỗi cài đặt</h2>
        <p><strong>Lỗi:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <p>Vui lòng kiểm tra kết nối database và thử lại.</p>
    </div>
</body>
</html>";
}

