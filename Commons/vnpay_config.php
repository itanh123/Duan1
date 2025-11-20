<?php
/**
 * VNPay Configuration
 * Sandbox Environment
 */

$vnp_TmnCode = "RBAAWEC9";  
$vnp_HashSecret = "TC9PTPS6V6L3A8B4S4X84LFRKG0ATM0L"; 
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html"; 

// Tự động lấy URL return dựa trên server hiện tại
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    
    // Xử lý trường hợp thư mục gốc
    if ($scriptPath == '/' || $scriptPath == '\\') {
        $scriptPath = '';
    }
    
    $vnp_Returnurl = $protocol . $host . $scriptPath . "/index.php?act=vnpay-return";
} else {
    // Fallback nếu không có $_SERVER (chạy CLI)
    $vnp_Returnurl = "http://localhost/PRO1014-duan1/index.php?act=vnpay-return";
}

// Define constants
define('VNPAY_TMN_CODE', $vnp_TmnCode);
define('VNPAY_HASH_SECRET', $vnp_HashSecret);
define('VNPAY_URL', $vnp_Url);
define('VNPAY_RETURN_URL', $vnp_Returnurl);
define('VNPAY_IPN_URL', $vnp_Returnurl); // IPN URL giống return URL

