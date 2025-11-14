<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// require_once file common
require_once('./Commons/env.php');
require_once('./Commons/function.php');

// route
$act = $_GET['act'] ?? '/';

match ($act) {

    // ============================
    //  TRANG CHỦ (MẶC ĐỊNH)
    // ============================
    '/', '' => (function () {
        require_once './client/Controller/KhoaHocController.php';
        $ctrl = new KhoaHocController();
        return $ctrl->index();
    })(),

    // ============================
    //  TRANG DANH SÁCH KHÓA HỌC
    // ============================
    'khoa_hoc' => (function () {
        require_once './client/Controller/KhoaHocController.php';
        $ctrl = new KhoaHocController();
        return $ctrl->index();
    })(),

    // ============================
    //  TRANG CHI TIẾT KHÓA HỌC
    // ============================
    'khoa_hoc_detail' => (function () {
        require_once './client/Controller/KhoaHocController.php';
        $ctrl = new KhoaHocController();
        return $ctrl->detail();
    })(),

    // ============================
    //  GỬI BÌNH LUẬN
    // ============================
    'khoa_hoc_add_comment' => (function () {
        require_once './client/Controller/KhoaHocController.php';
        $ctrl = new KhoaHocController();
        return $ctrl->addComment();
    })(),

    // ============================
    //  ĐĂNG KÝ KHÓA HỌC
    // ============================
    'khoa_hoc_dang_ky' => (function () {
        session_start();
        require_once './client/Controller/KhoaHocController.php';
        $ctrl = new KhoaHocController();
        return $ctrl->dangKy();
    })(),

    // ============================
    //  MẶC ĐỊNH – KHÔNG TÌM THẤY
    // ============================
    default => notFound(),
};

?>
