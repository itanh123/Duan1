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

    // ===========================================
    //  XEM LỚP HỌC CỦA GIẢNG VIÊN (action = myClasses)
    // ===========================================
    public function myClasses()
    {
        // Kiểm tra đăng nhập và vai trò giảng viên
        if (!isset($_SESSION['client_id']) || !isset($_SESSION['client_vai_tro'])) {
            header('Location: ?act=client-login');
            exit;
        }
        
        // Kiểm tra có vai trò giảng viên không
        require_once(__DIR__ . '/../../admin/Model/adminmodel.php');
        $adminModel = new adminmodel();
        $userId = $_SESSION['client_id'];
        $hasGiangVienRole = $adminModel->hasVaiTro($userId, 'giang_vien');
        
        if (!$hasGiangVienRole) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: ?act=client-khoa-hoc');
            exit;
        }
        
        // Kiểm tra tài khoản có bị khóa không
        $user = $adminModel->getNguoiDungById($userId);
        if (!$user || $user['trang_thai'] != 1) {
            unset($_SESSION['client_id']);
            unset($_SESSION['client_email']);
            unset($_SESSION['client_ho_ten']);
            unset($_SESSION['client_vai_tro']);
            $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa!';
            header('Location: ?act=client-login');
            exit;
        }
        
        $id_giang_vien = $userId;
        $lopHocs = $this->model->getLopHocByGiangVien($id_giang_vien);
        
        require __DIR__ . '/../views/giang_vien/my_classes.php';
    }
}

