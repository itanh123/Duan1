<?php
/**
 * Cron Job: Hủy các đăng ký quá hạn (sau 10 phút chưa thanh toán)
 * 
 * Cách sử dụng:
 * 1. Windows Task Scheduler: Tạo task chạy file này mỗi 5 phút
 * 2. Linux Cron: Thêm vào crontab: */5 * * * * php /path/to/cron/cancel_expired_registrations.php
 * 3. Hoặc gọi qua URL: http://yourdomain.com/cron/cancel_expired_registrations.php
 */

// Chỉ cho phép chạy từ CLI hoặc có token bảo mật
$allowed = false;

// Cho phép chạy từ CLI
if (php_sapi_name() === 'cli') {
    $allowed = true;
}

// Cho phép chạy từ web với token (nếu cần)
$token = $_GET['token'] ?? '';
$secretToken = 'your_secret_token_here'; // Thay đổi token này
if ($token === $secretToken) {
    $allowed = true;
}

if (!$allowed) {
    http_response_code(403);
    die('Access denied');
}

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Load dependencies
require_once __DIR__ . '/../Commons/env.php';
require_once __DIR__ . '/../Commons/function.php';
require_once __DIR__ . '/../client/Model/khoahoc.php';

try {
    $khoaHocModel = new KhoaHoc();
    $count = $khoaHocModel->huyDangKyQuaHan();
    
    $message = date('Y-m-d H:i:s') . " - Đã hủy $count đăng ký quá hạn\n";
    
    // Ghi log
    error_log($message);
    
    // Nếu chạy từ CLI, in ra màn hình
    if (php_sapi_name() === 'cli') {
        echo $message;
    } else {
        // Nếu chạy từ web, trả về JSON
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'count' => $count,
            'message' => $message
        ]);
    }
} catch (Exception $e) {
    $error = date('Y-m-d H:i:s') . " - Lỗi: " . $e->getMessage() . "\n";
    error_log($error);
    
    if (php_sapi_name() === 'cli') {
        echo $error;
    } else {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
