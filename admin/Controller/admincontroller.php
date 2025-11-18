<?php
require_once('./admin/Model/adminmodel.php');

class admincontroller{
    public $model;

    public function __construct(){
        $this->model = new adminmodel();
    }

    // Trang chủ admin (Dashboard)
    public function dashboard(){
        $thongKe = $this->model->getThongKe();
        $dangKyMoiNhat = $this->model->getDangKyMoiNhat(5);
        $thanhToanMoiNhat = $this->model->getThanhToanMoiNhat(5);
        
        require_once('./admin/View/dashboard.php');
    }

    // Danh sách khóa học
    public function listKhoaHoc(){
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
        $danhMuc = $this->model->getDanhMuc();
        require_once('./admin/View/khoa_hoc/form.php');
    }

    // Xử lý thêm khóa học
    public function saveKhoaHoc(){
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
}

?>
