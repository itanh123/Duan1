<?php
// Controller riêng cho Giảng viên
require_once __DIR__ . '/../../admin/Model/adminmodel.php';

class GiangVienController {

    private $model;

    public function __construct() {
        $this->model = new adminmodel();
    }

    // ===========================================
    //  HIỂN THỊ DANH SÁCH GIẢNG VIÊN (action = index)
    // ===========================================
    public function index() 
    {
        // Không cần đăng nhập để xem danh sách giảng viên
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 12;
        $search = $_GET['search'] ?? '';
        
        // Lấy danh sách giảng viên (chỉ lấy những người đang hoạt động)
        $giangVien = $this->model->getGiangVienForClient($page, $limit, $search);
        $total = $this->model->countGiangVienForClient($search);
        $totalPages = ceil($total / $limit);

        // gọi view
        require __DIR__ . '/../views/giang_vien/list.php';
    }
}

