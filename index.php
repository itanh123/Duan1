<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bắt đầu session
session_start();

// Thu thập thông báo flash để hiển thị cố định trên đầu trang (client + admin)
function __collectFlashMessages() {
    $keys = [
        ['key' => 'success', 'type' => 'success'],
        ['key' => 'error', 'type' => 'error'],
        ['key' => 'warning', 'type' => 'warning'],
        ['key' => 'info', 'type' => 'info'],
        ['key' => 'dang_ky_error', 'type' => 'error'],
        ['key' => 'dang_ky_message', 'type' => 'info'],
        ['key' => 'dang_ky_success', 'type' => 'success'],
        ['key' => 'vnpay_error', 'type' => 'error'],
        ['key' => 'vnpay_success', 'type' => 'success'],
        ['key' => 'login_error', 'type' => 'error'],
        ['key' => 'register_error', 'type' => 'error'],
        ['key' => 'register_success', 'type' => 'success'],
    ];

    $messages = [];
    foreach ($keys as $item) {
        $k = $item['key'];
        if (!empty($_SESSION[$k])) {
            $messages[] = ['type' => $item['type'], 'text' => $_SESSION[$k]];
            unset($_SESSION[$k]);
        }
    }
    return $messages;
}

$__flashPayload = __collectFlashMessages();
register_shutdown_function(function () use (&$__flashPayload) {
    if (empty($__flashPayload)) {
        return;
    }
    $json = json_encode($__flashPayload, JSON_UNESCAPED_UNICODE);
    echo <<<HTML
<script>
(function() {
    var payload = {$json} || [];
    if (!payload.length) return;
    var container = document.createElement('div');
    container.style.position = 'fixed';
    container.style.top = '12px';
    container.style.left = '50%';
    container.style.transform = 'translateX(-50%)';
    container.style.zIndex = '99999';
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.gap = '8px';
    container.style.width = 'min(640px, 90vw)';
    container.setAttribute('aria-live', 'polite');

    var typeStyles = {
        success: {bg: '#e8f7ee', border: '#16a34a', text: '#14532d'},
        error:   {bg: '#fef2f2', border: '#dc2626', text: '#7f1d1d'},
        warning: {bg: '#fffbeb', border: '#d97706', text: '#92400e'},
        info:    {bg: '#eff6ff', border: '#2563eb', text: '#1e3a8a'}
    };

    payload.forEach(function(msg) {
        var style = typeStyles[msg.type] || typeStyles.info;
        var item = document.createElement('div');
        item.style.background = style.bg;
        item.style.border = '1px solid ' + style.border;
        item.style.color = style.text;
        item.style.padding = '12px 14px';
        item.style.borderRadius = '10px';
        item.style.boxShadow = '0 8px 24px rgba(0,0,0,0.08)';
        item.style.display = 'flex';
        item.style.justifyContent = 'space-between';
        item.style.alignItems = 'center';
        item.style.fontWeight = '600';
        item.style.fontFamily = 'Inter, Arial, sans-serif';

        var text = document.createElement('div');
        text.textContent = msg.text;

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = '×';
        btn.style.marginLeft = '12px';
        btn.style.border = 'none';
        btn.style.background = 'transparent';
        btn.style.color = style.text;
        btn.style.fontSize = '18px';
        btn.style.cursor = 'pointer';
        btn.style.fontWeight = '700';
        btn.setAttribute('aria-label', 'Đóng thông báo');
        btn.onclick = function() { container.removeChild(item); };

        item.appendChild(text);
        item.appendChild(btn);
        container.appendChild(item);

        setTimeout(function() {
            if (container.contains(item)) container.removeChild(item);
        }, 6000);
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.body.appendChild(container);
    });
})();
</script>
HTML;
});

// require_once file common
require_once('./Commons/env.php');
require_once('./Commons/function.php');
require_once('./admin/Model/adminmodel.php');
require_once('./admin/Controller/admincontroller.php');
require_once('./client/Model/khoahoc.php');
require_once('./client/Controller/KhoaHocController.php');
require_once('./client/Controller/CaHocController.php');
require_once('./client/Controller/LopHocController.php');
require_once('./client/Controller/DanhMucController.php');
require_once('./client/Controller/GiangVienController.php');
require_once('./client/Controller/VNPayController.php');

// Tự động hủy các đăng ký quá hạn (chạy mỗi lần có request, nhưng chỉ kiểm tra nếu đã qua ít nhất 1 phút kể từ lần kiểm tra cuối)
// Để tránh chạy quá nhiều, chỉ chạy nếu đã qua 5 phút hoặc random 10% (để đảm bảo luôn có kiểm tra)
if (!isset($_SESSION['last_expired_check'])) {
    $_SESSION['last_expired_check'] = 0;
}

$timeSinceLastCheck = time() - $_SESSION['last_expired_check'];
// Chỉ chạy nếu đã qua ít nhất 5 phút hoặc random 10% (để đảm bảo luôn có kiểm tra)
if ($timeSinceLastCheck >= 300 || (rand(1, 10) === 1 && $timeSinceLastCheck >= 60)) {
    try {
        $khoaHocModel = new KhoaHoc();
        $khoaHocModel->cancelExpiredRegistrations();
        $_SESSION['last_expired_check'] = time();
    } catch (Exception $e) {
        // Không làm gián đoạn flow chính nếu có lỗi
        error_log("Lỗi khi kiểm tra đăng ký quá hạn: " . $e->getMessage());
    }
}

// route
$act = $_GET['act'] ?? '';

// Khởi tạo controllers
$adminController = new admincontroller();
$khoaHocController = new KhoaHocController();
$caHocController = new CaHocController();
$lopHocController = new LopHocController();
$danhMucController = new DanhMucController();
$giangVienController = new GiangVienController();
$vnpayController = new VNPayController();

match ($act) {
    // ============================
    //  CLIENT - ĐĂNG NHẬP/ĐĂNG XUẤT/ĐĂNG KÝ
    // ============================
    'client-login' => $khoaHocController->login(),
    'client-register' => $khoaHocController->register(),
    'client-process-register' => $khoaHocController->processRegister(),
    'client-process-login' => $khoaHocController->processLogin(),
    'unified-process-login' => $khoaHocController->unifiedProcessLogin(),
    'client-choose-role' => $khoaHocController->chooseRole(),
    'client-process-choose-role' => $khoaHocController->processChooseRole(),
    'client-logout' => $khoaHocController->logout(),
    
    // ============================
    //  CLIENT - TRANG CHỦ (MẶC ĐỊNH)
    // ============================
    'client' => $khoaHocController->index(),
    '' => $khoaHocController->index(),
    
    // ============================
    //  CLIENT - LIÊN HỆ
    // ============================
    'client-lien-he' => $khoaHocController->lienHe(),
    
    // ============================
    //  CLIENT - KHÓA HỌC
    // ============================
    'client-khoa-hoc' => $khoaHocController->index(),
    'client-chi-tiet-khoa-hoc' => $khoaHocController->detail(),
    'client-binh-luan-khoa-hoc' => $khoaHocController->addComment(),
    'client-dang-ky-khoa-hoc' => $khoaHocController->dangKy(),
    'client-search-khoa-hoc' => $khoaHocController->search(),
    
    // ============================
    //  CLIENT - CA HỌC
    // ============================
    'client-chi-tiet-ca-hoc' => $caHocController->chiTietCaHoc(),
    
    // ============================
    //  CLIENT - LỚP HỌC
    // ============================
    'client-lop-hoc' => $lopHocController->index(),
    'client-chi-tiet-lop-hoc' => $lopHocController->chiTietLopHoc(),
    
    // ============================
    //  CLIENT - DANH MỤC
    // ============================
    'client-danh-muc' => $danhMucController->index(),
    'client-chi-tiet-danh-muc' => $danhMucController->detail(),
    
    // ============================
    //  CLIENT - GIẢNG VIÊN
    // ============================
    // ============================
    //  GIẢNG VIÊN - ĐĂNG NHẬP/ĐĂNG XUẤT
    // ============================
    'giang-vien-login' => $giangVienController->login(), // Redirect về unified login
    'giang-vien-process-login' => $giangVienController->processLogin(), // Redirect về unified login
    'giang-vien-logout' => $giangVienController->logout(),
    
    // ============================
    //  GIẢNG VIÊN - DASHBOARD VÀ QUẢN LÝ
    // ============================
    'giang-vien-dashboard' => $giangVienController->dashboard(),
    'giang-vien-lop-hoc' => $giangVienController->myClasses(),
    'giang-vien-list-hoc-sinh' => $giangVienController->listHocSinh(),
    'giang-vien-view-hoc-sinh-detail' => $giangVienController->viewHocSinhDetail(),
    'giang-vien-view-hoc-sinh-trong-lop' => $giangVienController->viewHocSinhTrongLop(),
    'giang-vien-profile' => $giangVienController->profile(),
    'giang-vien-yeu-cau-doi-lich' => $giangVienController->yeuCauDoiLich(),
    'giang-vien-process-yeu-cau-doi-lich' => $giangVienController->processYeuCauDoiLich(),
    'giang-vien-danh-sach-yeu-cau-doi-lich' => $giangVienController->danhSachYeuCauDoiLich(),
    
    // ============================
    //  CLIENT - XEM DANH SÁCH GIẢNG VIÊN
    // ============================
    'client-giang-vien' => $giangVienController->index(),
    
    // ============================
    //  CLIENT - LỚP HỌC CỦA HỌC SINH
    // ============================
    'client-hoc-sinh-lop-hoc' => $khoaHocController->myClasses(),
    'client-khoa-hoc-da-dang-ky' => $khoaHocController->myCourses(),
    'client-profile' => $khoaHocController->profile(),
    
    // ============================
    //  VNPAY - THANH TOÁN
    // ============================
    'vnpay-return' => $vnpayController->returnUrl(),
    'vnpay-ipn' => $vnpayController->ipnUrl(),
    
    //  ADMIN - ĐĂNG NHẬP/ĐĂNG XUẤT
    // ============================
    'admin-login' => $adminController->login(),
    'admin-process-login' => $adminController->processLogin(), // Redirect về unified login
    'admin-logout' => $adminController->logout(),
    
    // ============================
    //  ADMIN - TRANG CHỦ ADMIN
    // ============================
    'admin' => $adminController->dashboard(),
    'admin-dashboard' => $adminController->dashboard(),
    'admin-thong-ke' => $adminController->thongKe(),
    
    // ============================
    //  ADMIN - QUẢN LÝ KHÓA HỌC
    // ==========================
    
    // Quản lý khóa học
    'admin-list-khoa-hoc' => $adminController->listKhoaHoc(),
    'admin-add-khoa-hoc' => $adminController->addKhoaHoc(),
    'admin-save-khoa-hoc' => $adminController->saveKhoaHoc(),
    'admin-edit-khoa-hoc' => $adminController->editKhoaHoc(),
    'admin-update-khoa-hoc' => $adminController->updateKhoaHoc(),
    'admin-delete-khoa-hoc' => $adminController->deleteKhoaHoc(),
    'admin-toggle-khoa-hoc-status' => $adminController->toggleKhoaHocStatus(),
    
    // ============================
    //  ADMIN - QUẢN LÝ HỌC SINH
    // ============================
    
    // Quản lý học sinh
    'admin-list-hoc-sinh' => $adminController->listHocSinh(),
    'admin-view-hoc-sinh' => $adminController->viewHocSinh(),
    'admin-delete-hoc-sinh' => $adminController->deleteHocSinh(),
    'admin-toggle-hoc-sinh-status' => $adminController->toggleHocSinhStatus(),
    'admin-view-lop-hoc-hoc-sinh' => $adminController->viewLopHocHocSinh(),
    
    // ============================
    //  ADMIN - QUẢN LÝ DANH MỤC
    // ============================
    
    // Quản lý danh mục
    'admin-list-danh-muc' => $adminController->listDanhMuc(),
    'admin-add-danh-muc' => $adminController->addDanhMuc(),
    'admin-save-danh-muc' => $adminController->saveDanhMuc(),
    'admin-edit-danh-muc' => $adminController->editDanhMuc(),
    'admin-update-danh-muc' => $adminController->updateDanhMuc(),
    'admin-delete-danh-muc' => $adminController->deleteDanhMuc(),
    'admin-toggle-danh-muc-status' => $adminController->toggleDanhMucStatus(),
    
    // ============================
    //  ADMIN - QUẢN LÝ GIẢNG VIÊN
    // ============================
    
    // Quản lý giảng viên
    'admin-list-giang-vien' => $adminController->listGiangVien(),
    'admin-add-giang-vien' => $adminController->addGiangVien(),
    'admin-save-giang-vien' => $adminController->saveGiangVien(),
    'admin-edit-giang-vien' => $adminController->editGiangVien(),
    'admin-update-giang-vien' => $adminController->updateGiangVien(),
    'admin-delete-giang-vien' => $adminController->deleteGiangVien(),
    'admin-view-lop-hoc-giang-vien' => $adminController->viewLopHocGiangVien(),
    
    // ============================
    //  ADMIN - QUẢN LÝ LỚP HỌC
    // ============================
    
    // Quản lý lớp học
    'admin-list-lop-hoc' => $adminController->listLopHoc(),
    'admin-add-lop-hoc' => $adminController->addLopHoc(),
    'admin-save-lop-hoc' => $adminController->saveLopHoc(),
    'admin-edit-lop-hoc' => $adminController->editLopHoc(),
    'admin-update-lop-hoc' => $adminController->updateLopHoc(),
    'admin-delete-lop-hoc' => $adminController->deleteLopHoc(),
    
    // ============================
    //  ADMIN - QUẢN LÝ CA HỌC
    // ============================
    
    // Quản lý ca học
    'admin-list-ca-hoc' => $adminController->listCaHoc(),
    'admin-add-ca-hoc' => $adminController->addCaHoc(),
    'admin-save-ca-hoc' => $adminController->saveCaHoc(),
    'admin-edit-ca-hoc' => $adminController->editCaHoc(),
    'admin-check-ca-hoc-trung' => $adminController->checkCaHocTrung(),
    'admin-get-lop-hoc-info' => $adminController->getLopHocInfo(),
    'admin-get-phong-hoc-by-suc-chua' => $adminController->getPhongHocBySucChua(),
    'admin-update-ca-hoc' => $adminController->updateCaHoc(),
    'admin-delete-ca-hoc' => $adminController->deleteCaHoc(),
    
    // ============================
    //  ADMIN - QUẢN LÝ ĐĂNG KÝ
    // ============================
    
    // Quản lý đăng ký
    'admin-list-dang-ky' => $adminController->listDangKy(),
    'admin-edit-dang-ky' => $adminController->editDangKy(),
    'admin-update-dang-ky' => $adminController->updateDangKy(),
    'admin-delete-dang-ky' => $adminController->deleteDangKy(),
    'admin-hoan-tien' => $adminController->hoanTien(),
    
    // ============================
    //  ADMIN - QUẢN LÝ BÌNH LUẬN
    // ============================
    
    // Quản lý bình luận
    'admin-list-binh-luan' => $adminController->listBinhLuan(),
    'admin-tra-loi-binh-luan' => $adminController->traLoiBinhLuan(),
    'admin-process-tra-loi-binh-luan' => $adminController->processTraLoiBinhLuan(),
    'admin-edit-phan-hoi-binh-luan' => $adminController->editPhanHoiBinhLuan(),
    'admin-update-phan-hoi-binh-luan' => $adminController->updatePhanHoiBinhLuan(),
    
    // ============================
    //  ADMIN - QUẢN LÝ PHÒNG HỌC
    // ============================
    
    // Quản lý phòng học
    'admin-list-phong-hoc' => $adminController->listPhongHoc(),
    'admin-add-phong-hoc' => $adminController->addPhongHoc(),
    'admin-save-phong-hoc' => $adminController->savePhongHoc(),
    'admin-edit-phong-hoc' => $adminController->editPhongHoc(),
    'admin-update-phong-hoc' => $adminController->updatePhongHoc(),
    'admin-delete-phong-hoc' => $adminController->deletePhongHoc(),
    
    // Quản lý tài khoản
    'admin-list-tai-khoan' => $adminController->listTaiKhoan(),
    'admin-edit-tai-khoan' => $adminController->editTaiKhoan(),
    'admin-update-tai-khoan' => $adminController->updateTaiKhoan(),
    'admin-toggle-tai-khoan-status' => $adminController->toggleTaiKhoanStatus(),
    
    // ============================
    //  ADMIN - QUẢN LÝ YÊU CẦU ĐỔI LỊCH
    // ============================
    'admin-list-yeu-cau-doi-lich' => $adminController->listYeuCauDoiLich(),
    'admin-detail-yeu-cau-doi-lich' => $adminController->detailYeuCauDoiLich(),
    'admin-duyet-yeu-cau-doi-lich' => $adminController->duyetYeuCauDoiLich(),
    'admin-tu-choi-yeu-cau-doi-lich' => $adminController->tuChoiYeuCauDoiLich(),
    'admin-huy-yeu-cau-doi-lich' => $adminController->huyYeuCauDoiLich(),
    'admin-xac-nhan-thay-doi-lich' => $adminController->xacNhanThayDoiLich(),
    'admin-hoan-nguyen-lich' => $adminController->hoanNguyenLich(),
    
    // ============================
    //  ADMIN - QUẢN LÝ LIÊN HỆ
    // ============================
    
    // Quản lý liên hệ
    'admin-list-lien-he' => $adminController->listLienHe(),
    'admin-add-lien-he' => $adminController->addLienHe(),
    'admin-save-lien-he' => $adminController->saveLienHe(),
    'admin-edit-lien-he' => $adminController->editLienHe(),
    'admin-update-lien-he' => $adminController->updateLienHe(),
    'admin-delete-lien-he' => $adminController->deleteLienHe(),
    'admin-toggle-lien-he-status' => $adminController->toggleLienHeStatus(),
    
    // ============================
    //  MẶC ĐỊNH – KHÔNG TÌM THẤY
    // ============================
    default => notFound(),
};

?>
