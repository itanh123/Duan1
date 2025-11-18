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
    //  CLIENT - TRANG CHỦ (MẶC ĐỊNH)
    // ============================
    'cliet' => $khoaHocController->index(),
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
    //  MẶC ĐỊNH – KHÔNG TÌM THẤY
    // ============================
    default => notFound(),
};

?>
