<?php
// Controller riêng cho Danh mục
require_once __DIR__ . '/../Model/DanhMuc.php';

class DanhMucController {

    private $model;

    public function __construct() {
        $this->model = new DanhMuc();
    }

    // ===========================================
    //  HIỂN THỊ DANH SÁCH DANH MỤC (action = index)
    // ===========================================
    public function index() 
    {
        $danhMucs = $this->model->getAll();

        // gọi view
        require __DIR__ . '/../views/danh_muc/list.php';
    }

    // ===========================================
    //  CHI TIẾT DANH MỤC - Hiển thị khóa học theo danh mục
    // ===========================================
    public function detail()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header("Location: index.php?act=client-danh-muc");
            exit;
        }

        $danhMuc = $this->model->getById($id);
        if (!$danhMuc) {
            header("Location: index.php?act=client-danh-muc");
            exit;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $courses = $this->model->getKhoaHocByDanhMuc($id, $perPage, $offset);
        $total = $this->model->countKhoaHocByDanhMuc($id);
        $totalPages = ceil($total / $perPage);

        require __DIR__ . '/../views/danh_muc/detail.php';
    }
}

