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

//route

$act = $_GET['act'] ?? '/';

// Khởi tạo controller
$adminController = new admincontroller();

match ($act) {
    // Trang chủ admin
    'admin' => $adminController->dashboard(),
    'admin-dashboard' => $adminController->dashboard(),
    
    // Quản lý khóa học
    'admin-list-khoa-hoc' => $adminController->listKhoaHoc(),
    'admin-add-khoa-hoc' => $adminController->addKhoaHoc(),
    'admin-save-khoa-hoc' => $adminController->saveKhoaHoc(),
    'admin-edit-khoa-hoc' => $adminController->editKhoaHoc(),
    'admin-update-khoa-hoc' => $adminController->updateKhoaHoc(),
    'admin-delete-khoa-hoc' => $adminController->deleteKhoaHoc(),
   
    default => notFound(),
}


    ?>
