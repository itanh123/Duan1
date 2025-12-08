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
        
        // Nếu đang dùng client session, chuyển sang admin session
        if (isset($_SESSION['client_id']) && !isset($_SESSION['admin_id'])) {
            $_SESSION['admin_id'] = $_SESSION['client_id'];
            $_SESSION['admin_email'] = $_SESSION['client_email'];
            $_SESSION['admin_ho_ten'] = $_SESSION['client_ho_ten'];
            $_SESSION['admin_vai_tro'] = 'admin';
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

    // Xử lý đăng nhập admin - redirect về unified login
    public function processLogin(){
        // Redirect về form đăng nhập chung
        header('Location: ?act=client-login');
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
        $yeuCauDoiLichMoiNhat = $this->model->getYeuCauDoiLich(1, 5, 'cho_duyet');
        
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
        $this->checkAdminLogin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_danh_muc = $_GET['id_danh_muc'] ?? '';
        
        $total = $this->model->countKhoaHoc($search, $id_danh_muc);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        
        $khoaHoc = $this->model->getKhoaHoc($page, $limit, $search, $id_danh_muc);
        $danhMuc = $this->model->getDanhMuc();
        
        $data = [
            'khoaHoc' => $khoaHoc,
            'danhMuc' => $danhMuc,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'id_danh_muc' => $id_danh_muc
        ];
        
        ob_start();
        extract($data);
        require_once('./admin/View/khoa_hoc/list_content.php');
        $content = ob_get_clean();
        
        $pageTitle = 'Quản lý Khóa Học';
        require_once('./admin/View/layout.php');
        exit;
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
            'ten_khoa_hoc' => trim($_POST['ten_khoa_hoc'] ?? ''),
            'mo_ta' => trim($_POST['mo_ta'] ?? ''),
            'gia' => !empty($_POST['gia']) ? (float)$_POST['gia'] : 0,
            'hinh_anh' => $this->uploadImage() ?? null,
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['id_danh_muc'])) {
            $errors[] = 'Vui lòng chọn danh mục!';
        }
        
        if (empty($data['ten_khoa_hoc'])) {
            $errors[] = 'Vui lòng nhập tên khóa học!';
        } elseif (strlen($data['ten_khoa_hoc']) > 200) {
            $errors[] = 'Tên khóa học không được vượt quá 200 ký tự!';
        }
        
        if ($data['gia'] < 0) {
            $errors[] = 'Giá khóa học phải lớn hơn hoặc bằng 0!';
        }
        
        if (empty($data['hinh_anh'])) {
            $errors[] = 'Vui lòng chọn hình ảnh cho khóa học!';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
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
            'ten_khoa_hoc' => trim($_POST['ten_khoa_hoc'] ?? ''),
            'mo_ta' => trim($_POST['mo_ta'] ?? ''),
            'gia' => !empty($_POST['gia']) ? (float)$_POST['gia'] : 0,
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
        
        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['id_danh_muc'])) {
            $errors[] = 'Vui lòng chọn danh mục!';
        }
        
        if (empty($data['ten_khoa_hoc'])) {
            $errors[] = 'Vui lòng nhập tên khóa học!';
        } elseif (strlen($data['ten_khoa_hoc']) > 200) {
            $errors[] = 'Tên khóa học không được vượt quá 200 ký tự!';
        }
        
        if ($data['gia'] < 0) {
            $errors[] = 'Giá khóa học phải lớn hơn hoặc bằng 0!';
        }
        
        if (empty($data['hinh_anh'])) {
            $errors[] = 'Vui lòng chọn hình ảnh cho khóa học!';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
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

    // Ẩn khóa học (thay vì xóa)
    public function deleteKhoaHoc(){
        $this->checkAdminLogin();
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-khoa-hoc');
            exit;
        }

        // Không xóa file hình ảnh nữa, chỉ ẩn khóa học
        if ($this->model->deleteKhoaHoc($id)) {
            $_SESSION['success'] = 'Ẩn khóa học thành công!';
        } else {
            $_SESSION['error'] = 'Không thể ẩn khóa học!';
        }
        header('Location: ?act=admin-list-khoa-hoc');
        exit;
    }
    
    // Toggle trạng thái khóa học (ẩn/hiện)
    public function toggleKhoaHocStatus(){
        $this->checkAdminLogin();
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-khoa-hoc');
            exit;
        }
        
        // Lấy thông tin khóa học để biết trạng thái hiện tại
        $khoaHoc = $this->model->getKhoaHocById($id);
        if (!$khoaHoc) {
            $_SESSION['error'] = 'Không tìm thấy khóa học!';
            header('Location: ?act=admin-list-khoa-hoc');
            exit;
        }

        $result = $this->model->toggleKhoaHocStatus($id);
        if ($result) {
            $newStatus = $khoaHoc['trang_thai'] == 1 ? 'ẩn' : 'hiện';
            $_SESSION['success'] = ucfirst($newStatus) . ' khóa học thành công!';
        } else {
            $_SESSION['error'] = 'Không thể thay đổi trạng thái khóa học!';
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
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        
        $total = $this->model->countHocSinh($search);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        
        $hocSinh = $this->model->getHocSinh($page, $limit, $search);
        
        $data = [
            'hocSinh' => $hocSinh,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search
        ];

        $this->renderView('./admin/View/hoc_sinh/list_content.php', 'Quản lý Học sinh', $data);
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
            'ho_ten' => trim($_POST['ho_ten'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'mat_khau' => $_POST['mat_khau'] ?? '',
            'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
            'dia_chi' => trim($_POST['dia_chi'] ?? ''),
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['ho_ten'])) {
            $errors[] = 'Vui lòng nhập họ tên!';
        } elseif (strlen($data['ho_ten']) < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự!';
        } elseif (strlen($data['ho_ten']) > 200) {
            $errors[] = 'Họ tên không được vượt quá 200 ký tự!';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Vui lòng nhập email!';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ!';
        } elseif (strlen($data['email']) > 200) {
            $errors[] = 'Email không được vượt quá 200 ký tự!';
        } elseif ($this->model->checkEmailExists($data['email'])) {
            $errors[] = 'Email đã tồn tại trong hệ thống!';
        }
        
        if (empty($data['mat_khau'])) {
            $errors[] = 'Vui lòng nhập mật khẩu!';
        } elseif (strlen($data['mat_khau']) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        }
        
        if (!empty($data['so_dien_thoai'])) {
            // Validate số điện thoại: 0xxxxxxxxx hoặc +84xxxxxxxxx
            $phonePattern = '/^(0|\+84)[0-9]{9,10}$/';
            $cleanPhone = preg_replace('/[\s\-]/', '', $data['so_dien_thoai']);
            if (!preg_match($phonePattern, $cleanPhone)) {
                $errors[] = 'Số điện thoại không hợp lệ! (Định dạng: 0xxxxxxxxx hoặc +84xxxxxxxxx)';
            } elseif (strlen($data['so_dien_thoai']) > 20) {
                $errors[] = 'Số điện thoại không được vượt quá 20 ký tự!';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
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
            'ho_ten' => trim($_POST['ho_ten'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
            'dia_chi' => trim($_POST['dia_chi'] ?? ''),
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Nếu có mật khẩu mới
        if (!empty($_POST['mat_khau'])) {
            $data['mat_khau'] = $_POST['mat_khau'];
        }

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['ho_ten'])) {
            $errors[] = 'Vui lòng nhập họ tên!';
        } elseif (strlen($data['ho_ten']) < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự!';
        } elseif (strlen($data['ho_ten']) > 200) {
            $errors[] = 'Họ tên không được vượt quá 200 ký tự!';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Vui lòng nhập email!';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ!';
        } elseif (strlen($data['email']) > 200) {
            $errors[] = 'Email không được vượt quá 200 ký tự!';
        } elseif ($this->model->checkEmailExists($data['email'], $id)) {
            $errors[] = 'Email đã tồn tại trong hệ thống!';
        }
        
        if (!empty($data['mat_khau']) && strlen($data['mat_khau']) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        }
        
        if (!empty($data['so_dien_thoai'])) {
            // Validate số điện thoại: 0xxxxxxxxx hoặc +84xxxxxxxxx
            $phonePattern = '/^(0|\+84)[0-9]{9,10}$/';
            $cleanPhone = preg_replace('/[\s\-]/', '', $data['so_dien_thoai']);
            if (!preg_match($phonePattern, $cleanPhone)) {
                $errors[] = 'Số điện thoại không hợp lệ! (Định dạng: 0xxxxxxxxx hoặc +84xxxxxxxxx)';
            } elseif (strlen($data['so_dien_thoai']) > 20) {
                $errors[] = 'Số điện thoại không được vượt quá 20 ký tự!';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
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
            $_SESSION['success'] = 'Ẩn học sinh thành công!';
        } else {
            $_SESSION['error'] = 'Không thể ẩn học sinh!';
        }
        header('Location: ?act=admin-list-hoc-sinh');
        exit;
    }
    
    // Toggle trạng thái học sinh (ẩn/hiện)
    public function toggleHocSinhStatus(){
        $this->checkAdminLogin();
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-hoc-sinh');
            exit;
        }
        
        // Lấy thông tin học sinh để biết trạng thái hiện tại
        $hocSinh = $this->model->getHocSinhById($id);
        if (!$hocSinh) {
            $_SESSION['error'] = 'Không tìm thấy học sinh!';
            header('Location: ?act=admin-list-hoc-sinh');
            exit;
        }

        $result = $this->model->toggleHocSinhStatus($id);
        if ($result) {
            $newStatus = $hocSinh['trang_thai'] == 1 ? 'ẩn' : 'hiện';
            $_SESSION['success'] = ucfirst($newStatus) . ' học sinh thành công!';
        } else {
            $_SESSION['error'] = 'Không thể thay đổi trạng thái học sinh!';
        }
        header('Location: ?act=admin-list-hoc-sinh');
        exit;
    }

    // Xem lớp học của học sinh
    public function viewLopHocHocSinh(){
        $this->checkAdminLogin();
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-hoc-sinh');
            exit;
        }
        
        $hocSinh = $this->model->getHocSinhById($id);
        if (!$hocSinh) {
            $_SESSION['error'] = 'Không tìm thấy học sinh!';
            header('Location: ?act=admin-list-hoc-sinh');
            exit;
        }
        
        $lopHocs = $this->model->getLopHocDetailByHocSinh($id);
        
        $data = [
            'hocSinh' => $hocSinh,
            'lopHocs' => $lopHocs
        ];
        
        $this->renderView('./admin/View/hoc_sinh/lop_hoc_detail.php', 'Lớp học của ' . htmlspecialchars($hocSinh['ho_ten']), $data);
    }


    // ===========================================
    //  QUẢN LÝ DANH MỤC
    // ===========================================

    // Danh sách danh mục
    public function listDanhMuc(){
        $this->checkAdminLogin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        
        $total = $this->model->countDanhMuc($search);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        
        $danhMuc = $this->model->getDanhMucList($page, $limit, $search);
        
        $data = [
            'danhMuc' => $danhMuc,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search
        ];
        
        ob_start();
        extract($data);
        require_once('./admin/View/danh_muc/list_content.php');
        $content = ob_get_clean();
        
        $pageTitle = 'Quản lý Danh Mục';
        require_once('./admin/View/layout.php');
        exit;
    }

    // Form thêm danh mục
    public function addDanhMuc(){
        $this->checkAdminLogin();
        require_once('./admin/View/danh_muc/form.php');
    }

    // Xử lý thêm danh mục
    public function saveDanhMuc(){
        $this->checkAdminLogin();
        $data = [
            'ten_danh_muc' => trim($_POST['ten_danh_muc'] ?? ''),
            'mo_ta' => trim($_POST['mo_ta'] ?? ''),
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['ten_danh_muc'])) {
            $errors[] = 'Vui lòng nhập tên danh mục!';
        } elseif (strlen($data['ten_danh_muc']) < 2) {
            $errors[] = 'Tên danh mục phải có ít nhất 2 ký tự!';
        } elseif (strlen($data['ten_danh_muc']) > 200) {
            $errors[] = 'Tên danh mục không được vượt quá 200 ký tự!';
        } elseif ($this->model->checkDanhMucExists($data['ten_danh_muc'])) {
            $errors[] = 'Tên danh mục đã tồn tại trong hệ thống!';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
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
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
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
            'ten_danh_muc' => trim($_POST['ten_danh_muc'] ?? ''),
            'mo_ta' => trim($_POST['mo_ta'] ?? ''),
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['ten_danh_muc'])) {
            $errors[] = 'Vui lòng nhập tên danh mục!';
        } elseif (strlen($data['ten_danh_muc']) < 2) {
            $errors[] = 'Tên danh mục phải có ít nhất 2 ký tự!';
        } elseif (strlen($data['ten_danh_muc']) > 200) {
            $errors[] = 'Tên danh mục không được vượt quá 200 ký tự!';
        } elseif ($this->model->checkDanhMucExists($data['ten_danh_muc'], $id)) {
            $errors[] = 'Tên danh mục đã tồn tại trong hệ thống!';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
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
        $this->checkAdminLogin();
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-danh-muc');
            exit;
        }

        $result = $this->model->deleteDanhMuc($id);
        if ($result) {
            $_SESSION['success'] = 'Ẩn danh mục thành công!';
        } else {
            $_SESSION['error'] = 'Không thể ẩn danh mục!';
        }
        header('Location: ?act=admin-list-danh-muc');
        exit;
    }
    
    // Toggle trạng thái danh mục (ẩn/hiện)
    public function toggleDanhMucStatus(){
        $this->checkAdminLogin();
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-danh-muc');
            exit;
        }
        
        // Lấy thông tin danh mục để biết trạng thái hiện tại
        $danhMuc = $this->model->getDanhMucById($id);
        if (!$danhMuc) {
            $_SESSION['error'] = 'Không tìm thấy danh mục!';
            header('Location: ?act=admin-list-danh-muc');
            exit;
        }

        $result = $this->model->toggleDanhMucStatus($id);
        if ($result) {
            $newStatus = $danhMuc['trang_thai'] == 1 ? 'ẩn' : 'hiện';
            $_SESSION['success'] = ucfirst($newStatus) . ' danh mục thành công!';
        } else {
            $_SESSION['error'] = 'Không thể thay đổi trạng thái danh mục!';
        }
        header('Location: ?act=admin-list-danh-muc');
        exit;
    }

    // ===========================================
    //  QUẢN LÝ GIẢNG VIÊN
    // ===========================================

    // Danh sách giảng viên
    public function listGiangVien(){
        $this->checkAdminLogin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        
        $total = $this->model->countGiangVien($search);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        
        $giangVien = $this->model->getGiangVien($page, $limit, $search);
        
        $data = [
            'giangVien' => $giangVien,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search
        ];

        $this->renderView('./admin/View/giang_vien/list_content.php', 'Quản lý Giảng viên', $data);
    }

    // Form thêm giảng viên
    public function addGiangVien(){
        $this->checkAdminLogin();
        require_once('./admin/View/giang_vien/form.php');
    }

    // Xử lý thêm giảng viên
    public function saveGiangVien(){
        $this->checkAdminLogin();
        $data = [
            'ho_ten' => trim($_POST['ho_ten'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'mat_khau' => $_POST['mat_khau'] ?? '',
            'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
            'dia_chi' => trim($_POST['dia_chi'] ?? ''),
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['ho_ten'])) {
            $errors[] = 'Vui lòng nhập họ tên!';
        } elseif (strlen($data['ho_ten']) < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự!';
        } elseif (strlen($data['ho_ten']) > 200) {
            $errors[] = 'Họ tên không được vượt quá 200 ký tự!';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Vui lòng nhập email!';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ!';
        } elseif (strlen($data['email']) > 200) {
            $errors[] = 'Email không được vượt quá 200 ký tự!';
        } elseif ($this->model->checkEmailExists($data['email'])) {
            $errors[] = 'Email đã tồn tại trong hệ thống!';
        }
        
        if (empty($data['mat_khau'])) {
            $errors[] = 'Vui lòng nhập mật khẩu!';
        } elseif (strlen($data['mat_khau']) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        }
        
        if (!empty($data['so_dien_thoai'])) {
            // Validate số điện thoại: 0xxxxxxxxx hoặc +84xxxxxxxxx
            $phonePattern = '/^(0|\+84)[0-9]{9,10}$/';
            $cleanPhone = preg_replace('/[\s\-]/', '', $data['so_dien_thoai']);
            if (!preg_match($phonePattern, $cleanPhone)) {
                $errors[] = 'Số điện thoại không hợp lệ! (Định dạng: 0xxxxxxxxx hoặc +84xxxxxxxxx)';
            } elseif (strlen($data['so_dien_thoai']) > 20) {
                $errors[] = 'Số điện thoại không được vượt quá 20 ký tự!';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
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
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
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
            'ho_ten' => trim($_POST['ho_ten'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
            'dia_chi' => trim($_POST['dia_chi'] ?? ''),
            'trang_thai' => $_POST['trang_thai'] ?? 1
        ];

        // Nếu có mật khẩu mới
        if (!empty($_POST['mat_khau'])) {
            $data['mat_khau'] = $_POST['mat_khau'];
        }

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['ho_ten'])) {
            $errors[] = 'Vui lòng nhập họ tên!';
        } elseif (strlen($data['ho_ten']) < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự!';
        } elseif (strlen($data['ho_ten']) > 200) {
            $errors[] = 'Họ tên không được vượt quá 200 ký tự!';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Vui lòng nhập email!';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ!';
        } elseif (strlen($data['email']) > 200) {
            $errors[] = 'Email không được vượt quá 200 ký tự!';
        } elseif ($this->model->checkEmailExists($data['email'], $id)) {
            $errors[] = 'Email đã tồn tại trong hệ thống!';
        }
        
        if (!empty($data['mat_khau']) && strlen($data['mat_khau']) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        }
        
        if (!empty($data['so_dien_thoai'])) {
            // Validate số điện thoại: 0xxxxxxxxx hoặc +84xxxxxxxxx
            $phonePattern = '/^(0|\+84)[0-9]{9,10}$/';
            $cleanPhone = preg_replace('/[\s\-]/', '', $data['so_dien_thoai']);
            if (!preg_match($phonePattern, $cleanPhone)) {
                $errors[] = 'Số điện thoại không hợp lệ! (Định dạng: 0xxxxxxxxx hoặc +84xxxxxxxxx)';
            } elseif (strlen($data['so_dien_thoai']) > 20) {
                $errors[] = 'Số điện thoại không được vượt quá 20 ký tự!';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
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
        $this->checkAdminLogin();
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

    // Xem lớp học của giảng viên
    public function viewLopHocGiangVien(){
        $this->checkAdminLogin();
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID không hợp lệ!';
            header('Location: ?act=admin-list-giang-vien');
            exit;
        }
        
        $giangVien = $this->model->getGiangVienById($id);
        if (!$giangVien) {
            $_SESSION['error'] = 'Không tìm thấy giảng viên!';
            header('Location: ?act=admin-list-giang-vien');
            exit;
        }
        
        $lopHocs = $this->model->getLopHocDetailByGiangVien($id);
        
        $data = [
            'giangVien' => $giangVien,
            'lopHocs' => $lopHocs
        ];
        
        $this->renderView('./admin/View/giang_vien/lop_hoc_detail.php', 'Lớp học của ' . htmlspecialchars($giangVien['ho_ten']), $data);
    }

    // ===========================================
    //  QUẢN LÝ LỚP HỌC
    // ===========================================

    // Danh sách lớp học
    public function listLopHoc(){
        $this->checkAdminLogin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_khoa_hoc = $_GET['id_khoa_hoc'] ?? '';
        
        $total = $this->model->countLopHoc($search, $id_khoa_hoc);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        
        $lopHoc = $this->model->getLopHoc($page, $limit, $search, $id_khoa_hoc);
        $khoaHocList = $this->model->getKhoaHoc(1, 1000, '', ''); // Lấy tất cả khóa học để filter
        
        $data = [
            'lopHoc' => $lopHoc,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'id_khoa_hoc' => $id_khoa_hoc,
            'khoaHocList' => $khoaHocList
        ];

        $this->renderView('./admin/View/lop_hoc/list_content.php', 'Quản lý Lớp học', $data);
    }

    // Xử lý thêm lớp học
    public function saveLopHoc(){
        $this->checkAdminLogin();
        $trang_thai = $_POST['trang_thai'] ?? 'Chưa khai giảng';
        // Đảm bảo trang_thai là một trong các giá trị ENUM hợp lệ
        $validTrangThai = ['Chưa khai giảng', 'Đang học', 'Kết thúc'];
        if (!in_array($trang_thai, $validTrangThai)) {
            $trang_thai = 'Chưa khai giảng'; // Mặc định
        }
        
        $id_phong_hoc = isset($_POST['id_phong_hoc']) ? (int)$_POST['id_phong_hoc'] : 0;
        $so_luong_toi_da = !empty($_POST['so_luong_toi_da']) ? (int)$_POST['so_luong_toi_da'] : null;
        
        // Validation: Phải chọn phòng học
        if (!$id_phong_hoc) {
            $_SESSION['error'] = 'Vui lòng chọn phòng học!';
            header('Location: ?act=admin-add-lop-hoc');
            exit;
        }
        
        // Lấy thông tin phòng học để kiểm tra sức chứa
        $phongHoc = $this->model->getPhongHocById($id_phong_hoc);
        if (!$phongHoc) {
            $_SESSION['error'] = 'Phòng học không tồn tại!';
            header('Location: ?act=admin-add-lop-hoc');
            exit;
        }
        
        // Validation: Số lượng tối đa không được vượt quá sức chứa phòng học
        if ($so_luong_toi_da && $so_luong_toi_da > $phongHoc['suc_chua']) {
            $_SESSION['error'] = "Số lượng tối đa ({$so_luong_toi_da}) không được vượt quá sức chứa phòng học ({$phongHoc['suc_chua']})!";
            header('Location: ?act=admin-add-lop-hoc');
            exit;
        }
        
        $data = [
            'id_khoa_hoc' => $_POST['id_khoa_hoc'] ?? '',
            'ten_lop' => trim($_POST['ten_lop'] ?? ''),
            'mo_ta' => trim($_POST['mo_ta'] ?? ''),
            'so_luong_toi_da' => $so_luong_toi_da,
            'trang_thai' => $trang_thai
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['id_khoa_hoc'])) {
            $errors[] = 'Vui lòng chọn khóa học!';
        }
        
        if (empty($data['ten_lop'])) {
            $errors[] = 'Vui lòng nhập tên lớp học!';
        } elseif (strlen($data['ten_lop']) > 200) {
            $errors[] = 'Tên lớp học không được vượt quá 200 ký tự!';
        }
        
        if ($so_luong_toi_da !== null && $so_luong_toi_da <= 0) {
            $errors[] = 'Số lượng tối đa phải lớn hơn 0!';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
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
        $this->checkAdminLogin();
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
        $soLuongDangKy = $this->model->countDangKyByLop($id); // Đếm số lượng đăng ký hiện tại
        $phongHocInfo = $this->model->getSucChuaPhongHocNhoNhatByLop($id); // Lấy thông tin sức chứa phòng học
        $phongHocList = $this->model->getPhongHocList(); // Lấy danh sách phòng học
        
        $data = [
            'lopHoc' => $lopHoc,
            'khoaHocList' => $khoaHocList,
            'soLuongDangKy' => $soLuongDangKy,
            'phongHocInfo' => $phongHocInfo,
            'phongHocList' => $phongHocList
        ];

        $this->renderView('./admin/View/lop_hoc/form_content.php', 'Sửa Lớp học', $data);
    }
    
    public function addLopHoc(){
        $this->checkAdminLogin();
        $khoaHocList = $this->model->getKhoaHoc(1, 1000, '', ''); // Lấy tất cả khóa học
        $phongHocList = $this->model->getPhongHocList(); // Lấy danh sách phòng học
        
        $data = [
            'khoaHocList' => $khoaHocList,
            'phongHocList' => $phongHocList
        ];

        $this->renderView('./admin/View/lop_hoc/form_content.php', 'Thêm Lớp học', $data);
    }

    // Xử lý cập nhật lớp học
    public function updateLopHoc(){
        $this->checkAdminLogin();
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
            'ten_lop' => trim($_POST['ten_lop'] ?? ''),
            'mo_ta' => trim($_POST['mo_ta'] ?? ''),
            'so_luong_toi_da' => !empty($_POST['so_luong_toi_da']) ? (int)$_POST['so_luong_toi_da'] : null,
            'trang_thai' => $trang_thai
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['id_khoa_hoc'])) {
            $errors[] = 'Vui lòng chọn khóa học!';
        }
        
        if (empty($data['ten_lop'])) {
            $errors[] = 'Vui lòng nhập tên lớp học!';
        } elseif (strlen($data['ten_lop']) > 200) {
            $errors[] = 'Tên lớp học không được vượt quá 200 ký tự!';
        }
        
        if ($data['so_luong_toi_da'] !== null && $data['so_luong_toi_da'] <= 0) {
            $errors[] = 'Số lượng tối đa phải lớn hơn 0!';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header('Location: ?act=admin-edit-lop-hoc&id=' . $id);
            exit;
        }

        // Validation: Phải chọn phòng học
        $id_phong_hoc = isset($_POST['id_phong_hoc']) ? (int)$_POST['id_phong_hoc'] : 0;
        if (!$id_phong_hoc) {
            $_SESSION['error'] = 'Vui lòng chọn phòng học!';
            header('Location: ?act=admin-edit-lop-hoc&id=' . $id);
            exit;
        }
        
        // Lấy thông tin phòng học để kiểm tra sức chứa
        $phongHoc = $this->model->getPhongHocById($id_phong_hoc);
        if (!$phongHoc) {
            $_SESSION['error'] = 'Phòng học không tồn tại!';
            header('Location: ?act=admin-edit-lop-hoc&id=' . $id);
            exit;
        }
        
        // Kiểm tra số lượng tối đa không được nhỏ hơn số lượng đăng ký hiện tại
        $soLuongDangKy = $this->model->countDangKyByLop($id);
        $soLuongToiDaCu = $lopHoc['so_luong_toi_da'] ?? null;
        
        if (!empty($data['so_luong_toi_da'])) {
            // Kiểm tra 1: Số lượng tối đa không được nhỏ hơn số lượng đăng ký hiện tại
            if ($data['so_luong_toi_da'] < $soLuongDangKy) {
                $_SESSION['error'] = "Không thể đặt số lượng tối đa là {$data['so_luong_toi_da']}! Lớp học này hiện có {$soLuongDangKy} học sinh đã đăng ký (đã xác nhận). Số lượng tối đa phải >= {$soLuongDangKy}.";
                header('Location: ?act=admin-edit-lop-hoc&id=' . $id);
                exit;
            }
            
            // Kiểm tra 2: Số lượng tối đa không được lớn hơn sức chứa của phòng học đã chọn
            if ($data['so_luong_toi_da'] > $phongHoc['suc_chua']) {
                $_SESSION['error'] = "Số lượng tối đa ({$data['so_luong_toi_da']}) không được vượt quá sức chứa phòng học ({$phongHoc['suc_chua']})! Phòng: {$phongHoc['ten_phong']}.";
                header('Location: ?act=admin-edit-lop-hoc&id=' . $id);
                exit;
            }
        }

        if ($this->model->updateLopHoc($id, $data)) {
            // Tạo thông báo chi tiết
            $message = 'Cập nhật lớp học thành công!';
            
            // Thông báo về số lượng nếu có thay đổi
            if ($soLuongToiDaCu !== $data['so_luong_toi_da']) {
                if ($soLuongToiDaCu === null && !empty($data['so_luong_toi_da'])) {
                    // Từ không giới hạn -> có giới hạn
                    $message .= " Đã đặt số lượng tối đa: {$data['so_luong_toi_da']} học sinh. Hiện có {$soLuongDangKy} học sinh đã đăng ký (đã xác nhận).";
                } elseif (!empty($soLuongToiDaCu) && empty($data['so_luong_toi_da'])) {
                    // Từ có giới hạn -> không giới hạn
                    $message .= " Đã bỏ giới hạn số lượng. Hiện có {$soLuongDangKy} học sinh đã đăng ký (đã xác nhận).";
                } elseif (!empty($soLuongToiDaCu) && !empty($data['so_luong_toi_da'])) {
                    // Thay đổi số lượng
                    if ($data['so_luong_toi_da'] > $soLuongToiDaCu) {
                        $tang = $data['so_luong_toi_da'] - $soLuongToiDaCu;
                        $conLai = $data['so_luong_toi_da'] - $soLuongDangKy;
                        $message .= " Đã tăng số lượng tối đa từ {$soLuongToiDaCu} lên {$data['so_luong_toi_da']} (+{$tang}). Hiện có {$soLuongDangKy} học sinh đã đăng ký, còn lại {$conLai} chỗ trống.";
                    } elseif ($data['so_luong_toi_da'] < $soLuongToiDaCu) {
                        $giam = $soLuongToiDaCu - $data['so_luong_toi_da'];
                        $conLai = $data['so_luong_toi_da'] - $soLuongDangKy;
                        $message .= " Đã giảm số lượng tối đa từ {$soLuongToiDaCu} xuống {$data['so_luong_toi_da']} (-{$giam}). Hiện có {$soLuongDangKy} học sinh đã đăng ký, còn lại {$conLai} chỗ trống.";
                    }
                }
            } else {
                // Không thay đổi số lượng nhưng vẫn hiển thị thông tin
                if (!empty($data['so_luong_toi_da'])) {
                    $conLai = $data['so_luong_toi_da'] - $soLuongDangKy;
                    $message .= " Số lượng tối đa: {$data['so_luong_toi_da']}. Hiện có {$soLuongDangKy} học sinh đã đăng ký, còn lại {$conLai} chỗ trống.";
                } else {
                    $message .= " Hiện có {$soLuongDangKy} học sinh đã đăng ký (đã xác nhận).";
                }
            }
            
            $_SESSION['success'] = $message;
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
        $this->checkAdminLogin();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_lop = $_GET['id_lop'] ?? '';
        
        $total = $this->model->countCaHoc($search, $id_lop);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        
        $caHoc = $this->model->getCaHoc($page, $limit, $search, $id_lop);
        $lopHocList = $this->model->getLopHocList(); // Lấy danh sách lớp học để filter
        
        $data = [
            'caHoc' => $caHoc,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'id_lop' => $id_lop,
            'lopHocList' => $lopHocList
        ];

        $this->renderView('./admin/View/ca_hoc/list_content.php', 'Quản lý Ca học', $data);
    }

    // Form thêm ca học
    public function addCaHoc(){
        $this->checkAdminLogin();
        $lopHocList = $this->model->getLopHocList(); // Lấy danh sách lớp học
        $giangVienList = $this->model->getGiangVienList(); // Lấy danh sách giảng viên
        $caMacDinhList = $this->model->getCaMacDinhList(); // Lấy danh sách ca mặc định
        $phongHocList = $this->model->getPhongHocList(); // Lấy danh sách phòng học
        
        // Lấy dữ liệu form từ session nếu có (khi có lỗi validation)
        $formData = $_SESSION['form_data'] ?? null;
        $errorField = $_SESSION['error_field'] ?? null;
        
        // Xóa session sau khi lấy
        unset($_SESSION['form_data']);
        unset($_SESSION['error_field']);
        
        $data = [
            'lopHocList' => $lopHocList,
            'giangVienList' => $giangVienList,
            'caMacDinhList' => $caMacDinhList,
            'phongHocList' => $phongHocList,
            'formData' => $formData,
            'errorField' => $errorField
        ];

        $this->renderView('./admin/View/ca_hoc/form_content.php', 'Thêm Ca học', $data);
    }

    // Xử lý thêm ca học
    public function saveCaHoc(){
        $this->checkAdminLogin();
        $id_giang_vien = $_POST['id_giang_vien'] ?? '';
        $ngay_hoc_raw = trim($_POST['ngay_hoc'] ?? '');
        
        // Chuẩn hóa ngày học: nếu rỗng thì chuyển thành null
        $ngay_hoc = !empty($ngay_hoc_raw) ? $ngay_hoc_raw : null;
        
        $data = [
            'id_lop' => $_POST['id_lop'] ?? '',
            'id_giang_vien' => !empty($id_giang_vien) ? (int)$id_giang_vien : null,
            'id_ca' => $_POST['id_ca'] ?? '',
            'thu_trong_tuan' => trim($_POST['thu_trong_tuan'] ?? ''),
            'id_phong' => $_POST['id_phong'] ?? '',
            'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
            'ngay_hoc' => $ngay_hoc
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['id_lop'])) {
            $errors[] = 'Vui lòng chọn lớp học!';
        } else {
            $data['id_lop'] = (int)$data['id_lop'];
            if ($data['id_lop'] <= 0) {
                $errors[] = 'ID lớp học không hợp lệ!';
            }
        }
        
        if (empty($data['id_ca'])) {
            $errors[] = 'Vui lòng chọn ca học!';
        } else {
            $data['id_ca'] = (int)$data['id_ca'];
            if ($data['id_ca'] <= 0) {
                $errors[] = 'ID ca học không hợp lệ!';
            }
        }
        
        // Validate id_giang_vien nếu có
        if ($data['id_giang_vien'] !== null && $data['id_giang_vien'] <= 0) {
            $errors[] = 'ID giảng viên không hợp lệ!';
        }
        
        // Chuẩn hóa thu_trong_tuan: nếu rỗng thì chuyển thành null
        if (empty($data['thu_trong_tuan'])) {
            $data['thu_trong_tuan'] = null;
        }
        
        if (empty($data['thu_trong_tuan']) && empty($data['ngay_hoc'])) {
            $errors[] = 'Vui lòng chọn thứ trong tuần hoặc ngày học!';
        }
        
        // Kiểm tra giá trị ENUM hợp lệ cho thứ trong tuần
        if (!empty($data['thu_trong_tuan'])) {
            $validThu = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
            if (!in_array($data['thu_trong_tuan'], $validThu)) {
                $errors[] = 'Thứ trong tuần không hợp lệ!';
            }
        }
        
        // Validate ngày học format nếu có
        if (!empty($data['ngay_hoc'])) {
            // Kiểm tra format YYYY-MM-DD
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['ngay_hoc'])) {
                $errors[] = 'Ngày học phải có định dạng YYYY-MM-DD (ví dụ: 2024-01-15)!';
            } else {
                $dateParts = explode('-', $data['ngay_hoc']);
                if (count($dateParts) != 3) {
                    $errors[] = 'Ngày học không hợp lệ!';
                } else {
                    $year = (int)$dateParts[0];
                    $month = (int)$dateParts[1];
                    $day = (int)$dateParts[2];
                    
                    if (!checkdate($month, $day, $year)) {
                        $errors[] = 'Ngày học không hợp lệ! (Ngày/tháng/năm không tồn tại)';
                    } else {
                        // Kiểm tra ngày không được là quá khứ xa hoặc quá tương lai
                        $dateObj = DateTime::createFromFormat('Y-m-d', $data['ngay_hoc']);
                        if (!$dateObj) {
                            $errors[] = 'Ngày học không hợp lệ!';
                        }
                    }
                }
            }
        }
        
        // Chuẩn hóa id_phong: nếu rỗng thì chuyển thành null
        if (empty($data['id_phong'])) {
            $data['id_phong'] = null;
        } else {
            $data['id_phong'] = (int)$data['id_phong'];
            if ($data['id_phong'] <= 0) {
                $errors[] = 'ID phòng học không hợp lệ!';
            }
        }
        
        if (strlen($data['ghi_chu']) > 255) {
            $errors[] = 'Ghi chú không được vượt quá 255 ký tự!';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            $_SESSION['form_data'] = $data;
            header('Location: ?act=admin-add-ca-hoc');
            exit;
        }

        // Kiểm tra trùng ca học (cùng ca, cùng thứ/ngày thì phải khác giảng viên và khác phòng)
        $checkTrung = $this->model->checkTrungCaHoc(
            $data['id_ca'], 
            $data['thu_trong_tuan'], 
            $data['id_giang_vien'] ?? null, 
            $data['id_phong'] ?? null,
            null,
            $data['ngay_hoc'] ?? null
        );
        
        if ($checkTrung['trung']) {
            // Lưu lại dữ liệu đã nhập để giữ lại form
            $_SESSION['form_data'] = $data;
            
            if ($checkTrung['loi'] == 'giang_vien') {
                $lopTrung = $checkTrung['thong_tin']['ten_lop'] ?? 'N/A';
                $giangVienTrung = $checkTrung['thong_tin']['ten_giang_vien'] ?? 'N/A';
                $thoiGian = !empty($data['ngay_hoc']) ? date('d/m/Y', strtotime($data['ngay_hoc'])) : $data['thu_trong_tuan'];
                $_SESSION['error'] = "Giảng viên này đã có lớp khác học cùng ca vào {$thoiGian}! (Lớp: {$lopTrung}, Giảng viên: {$giangVienTrung})";
                $_SESSION['error_field'] = 'id_giang_vien';
            } elseif ($checkTrung['loi'] == 'phong') {
                $lopTrung = $checkTrung['thong_tin']['ten_lop'] ?? 'N/A';
                $phongTrung = $checkTrung['thong_tin']['ten_phong'] ?? 'N/A';
                $thoiGian = !empty($data['ngay_hoc']) ? date('d/m/Y', strtotime($data['ngay_hoc'])) : $data['thu_trong_tuan'];
                $_SESSION['error'] = "Phòng học này đã được sử dụng bởi lớp khác cùng ca vào {$thoiGian}! (Lớp: {$lopTrung}, Phòng: {$phongTrung})";
                $_SESSION['error_field'] = 'id_phong';
            }
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

    // API kiểm tra trùng ca học (AJAX)
    public function checkCaHocTrung(){
        $this->checkAdminLogin();
        header('Content-Type: application/json');
        
        $id_ca = $_GET['id_ca'] ?? '';
        $thu_trong_tuan = $_GET['thu_trong_tuan'] ?? '';
        $ngay_hoc = $_GET['ngay_hoc'] ?? '';
        $exclude_id = $_GET['exclude_id'] ?? null;
        
        if (empty($id_ca) || (empty($thu_trong_tuan) && empty($ngay_hoc))) {
            echo json_encode(['error' => 'Thiếu thông tin']);
            exit;
        }
        
        // Lấy danh sách giảng viên và phòng học bị trùng
        $giangVienTrung = $this->model->getGiangVienTrung($id_ca, $thu_trong_tuan, !empty($ngay_hoc) ? $ngay_hoc : null, $exclude_id);
        $phongHocTrung = $this->model->getPhongHocTrung($id_ca, $thu_trong_tuan, !empty($ngay_hoc) ? $ngay_hoc : null, $exclude_id);
        
        echo json_encode([
            'giang_vien_trung' => $giangVienTrung,
            'phong_hoc_trung' => $phongHocTrung
        ]);
        exit;
    }

    // Form sửa ca học
    public function editCaHoc(){
        $this->checkAdminLogin();
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
        
        // Lấy dữ liệu form từ session nếu có (khi có lỗi validation)
        $formData = $_SESSION['form_data'] ?? null;
        $errorField = $_SESSION['error_field'] ?? null;
        
        // Nếu có formData từ session, ưu tiên dùng nó thay vì caHoc
        if ($formData) {
            $caHoc = array_merge($caHoc, $formData);
        }
        
        // Xóa session sau khi lấy
        unset($_SESSION['form_data']);
        unset($_SESSION['error_field']);
        
        $data = [
            'caHoc' => $caHoc,
            'lopHocList' => $lopHocList,
            'giangVienList' => $giangVienList,
            'caMacDinhList' => $caMacDinhList,
            'phongHocList' => $phongHocList,
            'formData' => $formData,
            'errorField' => $errorField
        ];

        $this->renderView('./admin/View/ca_hoc/form_content.php', 'Sửa Ca học', $data);
    }

    // Xử lý cập nhật ca học
    public function updateCaHoc(){
        $this->checkAdminLogin();
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
        $ngay_hoc_raw = trim($_POST['ngay_hoc'] ?? '');
        
        // Chuẩn hóa ngày học: nếu rỗng thì chuyển thành null
        $ngay_hoc = !empty($ngay_hoc_raw) ? $ngay_hoc_raw : null;
        
        $data = [
            'id_lop' => $_POST['id_lop'] ?? '',
            'id_giang_vien' => !empty($id_giang_vien) ? (int)$id_giang_vien : null,
            'id_ca' => $_POST['id_ca'] ?? '',
            'thu_trong_tuan' => trim($_POST['thu_trong_tuan'] ?? ''),
            'id_phong' => $_POST['id_phong'] ?? '',
            'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
            'ngay_hoc' => $ngay_hoc
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['id_lop'])) {
            $errors[] = 'Vui lòng chọn lớp học!';
        } else {
            $data['id_lop'] = (int)$data['id_lop'];
            if ($data['id_lop'] <= 0) {
                $errors[] = 'ID lớp học không hợp lệ!';
            }
        }
        
        if (empty($data['id_ca'])) {
            $errors[] = 'Vui lòng chọn ca học!';
        } else {
            $data['id_ca'] = (int)$data['id_ca'];
            if ($data['id_ca'] <= 0) {
                $errors[] = 'ID ca học không hợp lệ!';
            }
        }
        
        // Validate id_giang_vien nếu có
        if ($data['id_giang_vien'] !== null && $data['id_giang_vien'] <= 0) {
            $errors[] = 'ID giảng viên không hợp lệ!';
        }
        
        // Chuẩn hóa thu_trong_tuan: nếu rỗng thì chuyển thành null
        if (empty($data['thu_trong_tuan'])) {
            $data['thu_trong_tuan'] = null;
        }
        
        if (empty($data['thu_trong_tuan']) && empty($data['ngay_hoc'])) {
            $errors[] = 'Vui lòng chọn thứ trong tuần hoặc ngày học!';
        }
        
        // Kiểm tra giá trị ENUM hợp lệ cho thứ trong tuần
        if (!empty($data['thu_trong_tuan'])) {
            $validThu = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật'];
            if (!in_array($data['thu_trong_tuan'], $validThu)) {
                $errors[] = 'Thứ trong tuần không hợp lệ!';
            }
        }
        
        // Validate ngày học format nếu có
        if (!empty($data['ngay_hoc'])) {
            // Kiểm tra format YYYY-MM-DD
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['ngay_hoc'])) {
                $errors[] = 'Ngày học phải có định dạng YYYY-MM-DD (ví dụ: 2024-01-15)!';
            } else {
                $dateParts = explode('-', $data['ngay_hoc']);
                if (count($dateParts) != 3) {
                    $errors[] = 'Ngày học không hợp lệ!';
                } else {
                    $year = (int)$dateParts[0];
                    $month = (int)$dateParts[1];
                    $day = (int)$dateParts[2];
                    
                    if (!checkdate($month, $day, $year)) {
                        $errors[] = 'Ngày học không hợp lệ! (Ngày/tháng/năm không tồn tại)';
                    } else {
                        // Kiểm tra ngày không được là quá khứ xa hoặc quá tương lai
                        $dateObj = DateTime::createFromFormat('Y-m-d', $data['ngay_hoc']);
                        if (!$dateObj) {
                            $errors[] = 'Ngày học không hợp lệ!';
                        }
                    }
                }
            }
        }
        
        // Chuẩn hóa id_phong: nếu rỗng thì chuyển thành null
        if (empty($data['id_phong'])) {
            $data['id_phong'] = null;
        } else {
            $data['id_phong'] = (int)$data['id_phong'];
            if ($data['id_phong'] <= 0) {
                $errors[] = 'ID phòng học không hợp lệ!';
            }
        }
        
        if (strlen($data['ghi_chu']) > 255) {
            $errors[] = 'Ghi chú không được vượt quá 255 ký tự!';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            $_SESSION['form_data'] = $data;
            header('Location: ?act=admin-edit-ca-hoc&id=' . $id);
            exit;
        }

        // Kiểm tra trùng ca học (cùng ca, cùng thứ/ngày thì phải khác giảng viên và khác phòng)
        $checkTrung = $this->model->checkTrungCaHoc(
            $data['id_ca'], 
            $data['thu_trong_tuan'], 
            $data['id_giang_vien'] ?? null, 
            $data['id_phong'] ?? null,
            $id, // Loại trừ ca học hiện tại
            $data['ngay_hoc'] ?? null
        );
        
        if ($checkTrung['trung']) {
            // Lưu lại dữ liệu đã nhập để giữ lại form
            $_SESSION['form_data'] = $data;
            
            if ($checkTrung['loi'] == 'giang_vien') {
                $lopTrung = $checkTrung['thong_tin']['ten_lop'] ?? 'N/A';
                $giangVienTrung = $checkTrung['thong_tin']['ten_giang_vien'] ?? 'N/A';
                $thoiGian = !empty($data['ngay_hoc']) ? date('d/m/Y', strtotime($data['ngay_hoc'])) : $data['thu_trong_tuan'];
                $_SESSION['error'] = "Giảng viên này đã có lớp khác học cùng ca vào {$thoiGian}! (Lớp: {$lopTrung}, Giảng viên: {$giangVienTrung})";
                $_SESSION['error_field'] = 'id_giang_vien';
            } elseif ($checkTrung['loi'] == 'phong') {
                $lopTrung = $checkTrung['thong_tin']['ten_lop'] ?? 'N/A';
                $phongTrung = $checkTrung['thong_tin']['ten_phong'] ?? 'N/A';
                $thoiGian = !empty($data['ngay_hoc']) ? date('d/m/Y', strtotime($data['ngay_hoc'])) : $data['thu_trong_tuan'];
                $_SESSION['error'] = "Phòng học này đã được sử dụng bởi lớp khác cùng ca vào {$thoiGian}! (Lớp: {$lopTrung}, Phòng: {$phongTrung})";
                $_SESSION['error_field'] = 'id_phong';
            }
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
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_lop = $_GET['id_lop'] ?? '';
        $trang_thai = $_GET['trang_thai'] ?? '';

        $total = $this->model->countDangKy($search, $id_lop, $trang_thai);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));

        $dangKy = $this->model->getDangKy($page, $limit, $search, $id_lop, $trang_thai);

        $lopHocList = $this->model->getLopHocList(); // Lấy danh sách lớp học để filter
        
        $data = [
            'dangKy' => $dangKy,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'id_lop' => $id_lop,
            'trang_thai' => $trang_thai,
            'lopHocList' => $lopHocList
        ];

        $this->renderView('./admin/View/dang_ky/list_content.php', 'Quản lý Đăng ký', $data);
    }

    // Form sửa đăng ký
    public function editDangKy(){
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
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

    // Hoàn tiền cho đăng ký
    public function hoanTien(){
        $this->checkAdminLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['error'] = 'Phương thức không hợp lệ!';
            header('Location: ?act=admin-list-dang-ky');
            exit;
        }
        
        $id_dang_ky = isset($_POST['id_dang_ky']) ? (int)$_POST['id_dang_ky'] : 0;
        $ly_do = $_POST['ly_do'] ?? 'Hoàn tiền theo yêu cầu';
        
        if (!$id_dang_ky) {
            $_SESSION['error'] = 'ID đăng ký không hợp lệ!';
            header('Location: ?act=admin-list-dang-ky');
            exit;
        }
        
        // Lấy thông tin đăng ký
        $dangKy = $this->model->getDangKyById($id_dang_ky);
        if (!$dangKy) {
            $_SESSION['error'] = 'Không tìm thấy đăng ký!';
            header('Location: ?act=admin-list-dang-ky');
            exit;
        }
        
        // Kiểm tra đăng ký đã được thanh toán chưa
        if ($dangKy['trang_thai'] !== 'Đã xác nhận') {
            $_SESSION['error'] = 'Chỉ có thể hoàn tiền cho đăng ký đã được xác nhận!';
            header('Location: ?act=admin-edit-dang-ky&id=' . $id_dang_ky);
            exit;
        }
        
        // Lấy thông tin thanh toán
        $thanhToan = $this->model->getThanhToanByIdDangKy($id_dang_ky);
        if (!$thanhToan || $thanhToan['phuong_thuc'] !== 'VNPAY') {
            $_SESSION['error'] = 'Không tìm thấy thông tin thanh toán VNPay hoặc đăng ký này không thanh toán qua VNPay!';
            header('Location: ?act=admin-edit-dang-ky&id=' . $id_dang_ky);
            exit;
        }
        
        // Kiểm tra đã hoàn tiền chưa
        $checkHoanTien = $this->model->conn->prepare("
            SELECT * FROM hoan_tien 
            WHERE id_thanh_toan = :id_thanh_toan 
            AND trang_thai IN ('Thành công', 'Đang xử lý')
            LIMIT 1
        ");
        $checkHoanTien->execute([':id_thanh_toan' => $thanhToan['id']]);
        if ($checkHoanTien->fetch()) {
            $_SESSION['error'] = 'Đăng ký này đã được hoàn tiền hoặc đang trong quá trình hoàn tiền!';
            header('Location: ?act=admin-edit-dang-ky&id=' . $id_dang_ky);
            exit;
        }
        
        // Lấy thông tin giao dịch VNPay
        $vnp_TxnRef = $dangKy['vnp_TxnRef'] ?? '';
        $vnp_TransactionNo = $dangKy['vnp_TransactionNo'] ?? $thanhToan['ma_giao_dich'] ?? '';
        
        if (empty($vnp_TxnRef) || empty($vnp_TransactionNo)) {
            $_SESSION['error'] = 'Không tìm thấy thông tin giao dịch VNPay!';
            header('Location: ?act=admin-edit-dang-ky&id=' . $id_dang_ky);
            exit;
        }
        
        // Lấy ngày giao dịch (từ mã đơn hàng hoặc database)
        $vnp_TransactionDate = '';
        if (preg_match('/DK(\d{14})/', $vnp_TxnRef, $matches)) {
            $vnp_TransactionDate = $matches[1];
        } else {
            // Nếu không lấy được từ mã, dùng ngày thanh toán
            $vnp_TransactionDate = date('YmdHis', strtotime($thanhToan['ngay_thanh_toan']));
        }
        
        // Số tiền hoàn (nhân với 100 vì VNPay yêu cầu)
        $vnp_Amount = (int)($thanhToan['so_tien'] * 100);
        
        // Gọi API hoàn tiền VNPay
        require_once('./Commons/vnpay_helper.php');
        $adminName = $_SESSION['admin_ho_ten'] ?? $_SESSION['client_ho_ten'] ?? 'Admin';
        
        $refundResult = VNPayHelper::refundTransaction(
            $vnp_TxnRef,
            $vnp_TransactionNo,
            $vnp_Amount,
            $vnp_TransactionDate,
            $adminName,
            $ly_do
        );
        
        if ($refundResult === false || !isset($refundResult['verified'])) {
            $_SESSION['error'] = 'Không thể thực hiện hoàn tiền. Vui lòng thử lại sau hoặc liên hệ VNPay!';
            error_log("Lỗi hoàn tiền - ID đăng ký: $id_dang_ky, TxnRef: $vnp_TxnRef");
            header('Location: ?act=admin-edit-dang-ky&id=' . $id_dang_ky);
            exit;
        }
        
        // Kiểm tra response code
        $vnp_ResponseCode = $refundResult['vnp_ResponseCode'] ?? '';
        
        if ($vnp_ResponseCode === '00') {
            // Hoàn tiền thành công
            $trangThaiHoanTien = 'Thành công';
            
            // Cập nhật trạng thái đăng ký thành "Đã hủy" hoặc "Hoàn tiền"
            $this->model->updateDangKy($id_dang_ky, [
                'trang_thai' => 'Hoàn tiền'
            ]);
            
            $_SESSION['success'] = 'Hoàn tiền thành công! Mã giao dịch hoàn tiền: ' . ($refundResult['vnp_TransactionNo'] ?? '');
        } else {
            // Hoàn tiền thất bại hoặc đang xử lý
            $trangThaiHoanTien = ($vnp_ResponseCode === '94' || $vnp_ResponseCode === '95') ? 'Đang xử lý' : 'Thất bại';
            $errorMsg = $refundResult['vnp_ResponseMessage'] ?? 'Không xác định';
            $_SESSION['error'] = "Hoàn tiền không thành công. Mã lỗi: $vnp_ResponseCode - $errorMsg";
        }
        
        // Lưu thông tin hoàn tiền vào database
        $this->model->saveHoanTien(
            $thanhToan['id'],
            $refundResult['vnp_RefundRef'] ?? $refundResult['vnp_TxnRef'] ?? '',
            $refundResult['vnp_TransactionNo'] ?? '',
            $thanhToan['so_tien'],
            $ly_do,
            $trangThaiHoanTien
        );
        
        header('Location: ?act=admin-edit-dang-ky&id=' . $id_dang_ky);
        exit;
    }

    // Xóa đăng ký
    public function deleteDangKy(){
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $id_khoa_hoc = $_GET['id_khoa_hoc'] ?? '';
        $trang_thai = $_GET['trang_thai'] ?? '';

        $total = $this->model->countBinhLuan($search, $id_khoa_hoc, $trang_thai);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));

        $binhLuan = $this->model->getBinhLuan($page, $limit, $search, $id_khoa_hoc, $trang_thai);

        $khoaHocList = $this->model->getKhoaHoc(1, 1000, '', ''); // Lấy tất cả khóa học để filter
        
        $data = [
            'binhLuan' => $binhLuan,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'id_khoa_hoc' => $id_khoa_hoc,
            'trang_thai' => $trang_thai,
            'khoaHocList' => $khoaHocList
        ];
        
        ob_start();
        extract($data);
        require_once('./admin/View/binh_luan/list_content.php');
        $content = ob_get_clean();
        
        $pageTitle = 'Quản lý Bình Luận';
        require_once('./admin/View/layout.php');
        exit;
    }

    // Form trả lời bình luận
    public function traLoiBinhLuan(){
        $this->checkAdminLogin();
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID bình luận không hợp lệ!';
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }
        
        $binhLuan = $this->model->getBinhLuanById($id);
        if (!$binhLuan) {
            $_SESSION['error'] = 'Không tìm thấy bình luận!';
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }
        
        // Lấy danh sách phản hồi của bình luận này
        $phanHoiList = $this->model->getPhanHoiBinhLuan($id);
        
        $data = [
            'binhLuan' => $binhLuan,
            'phanHoiList' => $phanHoiList
        ];
        extract($data);
        
        require_once('./admin/View/binh_luan/tra_loi.php');
    }

    // Xử lý trả lời bình luận
    public function processTraLoiBinhLuan(){
        $this->checkAdminLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }
        
        $id_binh_luan = isset($_POST['id_binh_luan']) ? (int)$_POST['id_binh_luan'] : 0;
        $noi_dung = $_POST['noi_dung'] ?? '';
        $id_admin = $_SESSION['admin_id'] ?? $_SESSION['client_id'] ?? 0;
        
        if (!$id_binh_luan || empty($noi_dung) || !$id_admin) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            header('Location: ?act=admin-tra-loi-binh-luan&id=' . $id_binh_luan);
            exit;
        }
        
        // Kiểm tra bình luận có tồn tại không
        $binhLuan = $this->model->getBinhLuanById($id_binh_luan);
        if (!$binhLuan) {
            $_SESSION['error'] = 'Không tìm thấy bình luận!';
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }
        
        if ($this->model->taoPhanHoiBinhLuan($id_binh_luan, $id_admin, $noi_dung)) {
            $_SESSION['success'] = 'Trả lời bình luận thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi trả lời bình luận!';
        }
        
        header('Location: ?act=admin-tra-loi-binh-luan&id=' . $id_binh_luan);
        exit;
    }

    // Form chỉnh sửa phản hồi bình luận
    public function editPhanHoiBinhLuan(){
        $this->checkAdminLogin();
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID phản hồi không hợp lệ!';
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }
        
        $phanHoi = $this->model->getPhanHoiBinhLuanById($id);
        if (!$phanHoi) {
            $_SESSION['error'] = 'Không tìm thấy phản hồi!';
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }
        
        // Kiểm tra xem admin hiện tại có phải là người tạo phản hồi không
        $currentAdminId = $_SESSION['admin_id'] ?? $_SESSION['client_id'] ?? 0;
        if ($phanHoi['id_admin'] != $currentAdminId) {
            $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa phản hồi này!';
            header('Location: ?act=admin-tra-loi-binh-luan&id=' . $phanHoi['id_binh_luan']);
            exit;
        }
        
        // Lấy thông tin bình luận gốc
        $binhLuan = $this->model->getBinhLuanById($phanHoi['id_binh_luan']);
        
        $data = [
            'phanHoi' => $phanHoi,
            'binhLuan' => $binhLuan
        ];
        extract($data);
        
        require_once('./admin/View/binh_luan/edit_phan_hoi.php');
    }

    // Xử lý cập nhật phản hồi bình luận
    public function updatePhanHoiBinhLuan(){
        $this->checkAdminLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $noi_dung = trim($_POST['noi_dung'] ?? '');
        $currentAdminId = $_SESSION['admin_id'] ?? $_SESSION['client_id'] ?? 0;
        
        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (!$id || $id <= 0) {
            $errors[] = 'ID phản hồi không hợp lệ!';
        }
        
        if (empty($noi_dung)) {
            $errors[] = 'Vui lòng nhập nội dung phản hồi!';
        } elseif (strlen($noi_dung) < 2) {
            $errors[] = 'Nội dung phản hồi phải có ít nhất 2 ký tự!';
        } elseif (strlen($noi_dung) > 1000) {
            $errors[] = 'Nội dung phản hồi không được vượt quá 1000 ký tự!';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header('Location: ?act=admin-edit-phan-hoi-binh-luan&id=' . $id);
            exit;
        }
        
        // Kiểm tra phản hồi có tồn tại không và admin có quyền sửa không
        $phanHoi = $this->model->getPhanHoiBinhLuanById($id);
        if (!$phanHoi) {
            $_SESSION['error'] = 'Không tìm thấy phản hồi!';
            header('Location: ?act=admin-list-binh-luan');
            exit;
        }
        
        if ($phanHoi['id_admin'] != $currentAdminId) {
            $_SESSION['error'] = 'Bạn không có quyền chỉnh sửa phản hồi này!';
            header('Location: ?act=admin-tra-loi-binh-luan&id=' . $phanHoi['id_binh_luan']);
            exit;
        }
        
        if ($this->model->updatePhanHoiBinhLuan($id, $noi_dung)) {
            $_SESSION['success'] = 'Cập nhật phản hồi thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật phản hồi!';
        }
        
        header('Location: ?act=admin-tra-loi-binh-luan&id=' . $phanHoi['id_binh_luan']);
        exit;
    }

    // ===========================================
    //  QUẢN LÝ PHÒNG HỌC
    // ===========================================

    // Danh sách phòng học
    public function listPhongHoc(){
        $this->checkAdminLogin();
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $trang_thai = $_GET['trang_thai'] ?? '';
        
        $total = $this->model->countPhongHoc($search, $trang_thai);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        
        $phongHoc = $this->model->getPhongHoc($page, $limit, $search, $trang_thai);
        
        $data = [
            'phongHoc' => $phongHoc,
            'page' => $page,
            'totalPages' => $totalPages,
            'search' => $search,
            'trang_thai' => $trang_thai
        ];
        
        ob_start();
        extract($data);
        require_once('./admin/View/phong_hoc/list_content.php');
        $content = ob_get_clean();
        
        $pageTitle = 'Quản lý Phòng Học';
        require_once('./admin/View/layout.php');
        exit;
    }

    // Form thêm phòng học
    public function addPhongHoc(){
        $this->checkAdminLogin();
        require_once('./admin/View/phong_hoc/form.php');
    }

    // Xử lý thêm phòng học
    public function savePhongHoc(){
        $this->checkAdminLogin();
        $data = [
            'ten_phong' => trim($_POST['ten_phong'] ?? ''),
            'suc_chua' => !empty($_POST['suc_chua']) ? (int)$_POST['suc_chua'] : 30,
            'mo_ta' => trim($_POST['mo_ta'] ?? ''),
            'trang_thai' => $_POST['trang_thai'] ?? 'Sử dụng'
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['ten_phong'])) {
            $errors[] = 'Vui lòng nhập tên phòng học!';
        } elseif (strlen($data['ten_phong']) > 200) {
            $errors[] = 'Tên phòng học không được vượt quá 200 ký tự!';
        } elseif ($this->model->checkPhongHocExists($data['ten_phong'])) {
            $errors[] = 'Tên phòng học đã tồn tại trong hệ thống!';
        }
        
        if ($data['suc_chua'] <= 0) {
            $errors[] = 'Sức chứa phải lớn hơn 0!';
        }
        
        // Kiểm tra giá trị ENUM hợp lệ
        $validTrangThai = ['Sử dụng', 'Bảo trì', 'Khóa'];
        if (!in_array($data['trang_thai'], $validTrangThai)) {
            $data['trang_thai'] = 'Sử dụng'; // Mặc định
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header('Location: ?act=admin-add-phong-hoc');
            exit;
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
        $this->checkAdminLogin();
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
        $this->checkAdminLogin();
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
            'ten_phong' => trim($_POST['ten_phong'] ?? ''),
            'suc_chua' => !empty($_POST['suc_chua']) ? (int)$_POST['suc_chua'] : 30,
            'mo_ta' => trim($_POST['mo_ta'] ?? ''),
            'trang_thai' => $_POST['trang_thai'] ?? 'Sử dụng'
        ];

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['ten_phong'])) {
            $errors[] = 'Vui lòng nhập tên phòng học!';
        } elseif (strlen($data['ten_phong']) > 200) {
            $errors[] = 'Tên phòng học không được vượt quá 200 ký tự!';
        } elseif ($this->model->checkPhongHocExists($data['ten_phong'], $id)) {
            $errors[] = 'Tên phòng học đã tồn tại trong hệ thống!';
        }
        
        if ($data['suc_chua'] <= 0) {
            $errors[] = 'Sức chứa phải lớn hơn 0!';
        }
        
        // Kiểm tra giá trị ENUM hợp lệ
        $validTrangThai = ['Sử dụng', 'Bảo trì', 'Khóa'];
        if (!in_array($data['trang_thai'], $validTrangThai)) {
            $data['trang_thai'] = 'Sử dụng'; // Mặc định
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header('Location: ?act=admin-edit-phong-hoc&id=' . $id);
            exit;
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
        $this->checkAdminLogin();
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
    //  QUẢN LÝ TÀI KHOẢN
    // ===========================================

    // Danh sách tài khoản
    public function listTaiKhoan(){
        $this->checkAdminLogin();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $trang_thai = $_GET['trang_thai'] ?? '';
        
        $total = $this->model->countAllTaiKhoan($search, $trang_thai);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        
        $taiKhoan = $this->model->getAllTaiKhoan($page, $limit, $search, $trang_thai);
        
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
        $this->checkAdminLogin();
        
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
        $this->checkAdminLogin();
        
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            $_SESSION['error'] = 'ID tài khoản không hợp lệ!';
            header('Location: ?act=admin-list-tai-khoan');
            exit;
        }

        $data = [
            'ho_ten' => trim($_POST['ho_ten'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
            'dia_chi' => trim($_POST['dia_chi'] ?? ''),
            'trang_thai' => isset($_POST['trang_thai']) ? (int)$_POST['trang_thai'] : 1
        ];

        // Nếu có mật khẩu mới
        if (!empty($_POST['mat_khau'])) {
            $data['mat_khau'] = $_POST['mat_khau'];
        }

        // Validation: Tất cả trường bắt buộc
        $errors = [];
        
        if (empty($data['ho_ten'])) {
            $errors[] = 'Vui lòng nhập họ tên!';
        } elseif (strlen($data['ho_ten']) < 2) {
            $errors[] = 'Họ tên phải có ít nhất 2 ký tự!';
        } elseif (strlen($data['ho_ten']) > 200) {
            $errors[] = 'Họ tên không được vượt quá 200 ký tự!';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Vui lòng nhập email!';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ!';
        } elseif (strlen($data['email']) > 200) {
            $errors[] = 'Email không được vượt quá 200 ký tự!';
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
                $errors[] = 'Email đã tồn tại!';
            }
        }
        
        if (!empty($data['mat_khau']) && strlen($data['mat_khau']) < 6) {
            $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự!';
        }
        
        if (!empty($data['so_dien_thoai'])) {
            // Validate số điện thoại: 0xxxxxxxxx hoặc +84xxxxxxxxx
            $phonePattern = '/^(0|\+84)[0-9]{9,10}$/';
            $cleanPhone = preg_replace('/[\s\-]/', '', $data['so_dien_thoai']);
            if (!preg_match($phonePattern, $cleanPhone)) {
                $errors[] = 'Số điện thoại không hợp lệ! (Định dạng: 0xxxxxxxxx hoặc +84xxxxxxxxx)';
            } elseif (strlen($data['so_dien_thoai']) > 20) {
                $errors[] = 'Số điện thoại không được vượt quá 20 ký tự!';
            }
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header('Location: ?act=admin-edit-tai-khoan&id=' . $id);
            exit;
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
        $this->checkAdminLogin();
        
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

    // ===========================================
    //  QUẢN LÝ YÊU CẦU ĐỔI LỊCH
    // ===========================================
    
    // Danh sách yêu cầu đổi lịch
    public function listYeuCauDoiLich()
    {
        $this->checkAdminLogin();
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $trang_thai = $_GET['trang_thai'] ?? '';
        
        $yeuCauList = $this->model->getYeuCauDoiLich($page, $limit, $trang_thai);
        $total = $this->model->countYeuCauDoiLich($trang_thai);
        $totalPages = ceil($total / $limit);
        
        $data = [
            'yeuCauList' => $yeuCauList,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'trang_thai' => $trang_thai
        ];
        
        $this->renderView('./admin/View/yeu_cau_doi_lich/list.php', 'Quản lý yêu cầu đổi lịch', $data);
    }
    
    // Chi tiết yêu cầu đổi lịch
    public function detailYeuCauDoiLich()
    {
        $this->checkAdminLogin();
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            $_SESSION['error'] = 'ID yêu cầu không hợp lệ!';
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        $yeuCau = $this->model->getYeuCauDoiLichById($id);
        if (!$yeuCau) {
            $_SESSION['error'] = 'Không tìm thấy yêu cầu!';
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        // Kiểm tra trùng lịch
        $trungLich = $this->model->kiemTraTrungLich(
            $yeuCau['id_giang_vien'],
            $yeuCau['thu_trong_tuan_moi'],
            $yeuCau['id_ca_moi'],
            $yeuCau['id_phong_moi'],
            $yeuCau['ngay_doi'],
            $yeuCau['id_ca_hoc_cu']
        );
        
        $data = [
            'yeuCau' => $yeuCau,
            'trungLich' => $trungLich
        ];
        
        $this->renderView('./admin/View/yeu_cau_doi_lich/detail.php', 'Chi tiết yêu cầu đổi lịch', $data);
    }
    
    // Duyệt yêu cầu đổi lịch
    public function duyetYeuCauDoiLich()
    {
        $this->checkAdminLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $ghi_chu = $_POST['ghi_chu'] ?? '';
        
        if (!$id) {
            $_SESSION['error'] = 'ID yêu cầu không hợp lệ!';
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        $result = $this->model->duyetYeuCauDoiLich($id, $ghi_chu);
        
        if ($result === true) {
            $_SESSION['success'] = 'Duyệt yêu cầu đổi lịch thành công!';
        } else {
            $_SESSION['error'] = $result['error'] ?? 'Có lỗi xảy ra khi duyệt yêu cầu!';
        }
        
        header('Location: ?act=admin-list-yeu-cau-doi-lich');
        exit;
    }
    
    // Từ chối yêu cầu đổi lịch
    public function tuChoiYeuCauDoiLich()
    {
        $this->checkAdminLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $ghi_chu = $_POST['ghi_chu'] ?? '';
        
        if (!$id) {
            $_SESSION['error'] = 'ID yêu cầu không hợp lệ!';
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        if ($this->model->tuChoiYeuCauDoiLich($id, $ghi_chu)) {
            $_SESSION['success'] = 'Từ chối yêu cầu đổi lịch thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi từ chối yêu cầu!';
        }
        
        header('Location: ?act=admin-list-yeu-cau-doi-lich');
        exit;
    }
    
    // Hủy yêu cầu đổi lịch
    public function huyYeuCauDoiLich()
    {
        $this->checkAdminLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $ghi_chu = $_POST['ghi_chu'] ?? '';
        
        if (!$id) {
            $_SESSION['error'] = 'ID yêu cầu không hợp lệ!';
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        if ($this->model->huyYeuCauDoiLich($id, $ghi_chu)) {
            $_SESSION['success'] = 'Hủy yêu cầu đổi lịch thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi hủy yêu cầu!';
        }
        
        header('Location: ?act=admin-list-yeu-cau-doi-lich');
        exit;
    }
    
    // Xác nhận thay đổi lịch dạy
    public function xacNhanThayDoiLich()
    {
        $this->checkAdminLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $ghi_chu = $_POST['ghi_chu'] ?? '';
        
        if (!$id) {
            $_SESSION['error'] = 'ID yêu cầu không hợp lệ!';
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        if ($this->model->xacNhanThayDoiLich($id, $ghi_chu)) {
            $_SESSION['success'] = 'Xác nhận thay đổi lịch dạy thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xác nhận! Yêu cầu phải ở trạng thái "Đã duyệt".';
        }
        
        header('Location: ?act=admin-detail-yeu-cau-doi-lich&id=' . $id);
        exit;
    }
    
    // Hoàn nguyên lịch đã thay đổi
    public function hoanNguyenLich()
    {
        $this->checkAdminLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $ghi_chu = $_POST['ghi_chu'] ?? '';
        
        if (!$id) {
            $_SESSION['error'] = 'ID yêu cầu không hợp lệ!';
            header('Location: ?act=admin-list-yeu-cau-doi-lich');
            exit;
        }
        
        if ($this->model->hoanNguyenLich($id, $ghi_chu)) {
            $_SESSION['success'] = 'Hoàn nguyên lịch thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi hoàn nguyên lịch! Yêu cầu phải ở trạng thái "Đã duyệt".';
        }
        
        header('Location: ?act=admin-detail-yeu-cau-doi-lich&id=' . $id);
        exit;
    }
}

?>
