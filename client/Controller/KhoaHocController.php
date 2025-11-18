<?php
// Controller riêng cho Khóa học
require_once __DIR__ . '/../Model/KhoaHoc.php';
require_once __DIR__ . '/../Model/CaHoc.php';
require_once __DIR__ . '/../../admin/Model/adminmodel.php';

class KhoaHocController {

    private $model;
    private $caHocModel;

    public function __construct() {
        $this->model = new KhoaHoc();
        $this->caHocModel = new CaHoc(); // Để lấy thông tin ca học khi hiển thị chi tiết khóa học
        $this->userModel = new adminmodel();
    private $userModel;
      
    }

    // Kiểm tra đăng nhập client
    private function checkClientLogin(){
        if (!isset($_SESSION['client_id']) || !isset($_SESSION['client_vai_tro']) || $_SESSION['client_vai_tro'] !== 'hoc_sinh') {
            header('Location: ?act=client-login');
            exit;
        }
        
        // Kiểm tra tài khoản có bị khóa không
        $user = $this->userModel->getNguoiDungById($_SESSION['client_id']);
        if (!$user || $user['trang_thai'] != 1) {
            // Tài khoản bị khóa hoặc không tồn tại, đăng xuất
            unset($_SESSION['client_id']);
            unset($_SESSION['client_email']);
            unset($_SESSION['client_ho_ten']);
            unset($_SESSION['client_vai_tro']);
            $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa!';
            header('Location: ?act=client-login');
            exit;
        }
    }

    // Trang đăng nhập client (chung cho cả admin và client)
    public function login(){
        // Nếu đã đăng nhập client thì chuyển về trang danh sách
        if (isset($_SESSION['client_id']) && $_SESSION['client_vai_tro'] === 'hoc_sinh') {
            header('Location: ?act=client-khoa-hoc');
            exit;
        }
        // Nếu đã đăng nhập admin thì chuyển về dashboard
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_vai_tro'] === 'admin') {
            header('Location: ?act=admin-dashboard');
            exit;
        }
        require_once(__DIR__ . '/../views/login.php');
    }

    // Xử lý đăng nhập client
    public function processLogin(){
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ email và mật khẩu!';
            header('Location: ?act=client-login');
            exit;
        }

        $user = $this->userModel->login($email, $password, 'hoc_sinh');
        
        if ($user) {
            $_SESSION['client_id'] = $user['id'];
            $_SESSION['client_email'] = $user['email'];
            $_SESSION['client_ho_ten'] = $user['ho_ten'];
            $_SESSION['client_vai_tro'] = $user['vai_tro'];
            $_SESSION['success'] = 'Đăng nhập thành công!';
            header('Location: ?act=client-khoa-hoc');
        } else {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng!';
            header('Location: ?act=client-login');
        }
        exit;
    }

    // Trang đăng ký
    public function register(){
        // Nếu đã đăng nhập thì chuyển về trang chủ
        if (isset($_SESSION['client_id']) && $_SESSION['client_vai_tro'] === 'hoc_sinh') {
            header('Location: ?act=client-khoa-hoc');
            exit;
        }
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_vai_tro'] === 'admin') {
            header('Location: ?act=admin-dashboard');
            exit;
        }
        require_once(__DIR__ . '/../views/register.php');
    }

    // Xử lý đăng ký
    public function processRegister(){
        $ho_ten = trim($_POST['ho_ten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $so_dien_thoai = trim($_POST['so_dien_thoai'] ?? '');
        $dia_chi = trim($_POST['dia_chi'] ?? '');
        $mat_khau = $_POST['mat_khau'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate dữ liệu
        if (empty($ho_ten)) {
            $_SESSION['error'] = 'Vui lòng nhập họ và tên!';
            header('Location: ?act=client-register');
            exit;
        }

        if (empty($email)) {
            $_SESSION['error'] = 'Vui lòng nhập email!';
            header('Location: ?act=client-register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ!';
            header('Location: ?act=client-register');
            exit;
        }

        // Kiểm tra email đã tồn tại chưa
        if ($this->userModel->checkEmailExistsByRole($email, 'hoc_sinh')) {
            $_SESSION['error'] = 'Email này đã được sử dụng! Vui lòng chọn email khác.';
            header('Location: ?act=client-register');
            exit;
        }

        if (empty($mat_khau)) {
            $_SESSION['error'] = 'Vui lòng nhập mật khẩu!';
            header('Location: ?act=client-register');
            exit;
        }

        if (strlen($mat_khau) < 6) {
            $_SESSION['error'] = 'Mật khẩu phải có ít nhất 6 ký tự!';
            header('Location: ?act=client-register');
            exit;
        }

        if ($mat_khau !== $confirm_password) {
            $_SESSION['error'] = 'Mật khẩu xác nhận không khớp!';
            header('Location: ?act=client-register');
            exit;
        }

        // Hash mật khẩu
        $hashedPassword = password_hash($mat_khau, PASSWORD_DEFAULT);

        // Chuẩn bị dữ liệu
        $data = [
            'ho_ten' => $ho_ten,
            'email' => $email,
            'mat_khau' => $hashedPassword,
            'so_dien_thoai' => !empty($so_dien_thoai) ? $so_dien_thoai : null,
            'dia_chi' => !empty($dia_chi) ? $dia_chi : null,
            'trang_thai' => 1 // Mặc định là hoạt động
        ];

        // Thêm học sinh vào database
        if ($this->userModel->addHocSinh($data)) {
            $_SESSION['success'] = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
            header('Location: ?act=client-login');
        } else {
            $_SESSION['error'] = 'Đăng ký thất bại! Vui lòng thử lại.';
            header('Location: ?act=client-register');
        }
        exit;
    }

    // Xử lý đăng nhập chung cho cả admin và client
    public function unifiedProcessLogin(){
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ email và mật khẩu!';
            header('Location: ?act=client-login');
            exit;
        }

        // Kiểm tra đăng nhập (không cần chỉ định vai trò cụ thể)
        $user = $this->userModel->loginByEmail($email, $password);
        
        if ($user) {
            // Lấy tất cả vai trò của người dùng
            require_once('./admin/Model/adminmodel.php');
            $adminModel = new adminmodel();
            $vaiTroList = $adminModel->getVaiTroByNguoiDung($user['id']);
            
            if (empty($vaiTroList)) {
                $_SESSION['error'] = 'Tài khoản chưa được phân vai trò!';
                header('Location: ?act=client-login');
                exit;
            }
            
            // Nếu chỉ có 1 vai trò, tự động đăng nhập với vai trò đó
            if (count($vaiTroList) == 1) {
                $vaiTro = $vaiTroList[0];
                $this->setSessionByVaiTro($user, $vaiTro);
                exit;
            }
            
            // Nếu có nhiều vai trò, hiển thị form chọn vai trò
            $_SESSION['temp_user_id'] = $user['id'];
            $_SESSION['temp_user_email'] = $user['email'];
            $_SESSION['temp_user_ho_ten'] = $user['ho_ten'];
            $_SESSION['temp_vai_tro_list'] = $vaiTroList;
            header('Location: ?act=client-choose-role');
            exit;
        }

        // Nếu không đăng nhập được
        $_SESSION['error'] = 'Email hoặc mật khẩu không đúng!';
        header('Location: ?act=client-login');
        exit;
    }

    // Thiết lập session theo vai trò
    private function setSessionByVaiTro($user, $vaiTro) {
        if ($vaiTro == 'admin') {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_ho_ten'] = $user['ho_ten'];
            $_SESSION['admin_vai_tro'] = $vaiTro;
            $_SESSION['success'] = 'Đăng nhập Admin thành công!';
            header('Location: ?act=admin-dashboard');
        } else {
            $_SESSION['client_id'] = $user['id'];
            $_SESSION['client_email'] = $user['email'];
            $_SESSION['client_ho_ten'] = $user['ho_ten'];
            $_SESSION['client_vai_tro'] = $vaiTro;
            $_SESSION['success'] = 'Đăng nhập thành công!';
            header('Location: ?act=client-khoa-hoc');
        }
    }

    // Form chọn vai trò khi có nhiều vai trò
    public function chooseRole(){
        if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_vai_tro_list'])) {
            header('Location: ?act=client-login');
            exit;
        }
        require_once(__DIR__ . '/../views/choose_role.php');
    }

    // Xử lý chọn vai trò
    public function processChooseRole(){
        $vaiTro = $_POST['vai_tro'] ?? '';
        
        if (!isset($_SESSION['temp_user_id']) || !isset($_SESSION['temp_vai_tro_list'])) {
            header('Location: ?act=client-login');
            exit;
        }
        
        if (empty($vaiTro) || !in_array($vaiTro, $_SESSION['temp_vai_tro_list'])) {
            $_SESSION['error'] = 'Vai trò không hợp lệ!';
            header('Location: ?act=client-choose-role');
            exit;
        }
        
        $user = [
            'id' => $_SESSION['temp_user_id'],
            'email' => $_SESSION['temp_user_email'],
            'ho_ten' => $_SESSION['temp_user_ho_ten']
        ];
        
        // Xóa session tạm
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_user_email']);
        unset($_SESSION['temp_user_ho_ten']);
        unset($_SESSION['temp_vai_tro_list']);
        
        // Thiết lập session theo vai trò đã chọn
        $this->setSessionByVaiTro($user, $vaiTro);
    }

    // Đăng xuất client
    public function logout(){
        unset($_SESSION['client_id']);
        unset($_SESSION['client_email']);
        unset($_SESSION['client_ho_ten']);
        unset($_SESSION['client_vai_tro']);
        $_SESSION['success'] = 'Đăng xuất thành công!';
        header('Location: ?act=client-khoa-hoc');
        exit;
    }

    // ===========================================
    //  HIỂN THỊ DANH SÁCH KHÓA HỌC  (action = index)
    // ===========================================
    public function index() 
    {
        $this->checkClientLogin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $courses = $this->model->getAll($perPage, $offset);
        $total   = $this->model->countAll();
        $totalPages = ceil($total / $perPage);

        // gọi view
        require __DIR__ . '/../views/khoa_hoc/list.php';
    }

    // ===========================================
    //  CHI TIẾT KHÓA HỌC (action = detail)
    // ===========================================
    public function detail()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header("Location: index.php?act=client-khoa-hoc");
            exit;
        }

        $course = $this->model->getById($id);
        if (!$course) {
            header("Location: index.php?act=client-khoa-hoc");
            exit;
        }

        $lops = $this->model->getLopHocByKhoaHoc($id);

        // Sử dụng CaHoc model để lấy thông tin ca học
        $lopCa = [];
        foreach ($lops as $lop) {
            $lopCa[$lop['id']] = $this->caHocModel->getCaHocByLop($lop['id']);
        }

        $binh_luan = $this->model->getBinhLuan($id);

        require __DIR__ . '/../views/khoa_hoc/detail.php';
    }

    // ===========================================
    //  THÊM BÌNH LUẬN (action = addComment)
    // ===========================================
    public function addComment()
    {
        $id_khoa_hoc = $_POST['id_khoa_hoc'] ?? 0;
        $id_hoc_sinh = $_POST['id_hoc_sinh'] ?? 0;
        $noi_dung    = trim($_POST['noi_dung'] ?? '');
        $danh_gia    = isset($_POST['danh_gia']) ? (int)$_POST['danh_gia'] : null;

        if ($id_khoa_hoc && $id_hoc_sinh && $noi_dung !== '') {
            $this->model->addBinhLuan($id_khoa_hoc, $id_hoc_sinh, $noi_dung, $danh_gia);
        }

        header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
        exit;
    }

    // ===========================================
    //  ĐĂNG KÝ KHÓA HỌC (action = dangKy)
    // ===========================================
    public function dangKy()
    {
        $id_khoa_hoc = isset($_POST['id_khoa_hoc']) ? (int)$_POST['id_khoa_hoc'] : 0;
        $id_lop      = isset($_POST['id_lop']) ? (int)$_POST['id_lop'] : 0;
        $ho_ten      = trim($_POST['ho_ten'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $sdt         = trim($_POST['sdt'] ?? '');
        $ghi_chu     = trim($_POST['ghi_chu'] ?? '');

        if ($id_khoa_hoc && $ho_ten && $email && $sdt) {
            $result = $this->model->dangKyKhoaHoc($id_khoa_hoc, $id_lop, $ho_ten, $email, $sdt, $ghi_chu);
            
            if ($result) {
                $_SESSION['dang_ky_success'] = true;
            } else {
                $_SESSION['dang_ky_error'] = 'Đăng ký thất bại. Vui lòng thử lại!';
            }
        } else {
            $_SESSION['dang_ky_error'] = 'Vui lòng điền đầy đủ thông tin!';
        }

        header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
        exit;
    }
}
