<?php
/**
 * Script kiểm tra và tự động sửa bảng dang_ky
 * Chạy file này một lần để đảm bảo bảng có đủ các cột cần thiết
 */

require_once __DIR__ . '/../Commons/env.php';
require_once __DIR__ . '/../Commons/function.php';

try {
    $db = connectDB();
    
    echo "Đang kiểm tra bảng dang_ky...\n\n";
    
    // Kiểm tra và thêm các cột cần thiết
    $columnsToAdd = [
        'id_hoc_sinh' => "ALTER TABLE `dang_ky` ADD COLUMN IF NOT EXISTS `id_hoc_sinh` INT(11) NULL AFTER `id`",
        'ngay_dang_ky' => "ALTER TABLE `dang_ky` ADD COLUMN IF NOT EXISTS `ngay_dang_ky` DATETIME NULL AFTER `trang_thai`",
        'vnp_TxnRef' => "ALTER TABLE `dang_ky` ADD COLUMN IF NOT EXISTS `vnp_TxnRef` VARCHAR(100) NULL COMMENT 'Mã đơn hàng VNPay' AFTER `ngay_dang_ky`",
        'vnp_TransactionNo' => "ALTER TABLE `dang_ky` ADD COLUMN IF NOT EXISTS `vnp_TransactionNo` VARCHAR(100) NULL COMMENT 'Mã giao dịch VNPay' AFTER `vnp_TxnRef`"
    ];
    
    foreach ($columnsToAdd as $columnName => $sql) {
        // Kiểm tra xem cột đã tồn tại chưa
        $checkSql = "SHOW COLUMNS FROM dang_ky LIKE '$columnName'";
        $result = $db->query($checkSql);
        
        if ($result->rowCount() == 0) {
            echo "Thêm cột: $columnName\n";
            try {
                // MySQL không hỗ trợ IF NOT EXISTS trong ALTER TABLE, nên dùng cách khác
                $db->exec($sql);
                echo "  ✓ Đã thêm cột $columnName thành công\n";
            } catch (PDOException $e) {
                // Nếu lỗi do cột đã tồn tại, bỏ qua
                if (strpos($e->getMessage(), 'Duplicate column') === false) {
                    echo "  ✗ Lỗi khi thêm cột $columnName: " . $e->getMessage() . "\n";
                } else {
                    echo "  ✓ Cột $columnName đã tồn tại\n";
                }
            }
        } else {
            echo "  ✓ Cột $columnName đã tồn tại\n";
        }
    }
    
    // Thêm index nếu chưa có
    echo "\nĐang kiểm tra index...\n";
    
    // Kiểm tra và tạo index cho id_hoc_sinh
    $checkIndex1 = $db->query("SHOW INDEX FROM dang_ky WHERE Key_name = 'idx_id_hoc_sinh'");
    if ($checkIndex1->rowCount() == 0) {
        try {
            $db->exec("CREATE INDEX idx_id_hoc_sinh ON dang_ky(id_hoc_sinh)");
            echo "  ✓ Đã tạo index idx_id_hoc_sinh\n";
        } catch (PDOException $e) {
            echo "  ✗ Lỗi khi tạo index idx_id_hoc_sinh: " . $e->getMessage() . "\n";
        }
    } else {
        echo "  ✓ Index idx_id_hoc_sinh đã tồn tại\n";
    }
    
    // Kiểm tra và tạo index cho vnp_TxnRef
    $checkIndex2 = $db->query("SHOW INDEX FROM dang_ky WHERE Key_name = 'idx_vnp_TxnRef'");
    if ($checkIndex2->rowCount() == 0) {
        try {
            $db->exec("CREATE INDEX idx_vnp_TxnRef ON dang_ky(vnp_TxnRef)");
            echo "  ✓ Đã tạo index idx_vnp_TxnRef\n";
        } catch (PDOException $e) {
            echo "  ✗ Lỗi khi tạo index idx_vnp_TxnRef: " . $e->getMessage() . "\n";
        }
    } else {
        echo "  ✓ Index idx_vnp_TxnRef đã tồn tại\n";
    }
    
    echo "\n✓ Hoàn tất kiểm tra và cập nhật bảng dang_ky!\n";
    
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
    exit(1);
}

