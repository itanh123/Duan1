<?php
/**
 * Script kiểm tra và sửa lỗi bảng nguoi_dung
 * Chạy file này một lần để đảm bảo bảng nguoi_dung có AUTO_INCREMENT cho trường id
 */

require_once __DIR__ . '/Commons/env.php';
require_once __DIR__ . '/Commons/function.php';

try {
    $db = connectDB();
    
    // Kiểm tra cấu trúc bảng hiện tại
    $result = $db->query("SHOW COLUMNS FROM nguoi_dung WHERE Field = 'id'");
    $column = $result->fetch(PDO::FETCH_ASSOC);
    
    if ($column) {
        echo "Thông tin trường 'id' hiện tại:\n";
        print_r($column);
        echo "\n";
        
        // Kiểm tra xem có AUTO_INCREMENT chưa
        if (strpos($column['Extra'], 'auto_increment') === false) {
            echo "Trường 'id' chưa có AUTO_INCREMENT. Đang sửa...\n";
            
            // Sửa trường id để có AUTO_INCREMENT
            $sql = "ALTER TABLE nguoi_dung MODIFY id INT NOT NULL AUTO_INCREMENT";
            $db->exec($sql);
            
            echo "Đã thêm AUTO_INCREMENT cho trường 'id' thành công!\n";
        } else {
            echo "Trường 'id' đã có AUTO_INCREMENT.\n";
        }
    } else {
        echo "Không tìm thấy trường 'id' trong bảng nguoi_dung!\n";
    }
    
    // Kiểm tra và đảm bảo có PRIMARY KEY
    $result = $db->query("SHOW INDEXES FROM nguoi_dung WHERE Key_name = 'PRIMARY'");
    $primaryKey = $result->fetch(PDO::FETCH_ASSOC);
    
    if (!$primaryKey) {
        echo "Bảng chưa có PRIMARY KEY. Đang thêm...\n";
        $sql = "ALTER TABLE nguoi_dung ADD PRIMARY KEY (id)";
        $db->exec($sql);
        echo "Đã thêm PRIMARY KEY thành công!\n";
    } else {
        echo "Bảng đã có PRIMARY KEY.\n";
    }
    
    echo "\nHoàn tất!\n";
    
} catch (PDOException $e) {
    echo "Lỗi: " . $e->getMessage() . "\n";
}

