<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bắt đầu session
session_start();

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
$act = $_GET['act'] ?? '/';

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
    
    // ============================
    //  ADMIN - QUẢN LÝ HỌC SINH
    // ============================
    
    // Quản lý học sinh
    'admin-list-hoc-sinh' => $adminController->listHocSinh(),
    'admin-add-hoc-sinh' => $adminController->addHocSinh(),
    'admin-save-hoc-sinh' => $adminController->saveHocSinh(),
    'admin-edit-hoc-sinh' => $adminController->editHocSinh(),
    'admin-update-hoc-sinh' => $adminController->updateHocSinh(),
    'admin-delete-hoc-sinh' => $adminController->deleteHocSinh(),
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
    
    // ============================
    //  ADMIN - QUẢN LÝ BÌNH LUẬN
    // ============================
    
    // Quản lý bình luận
    'admin-list-binh-luan' => $adminController->listBinhLuan(),
    'admin-tra-loi-binh-luan' => $adminController->traLoiBinhLuan(),
    'admin-process-tra-loi-binh-luan' => $adminController->processTraLoiBinhLuan(),
    
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
    //  MẶC ĐỊNH – KHÔNG TÌM THẤY
    // ============================
    default => notFound(),
};

?>
