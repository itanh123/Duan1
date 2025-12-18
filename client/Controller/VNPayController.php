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
        
        $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
        $vnp_TransactionDate = $_GET['vnp_TransactionDate'] ?? $_GET['vnp_CreateDate'] ?? date('YmdHis');
        
        if (empty($vnp_TxnRef)) {
            $_SESSION['error'] = 'Thiếu thông tin giao dịch!';
            header('Location: index.php?act=client-khoa-hoc');
            exit;
        }
        
        // Log tất cả các tham số từ callback để debug
        error_log("VNPay Callback - Tất cả tham số: " . json_encode($_GET, JSON_UNESCAPED_UNICODE));
        
        // Xác thực dữ liệu từ callback URL trước
        $callbackInfo = VNPayHelper::verifyPayment($_GET);
        
        if ($callbackInfo === false || !isset($callbackInfo['verified'])) {
            $_SESSION['error'] = 'Dữ liệu callback không hợp lệ!';
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
        
        // QUAN TRỌNG: Gọi API VNPay để lấy thông tin chính xác từ server
        // Ưu tiên dùng API, nhưng nếu fail thì fallback về callback
        $paymentInfo = VNPayHelper::queryTransaction($vnp_TxnRef, $vnp_TransactionDate);
        
        // Nếu API query fail, fallback về callback info (nhưng vẫn ưu tiên API)
        if ($paymentInfo === false || !isset($paymentInfo['verified'])) {
            error_log("Không thể query thông tin giao dịch từ VNPay API - TxnRef: $vnp_TxnRef. Sử dụng callback info.");
            // Fallback: sử dụng thông tin từ callback nếu hợp lệ
            $paymentInfo = $callbackInfo;
        }
        
        // Lấy thông tin ngân hàng từ callback (nếu có)
        // Lưu ý: VNPay không trả về số tài khoản ngân hàng cụ thể vì lý do bảo mật
        // Chỉ có thể lấy được:
        // - vnp_BankCode: Mã ngân hàng (NCB, VCB, TCB, VTB, DAB, TPB, VIB, BID, VPB, HDB, MSB, ACB, OJB, MBB, STB, EXB, VAB, NAB, PGB, GPB, AGB, SGB, NVB, OCB, VCB, BVB, VPB, SCB, HDB, SHB, EIB, MSB, TPB, VTB, BID, ACB, VIB, EXB, MBB, STB, NAB, PGB, GPB, AGB, SGB, NVB, OCB, BVB, SCB, SHB, EIB)
        // - vnp_CardType: Loại thẻ (ATM, CREDIT, DEBIT)
        $vnp_BankCode = $paymentInfo['vnp_BankCode'] ?? $_GET['vnp_BankCode'] ?? '';
        $vnp_CardType = $paymentInfo['vnp_CardType'] ?? $_GET['vnp_CardType'] ?? '';
        
        // Log thông tin ngân hàng để debug
        if ($vnp_BankCode) {
            error_log("VNPay Callback - Mã ngân hàng: $vnp_BankCode, Loại thẻ: $vnp_CardType");
        }
        
        // Log tất cả các tham số từ callback để xem có thông tin nào khác không
        error_log("VNPay Callback - Tất cả tham số GET: " . json_encode($_GET, JSON_UNESCAPED_UNICODE));
        
        $vnp_ResponseCode = $paymentInfo['vnp_ResponseCode'] ?? '';
        
        // Xử lý theo response code từ API
        if ($vnp_ResponseCode === '00') {
            // Thanh toán thành công - Lấy số tiền từ API response
            $so_tien = ($paymentInfo['vnp_Amount'] ?? 0) / 100; // Chia 100 để lấy số tiền thực
            
            // Cập nhật thông tin VNPay vào đăng ký (từ API response)
            $this->khoaHocModel->updateVNPayInfo(
                $vnp_TxnRef,
                $paymentInfo['vnp_TransactionNo'] ?? '',
                $vnp_ResponseCode
            );
            
            // Cập nhật trạng thái đăng ký thành "Đã xác nhận"
            $adminModel = new adminmodel();
            $adminModel->updateDangKy($dangKy['id'], [
                'trang_thai' => 'Đã xác nhận'
            ]);
            
            // Lưu vào bảng thanh_toan với số tiền từ API
            // Lưu ý: VNPay không trả về số tài khoản ngân hàng cụ thể trong callback
            // Chỉ có thể lấy được mã ngân hàng (vnp_BankCode) và loại thẻ (vnp_CardType)
            $this->saveThanhToan(
                $dangKy['id_hoc_sinh'],
                $dangKy['id'],
                'VNPAY',
                $so_tien,
                $paymentInfo['vnp_TransactionNo'] ?? '',
                $vnp_BankCode, // Mã ngân hàng (NCB, VCB, TCB...)
                $vnp_CardType  // Loại thẻ (ATM, CREDIT, DEBIT...)
            );
            
            $_SESSION['success'] = 'Thanh toán thành công! Đăng ký khóa học của bạn đã được xác nhận.';
        } else {
            // Thanh toán thất bại
            $this->khoaHocModel->updateVNPayInfo(
                $vnp_TxnRef,
                $paymentInfo['vnp_TransactionNo'] ?? '',
                $vnp_ResponseCode
            );
            
            // Hủy đăng ký để trả chỗ khi thanh toán thất bại
            $adminModel = new adminmodel();
            $adminModel->updateDangKy($dangKy['id'], [
                'trang_thai' => 'Đã hủy'
            ]);
            
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
     * 
     * @param int $id_hoc_sinh ID học sinh
     * @param int $id_dang_ky ID đăng ký
     * @param string $phuong_thuc Phương thức thanh toán
     * @param float $so_tien Số tiền
     * @param string $ma_giao_dich Mã giao dịch
     * @param string $ma_ngan_hang Mã ngân hàng (tùy chọn)
     * @param string $loai_the Loại thẻ (tùy chọn)
     */
    private function saveThanhToan($id_hoc_sinh, $id_dang_ky, $phuong_thuc, $so_tien, $ma_giao_dich, $ma_ngan_hang = '', $loai_the = '') {
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
            
            // Kiểm tra xem bảng có cột ma_ngan_hang và loai_the không
            $checkBankCode = $db->query("SHOW COLUMNS FROM thanh_toan LIKE 'ma_ngan_hang'");
            $checkCardType = $db->query("SHOW COLUMNS FROM thanh_toan LIKE 'loai_the'");
            
            $hasBankCode = $checkBankCode && $checkBankCode->rowCount() > 0;
            $hasCardType = $checkCardType && $checkCardType->rowCount() > 0;
            
            if ($hasBankCode && $hasCardType) {
                // Nếu có cả 2 cột, insert với thông tin ngân hàng
                $sql = "INSERT INTO thanh_toan (id_hoc_sinh, id_dang_ky, phuong_thuc, so_tien, ngay_thanh_toan, trang_thai, ma_giao_dich, ma_ngan_hang, loai_the)
                        VALUES (:id_hoc_sinh, :id_dang_ky, :phuong_thuc, :so_tien, NOW(), :trang_thai, :ma_giao_dich, :ma_ngan_hang, :loai_the)";
                
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([
                    ':id_hoc_sinh' => $id_hoc_sinh,
                    ':id_dang_ky' => $id_dang_ky,
                    ':phuong_thuc' => $phuong_thuc,
                    ':so_tien' => $so_tien,
                    ':trang_thai' => $trang_thai,
                    ':ma_giao_dich' => $ma_giao_dich,
                    ':ma_ngan_hang' => $ma_ngan_hang,
                    ':loai_the' => $loai_the
                ]);
            } else {
                // Nếu không có cột, insert như cũ
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
            }
            
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
        
        $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
        $vnp_TransactionDate = $_GET['vnp_TransactionDate'] ?? $_GET['vnp_CreateDate'] ?? date('YmdHis');
        
        if (empty($vnp_TxnRef)) {
            http_response_code(400);
            echo "INVALID";
            exit;
        }
        
        // Xác thực dữ liệu từ callback URL trước
        $callbackInfo = VNPayHelper::verifyPayment($_GET);
        
        if ($callbackInfo === false || !isset($callbackInfo['verified'])) {
            http_response_code(400);
            echo "INVALID";
            exit;
        }
        
        // QUAN TRỌNG: Gọi API VNPay để lấy thông tin chính xác từ server
        // Ưu tiên dùng API, nhưng nếu fail thì fallback về callback
        $paymentInfo = VNPayHelper::queryTransaction($vnp_TxnRef, $vnp_TransactionDate);
        
        // Nếu API query fail, fallback về callback info (nhưng vẫn ưu tiên API)
        if ($paymentInfo === false || !isset($paymentInfo['verified'])) {
            error_log("IPN - Không thể query thông tin giao dịch từ VNPay API - TxnRef: $vnp_TxnRef. Sử dụng callback info.");
            // Fallback: sử dụng thông tin từ callback nếu hợp lệ
            $paymentInfo = $callbackInfo;
        }
        
        $vnp_ResponseCode = $paymentInfo['vnp_ResponseCode'] ?? '';
        
        // Lấy thông tin đăng ký
        $dangKy = $this->khoaHocModel->getDangKyByVnpTxnRef($vnp_TxnRef);
        
        if (!$dangKy) {
            http_response_code(400);
            echo "INVALID";
            exit;
        }
        
        // Cập nhật thông tin VNPay từ API response
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
            
            // Lưu vào bảng thanh_toan với số tiền từ API
            $so_tien = ($paymentInfo['vnp_Amount'] ?? 0) / 100;
            
            // Lấy thông tin ngân hàng từ callback (nếu có)
            $vnp_BankCode = $paymentInfo['vnp_BankCode'] ?? $_GET['vnp_BankCode'] ?? '';
            $vnp_CardType = $_GET['vnp_CardType'] ?? '';
            
            $this->saveThanhToan(
                $dangKy['id_hoc_sinh'],
                $dangKy['id'],
                'VNPAY',
                $so_tien,
                $paymentInfo['vnp_TransactionNo'] ?? '',
                $vnp_BankCode,
                $vnp_CardType
            );
        } else {
            // Thanh toán thất bại qua IPN -> hủy đăng ký để trả chỗ
            $adminModel = new adminmodel();
            $adminModel->updateDangKy($dangKy['id'], [
                'trang_thai' => 'Đã hủy'
            ]);
        }
        
        http_response_code(200);
        echo "SUCCESS";
        exit;
    }
}

