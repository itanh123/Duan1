<?php
// Đúng đường dẫn model (theo ZIP bạn gửi)
require_once __DIR__ . '/../Model/KhoaHoc.php';

class KhoaHocController {

    private $model;

    public function __construct() {
        $this->model = new KhoaHoc();
    }

    // ===========================================
    //  HIỂN THỊ DANH SÁCH KHÓA HỌC  (action = index)
    // ===========================================
    public function index() 
    {
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

        $lopCa = [];
        foreach ($lops as $lop) {
            $lopCa[$lop['id']] = $this->model->getCaHocByLop($lop['id']);
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
