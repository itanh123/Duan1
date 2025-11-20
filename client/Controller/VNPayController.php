<?php
require_once __DIR__ . '/../../Commons/vnpay_helper.php';
require_once __DIR__ . '/../Model/khoahoc.php';

class VNPayController {
    
    private $khoaHocModel;
    
    public function __construct() {
        $this->khoaHocModel = new KhoaHoc();
    }
    
    /**
     * Xử lý callback từ VNPay (Return URL)
     */
    public function returnUrl() {
        require_once __DIR__ . '/../../Commons/vnpay_helper.php';
        require_once __DIR__ . '/../../admin/Model/adminmodel.php';
        
        // Kiểm tra và hủy các đăng ký quá hạn trước khi xử lý callback
        $this->khoaHocModel->huyDangKyQuaHan();
        
        $vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
        $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
        $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
        
        // Xác thực dữ liệu từ VNPay
        $paymentInfo = VNPayHelper::verifyPayment($_GET);
        
        if ($paymentInfo === false || !isset($paymentInfo['verified'])) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ!';
            header('Location: index.php?act=client-khoa-hoc');
            exit;
        }
        
        // Lấy thông tin đăng ký
        $dangKy = $this->khoaHocModel->getDangKyByVnpTxnRef($vnp_TxnRef);
        
        if (!$dangKy) {
            $_SESSION['error'] = 'Không tìm thấy thông tin đăng ký!';
            header('Location: index.php?act=client-khoa-hoc');
            exit;
        }
        
        // Xử lý theo response code
        if ($vnp_ResponseCode === '00') {
            // Thanh toán thành công
            $so_tien = ($paymentInfo['vnp_Amount'] ?? 0) / 100; // Chia 100 để lấy số tiền thực
            
            // Cập nhật thông tin VNPay vào đăng ký
            $this->khoaHocModel->updateVNPayInfo(
                $vnp_TxnRef,
                $paymentInfo['vnp_TransactionNo'],
                $vnp_ResponseCode
            );
            
            // Cập nhật trạng thái đăng ký thành "Đã xác nhận"
            $adminModel = new adminmodel();
            $adminModel->updateDangKy($dangKy['id'], [
                'trang_thai' => 'Đã xác nhận'
            ]);
            
            // Lưu vào bảng thanh_toan
            $this->saveThanhToan(
                $dangKy['id_hoc_sinh'],
                $dangKy['id'],
                'VNPAY',
                $so_tien,
                $paymentInfo['vnp_TransactionNo']
            );
            
            $_SESSION['success'] = 'Thanh toán thành công! Đăng ký khóa học của bạn đã được xác nhận.';
        } else {
            // Thanh toán thất bại
            $this->khoaHocModel->updateVNPayInfo(
                $vnp_TxnRef,
                $paymentInfo['vnp_TransactionNo'] ?? '',
                $vnp_ResponseCode
            );
            
            $_SESSION['error'] = 'Thanh toán thất bại! ' . ($paymentInfo['vnp_ResponseMessage'] ?? 'Vui lòng thử lại.');
        }
        
        // Redirect về trang chi tiết khóa học
        $adminModel = new adminmodel();
        $lopHoc = $adminModel->getLopHocById($dangKy['id_lop']);
        if ($lopHoc) {
            header('Location: index.php?act=client-chi-tiet-khoa-hoc&id=' . $lopHoc['id_khoa_hoc']);
        } else {
            header('Location: index.php?act=client-khoa-hoc');
        }
        exit;
    }
    
    /**
     * Lưu thông tin thanh toán vào bảng thanh_toan
     */
    private function saveThanhToan($id_hoc_sinh, $id_dang_ky, $phuong_thuc, $so_tien, $ma_giao_dich) {
        try {
            require_once __DIR__ . '/../../Commons/function.php';
            $db = connectDB();
            
            // Kiểm tra xem đã có thanh toán cho đăng ký này chưa
            $checkSql = "SELECT id FROM thanh_toan WHERE id_dang_ky = :id_dang_ky AND ma_giao_dich = :ma_giao_dich LIMIT 1";
            $checkStmt = $db->prepare($checkSql);
            $checkStmt->execute([
                ':id_dang_ky' => $id_dang_ky,
                ':ma_giao_dich' => $ma_giao_dich
            ]);
            
            if ($checkStmt->fetch()) {
                // Đã có thanh toán rồi, không cần insert lại
                error_log("Thanh toán đã tồn tại cho đăng ký ID: $id_dang_ky, mã giao dịch: $ma_giao_dich");
                return true;
            }
            
            $trang_thai = 'Thành công';
            
            $sql = "INSERT INTO thanh_toan (id_hoc_sinh, id_dang_ky, phuong_thuc, so_tien, ngay_thanh_toan, trang_thai, ma_giao_dich)
                    VALUES (:id_hoc_sinh, :id_dang_ky, :phuong_thuc, :so_tien, NOW(), :trang_thai, :ma_giao_dich)";
            
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                ':id_hoc_sinh' => $id_hoc_sinh,
                ':id_dang_ky' => $id_dang_ky,
                ':phuong_thuc' => $phuong_thuc,
                ':so_tien' => $so_tien,
                ':trang_thai' => $trang_thai,
                ':ma_giao_dich' => $ma_giao_dich
            ]);
            
            if ($result) {
                error_log("Đã lưu thanh toán thành công - Đăng ký ID: $id_dang_ky, Số tiền: $so_tien");
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Lỗi lưu thanh toán: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xử lý IPN (Instant Payment Notification) từ VNPay
     */
    public function ipnUrl() {
        require_once __DIR__ . '/../../Commons/vnpay_helper.php';
        require_once __DIR__ . '/../../admin/Model/adminmodel.php';
        
        // Xác thực dữ liệu từ VNPay
        $paymentInfo = VNPayHelper::verifyPayment($_GET);
        
        if ($paymentInfo === false || !isset($paymentInfo['verified'])) {
            http_response_code(400);
            echo "INVALID";
            exit;
        }
        
        $vnp_TxnRef = $paymentInfo['vnp_TxnRef'] ?? '';
        $vnp_ResponseCode = $paymentInfo['vnp_ResponseCode'] ?? '';
        
        // Lấy thông tin đăng ký
        $dangKy = $this->khoaHocModel->getDangKyByVnpTxnRef($vnp_TxnRef);
        
        if (!$dangKy) {
            http_response_code(400);
            echo "INVALID";
            exit;
        }
        
        // Cập nhật thông tin VNPay
        $this->khoaHocModel->updateVNPayInfo(
            $vnp_TxnRef,
            $paymentInfo['vnp_TransactionNo'] ?? '',
            $vnp_ResponseCode
        );
        
        // Nếu thanh toán thành công
        if ($vnp_ResponseCode === '00') {
            // Cập nhật trạng thái đăng ký
            $adminModel = new adminmodel();
            $adminModel->updateDangKy($dangKy['id'], [
                'trang_thai' => 'Đã xác nhận'
            ]);
            
            // Lưu vào bảng thanh_toan (kiểm tra xem đã lưu chưa)
            $so_tien = ($paymentInfo['vnp_Amount'] ?? 0) / 100;
            $this->saveThanhToan(
                $dangKy['id_hoc_sinh'],
                $dangKy['id'],
                'VNPAY',
                $so_tien,
                $paymentInfo['vnp_TransactionNo'] ?? ''
            );
        }
        
        http_response_code(200);
        echo "SUCCESS";
        exit;
    }
}

