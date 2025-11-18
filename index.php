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
require_once('./client/Controller/KhoaHocController.php');
require_once('./client/Controller/CaHocController.php');
require_once('./client/Controller/LopHocController.php');
require_once('./client/Controller/DanhMucController.php');

// route
$act = $_GET['act'] ?? '/';

// Khởi tạo controllers
$adminController = new admincontroller();
$khoaHocController = new KhoaHocController();
$caHocController = new CaHocController();
$lopHocController = new LopHocController();
$danhMucController = new DanhMucController();

match ($act) {
    // ============================
    //  CLIENT - ĐĂNG NHẬP/ĐĂNG XUẤT
    // ============================
    'client-login' => $khoaHocController->login(),
    'client-process-login' => $khoaHocController->processLogin(),
    'unified-process-login' => $khoaHocController->unifiedProcessLogin(),
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
    //  ADMIN - ĐĂNG NHẬP/ĐĂNG XUẤT
    // ============================
    'admin-login' => $adminController->login(),
    'admin-process-login' => $adminController->processLogin(),
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
    //  MẶC ĐỊNH – KHÔNG TÌM THẤY
    // ============================
    default => notFound(),
};

?>
