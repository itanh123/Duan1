<?php
// Controller riêng cho Ca học
require_once __DIR__ . '/../Model/CaHoc.php';

class CaHocController {

    private $model;

    public function __construct() {
        $this->model = new CaHoc();
    }

    // ===========================================
    //  CHI TIẾT CA HỌC (action = chiTietCaHoc)
    // ===========================================
    public function chiTietCaHoc()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header("Location: index.php?act=client-khoa-hoc");
            exit;
        }

        $caHoc = $this->model->getChiTietCaHoc($id);
        
        if (!$caHoc) {
            header("Location: index.php?act=client-khoa-hoc");
            exit;
        }

        require __DIR__ . '/../views/ca_hoc/chi_tiet.php';
    }
}

