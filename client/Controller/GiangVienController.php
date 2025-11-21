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
        // Đây là trang công khai, cho phép cả client và giảng viên xem
        // Nhưng navigation sẽ khác nhau tùy vào session
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
    //  TRANG ĐĂNG NHẬP GIẢNG VIÊN
    // ===========================================
    public function login()
    {
        // Ngăn client truy cập form đăng nhập giảng viên
        if (isset($_SESSION['client_id']) && (!isset($_SESSION['client_vai_tro']) || $_SESSION['client_vai_tro'] === 'hoc_sinh')) {
            header('Location: ?act=client-khoa-hoc');
            exit;
        }
        
        // Nếu đã đăng nhập giảng viên thì chuyển về dashboard
        if (isset($_SESSION['giang_vien_id'])) {
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        require_once(__DIR__ . '/../views/giang_vien/login.php');
    }

    // ===========================================
    //  XỬ LÝ ĐĂNG NHẬP GIẢNG VIÊN
    // ===========================================
    public function processLogin()
    {
        // Ngăn client đăng nhập qua form giảng viên
        if (isset($_SESSION['client_id']) && (!isset($_SESSION['client_vai_tro']) || $_SESSION['client_vai_tro'] === 'hoc_sinh')) {
            $_SESSION['error'] = 'Bạn đang đăng nhập với tài khoản học sinh. Vui lòng sử dụng form đăng nhập học sinh!';
            header('Location: ?act=client-khoa-hoc');
            exit;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ email và mật khẩu!';
            header('Location: ?act=giang-vien-login');
            exit;
        }

        // Kiểm tra đăng nhập với vai trò giảng viên
        $user = $this->model->login($email, $password, 'giang_vien');
        
        if ($user) {
            // Kiểm tra tài khoản có bị khóa không
            if ($user['trang_thai'] != 1) {
                $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa!';
                header('Location: ?act=giang-vien-login');
                exit;
            }

            // Xóa session client nếu có (để tránh xung đột)
            unset($_SESSION['client_id']);
            unset($_SESSION['client_email']);
            unset($_SESSION['client_ho_ten']);
            unset($_SESSION['client_vai_tro']);

            // Thiết lập session riêng cho giảng viên
            $_SESSION['giang_vien_id'] = $user['id'];
            $_SESSION['giang_vien_email'] = $user['email'];
            $_SESSION['giang_vien_ho_ten'] = $user['ho_ten'];
            $_SESSION['giang_vien_vai_tro'] = 'giang_vien';
            $_SESSION['success'] = 'Đăng nhập thành công!';
            header('Location: ?act=giang-vien-dashboard');
        } else {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng!';
            header('Location: ?act=giang-vien-login');
        }
        exit;
    }

    // ===========================================
    //  ĐĂNG XUẤT GIẢNG VIÊN
    // ===========================================
    public function logout()
    {
        unset($_SESSION['giang_vien_id']);
        unset($_SESSION['giang_vien_email']);
        unset($_SESSION['giang_vien_ho_ten']);
        unset($_SESSION['giang_vien_vai_tro']);
        $_SESSION['success'] = 'Đăng xuất thành công!';
        header('Location: ?act=giang-vien-login');
        exit;
    }

    // Kiểm tra đăng nhập giảng viên
    private function checkGiangVienLogin() {
        // Ngăn client truy cập các chức năng của giảng viên
        if (isset($_SESSION['client_id']) && (!isset($_SESSION['client_vai_tro']) || $_SESSION['client_vai_tro'] === 'hoc_sinh')) {
            $_SESSION['error'] = 'Bạn đang đăng nhập với tài khoản học sinh. Vui lòng đăng xuất và đăng nhập lại với tài khoản giảng viên!';
            header('Location: ?act=client-khoa-hoc');
            exit;
        }
        
        if (!isset($_SESSION['giang_vien_id'])) {
            header('Location: ?act=giang-vien-login');
            exit;
        }
        
        // Kiểm tra tài khoản có bị khóa không
        $user = $this->model->getNguoiDungById($_SESSION['giang_vien_id']);
        if (!$user || $user['trang_thai'] != 1) {
            unset($_SESSION['giang_vien_id']);
            unset($_SESSION['giang_vien_email']);
            unset($_SESSION['giang_vien_ho_ten']);
            unset($_SESSION['giang_vien_vai_tro']);
            $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa!';
            header('Location: ?act=giang-vien-login');
            exit;
        }
    }

    // ===========================================
    //  DASHBOARD GIẢNG VIÊN (action = dashboard)
    // ===========================================
    public function dashboard()
    {
        $this->checkGiangVienLogin();
        
        $id_giang_vien = $_SESSION['giang_vien_id'];
        
        // Lấy lịch dạy của giảng viên
        $lopHocs = $this->model->getLopHocByGiangVien($id_giang_vien);
        
        // Lấy danh sách học sinh đã đăng ký các lớp của giảng viên
        $hocSinhList = [];
        foreach ($lopHocs as $lop) {
            $hocSinh = $this->model->getHocSinhByLop($lop['id_lop']);
            foreach ($hocSinh as &$hs) {
                $hs['id_lop'] = $lop['id_lop'];
                $hs['ten_lop'] = $lop['ten_lop'];
                $hs['ten_khoa_hoc'] = $lop['ten_khoa_hoc'];
            }
            $hocSinhList = array_merge($hocSinhList, $hocSinh);
        }
        
        require __DIR__ . '/../views/giang_vien/dashboard.php';
    }

    // ===========================================
    //  XEM LỚP HỌC CỦA GIẢNG VIÊN (action = myClasses)
    // ===========================================
    public function myClasses()
    {
        $this->checkGiangVienLogin();
        
        $id_giang_vien = $_SESSION['giang_vien_id'];
        $lopHocs = $this->model->getLopHocByGiangVien($id_giang_vien);
        
        require __DIR__ . '/../views/giang_vien/my_classes.php';
    }
}

