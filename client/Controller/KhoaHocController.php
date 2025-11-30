<?php
// Controller riêng cho Khóa học
require_once __DIR__ . '/../Model/KhoaHoc.php';
require_once __DIR__ . '/../Model/CaHoc.php';
require_once __DIR__ . '/../../admin/Model/adminmodel.php';

class KhoaHocController {

    private $model;
    private $caHocModel;
    private $userModel;
    
    public function __construct() {
        $this->model = new KhoaHoc();
        $this->caHocModel = new CaHoc(); // Để lấy thông tin ca học khi hiển thị chi tiết khóa học
        $this->userModel = new adminmodel();
    
      
    }

    // Kiểm tra đăng nhập client
    private function checkClientLogin(){
        // Ngăn giảng viên truy cập các chức năng của client
        if (isset($_SESSION['giang_vien_id'])) {
            $_SESSION['error'] = 'Bạn đang đăng nhập với tài khoản giảng viên. Vui lòng đăng xuất và đăng nhập lại với tài khoản học sinh!';
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
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

    // Kiểm tra không phải giảng viên (cho các trang công khai của client)
    private function checkNotGiangVien(){
        if (isset($_SESSION['giang_vien_id'])) {
            $_SESSION['error'] = 'Bạn đang đăng nhập với tài khoản giảng viên. Vui lòng đăng xuất để truy cập trang này!';
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
    }

    // Trang đăng nhập client (chỉ dành cho học sinh)
    public function login(){
        // Ngăn giảng viên truy cập form đăng nhập client
        if (isset($_SESSION['giang_vien_id'])) {
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
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

    // Xử lý đăng nhập client (chỉ dành cho học sinh)
    public function processLogin(){
        // Ngăn giảng viên đăng nhập qua form client
        if (isset($_SESSION['giang_vien_id'])) {
            $_SESSION['error'] = 'Bạn đang đăng nhập với tài khoản giảng viên. Vui lòng sử dụng form đăng nhập giảng viên!';
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ email và mật khẩu!';
            header('Location: ?act=client-login');
            exit;
        }

        // Chỉ cho phép đăng nhập với vai trò học sinh
        $user = $this->userModel->login($email, $password, 'hoc_sinh');
        
        if ($user) {
            // Xóa session giảng viên nếu có (để tránh xung đột)
            unset($_SESSION['giang_vien_id']);
            unset($_SESSION['giang_vien_email']);
            unset($_SESSION['giang_vien_ho_ten']);
            unset($_SESSION['giang_vien_vai_tro']);
            
            $_SESSION['client_id'] = $user['id'];
            $_SESSION['client_email'] = $user['email'];
            $_SESSION['client_ho_ten'] = $user['ho_ten'];
            $_SESSION['client_vai_tro'] = 'hoc_sinh';
            $_SESSION['success'] = 'Đăng nhập thành công!';
            header('Location: ?act=client-khoa-hoc');
        } else {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng! Hoặc tài khoản này không phải là tài khoản học sinh.';
            header('Location: ?act=client-login');
        }
        exit;
    }

    // Trang đăng ký (chỉ dành cho học sinh)
    public function register(){
        // Ngăn giảng viên truy cập form đăng ký client
        if (isset($_SESSION['giang_vien_id'])) {
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
        // Nếu đã đăng nhập thì chuyển về trang chủ
        if (isset($_SESSION['client_id']) && isset($_SESSION['client_vai_tro']) && $_SESSION['client_vai_tro'] === 'hoc_sinh') {
            header('Location: ?act=client-khoa-hoc');
            exit;
        }
        if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_vai_tro']) && $_SESSION['admin_vai_tro'] === 'admin') {
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
        } elseif ($vaiTro == 'giang_vien') {
            $_SESSION['giang_vien_id'] = $user['id'];
            $_SESSION['giang_vien_email'] = $user['email'];
            $_SESSION['giang_vien_ho_ten'] = $user['ho_ten'];
            $_SESSION['giang_vien_vai_tro'] = $vaiTro;
            $_SESSION['success'] = 'Đăng nhập Giảng viên thành công!';
            header('Location: ?act=giang-vien-dashboard');
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
        
        // Xóa session tạm trước
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
    //  XEM LỚP HỌC CỦA HỌC SINH (action = myClasses)
    // ===========================================
    public function myClasses()
    {
        $this->checkClientLogin();
        
        $id_hoc_sinh = $_SESSION['client_id'] ?? 0;
        if (!$id_hoc_sinh) {
            header('Location: ?act=client-login');
            exit;
        }
        
        $lopHocs = $this->userModel->getLopHocByHocSinh($id_hoc_sinh);
        
        require __DIR__ . '/../views/khoa_hoc/my_classes.php';
    }

    //  XEM KHÓA HỌC ĐÃ ĐĂNG KÝ (action = myCourses)
    // ===========================================
    public function myCourses()
    {
        $this->checkClientLogin();
        
        $id_hoc_sinh = $_SESSION['client_id'] ?? 0;
        if (!$id_hoc_sinh) {
            header('Location: ?act=client-login');
            exit;
        }
        
        $khoaHocs = $this->model->getKhoaHocDaDangKy($id_hoc_sinh);
        
        require __DIR__ . '/../views/khoa_hoc/my_courses.php';
    }

    // ===========================================
    //  XEM THÔNG TIN CÁ NHÂN (action = profile)
    // ===========================================
    public function profile()
    {
        $this->checkClientLogin();
        
        $id_hoc_sinh = $_SESSION['client_id'] ?? 0;
        if (!$id_hoc_sinh) {
            header('Location: ?act=client-login');
            exit;
        }
        
        $user = $this->userModel->getNguoiDungById($id_hoc_sinh);
        if (!$user) {
            $_SESSION['error'] = 'Không tìm thấy thông tin người dùng!';
            header('Location: ?act=client-khoa-hoc');
            exit;
        }
        
        require __DIR__ . '/../views/profile.php';
    }

    // ===========================================
    //  HIỂN THỊ DANH SÁCH KHÓA HỌC  (action = index)
    // ===========================================
    public function index() 
    {
        // Kiểm tra và hủy các đăng ký quá hạn
        $this->checkAndCancelExpiredRegistrations();
        
        // Nếu giảng viên truy cập, redirect về dashboard giảng viên
        if (isset($_SESSION['giang_vien_id'])) {
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
        // Không cần đăng nhập để xem danh sách khóa học (chỉ dành cho client)
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
    //  TÌM KIẾM KHÓA HỌC  (action = search)
    // ===========================================
    public function search() 
    {
        // Nếu giảng viên truy cập, redirect về dashboard giảng viên
        if (isset($_SESSION['giang_vien_id'])) {
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
        // Không cần đăng nhập để tìm kiếm khóa học (chỉ dành cho client)
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        
        if (empty($keyword)) {
            // Nếu không có từ khóa, chuyển về trang danh sách
            header('Location: index.php?act=client-khoa-hoc');
            exit;
        }

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $courses = $this->model->search($keyword, $perPage, $offset);
        $total   = $this->model->countSearch($keyword);
        $totalPages = ceil($total / $perPage);

        // Truyền keyword và isSearch để view biết đang ở chế độ tìm kiếm
        $isSearch = true;
        $searchKeyword = $keyword;

        // gọi view (dùng chung view list.php)
        require __DIR__ . '/../views/khoa_hoc/list.php';
    }

    // ===========================================
    //  CHI TIẾT KHÓA HỌC (action = detail)
    // ===========================================
    /**
     * Kiểm tra và hủy các đăng ký quá hạn
     * Gọi hàm này trước khi hiển thị danh sách hoặc chi tiết khóa học
     */
    private function checkAndCancelExpiredRegistrations() {
        try {
            $this->model->huyDangKyQuaHan();
        } catch (Exception $e) {
            error_log("Lỗi khi kiểm tra đăng ký quá hạn: " . $e->getMessage());
        }
    }
    
    public function detail()
    {
        // Nếu giảng viên truy cập, redirect về dashboard giảng viên
        if (isset($_SESSION['giang_vien_id'])) {
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
        // Kiểm tra và hủy các đăng ký quá hạn
        $this->checkAndCancelExpiredRegistrations();
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
        $lopSoLuong = []; // Lưu số lượng đăng ký của mỗi lớp
        foreach ($lops as $lop) {
            $lopCa[$lop['id']] = $this->caHocModel->getCaHocByLop($lop['id']);
            // Đếm số lượng đăng ký đã xác nhận của lớp học
            $lopSoLuong[$lop['id']] = $this->userModel->countDangKyByLop($lop['id']);
        }

        $binh_luan = $this->model->getBinhLuan($id);
        
        // Lấy phản hồi của admin cho mỗi bình luận
        $phanHoiList = [];
        foreach ($binh_luan as $bl) {
            $phanHoiList[$bl['id']] = $this->userModel->getPhanHoiBinhLuan($bl['id']);
        }

        // Kiểm tra xem học sinh đã đăng ký khóa học chưa (chỉ hiển thị form bình luận nếu đã đăng ký)
        $daDangKy = false;
        if (isset($_SESSION['client_id'])) {
            $id_hoc_sinh = $_SESSION['client_id'];
            $daDangKy = $this->model->daDangKyKhoaHoc($id_hoc_sinh, $id);
        }

        require __DIR__ . '/../views/khoa_hoc/detail.php';
    }

    // ===========================================
    //  THÊM BÌNH LUẬN (action = addComment)
    // ===========================================
    public function addComment()
    {
        // Cần đăng nhập để bình luận
        $this->checkClientLogin();
        
        $id_khoa_hoc = $_POST['id_khoa_hoc'] ?? 0;
        $id_hoc_sinh = $_SESSION['client_id'] ?? 0;
        $noi_dung    = trim($_POST['noi_dung'] ?? '');
        $danh_gia    = isset($_POST['danh_gia']) ? (int)$_POST['danh_gia'] : null;

        // Kiểm tra học sinh đã đăng ký khóa học chưa
        if ($id_khoa_hoc && $id_hoc_sinh) {
            $daDangKy = $this->model->daDangKyKhoaHoc($id_hoc_sinh, $id_khoa_hoc);
            if (!$daDangKy) {
                $_SESSION['dang_ky_error'] = 'Bạn cần đăng ký khóa học trước khi bình luận!';
                header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
                exit;
            }
        }

        if ($id_khoa_hoc && $id_hoc_sinh && $noi_dung !== '') {
            $this->model->addBinhLuan($id_khoa_hoc, $id_hoc_sinh, $noi_dung, $danh_gia);
            $_SESSION['dang_ky_success'] = true;
            $_SESSION['dang_ky_message'] = 'Bình luận của bạn đã được gửi thành công!';
        }

        header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
        exit;
    }

    // ===========================================
    //  ĐĂNG KÝ KHÓA HỌC (action = dangKy)
    // ===========================================
    public function dangKy()
    {
        // Cần đăng nhập để đăng ký khóa học
        $this->checkClientLogin();
        
        $id_khoa_hoc = isset($_POST['id_khoa_hoc']) ? (int)$_POST['id_khoa_hoc'] : 0;
        $id_lop      = isset($_POST['id_lop']) ? (int)$_POST['id_lop'] : 0;
        $phuong_thuc_thanh_toan = isset($_POST['phuong_thuc_thanh_toan']) ? trim($_POST['phuong_thuc_thanh_toan']) : '';

        // Kiểm tra dữ liệu đầu vào
        if (!$id_khoa_hoc || !$id_lop || empty($phuong_thuc_thanh_toan)) {
            $_SESSION['dang_ky_error'] = 'Vui lòng điền đầy đủ thông tin!';
            header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
            exit;
        }

        // Lấy id học sinh từ session
        $id_hoc_sinh = $_SESSION['client_id'] ?? 0;
        if (!$id_hoc_sinh) {
            $_SESSION['dang_ky_error'] = 'Vui lòng đăng nhập để đăng ký khóa học!';
            header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
            exit;
        }

        // Kiểm tra số lượng đăng ký của lớp học
        require_once(__DIR__ . '/../../admin/Model/adminmodel.php');
        $adminModel = new adminmodel();
        $lopHoc = $adminModel->getLopHocById($id_lop);
        
        if (!$lopHoc) {
            $_SESSION['dang_ky_error'] = 'Không tìm thấy thông tin lớp học!';
            header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
            exit;
        }

        // Kiểm tra nếu lớp học có giới hạn số lượng
        if (!empty($lopHoc['so_luong_toi_da'])) {
            $soLuongDangKy = $adminModel->countDangKyByLop($id_lop);
            
            if ($soLuongDangKy >= $lopHoc['so_luong_toi_da']) {
                $_SESSION['dang_ky_error'] = 'Lớp học này đã đầy! Số lượng đăng ký hiện tại: ' . $soLuongDangKy . '/' . $lopHoc['so_luong_toi_da'] . '. Vui lòng chọn lớp học khác.';
                header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
                exit;
            }
        }

        // Xử lý theo phương thức thanh toán
        if ($phuong_thuc_thanh_toan === 'truc_tiep') {
            // Thanh toán trực tiếp: Lưu vào bảng dang_ky với trạng thái "Chờ xác nhận"
            $result = $this->model->dangKyKhoaHoc($id_hoc_sinh, $id_lop, 'Chờ xác nhận');
            
            if ($result) {
                $_SESSION['dang_ky_success'] = true;
                $_SESSION['dang_ky_message'] = 'Đăng ký thành công! Vui lòng đến trung tâm để thanh toán và xác nhận đăng ký.';
            } else {
                $_SESSION['dang_ky_error'] = 'Đăng ký thất bại. Vui lòng thử lại!';
            }
        } else if ($phuong_thuc_thanh_toan === 'online') {
            // Thanh toán online: Tích hợp VNPay
            try {
                // Kiểm tra file helper có tồn tại không
                $vnpayHelperPath = __DIR__ . '/../../Commons/vnpay_helper.php';
                if (!file_exists($vnpayHelperPath)) {
                    throw new Exception('File VNPay helper không tồn tại!');
                }
                
                require_once($vnpayHelperPath);
                require_once(__DIR__ . '/../../admin/Model/adminmodel.php');
                
                // Lấy thông tin lớp học để tính học phí
                $adminModel = new adminmodel();
                $lopHoc = $adminModel->getLopHocById($id_lop);
                
                if (!$lopHoc) {
                    $_SESSION['dang_ky_error'] = 'Không tìm thấy thông tin lớp học!';
                    header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
                    exit;
                }
                
                // Kiểm tra session và thông tin trước khi đăng ký
                if (empty($id_hoc_sinh) || empty($id_lop)) {
                    error_log("Thông tin không đầy đủ - ID học sinh: " . var_export($id_hoc_sinh, true) . ", ID lớp: " . var_export($id_lop, true));
                    $_SESSION['dang_ky_error'] = 'Thông tin đăng ký không đầy đủ!';
                    header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
                    exit;
                }
                
                // Tạo đăng ký tạm thời với trạng thái "Chờ xác nhận" (sẽ chuyển thành "Đã xác nhận" sau khi thanh toán thành công)
                error_log("Bắt đầu đăng ký - ID học sinh: $id_hoc_sinh, ID lớp: $id_lop");
                $id_dang_ky = $this->model->dangKyKhoaHoc($id_hoc_sinh, $id_lop, 'Chờ xác nhận');
                
                // Kiểm tra kết quả đăng ký
                if ($id_dang_ky === false || $id_dang_ky === 0 || empty($id_dang_ky)) {
                    // Lấy thông tin lỗi chi tiết từ error log
                    $errorMsg = 'Đăng ký thất bại. Vui lòng kiểm tra lại thông tin hoặc thử lại sau!';
                    
                    // Log để debug
                    error_log("Đăng ký thất bại - ID học sinh: $id_hoc_sinh, ID lớp: $id_lop");
                    error_log("Kết quả dangKyKhoaHoc: " . var_export($id_dang_ky, true));
                    error_log("Kiểm tra session - client_id: " . ($_SESSION['client_id'] ?? 'KHÔNG CÓ'));
                    
                    $_SESSION['dang_ky_error'] = $errorMsg;
                    header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
                    exit;
                }
                
                error_log("Đăng ký thành công - ID đăng ký: $id_dang_ky");
                
                // Tạo mã đơn hàng VNPay
                $vnp_TxnRef = VNPayHelper::generateOrderId($id_dang_ky);
                
                // Cập nhật mã đơn hàng vào đăng ký
                $updateResult = $this->model->updateVNPayTxnRef($id_dang_ky, $vnp_TxnRef);
                if (!$updateResult) {
                    error_log("Không thể cập nhật mã đơn hàng VNPay cho đăng ký ID: " . $id_dang_ky);
                }
                
                // Lấy thông tin khóa học
                $khoaHoc = $this->model->getById($id_khoa_hoc);
                if (!$khoaHoc) {
                    $_SESSION['dang_ky_error'] = 'Không tìm thấy thông tin khóa học!';
                    header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
                    exit;
                }
                
                // Tính số tiền (lấy từ khoa_hoc.gia hoặc lop_hoc.hoc_phi, nhân với 100 vì VNPay yêu cầu)
                $hoc_phi = 0;
                if (isset($lopHoc['hoc_phi']) && !empty($lopHoc['hoc_phi']) && $lopHoc['hoc_phi'] > 0) {
                    $hoc_phi = (int)$lopHoc['hoc_phi'] * 100;
                } elseif (isset($khoaHoc['gia']) && !empty($khoaHoc['gia']) && $khoaHoc['gia'] > 0) {
                    $hoc_phi = (int)$khoaHoc['gia'] * 100;
                } else {
                    // Nếu không có giá, đặt mặc định 10000 VND (100 đồng) để test
                    $hoc_phi = 10000; // 100 VND
                }
                
                // Kiểm tra số tiền tối thiểu của VNPay (thường là 1000 VND = 100000)
                if ($hoc_phi < 100000) {
                    $hoc_phi = 100000; // Tối thiểu 1000 VND
                }
                
                // Tạo thông tin đơn hàng (giới hạn 255 ký tự)
                $orderInfo = "Thanh toan don hang #" . $vnp_TxnRef;
                if (mb_strlen($orderInfo) > 255) {
                    $orderInfo = mb_substr($orderInfo, 0, 250) . '...';
                }
                
                // Tạo URL thanh toán VNPay
                $vnp_Url = VNPayHelper::createPaymentUrl([
                    'vnp_TxnRef' => $vnp_TxnRef,
                    'vnp_OrderInfo' => $orderInfo,
                    'vnp_OrderType' => 'billpayment',
                    'vnp_Amount' => $hoc_phi,
                    'vnp_Locale' => 'vn'
                ]);
                
                if (empty($vnp_Url)) {
                    throw new Exception('Không thể tạo URL thanh toán VNPay!');
                }
                
                // Log để debug
                error_log("VNPay URL created: " . $vnp_Url);
                error_log("Order ID: " . $vnp_TxnRef . ", Amount: " . $hoc_phi);
                
                // Redirect đến VNPay
                header('Location: ' . $vnp_Url);
                exit;
                
            } catch (Exception $e) {
                error_log("Lỗi thanh toán online: " . $e->getMessage());
                $_SESSION['dang_ky_error'] = 'Lỗi khi xử lý thanh toán online: ' . $e->getMessage();
                header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
                exit;
            }
        } else {
            $_SESSION['dang_ky_error'] = 'Phương thức thanh toán không hợp lệ!';
        }

        header("Location: index.php?act=client-chi-tiet-khoa-hoc&id=" . $id_khoa_hoc);
        exit;
    }
}
