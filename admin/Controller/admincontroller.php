<?php
require_once('./admin/Model/adminmodel.php');

class admincontroller{
    public $model;

    public function __construct(){
        $this->model = new adminmodel();
    }

    // Kiểm tra đăng nhập admin
    private function checkAdminLogin(){
        if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_vai_tro']) || $_SESSION['admin_vai_tro'] !== 'admin') {
            header('Location: ?act=client-login');
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
        $this->checkAdminLogin();
        $thongKe = $this->model->getThongKe();
        $dangKyMoiNhat = $this->model->getDangKyMoiNhat(5);
        $thanhToanMoiNhat = $this->model->getThanhToanMoiNhat(5);
        
        require_once('./admin/View/dashboard.php');
    }

    // Danh sách khóa học
    public function listKhoaHoc(){
        $this->checkAdminLogin();
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_danh_muc = $_GET['id_danh_muc'] ?? '';
        
        $khoaHoc = $this->model->getKhoaHoc($page, $limit, $search, $id_danh_muc);
        $total = $this->model->countKhoaHoc($search, $id_danh_muc);
        $totalPages = ceil($total / $limit);
        $danhMuc = $this->model->getDanhMuc();
        
        require_once('./admin/View/khoa_hoc/list.php');
    }

    // Form thêm khóa học
    public function addKhoaHoc(){
        $this->checkAdminLogin();
        $danhMuc = $this->model->getDanhMuc();
        require_once('./admin/View/khoa_hoc/form.php');
    }

    // Xử lý thêm khóa học
    public function saveKhoaHoc(){
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
        require_once('./admin/View/hoc_sinh/form.php');
    }

    // Xử lý thêm học sinh
    public function saveHocSinh(){
        $this->checkAdminLogin();
        $data = [
            'ho_ten' => $_POST['ho_ten'] ?? '',
            'email' => $_POST['email'] ?? '',
            'mat_khau' => $_POST['mat_khau'] ?? '',
            'sdt' => $_POST['sdt'] ?? '',
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
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
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
            'sdt' => $_POST['sdt'] ?? '',
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
        $this->checkAdminLogin();
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
}

?>
