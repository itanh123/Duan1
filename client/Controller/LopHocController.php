<?php
// Controller riêng cho Lớp học
require_once __DIR__ . '/../Model/LopHoc.php';
require_once __DIR__ . '/../Model/CaHoc.php';

class LopHocController {

    private $model;
    private $caHocModel;

    public function __construct() {
        $this->model = new LopHoc();
        $this->caHocModel = new CaHoc();
    }

    // ===========================================
    //  HIỂN THỊ DANH SÁCH LỚP HỌC (action = index)
    // ===========================================
    public function index() 
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $lopHocs = $this->model->getAll($perPage, $offset);
        $total = $this->model->countAll();
        $totalPages = ceil($total / $perPage);

        // gọi view
        require __DIR__ . '/../views/lop_hoc/list.php';
    }

    // ===========================================
    //  CHI TIẾT LỚP HỌC (action = chiTietLopHoc)
    // ===========================================
    public function chiTietLopHoc()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header("Location: index.php?act=client-khoa-hoc");
            exit;
        }

        $lopHoc = $this->model->getChiTietLopHoc($id);
        
        if (!$lopHoc) {
            header("Location: index.php?act=client-khoa-hoc");
            exit;
        }

        // Lấy danh sách ca học của lớp
        $danhSachCaHoc = $this->model->getCaHocByLop($id);

        require __DIR__ . '/../views/lop_hoc/chi_tiet.php';
    }
}

