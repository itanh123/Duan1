<?php
require_once __DIR__ . '/vnpay_config.php';

class VNPayHelper {
    
    /**
     * Tạo URL thanh toán VNPay
     * 
     * @param array $params Các tham số thanh toán
     * @return string URL thanh toán
     */
    public static function createPaymentUrl($params) {
        require_once __DIR__ . '/vnpay_config.php';
        
        $vnp_TmnCode = VNPAY_TMN_CODE;
        $vnp_HashSecret = VNPAY_HASH_SECRET;
        $vnp_Url = VNPAY_URL;
        $vnp_Returnurl = VNPAY_RETURN_URL;
        
        // Các tham số bắt buộc
        $vnp_TxnRef = $params['vnp_TxnRef']; // Mã đơn hàng
        $vnp_OrderInfo = $params['vnp_OrderInfo']; // Thông tin đơn hàng
        $vnp_OrderType = $params['vnp_OrderType'] ?? 'billpayment'; // Loại đơn hàng
        $vnp_Amount = $params['vnp_Amount']; // Số tiền (nhân với 100)
        $vnp_Locale = $params['vnp_Locale'] ?? 'vn'; // Ngôn ngữ
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; // IP khách hàng
        
        // Thời gian
        $vnp_CreateDate = date('YmdHis');
        
        // Tạo mảng input data
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );
        
        // Sắp xếp lại các tham số
        ksort($inputData);
        
        // Tạo query string
        $query = "";
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        // Tạo URL trước khi thêm SecureHash
        $vnp_Url = $vnp_Url . "?" . rtrim($query, '&');
        
        // Tạo checksum
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        }
        
        return $vnp_Url;
    }
    
    /**
     * Xác thực callback từ VNPay
     * 
     * @param array $data Dữ liệu callback
     * @return array|false Thông tin thanh toán hoặc false nếu không hợp lệ
     */
    public static function verifyPayment($data) {
        require_once __DIR__ . '/vnpay_config.php';
        
        $vnp_HashSecret = VNPAY_HASH_SECRET;
        $vnp_SecureHash = $data['vnp_SecureHash'] ?? '';
        
        // Lọc chỉ lấy các tham số bắt đầu bằng vnp_
        $inputData = array();
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        // Loại bỏ SecureHash khỏi mảng để tính toán
        unset($inputData['vnp_SecureHash']);
        
        // Sắp xếp lại các tham số
        ksort($inputData);
        
        // Tạo query string
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        // Tính toán checksum
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        // So sánh checksum
        if ($secureHash !== $vnp_SecureHash) {
            return false;
        }
        
        // Trả về tất cả thông tin, không kiểm tra response code ở đây
        // Để controller xử lý response code
        return [
            'vnp_TxnRef' => $inputData['vnp_TxnRef'] ?? '',
            'vnp_Amount' => $inputData['vnp_Amount'] ?? 0,
            'vnp_TransactionNo' => $inputData['vnp_TransactionNo'] ?? '',
            'vnp_BankCode' => $inputData['vnp_BankCode'] ?? '',
            'vnp_PayDate' => $inputData['vnp_PayDate'] ?? '',
            'vnp_OrderInfo' => $inputData['vnp_OrderInfo'] ?? '',
            'vnp_ResponseCode' => $inputData['vnp_ResponseCode'] ?? '',
            'vnp_ResponseMessage' => $inputData['vnp_ResponseMessage'] ?? '',
            'verified' => true
        ];
    }
    
    /**
     * Tạo mã đơn hàng duy nhất
     * 
     * @param int $id_dang_ky ID đăng ký
     * @return string Mã đơn hàng
     */
    public static function generateOrderId($id_dang_ky) {
        return 'DK' . date('YmdHis') . str_pad($id_dang_ky, 6, '0', STR_PAD_LEFT);
    }
}

