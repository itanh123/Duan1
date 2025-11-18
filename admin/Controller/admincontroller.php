<?php
require_once('./admin/Model/adminmodel.php');

class admincontroller{
    public $model;

    public function __construct(){
        $this->model = new adminmodel();
    }
    
    // Helper function để render view với layout
    private function renderView($viewPath, $pageTitle = 'Admin Panel', $data = []) {
        extract($data);
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        include './admin/View/layout.php';
        exit;
    }

    // Kiểm tra đăng nhập admin
    private function checkAdminLogin(){
        // Kiểm tra đã đăng nhập chưa
        if (!isset($_SESSION['admin_id']) && !isset($_SESSION['client_id'])) {
            header('Location: ?act=client-login');
            exit;
        }
        
        // Lấy ID người dùng (có thể là admin hoặc client)
        $userId = $_SESSION['admin_id'] ?? $_SESSION['client_id'];
        
        // Kiểm tra tài khoản có bị khóa không
        $user = $this->model->getNguoiDungById($userId);
        if (!$user || $user['trang_thai'] != 1) {
            // Tài khoản bị khóa hoặc không tồn tại, đăng xuất
            unset($_SESSION['admin_id']);
            unset($_SESSION['admin_email']);
            unset($_SESSION['admin_ho_ten']);
            unset($_SESSION['admin_vai_tro']);
            unset($_SESSION['client_id']);
            unset($_SESSION['client_email']);
            unset($_SESSION['client_ho_ten']);
            $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa!';
            header('Location: ?act=client-login');
            exit;
        }
        
        // Kiểm tra có vai trò admin không
        $hasAdminRole = $this->model->hasVaiTro($userId, 'admin');
        
        if (!$hasAdminRole) {
            header('Location: ?act=client-login');
            exit;
        }
        
        // Nếu đang dùng client session nhưng có vai trò admin, chuyển sang admin session
        if (isset($_SESSION['client_id']) && !isset($_SESSION['admin_id'])) {
            $_SESSION['admin_id'] = $_SESSION['client_id'];
            $_SESSION['admin_email'] = $_SESSION['client_email'];
            $_SESSION['admin_ho_ten'] = $_SESSION['client_ho_ten'];
            $_SESSION['admin_vai_tro'] = 'admin';
        }
    }

    // Kiểm tra quyền của admin
    private function checkPermission($ten_quyen){
        $this->checkAdminLogin();
        $adminId = $_SESSION['admin_id'];
        
        // Nếu có quyền 'quan_tri' thì có tất cả quyền (xem, them, sua, xoa)
        if ($this->model->hasPermission($adminId, 'quan_tri')) {
            return true; // Có quyền quan_tri = có tất cả quyền
        }
        
        // Kiểm tra quyền cụ thể
        if (!$this->model->hasPermission($adminId, $ten_quyen)) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện chức năng này!';
            header('Location: ?act=admin-dashboard');
            exit;
        }
    }

    // Trang đăng nhập admin - redirect về form đăng nhập chung
    public function login(){
        // Nếu đã đăng nhập thì chuyển về dashboard
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_vai_tro'] === 'admin') {
            header('Location: ?act=admin-dashboard');
            exit;
        }
        // Redirect về form đăng nhập chung ở client
        header('Location: ?act=client-login');
        exit;
    }

    // Xử lý đăng nhập admin
    public function processLogin(){
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ email và mật khẩu!';
            header('Location: ?act=client-login');
            exit;
        }

        $user = $this->model->login($email, $password, 'admin');
        
        if ($user) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_ho_ten'] = $user['ho_ten'];
            $_SESSION['admin_vai_tro'] = $user['vai_tro'];
            $_SESSION['success'] = 'Đăng nhập thành công!';
            header('Location: ?act=admin-dashboard');
        } else {
            $_SESSION['error'] = 'Email hoặc mật khẩu không đúng!';
            header('Location: ?act=client-login');
        }
        exit;
    }

    // Đăng xuất admin
    public function logout(){
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_ho_ten']);
        unset($_SESSION['admin_vai_tro']);
        $_SESSION['success'] = 'Đăng xuất thành công!';
        header('Location: ?act=client-login');
        exit;
    }

    // Trang chủ admin (Dashboard)
    public function dashboard(){
        $this->checkPermission('xem'); // Cần quyền xem để vào dashboard
        $thongKe = $this->model->getThongKe();
        $dangKyMoiNhat = $this->model->getDangKyMoiNhat(5);
        $thanhToanMoiNhat = $this->model->getThanhToanMoiNhat(5);
        
        // Load content
        ob_start();
        require_once('./admin/View/dashboard_content.php');
        $content = ob_get_clean();
        
        // Load layout
        $pageTitle = 'Dashboard';
        require_once('./admin/View/layout.php');
        exit;
    }

    // Danh sách khóa học
    public function listKhoaHoc(){
        $this->checkPermission('xem'); // Cần quyền xem
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_danh_muc = $_GET['id_danh_muc'] ?? '';
        
        $khoaHoc = $this->model->getKhoaHoc($page, $limit, $search, $id_danh_muc);
        $total = $this->model->countKhoaHoc($search, $id_danh_muc);
        $totalPages = ceil($total / $limit);
        $danhMuc = $this->model->getDanhMuc();
        
        ob_start();
        require_once('./admin/View/khoa_hoc/list_content.php');
        $content = ob_get_clean();
        
        $pageTitle = 'Quản lý Khóa Học';
        require_once('./admin/View/layout.php');
        exit;
    }

    // Form thêm khóa học
    public function addKhoaHoc(){
        $this->checkPermission('them'); // Cần quyền thêm
        $danhMuc = $this->model->getDanhMuc();
        require_once('./admin/View/khoa_hoc/form.php');
    }

    // Xử lý thêm khóa học
    public function saveKhoaHoc(){
        $this->checkPermission('them'); // Cần quyền thêm
        $data = [
            'id_danh_muc' => $_POST['id_danh_muc'] ?? '',
            'ten_khoa_hoc' => $_POST['ten_khoa_hoc'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'gia' => $_POST['gia'] ?? 0,
            'hinh_anh' => $this->uploadImage() ?? null,
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        if (empty($data['ten_khoa_hoc']) || empty($data['id_danh_muc'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            header('Location: ?act=admin-add-khoa-hoc');
            exit;
        }

        if ($this->model->addKhoaHoc($data)) {
            $_SESSION['success'] = 'Thêm khóa học thành công!';
            header('Location: ?act=admin-list-khoa-hoc');
        } else {
            $_SESSION['error'] = 'Thêm khóa học thất bại!';
            header('Location: ?act=admin-add-khoa-hoc');
        }
        exit;
    }

    // Form sửa khóa học
    public function editKhoaHoc(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-khoa-hoc');
            exit;
        }
        
        $khoaHoc = $this->model->getKhoaHocById($id);
        if (!$khoaHoc) {
            $_SESSION['error'] = 'Không tìm thấy khóa học!';
            header('Location: ?act=admin-list-khoa-hoc');
            exit;
        }
        
        $danhMuc = $this->model->getDanhMuc();
        require_once('./admin/View/khoa_hoc/form.php');
    }

    // Xử lý cập nhật khóa học
    public function updateKhoaHoc(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-khoa-hoc');
            exit;
        }

        $khoaHoc = $this->model->getKhoaHocById($id);
        if (!$khoaHoc) {
            $_SESSION['error'] = 'Không tìm thấy khóa học!';
            header('Location: ?act=admin-list-khoa-hoc');
            exit;
        }

        $data = [
            'id_danh_muc' => $_POST['id_danh_muc'] ?? '',
            'ten_khoa_hoc' => $_POST['ten_khoa_hoc'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'gia' => $_POST['gia'] ?? 0,
            'hinh_anh' => $khoaHoc['hinh_anh'], // Giữ ảnh cũ
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Nếu có ảnh mới được upload
        $newImage = $this->uploadImage();
        if ($newImage) {
            // Xóa ảnh cũ nếu có
            if ($khoaHoc['hinh_anh'] && file_exists('./uploads/' . $khoaHoc['hinh_anh'])) {
                unlink('./uploads/' . $khoaHoc['hinh_anh']);
            }
            $data['hinh_anh'] = $newImage;
        }

        if (empty($data['ten_khoa_hoc']) || empty($data['id_danh_muc'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            header('Location: ?act=admin-edit-khoa-hoc&id=' . $id);
            exit;
        }

        if ($this->model->updateKhoaHoc($id, $data)) {
            $_SESSION['success'] = 'Cập nhật khóa học thành công!';
            header('Location: ?act=admin-list-khoa-hoc');
        } else {
            $_SESSION['error'] = 'Cập nhật khóa học thất bại!';
            header('Location: ?act=admin-edit-khoa-hoc&id=' . $id);
        }
        exit;
    }

    // Xóa khóa học
    public function deleteKhoaHoc(){
        $this->checkPermission('xoa'); // Cần quyền xóa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-khoa-hoc');
            exit;
        }

        $khoaHoc = $this->model->getKhoaHocById($id);
        if ($khoaHoc && $khoaHoc['hinh_anh'] && file_exists('./uploads/' . $khoaHoc['hinh_anh'])) {
            unlink('./uploads/' . $khoaHoc['hinh_anh']);
        }

        if ($this->model->deleteKhoaHoc($id)) {
            $_SESSION['success'] = 'Xóa khóa học thành công!';
        } else {
            $_SESSION['error'] = 'Xóa khóa học thất bại!';
        }
        header('Location: ?act=admin-list-khoa-hoc');
        exit;
    }

    // Upload ảnh
    private function uploadImage(){
        if (!isset($_FILES['hinh_anh']) || $_FILES['hinh_anh']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $uploadDir = './uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $file = $_FILES['hinh_anh'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Kiểm tra kích thước file
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = 'Kích thước file vượt quá 5MB!';
            return null;
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['error'] = 'Định dạng file không hợp lệ! Chỉ chấp nhận JPG, PNG, GIF, WEBP.';
            return null;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'khoa_hoc_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        $_SESSION['error'] = 'Không thể upload file!';
        return null;
    }

    // ===========================================
    //  QUẢN LÝ HỌC SINH
    // ===========================================

    // Danh sách học sinh
    public function listHocSinh(){
        $this->checkPermission('xem'); // Cần quyền xem
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        
        $hocSinh = $this->model->getHocSinh($page, $limit, $search);
        $total = $this->model->countHocSinh($search);
        $totalPages = ceil($total / $limit);
        
        require_once('./admin/View/hoc_sinh/list.php');
    }

    // Form thêm học sinh
    public function addHocSinh(){
        $this->checkPermission('them'); // Cần quyền thêm
        require_once('./admin/View/hoc_sinh/form.php');
    }

    // Xử lý thêm học sinh
    public function saveHocSinh(){
        $this->checkPermission('them'); // Cần quyền thêm
        $data = [
            'ho_ten' => $_POST['ho_ten'] ?? '',
            'email' => $_POST['email'] ?? '',
            'mat_khau' => $_POST['mat_khau'] ?? '',
            'so_dien_thoai' => $_POST['so_dien_thoai'] ?? '',
            'dia_chi' => $_POST['dia_chi'] ?? '',
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Validation
        if (empty($data['ho_ten']) || empty($data['email']) || empty($data['mat_khau'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            header('Location: ?act=admin-add-hoc-sinh');
            exit;
        }

        // Kiểm tra email đã tồn tại chưa
        if ($this->model->checkEmailExists($data['email'])) {
            $_SESSION['error'] = 'Email đã tồn tại trong hệ thống!';
            header('Location: ?act=admin-add-hoc-sinh');
            exit;
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ!';
            header('Location: ?act=admin-add-hoc-sinh');
            exit;
        }

        if ($this->model->addHocSinh($data)) {
            $_SESSION['success'] = 'Thêm học sinh thành công!';
            header('Location: ?act=admin-list-hoc-sinh');
        } else {
            $_SESSION['error'] = 'Thêm học sinh thất bại!';
            header('Location: ?act=admin-add-hoc-sinh');
        }
        exit;
    }

    // Form sửa học sinh
    public function editHocSinh(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-hoc-sinh');
            exit;
        }
        
        $hocSinh = $this->model->getHocSinhById($id);
        if (!$hocSinh) {
            $_SESSION['error'] = 'Không tìm thấy học sinh!';
            header('Location: ?act=admin-list-hoc-sinh');
            exit;
        }
        
        require_once('./admin/View/hoc_sinh/form.php');
    }

    // Xử lý cập nhật học sinh
    public function updateHocSinh(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-hoc-sinh');
            exit;
        }

        $hocSinh = $this->model->getHocSinhById($id);
        if (!$hocSinh) {
            $_SESSION['error'] = 'Không tìm thấy học sinh!';
            header('Location: ?act=admin-list-hoc-sinh');
            exit;
        }

        $data = [
            'ho_ten' => $_POST['ho_ten'] ?? '',
            'email' => $_POST['email'] ?? '',
            'so_dien_thoai' => $_POST['so_dien_thoai'] ?? '',
            'dia_chi' => $_POST['dia_chi'] ?? '',
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Nếu có mật khẩu mới
        if (!empty($_POST['mat_khau'])) {
            $data['mat_khau'] = $_POST['mat_khau'];
        }

        // Validation
        if (empty($data['ho_ten']) || empty($data['email'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            header('Location: ?act=admin-edit-hoc-sinh&id=' . $id);
            exit;
        }

        // Kiểm tra email đã tồn tại chưa (trừ ID hiện tại)
        if ($this->model->checkEmailExists($data['email'], $id)) {
            $_SESSION['error'] = 'Email đã tồn tại trong hệ thống!';
            header('Location: ?act=admin-edit-hoc-sinh&id=' . $id);
            exit;
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ!';
            header('Location: ?act=admin-edit-hoc-sinh&id=' . $id);
            exit;
        }

        if ($this->model->updateHocSinh($id, $data)) {
            $_SESSION['success'] = 'Cập nhật học sinh thành công!';
            header('Location: ?act=admin-list-hoc-sinh');
        } else {
            $_SESSION['error'] = 'Cập nhật học sinh thất bại!';
            header('Location: ?act=admin-edit-hoc-sinh&id=' . $id);
        }
        exit;
    }

    // Xóa học sinh
    public function deleteHocSinh(){
        $this->checkPermission('xoa'); // Cần quyền xóa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-hoc-sinh');
            exit;
        }

        if ($this->model->deleteHocSinh($id)) {
            $_SESSION['success'] = 'Xóa học sinh thành công!';
        } else {
            $_SESSION['error'] = 'Xóa học sinh thất bại!';
        }
        header('Location: ?act=admin-list-hoc-sinh');
        exit;
    }

    // ===========================================
    //  QUẢN LÝ DANH MỤC
    // ===========================================

    // Danh sách danh mục
    public function listDanhMuc(){
        $this->checkPermission('xem'); // Cần quyền xem
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        
        $danhMuc = $this->model->getDanhMucList($page, $limit, $search);
        $total = $this->model->countDanhMuc($search);
        $totalPages = ceil($total / $limit);
        
        ob_start();
        require_once('./admin/View/danh_muc/list_content.php');
        $content = ob_get_clean();
        
        $pageTitle = 'Quản lý Danh Mục';
        require_once('./admin/View/layout.php');
        exit;
    }

    // Form thêm danh mục
    public function addDanhMuc(){
        $this->checkPermission('them'); // Cần quyền thêm
        require_once('./admin/View/danh_muc/form.php');
    }

    // Xử lý thêm danh mục
    public function saveDanhMuc(){
        $this->checkPermission('them'); // Cần quyền thêm
        $data = [
            'ten_danh_muc' => $_POST['ten_danh_muc'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Validation
        if (empty($data['ten_danh_muc'])) {
            $_SESSION['error'] = 'Vui lòng nhập tên danh mục!';
            header('Location: ?act=admin-add-danh-muc');
            exit;
        }

        // Kiểm tra tên danh mục đã tồn tại chưa
        if ($this->model->checkDanhMucExists($data['ten_danh_muc'])) {
            $_SESSION['error'] = 'Tên danh mục đã tồn tại trong hệ thống!';
            header('Location: ?act=admin-add-danh-muc');
            exit;
        }

        if ($this->model->addDanhMuc($data)) {
            $_SESSION['success'] = 'Thêm danh mục thành công!';
            header('Location: ?act=admin-list-danh-muc');
        } else {
            $_SESSION['error'] = 'Thêm danh mục thất bại!';
            header('Location: ?act=admin-add-danh-muc');
        }
        exit;
    }

    // Form sửa danh mục
    public function editDanhMuc(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-danh-muc');
            exit;
        }
        
        $danhMuc = $this->model->getDanhMucById($id);
        if (!$danhMuc) {
            $_SESSION['error'] = 'Không tìm thấy danh mục!';
            header('Location: ?act=admin-list-danh-muc');
            exit;
        }
        
        require_once('./admin/View/danh_muc/form.php');
    }

    // Xử lý cập nhật danh mục
    public function updateDanhMuc(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-danh-muc');
            exit;
        }

        $danhMuc = $this->model->getDanhMucById($id);
        if (!$danhMuc) {
            $_SESSION['error'] = 'Không tìm thấy danh mục!';
            header('Location: ?act=admin-list-danh-muc');
            exit;
        }

        $data = [
            'ten_danh_muc' => $_POST['ten_danh_muc'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Validation
        if (empty($data['ten_danh_muc'])) {
            $_SESSION['error'] = 'Vui lòng nhập tên danh mục!';
            header('Location: ?act=admin-edit-danh-muc&id=' . $id);
            exit;
        }

        // Kiểm tra tên danh mục đã tồn tại chưa (trừ ID hiện tại)
        if ($this->model->checkDanhMucExists($data['ten_danh_muc'], $id)) {
            $_SESSION['error'] = 'Tên danh mục đã tồn tại trong hệ thống!';
            header('Location: ?act=admin-edit-danh-muc&id=' . $id);
            exit;
        }

        if ($this->model->updateDanhMuc($id, $data)) {
            $_SESSION['success'] = 'Cập nhật danh mục thành công!';
            header('Location: ?act=admin-list-danh-muc');
        } else {
            $_SESSION['error'] = 'Cập nhật danh mục thất bại!';
            header('Location: ?act=admin-edit-danh-muc&id=' . $id);
        }
        exit;
    }

    // Xóa danh mục
    public function deleteDanhMuc(){
        $this->checkPermission('xoa'); // Cần quyền xóa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-danh-muc');
            exit;
        }

        $result = $this->model->deleteDanhMuc($id);
        if ($result) {
            $_SESSION['success'] = 'Xóa danh mục thành công!';
        } else {
            $_SESSION['error'] = 'Không thể xóa danh mục! Danh mục này đang được sử dụng trong khóa học.';
        }
        header('Location: ?act=admin-list-danh-muc');
        exit;
    }

    // ===========================================
    //  QUẢN LÝ GIẢNG VIÊN
    // ===========================================

    // Danh sách giảng viên
    public function listGiangVien(){
        $this->checkPermission('xem'); // Cần quyền xem
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        
        $giangVien = $this->model->getGiangVien($page, $limit, $search);
        $total = $this->model->countGiangVien($search);
        $totalPages = ceil($total / $limit);
        
        require_once('./admin/View/giang_vien/list.php');
    }

    // Form thêm giảng viên
    public function addGiangVien(){
        $this->checkPermission('them'); // Cần quyền thêm
        require_once('./admin/View/giang_vien/form.php');
    }

    // Xử lý thêm giảng viên
    public function saveGiangVien(){
        $this->checkPermission('them'); // Cần quyền thêm
        $data = [
            'ho_ten' => $_POST['ho_ten'] ?? '',
            'email' => $_POST['email'] ?? '',
            'mat_khau' => $_POST['mat_khau'] ?? '',
            'so_dien_thoai' => $_POST['so_dien_thoai'] ?? '',
            'dia_chi' => $_POST['dia_chi'] ?? '',
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Validation
        if (empty($data['ho_ten']) || empty($data['email']) || empty($data['mat_khau'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            header('Location: ?act=admin-add-giang-vien');
            exit;
        }

        // Kiểm tra email đã tồn tại chưa
        if ($this->model->checkEmailExists($data['email'])) {
            $_SESSION['error'] = 'Email đã tồn tại trong hệ thống!';
            header('Location: ?act=admin-add-giang-vien');
            exit;
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ!';
            header('Location: ?act=admin-add-giang-vien');
            exit;
        }

        if ($this->model->addGiangVien($data)) {
            $_SESSION['success'] = 'Thêm giảng viên thành công!';
            header('Location: ?act=admin-list-giang-vien');
        } else {
            $_SESSION['error'] = 'Thêm giảng viên thất bại!';
            header('Location: ?act=admin-add-giang-vien');
        }
        exit;
    }

    // Form sửa giảng viên
    public function editGiangVien(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-giang-vien');
            exit;
        }
        
        $giangVien = $this->model->getGiangVienById($id);
        if (!$giangVien) {
            $_SESSION['error'] = 'Không tìm thấy giảng viên!';
            header('Location: ?act=admin-list-giang-vien');
            exit;
        }
        
        require_once('./admin/View/giang_vien/form.php');
    }

    // Xử lý cập nhật giảng viên
    public function updateGiangVien(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-giang-vien');
            exit;
        }

        $giangVien = $this->model->getGiangVienById($id);
        if (!$giangVien) {
            $_SESSION['error'] = 'Không tìm thấy giảng viên!';
            header('Location: ?act=admin-list-giang-vien');
            exit;
        }

        $data = [
            'ho_ten' => $_POST['ho_ten'] ?? '',
            'email' => $_POST['email'] ?? '',
            'so_dien_thoai' => $_POST['so_dien_thoai'] ?? '',
            'dia_chi' => $_POST['dia_chi'] ?? '',
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Nếu có mật khẩu mới
        if (!empty($_POST['mat_khau'])) {
            $data['mat_khau'] = $_POST['mat_khau'];
        }

        // Validation
        if (empty($data['ho_ten']) || empty($data['email'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            header('Location: ?act=admin-edit-giang-vien&id=' . $id);
            exit;
        }

        // Kiểm tra email đã tồn tại chưa (trừ ID hiện tại)
        if ($this->model->checkEmailExists($data['email'], $id)) {
            $_SESSION['error'] = 'Email đã tồn tại trong hệ thống!';
            header('Location: ?act=admin-edit-giang-vien&id=' . $id);
            exit;
        }

        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email không hợp lệ!';
            header('Location: ?act=admin-edit-giang-vien&id=' . $id);
            exit;
        }

        if ($this->model->updateGiangVien($id, $data)) {
            $_SESSION['success'] = 'Cập nhật giảng viên thành công!';
            header('Location: ?act=admin-list-giang-vien');
        } else {
            $_SESSION['error'] = 'Cập nhật giảng viên thất bại!';
            header('Location: ?act=admin-edit-giang-vien&id=' . $id);
        }
        exit;
    }

    // Xóa giảng viên
    public function deleteGiangVien(){
        $this->checkPermission('xoa'); // Cần quyền xóa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-giang-vien');
            exit;
        }

        if ($this->model->deleteGiangVien($id)) {
            $_SESSION['success'] = 'Xóa giảng viên thành công!';
        } else {
            $_SESSION['error'] = 'Xóa giảng viên thất bại!';
        }
        header('Location: ?act=admin-list-giang-vien');
        exit;
    }

    // ===========================================
    //  QUẢN LÝ LỚP HỌC
    // ===========================================

    // Danh sách lớp học
    public function listLopHoc(){
        $this->checkPermission('xem'); // Cần quyền xem
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_khoa_hoc = $_GET['id_khoa_hoc'] ?? '';
        
        $lopHoc = $this->model->getLopHoc($page, $limit, $search, $id_khoa_hoc);
        $total = $this->model->countLopHoc($search, $id_khoa_hoc);
        $totalPages = ceil($total / $limit);
        $khoaHocList = $this->model->getKhoaHoc(1, 1000, '', ''); // Lấy tất cả khóa học để filter
        
        require_once('./admin/View/lop_hoc/list.php');
    }

    // Form thêm lớp học
    public function addLopHoc(){
        $this->checkPermission('them'); // Cần quyền thêm
        $khoaHocList = $this->model->getKhoaHoc(1, 1000, '', ''); // Lấy tất cả khóa học
        require_once('./admin/View/lop_hoc/form.php');
    }

    // Xử lý thêm lớp học
    public function saveLopHoc(){
        $this->checkPermission('them'); // Cần quyền thêm
        $trang_thai = $_POST['trang_thai'] ?? 'Chưa khai giảng';
        // Đảm bảo trang_thai là một trong các giá trị ENUM hợp lệ
        $validTrangThai = ['Chưa khai giảng', 'Đang học', 'Kết thúc'];
        if (!in_array($trang_thai, $validTrangThai)) {
            $trang_thai = 'Chưa khai giảng'; // Mặc định
        }
        $data = [
            'id_khoa_hoc' => $_POST['id_khoa_hoc'] ?? '',
            'ten_lop' => $_POST['ten_lop'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'so_luong_toi_da' => !empty($_POST['so_luong_toi_da']) ? (int)$_POST['so_luong_toi_da'] : null,
            'trang_thai' => $trang_thai
        ];

        // Validation
        if (empty($data['id_khoa_hoc']) || empty($data['ten_lop'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            header('Location: ?act=admin-add-lop-hoc');
            exit;
        }

        if ($this->model->addLopHoc($data)) {
            $_SESSION['success'] = 'Thêm lớp học thành công!';
            header('Location: ?act=admin-list-lop-hoc');
        } else {
            $_SESSION['error'] = 'Thêm lớp học thất bại!';
            header('Location: ?act=admin-add-lop-hoc');
        }
        exit;
    }

    // Form sửa lớp học
    public function editLopHoc(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-lop-hoc');
            exit;
        }
        
        $lopHoc = $this->model->getLopHocById($id);
        if (!$lopHoc) {
            $_SESSION['error'] = 'Không tìm thấy lớp học!';
            header('Location: ?act=admin-list-lop-hoc');
            exit;
        }
        
        $khoaHocList = $this->model->getKhoaHoc(1, 1000, '', ''); // Lấy tất cả khóa học
        require_once('./admin/View/lop_hoc/form.php');
    }

    // Xử lý cập nhật lớp học
    public function updateLopHoc(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-lop-hoc');
            exit;
        }

        $lopHoc = $this->model->getLopHocById($id);
        if (!$lopHoc) {
            $_SESSION['error'] = 'Không tìm thấy lớp học!';
            header('Location: ?act=admin-list-lop-hoc');
            exit;
        }

        $trang_thai = $_POST['trang_thai'] ?? 'Chưa khai giảng';
        // Đảm bảo trang_thai là một trong các giá trị ENUM hợp lệ
        $validTrangThai = ['Chưa khai giảng', 'Đang học', 'Kết thúc'];
        if (!in_array($trang_thai, $validTrangThai)) {
            $trang_thai = 'Chưa khai giảng'; // Mặc định
        }
        $data = [
            'id_khoa_hoc' => $_POST['id_khoa_hoc'] ?? '',
            'ten_lop' => $_POST['ten_lop'] ?? '',
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'so_luong_toi_da' => !empty($_POST['so_luong_toi_da']) ? (int)$_POST['so_luong_toi_da'] : null,
            'trang_thai' => $trang_thai
        ];

        // Validation
        if (empty($data['id_khoa_hoc']) || empty($data['ten_lop'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            header('Location: ?act=admin-edit-lop-hoc&id=' . $id);
            exit;
        }

        if ($this->model->updateLopHoc($id, $data)) {
            $_SESSION['success'] = 'Cập nhật lớp học thành công!';
            header('Location: ?act=admin-list-lop-hoc');
        } else {
            $_SESSION['error'] = 'Cập nhật lớp học thất bại!';
            header('Location: ?act=admin-edit-lop-hoc&id=' . $id);
        }
        exit;
    }

    // ===========================================
    //  QUẢN LÝ CA HỌC
    // ===========================================

    // Danh sách ca học
    public function listCaHoc(){
        $this->checkPermission('xem'); // Cần quyền xem
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_lop = $_GET['id_lop'] ?? '';
        
        $total = $this->model->countCaHoc($search, $id_lop);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages));
        
        $caHoc = $this->model->getCaHoc($page, $limit, $search, $id_lop);
        $lopHocList = $this->model->getLopHocList(); // Lấy danh sách lớp học để filter
        
        require_once('./admin/View/ca_hoc/list.php');
    }

    // Form thêm ca học
    public function addCaHoc(){
        $this->checkPermission('them'); // Cần quyền thêm
        $lopHocList = $this->model->getLopHocList(); // Lấy danh sách lớp học
        $giangVienList = $this->model->getGiangVienList(); // Lấy danh sách giảng viên
        $caMacDinhList = $this->model->getCaMacDinhList(); // Lấy danh sách ca mặc định
        $phongHocList = $this->model->getPhongHocList(); // Lấy danh sách phòng học
        require_once('./admin/View/ca_hoc/form.php');
    }

    // Xử lý thêm ca học
    public function saveCaHoc(){
        $this->checkPermission('them'); // Cần quyền thêm
        $id_giang_vien = $_POST['id_giang_vien'] ?? '';
        $data = [
            'id_lop' => $_POST['id_lop'] ?? '',
            'id_giang_vien' => !empty($id_giang_vien) ? (int)$id_giang_vien : null,
            'id_ca' => $_POST['id_ca'] ?? '',
            'thu_trong_tuan' => $_POST['thu_trong_tuan'] ?? '',
            'id_phong' => $_POST['id_phong'] ?? '',
            'ghi_chu' => $_POST['ghi_chu'] ?? ''
        ];

        // Validation
        if (empty($data['id_lop']) || empty($data['id_ca']) || empty($data['thu_trong_tuan'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            header('Location: ?act=admin-add-ca-hoc');
            exit;
        }

        // Kiểm tra giá trị ENUM hợp lệ
        $validThu = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
        if (!in_array($data['thu_trong_tuan'], $validThu)) {
            $_SESSION['error'] = 'Thứ trong tuần không hợp lệ!';
            header('Location: ?act=admin-add-ca-hoc');
            exit;
        }

        if ($this->model->addCaHoc($data)) {
            $_SESSION['success'] = 'Thêm ca học thành công!';
            header('Location: ?act=admin-list-ca-hoc');
        } else {
            $_SESSION['error'] = 'Thêm ca học thất bại!';
            header('Location: ?act=admin-add-ca-hoc');
        }
        exit;
    }

    // Form sửa ca học
    public function editCaHoc(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-ca-hoc');
            exit;
        }
        
        $caHoc = $this->model->getCaHocById($id);
        if (!$caHoc) {
            $_SESSION['error'] = 'Không tìm thấy ca học!';
            header('Location: ?act=admin-list-ca-hoc');
            exit;
        }
        
        $lopHocList = $this->model->getLopHocList(); // Lấy danh sách lớp học
        $giangVienList = $this->model->getGiangVienList(); // Lấy danh sách giảng viên
        $caMacDinhList = $this->model->getCaMacDinhList(); // Lấy danh sách ca mặc định
        $phongHocList = $this->model->getPhongHocList(); // Lấy danh sách phòng học
        require_once('./admin/View/ca_hoc/form.php');
    }

    // Xử lý cập nhật ca học
    public function updateCaHoc(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-ca-hoc');
            exit;
        }

        $caHoc = $this->model->getCaHocById($id);
        if (!$caHoc) {
            $_SESSION['error'] = 'Không tìm thấy ca học!';
            header('Location: ?act=admin-list-ca-hoc');
            exit;
        }

        $id_giang_vien = $_POST['id_giang_vien'] ?? '';
        $data = [
            'id_lop' => $_POST['id_lop'] ?? '',
            'id_giang_vien' => !empty($id_giang_vien) ? (int)$id_giang_vien : null,
            'id_ca' => $_POST['id_ca'] ?? '',
            'thu_trong_tuan' => $_POST['thu_trong_tuan'] ?? '',
            'id_phong' => $_POST['id_phong'] ?? '',
            'ghi_chu' => $_POST['ghi_chu'] ?? ''
        ];

        // Validation
        if (empty($data['id_lop']) || empty($data['id_ca']) || empty($data['thu_trong_tuan'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            header('Location: ?act=admin-edit-ca-hoc&id=' . $id);
            exit;
        }

        // Kiểm tra giá trị ENUM hợp lệ
        $validThu = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
        if (!in_array($data['thu_trong_tuan'], $validThu)) {
            $_SESSION['error'] = 'Thứ trong tuần không hợp lệ!';
            header('Location: ?act=admin-edit-ca-hoc&id=' . $id);
            exit;
        }

        if ($this->model->updateCaHoc($id, $data)) {
            $_SESSION['success'] = 'Cập nhật ca học thành công!';
            header('Location: ?act=admin-list-ca-hoc');
        } else {
            $_SESSION['error'] = 'Cập nhật ca học thất bại!';
            header('Location: ?act=admin-edit-ca-hoc&id=' . $id);
        }
        exit;
    }

    // Xóa ca học
    public function deleteCaHoc(){
        $this->checkPermission('xoa'); // Cần quyền xóa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-ca-hoc');
            exit;
        }

        if ($this->model->deleteCaHoc($id)) {
            $_SESSION['success'] = 'Xóa ca học thành công!';
        } else {
            $_SESSION['error'] = 'Xóa ca học thất bại!';
        }
        header('Location: ?act=admin-list-ca-hoc');
        exit;
    }

    // Xóa lớp học
    public function deleteLopHoc(){
        $this->checkPermission('xoa'); // Cần quyền xóa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-lop-hoc');
            exit;
        }

        $result = $this->model->deleteLopHoc($id);
        if ($result) {
            $_SESSION['success'] = 'Xóa lớp học thành công!';
        } else {
            $_SESSION['error'] = 'Không thể xóa lớp học! Lớp học này đang có học sinh đăng ký.';
        }
        header('Location: ?act=admin-list-lop-hoc');
        exit;
    }

    // ===========================================
    //  QUẢN LÝ ĐĂNG KÝ
    // ===========================================

    // Danh sách đăng ký
    public function listDangKy(){
        $this->checkPermission('xem'); // Cần quyền xem
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_lop = $_GET['id_lop'] ?? '';
        $trang_thai = $_GET['trang_thai'] ?? '';

        $dangKy = $this->model->getDangKy($page, $limit, $search, $id_lop, $trang_thai);
        $total = $this->model->countDangKy($search, $id_lop, $trang_thai);
        $totalPages = ceil($total / $limit);

        $lopHocList = $this->model->getLopHocList(); // Lấy danh sách lớp học để filter
        
        require_once('./admin/View/dang_ky/list.php');
    }

    // Form sửa đăng ký
    public function editDangKy(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-dang-ky');
            exit;
        }
        
        $dangKy = $this->model->getDangKyById($id);
        if (!$dangKy) {
            $_SESSION['error'] = 'Không tìm thấy đăng ký!';
            header('Location: ?act=admin-list-dang-ky');
            exit;
        }
        
        require_once('./admin/View/dang_ky/form.php');
    }

    // Xử lý cập nhật đăng ký
    public function updateDangKy(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-dang-ky');
            exit;
        }

        $dangKy = $this->model->getDangKyById($id);
        if (!$dangKy) {
            $_SESSION['error'] = 'Không tìm thấy đăng ký!';
            header('Location: ?act=admin-list-dang-ky');
            exit;
        }

        $data = [
            'trang_thai' => $_POST['trang_thai'] ?? ''
        ];

        // Validation
        $validTrangThai = ['Chờ xác nhận', 'Đã xác nhận', 'Đã hủy'];
        if (empty($data['trang_thai']) || !in_array($data['trang_thai'], $validTrangThai)) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ!';
            header('Location: ?act=admin-edit-dang-ky&id=' . $id);
            exit;
        }

        if ($this->model->updateDangKy($id, $data)) {
            $_SESSION['success'] = 'Cập nhật đăng ký thành công!';
            header('Location: ?act=admin-list-dang-ky');
        } else {
            $_SESSION['error'] = 'Cập nhật đăng ký thất bại!';
            header('Location: ?act=admin-edit-dang-ky&id=' . $id);
        }
        exit;
    }

    // Xóa đăng ký
    public function deleteDangKy(){
        $this->checkPermission('xoa'); // Cần quyền xóa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-dang-ky');
            exit;
        }

        if ($this->model->deleteDangKy($id)) {
            $_SESSION['success'] = 'Xóa đăng ký thành công!';
        } else {
            $_SESSION['error'] = 'Xóa đăng ký thất bại!';
        }
        header('Location: ?act=admin-list-dang-ky');
        exit;
    }

    // ===========================================
    //  QUẢN LÝ BÌNH LUẬN
    // ===========================================

    // Danh sách bình luận
    public function listBinhLuan(){
        $this->checkPermission('xem'); // Cần quyền xem
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_khoa_hoc = $_GET['id_khoa_hoc'] ?? '';
        $trang_thai = $_GET['trang_thai'] ?? '';

        $binhLuan = $this->model->getBinhLuan($page, $limit, $search, $id_khoa_hoc, $trang_thai);
        $total = $this->model->countBinhLuan($search, $id_khoa_hoc, $trang_thai);
        $totalPages = ceil($total / $limit);

        $khoaHocList = $this->model->getKhoaHoc(1, 1000, '', ''); // Lấy tất cả khóa học để filter
        
        ob_start();
        require_once('./admin/View/binh_luan/list_content.php');
        $content = ob_get_clean();
        
        $pageTitle = 'Quản lý Bình Luận';
        require_once('./admin/View/layout.php');
        exit;
    }

    // Form sửa bình luận
    public function editBinhLuan(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }
        
        $binhLuan = $this->model->getBinhLuanById($id);
        if (!$binhLuan) {
            $_SESSION['error'] = 'Không tìm thấy bình luận!';
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }
        
        require_once('./admin/View/binh_luan/form.php');
    }

    // Xử lý cập nhật bình luận
    public function updateBinhLuan(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }

        $binhLuan = $this->model->getBinhLuanById($id);
        if (!$binhLuan) {
            $_SESSION['error'] = 'Không tìm thấy bình luận!';
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }

        $data = [
            'noi_dung' => $_POST['noi_dung'] ?? '',
            'danh_gia' => !empty($_POST['danh_gia']) ? (int)$_POST['danh_gia'] : null,
            'trang_thai' => $_POST['trang_thai'] ?? 'Hiển thị'
        ];

        // Validation
        if (empty($data['noi_dung'])) {
            $_SESSION['error'] = 'Nội dung bình luận không được để trống!';
            header('Location: ?act=admin-edit-binh-luan&id=' . $id);
            exit;
        }

        // Kiểm tra giá trị ENUM hợp lệ
        $validTrangThai = ['Hiển thị', 'Ẩn', 'Đã xóa'];
        if (!in_array($data['trang_thai'], $validTrangThai)) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ!';
            header('Location: ?act=admin-edit-binh-luan&id=' . $id);
            exit;
        }

        // Kiểm tra đánh giá hợp lệ (1-5 hoặc null)
        if ($data['danh_gia'] !== null && ($data['danh_gia'] < 1 || $data['danh_gia'] > 5)) {
            $_SESSION['error'] = 'Đánh giá phải từ 1 đến 5 sao!';
            header('Location: ?act=admin-edit-binh-luan&id=' . $id);
            exit;
        }

        if ($this->model->updateBinhLuan($id, $data)) {
            $_SESSION['success'] = 'Cập nhật bình luận thành công!';
            header('Location: ?act=admin-list-binh-luan');
        } else {
            $_SESSION['error'] = 'Cập nhật bình luận thất bại!';
            header('Location: ?act=admin-edit-binh-luan&id=' . $id);
        }
        exit;
    }

    // Xóa bình luận
    public function deleteBinhLuan(){
        $this->checkPermission('xoa'); // Cần quyền xóa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }

        if ($this->model->deleteBinhLuan($id)) {
            $_SESSION['success'] = 'Xóa bình luận thành công!';
        } else {
            $_SESSION['error'] = 'Xóa bình luận thất bại!';
        }
        header('Location: ?act=admin-list-binh-luan');
        exit;
    }

    // ===========================================
    //  QUẢN LÝ PHÒNG HỌC
    // ===========================================

    // Danh sách phòng học
    public function listPhongHoc(){
        $this->checkPermission('xem'); // Cần quyền xem
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $trang_thai = $_GET['trang_thai'] ?? '';
        
        $phongHoc = $this->model->getPhongHoc($page, $limit, $search, $trang_thai);
        $total = $this->model->countPhongHoc($search, $trang_thai);
        $totalPages = ceil($total / $limit);
        
        ob_start();
        require_once('./admin/View/phong_hoc/list_content.php');
        $content = ob_get_clean();
        
        $pageTitle = 'Quản lý Phòng Học';
        require_once('./admin/View/layout.php');
        exit;
    }

    // Form thêm phòng học
    public function addPhongHoc(){
        $this->checkPermission('them'); // Cần quyền thêm
        require_once('./admin/View/phong_hoc/form.php');
    }

    // Xử lý thêm phòng học
    public function savePhongHoc(){
        $this->checkPermission('them'); // Cần quyền thêm
        $data = [
            'ten_phong' => $_POST['ten_phong'] ?? '',
            'suc_chua' => !empty($_POST['suc_chua']) ? (int)$_POST['suc_chua'] : 30,
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'trang_thai' => $_POST['trang_thai'] ?? 'Sử dụng'
        ];

        // Validation
        if (empty($data['ten_phong'])) {
            $_SESSION['error'] = 'Vui lòng nhập tên phòng học!';
            header('Location: ?act=admin-add-phong-hoc');
            exit;
        }

        // Kiểm tra tên phòng học đã tồn tại chưa
        if ($this->model->checkPhongHocExists($data['ten_phong'])) {
            $_SESSION['error'] = 'Tên phòng học đã tồn tại trong hệ thống!';
            header('Location: ?act=admin-add-phong-hoc');
            exit;
        }

        // Kiểm tra giá trị ENUM hợp lệ
        $validTrangThai = ['Sử dụng', 'Bảo trì', 'Khóa'];
        if (!in_array($data['trang_thai'], $validTrangThai)) {
            $data['trang_thai'] = 'Sử dụng'; // Mặc định
        }

        if ($this->model->addPhongHoc($data)) {
            $_SESSION['success'] = 'Thêm phòng học thành công!';
            header('Location: ?act=admin-list-phong-hoc');
        } else {
            $_SESSION['error'] = 'Thêm phòng học thất bại!';
            header('Location: ?act=admin-add-phong-hoc');
        }
        exit;
    }

    // Form sửa phòng học
    public function editPhongHoc(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-phong-hoc');
            exit;
        }
        
        $phongHoc = $this->model->getPhongHocById($id);
        if (!$phongHoc) {
            $_SESSION['error'] = 'Không tìm thấy phòng học!';
            header('Location: ?act=admin-list-phong-hoc');
            exit;
        }
        
        require_once('./admin/View/phong_hoc/form.php');
    }

    // Xử lý cập nhật phòng học
    public function updatePhongHoc(){
        $this->checkPermission('sua'); // Cần quyền sửa
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            header('Location: ?act=admin-list-phong-hoc');
            exit;
        }

        $phongHoc = $this->model->getPhongHocById($id);
        if (!$phongHoc) {
            $_SESSION['error'] = 'Không tìm thấy phòng học!';
            header('Location: ?act=admin-list-phong-hoc');
            exit;
        }

        $data = [
            'ten_phong' => $_POST['ten_phong'] ?? '',
            'suc_chua' => !empty($_POST['suc_chua']) ? (int)$_POST['suc_chua'] : 30,
            'mo_ta' => $_POST['mo_ta'] ?? '',
            'trang_thai' => $_POST['trang_thai'] ?? 'Sử dụng'
        ];

        // Validation
        if (empty($data['ten_phong'])) {
            $_SESSION['error'] = 'Vui lòng nhập tên phòng học!';
            header('Location: ?act=admin-edit-phong-hoc&id=' . $id);
            exit;
        }

        // Kiểm tra tên phòng học đã tồn tại chưa (trừ ID hiện tại)
        if ($this->model->checkPhongHocExists($data['ten_phong'], $id)) {
            $_SESSION['error'] = 'Tên phòng học đã tồn tại trong hệ thống!';
            header('Location: ?act=admin-edit-phong-hoc&id=' . $id);
            exit;
        }

        // Kiểm tra giá trị ENUM hợp lệ
        $validTrangThai = ['Sử dụng', 'Bảo trì', 'Khóa'];
        if (!in_array($data['trang_thai'], $validTrangThai)) {
            $data['trang_thai'] = 'Sử dụng'; // Mặc định
        }

        if ($this->model->updatePhongHoc($id, $data)) {
            $_SESSION['success'] = 'Cập nhật phòng học thành công!';
            header('Location: ?act=admin-list-phong-hoc');
        } else {
            $_SESSION['error'] = 'Cập nhật phòng học thất bại!';
            header('Location: ?act=admin-edit-phong-hoc&id=' . $id);
        }
        exit;
    }

    // Xóa phòng học
    public function deletePhongHoc(){
        $this->checkPermission('xoa'); // Cần quyền xóa
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-phong-hoc');
            exit;
        }

        $result = $this->model->deletePhongHoc($id);
        if ($result) {
            $_SESSION['success'] = 'Xóa phòng học thành công!';
        } else {
            $_SESSION['error'] = 'Không thể xóa phòng học! Phòng học này đang được sử dụng trong ca học.';
        }
        header('Location: ?act=admin-list-phong-hoc');
        exit;
    }

    // ===========================================
    //  QUẢN LÝ PHÂN QUYỀN
    // ===========================================

    // Danh sách phân quyền
    public function listPhanQuyen(){
        $this->checkPermission('quan_tri'); // Chỉ quản trị viên mới có quyền quản lý phân quyền
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_nguoi_dung = $_GET['id_nguoi_dung'] ?? '';
        $vai_tro = $_GET['vai_tro'] ?? ''; // Filter theo vai trò

        $phanQuyen = $this->model->getPhanQuyen($page, $limit, $search, $id_nguoi_dung, $vai_tro);
        $total = $this->model->countPhanQuyen($search, $id_nguoi_dung, $vai_tro);
        $totalPages = ceil($total / $limit);

        $nguoiDungList = $this->model->getNguoiDungForPhanQuyen();

        $data = [
            'phanQuyen' => $phanQuyen,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'search' => $search,
            'id_nguoi_dung' => $id_nguoi_dung,
            'vai_tro' => $vai_tro,
            'nguoiDungList' => $nguoiDungList
        ];

        $this->renderView('./admin/View/phan_quyen/list_content.php', 'Quản lý Phân quyền', $data);
    }

    // Form thêm phân quyền
    public function addPhanQuyen(){
        $this->checkPermission('quan_tri');
        
        $nguoiDungList = $this->model->getNguoiDungForPhanQuyen();
        
        $data = [
            'nguoiDungList' => $nguoiDungList,
            'phanQuyen' => null
        ];

        $this->renderView('./admin/View/phan_quyen/form_content.php', 'Thêm Phân quyền', $data);
    }

    // Xử lý thêm phân quyền
    public function savePhanQuyen(){
        $this->checkPermission('quan_tri');
        
        $id_nguoi_dung = $_POST['id_nguoi_dung'] ?? 0;
        $ten_quyen = $_POST['ten_quyen'] ?? '';

        if (empty($id_nguoi_dung) || empty($ten_quyen)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin!';
            header('Location: ?act=admin-add-phan-quyen');
            exit;
        }

        $data = [
            'id_nguoi_dung' => $id_nguoi_dung,
            'ten_quyen' => $ten_quyen
        ];

        $result = $this->model->addPhanQuyen($data);
        if ($result) {
            $_SESSION['success'] = 'Thêm phân quyền thành công!';
            header('Location: ?act=admin-list-phan-quyen');
        } else {
            $_SESSION['error'] = 'Phân quyền này đã tồn tại cho người dùng này!';
            header('Location: ?act=admin-add-phan-quyen');
        }
        exit;
    }

    // Form sửa phân quyền
    public function editPhanQuyen(){
        $this->checkPermission('quan_tri');
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        $phanQuyen = $this->model->getPhanQuyenById($id);
        if (!$phanQuyen) {
            $_SESSION['error'] = 'Không tìm thấy phân quyền!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        $nguoiDungList = $this->model->getNguoiDungForPhanQuyen();
        
        $data = [
            'phanQuyen' => $phanQuyen,
            'nguoiDungList' => $nguoiDungList
        ];

        $this->renderView('./admin/View/phan_quyen/form_content.php', 'Sửa Phân quyền', $data);
    }

    // Xử lý cập nhật phân quyền
    public function updatePhanQuyen(){
        $this->checkPermission('quan_tri');
        
        $id = $_POST['id'] ?? 0;
        $id_nguoi_dung = $_POST['id_nguoi_dung'] ?? 0;
        $ten_quyen = $_POST['ten_quyen'] ?? '';

        if (!$id || empty($id_nguoi_dung) || empty($ten_quyen)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        $data = [
            'id_nguoi_dung' => $id_nguoi_dung,
            'ten_quyen' => $ten_quyen
        ];

        $result = $this->model->updatePhanQuyen($id, $data);
        if ($result) {
            $_SESSION['success'] = 'Cập nhật phân quyền thành công!';
        } else {
            $_SESSION['error'] = 'Phân quyền này đã tồn tại cho người dùng này!';
        }
        header('Location: ?act=admin-list-phan-quyen');
        exit;
    }

    // Xóa phân quyền
    public function deletePhanQuyen(){
        $this->checkPermission('quan_tri');
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        $result = $this->model->deletePhanQuyen($id);
        if ($result) {
            $_SESSION['success'] = 'Xóa phân quyền thành công!';
        } else {
            $_SESSION['error'] = 'Xóa phân quyền thất bại!';
        }
        header('Location: ?act=admin-list-phan-quyen');
        exit;
    }

    // Quản lý phân quyền cho một người dùng cụ thể
    public function manageQuyenNguoiDung(){
        $this->checkPermission('quan_tri');
        
        $id_nguoi_dung = $_GET['id_nguoi_dung'] ?? 0;
        if (!$id_nguoi_dung) {
            $_SESSION['error'] = 'ID người dùng không hợp lệ!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        $nguoiDung = $this->model->getNguoiDungById($id_nguoi_dung);
        if (!$nguoiDung) {
            $_SESSION['error'] = 'Không tìm thấy người dùng!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        // Cho phép phân quyền cho bất kỳ ai

        $data = [
            'nguoiDung' => $nguoiDung
        ];

        $this->renderView('./admin/View/phan_quyen/manage_content.php', 'Quản lý Phân quyền: ' . $nguoiDung['ho_ten'], $data);
    }

    // Xử lý cập nhật quyền cho người dùng
    public function updateQuyenNguoiDung(){
        $this->checkPermission('quan_tri');
        
        $id_nguoi_dung = $_POST['id_nguoi_dung'] ?? 0;
        $quyenList = $_POST['quyen'] ?? [];

        if (!$id_nguoi_dung) {
            $_SESSION['error'] = 'ID người dùng không hợp lệ!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        // Lấy thông tin người dùng
        $nguoiDung = $this->model->getNguoiDungById($id_nguoi_dung);
        if (!$nguoiDung) {
            $_SESSION['error'] = 'Không tìm thấy người dùng!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        // Kiểm tra nếu đang xóa quyền quan_tri của admin
        // Và đây là admin cuối cùng có quyền quan_tri và vai trò admin
        $hasAdminRole = $this->model->hasVaiTro($id_nguoi_dung, 'admin');
        if ($hasAdminRole && in_array('quan_tri', $nguoiDung['quyen'] ?? [])) {
            if (!in_array('quan_tri', $quyenList)) {
                // Đang xóa quyền quan_tri
                $countAdmin = $this->model->countAdminWithQuanTri();
                if ($countAdmin <= 1) {
                    $_SESSION['error'] = 'Không thể xóa quyền quản trị! Phải luôn có ít nhất 1 người có vai trò Admin và quyền quản trị.';
                    header('Location: ?act=admin-manage-quyen-nguoi-dung&id_nguoi_dung=' . $id_nguoi_dung);
                    exit;
                }
            }
        }

        $result = $this->model->addMultipleQuyen($id_nguoi_dung, $quyenList);
        if ($result) {
            $_SESSION['success'] = 'Cập nhật phân quyền thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật phân quyền thất bại!';
        }
        header('Location: ?act=admin-manage-quyen-nguoi-dung&id_nguoi_dung=' . $id_nguoi_dung);
        exit;
    }

    // ===========================================
    //  QUẢN LÝ VAI TRÒ (CHỈ ADMIN CÓ QUYỀN)
    // ===========================================

    // Quản lý vai trò cho một người dùng
    public function manageVaiTroNguoiDung(){
        $this->checkPermission('quan_tri'); // Chỉ admin có quyền quan_tri mới được quản lý vai trò
        
        $id_nguoi_dung = $_GET['id_nguoi_dung'] ?? 0;
        if (!$id_nguoi_dung) {
            $_SESSION['error'] = 'ID người dùng không hợp lệ!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        $nguoiDung = $this->model->getNguoiDungWithVaiTro($id_nguoi_dung);
        if (!$nguoiDung) {
            $_SESSION['error'] = 'Không tìm thấy người dùng!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        $data = [
            'nguoiDung' => $nguoiDung
        ];

        $this->renderView('./admin/View/vai_tro/manage_content.php', 'Quản lý Vai trò: ' . $nguoiDung['ho_ten'], $data);
    }

    // Xử lý cập nhật vai trò cho người dùng
    public function updateVaiTroNguoiDung(){
        $this->checkPermission('quan_tri');
        
        $id_nguoi_dung = $_POST['id_nguoi_dung'] ?? 0;
        $vaiTroList = $_POST['vai_tro'] ?? [];

        if (!$id_nguoi_dung) {
            $_SESSION['error'] = 'ID người dùng không hợp lệ!';
            header('Location: ?act=admin-list-phan-quyen');
            exit;
        }

        // Kiểm tra nếu đang xóa vai trò admin của admin cuối cùng có quyền quan_tri
        $nguoiDung = $this->model->getNguoiDungWithVaiTro($id_nguoi_dung);
        if (in_array('admin', $nguoiDung['vai_tro_list'] ?? []) && !in_array('admin', $vaiTroList)) {
            // Đang xóa vai trò admin
            $countAdmin = $this->model->countAdminWithQuanTri();
            if ($countAdmin <= 1 && $this->model->hasPermission($id_nguoi_dung, 'quan_tri')) {
                $_SESSION['error'] = 'Không thể xóa vai trò Admin! Phải luôn có ít nhất 1 admin với quyền quản trị.';
                header('Location: ?act=admin-manage-vai-tro-nguoi-dung&id_nguoi_dung=' . $id_nguoi_dung);
                exit;
            }
        }

        $result = $this->model->updateVaiTro($id_nguoi_dung, $vaiTroList);
        if ($result) {
            $_SESSION['success'] = 'Cập nhật vai trò thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật vai trò thất bại!';
        }
        header('Location: ?act=admin-manage-vai-tro-nguoi-dung&id_nguoi_dung=' . $id_nguoi_dung);
        exit;
    }

    // ===========================================
    //  QUẢN LÝ TÀI KHOẢN (CHỈ ADMIN CÓ QUYỀN QUAN_TRI)
    // ===========================================

    // Danh sách tài khoản
    public function listTaiKhoan(){
        $this->checkPermission('quan_tri'); // Chỉ admin có quyền quan_tri
        
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $trang_thai = $_GET['trang_thai'] ?? '';
        
        $taiKhoan = $this->model->getAllTaiKhoan($page, $limit, $search, $trang_thai);
        $total = $this->model->countAllTaiKhoan($search, $trang_thai);
        $totalPages = ceil($total / $limit);
        
        $data = [
            'taiKhoan' => $taiKhoan,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'trang_thai' => $trang_thai
        ];

        $this->renderView('./admin/View/tai_khoan/list_content.php', 'Quản lý Tài khoản', $data);
    }

    // Form sửa tài khoản
    public function editTaiKhoan(){
        $this->checkPermission('quan_tri');
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID tài khoản không hợp lệ!';
            header('Location: ?act=admin-list-tai-khoan');
            exit;
        }

        $taiKhoan = $this->model->getTaiKhoanById($id);
        if (!$taiKhoan) {
            $_SESSION['error'] = 'Không tìm thấy tài khoản!';
            header('Location: ?act=admin-list-tai-khoan');
            exit;
        }

        $data = [
            'taiKhoan' => $taiKhoan
        ];

        $this->renderView('./admin/View/tai_khoan/form_content.php', 'Sửa Tài khoản: ' . $taiKhoan['ho_ten'], $data);
    }

    // Xử lý cập nhật tài khoản
    public function updateTaiKhoan(){
        $this->checkPermission('quan_tri');
        
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID tài khoản không hợp lệ!';
            header('Location: ?act=admin-list-tai-khoan');
            exit;
        }

        $data = [
            'ho_ten' => $_POST['ho_ten'] ?? '',
            'email' => $_POST['email'] ?? '',
            'so_dien_thoai' => $_POST['so_dien_thoai'] ?? '',
            'dia_chi' => $_POST['dia_chi'] ?? '',
            'trang_thai' => isset($_POST['trang_thai']) ? (int)$_POST['trang_thai'] : 1
        ];

        // Nếu có mật khẩu mới
        if (!empty($_POST['mat_khau'])) {
            $data['mat_khau'] = $_POST['mat_khau'];
        }

        // Validation
        if (empty($data['ho_ten']) || empty($data['email'])) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin!';
            header('Location: ?act=admin-edit-tai-khoan&id=' . $id);
            exit;
        }

        // Kiểm tra email trùng (trừ chính nó)
        $existing = $this->model->getTaiKhoanById($id);
        if ($existing && $existing['email'] != $data['email']) {
            $checkEmail = $this->model->conn->prepare("SELECT COUNT(*) as total FROM nguoi_dung WHERE email = :email AND id != :id");
            $checkEmail->bindValue(':email', $data['email']);
            $checkEmail->bindValue(':id', $id, PDO::PARAM_INT);
            $checkEmail->execute();
            $result = $checkEmail->fetch();
            if ($result['total'] > 0) {
                $_SESSION['error'] = 'Email đã tồn tại!';
                header('Location: ?act=admin-edit-tai-khoan&id=' . $id);
                exit;
            }
        }

        $result = $this->model->updateTaiKhoan($id, $data);
        if ($result) {
            $_SESSION['success'] = 'Cập nhật tài khoản thành công!';
        } else {
            $_SESSION['error'] = 'Cập nhật tài khoản thất bại!';
        }
        header('Location: ?act=admin-list-tai-khoan');
        exit;
    }

    // Toggle trạng thái tài khoản (ban/mở ban)
    public function toggleTaiKhoanStatus(){
        $this->checkPermission('quan_tri');
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID tài khoản không hợp lệ!';
            header('Location: ?act=admin-list-tai-khoan');
            exit;
        }

        // Không cho ban chính mình
        if ($id == $_SESSION['admin_id']) {
            $_SESSION['error'] = 'Bạn không thể ban chính tài khoản của mình!';
            header('Location: ?act=admin-list-tai-khoan');
            exit;
        }

        // Không cho ban admin cuối cùng có quyền quan_tri
        $taiKhoan = $this->model->getTaiKhoanById($id);
        if ($taiKhoan && $this->model->hasVaiTro($id, 'admin') && $this->model->hasPermission($id, 'quan_tri')) {
            $countAdmin = $this->model->countAdminWithQuanTri();
            if ($countAdmin <= 1) {
                $_SESSION['error'] = 'Không thể ban tài khoản admin cuối cùng có quyền quản trị!';
                header('Location: ?act=admin-list-tai-khoan');
                exit;
            }
        }

        $result = $this->model->toggleTaiKhoanStatus($id);
        if ($result) {
            $taiKhoan = $this->model->getTaiKhoanById($id);
            $statusText = $taiKhoan['trang_thai'] == 1 ? 'mở ban' : 'ban';
            $_SESSION['success'] = 'Đã ' . $statusText . ' tài khoản thành công!';
        } else {
            $_SESSION['error'] = 'Thao tác thất bại!';
        }
        header('Location: ?act=admin-list-tai-khoan');
        exit;
    }
}

?>
