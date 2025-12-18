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
    //  TRANG ĐĂNG NHẬP GIẢNG VIÊN - redirect về unified login
    // ===========================================
    public function login()
    {
        // Nếu đã đăng nhập giảng viên thì chuyển về dashboard
        if (isset($_SESSION['giang_vien_id'])) {
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        // Redirect về form đăng nhập chung
        header('Location: ?act=client-login');
        exit;
    }

    // ===========================================
    //  XỬ LÝ ĐĂNG NHẬP GIẢNG VIÊN - redirect về unified login
    // ===========================================
    public function processLogin()
    {
        // Redirect về form đăng nhập chung
        header('Location: ?act=client-login');
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
        
        // Đảm bảo lấy đúng ID giảng viên từ session
        $id_giang_vien = isset($_SESSION['giang_vien_id']) ? (int)$_SESSION['giang_vien_id'] : 0;
        
        // Kiểm tra ID hợp lệ
        if (!$id_giang_vien || $id_giang_vien <= 0) {
            $_SESSION['error'] = 'ID giảng viên không hợp lệ!';
            header('Location: ?act=giang-vien-login');
            exit;
        }
        
        // Lấy filter khoảng thời gian (nếu có)
        $tuNgay = $_GET['tu_ngay'] ?? '';
        $denNgay = $_GET['den_ngay'] ?? '';
        
        // Lấy lịch dạy của giảng viên (chỉ lịch của giảng viên đang đăng nhập)
        $lopHocs = $this->model->getLopHocByGiangVien($id_giang_vien);
        
        // Tính toán ngày cụ thể cho mỗi ca học và lọc theo khoảng thời gian nếu có
        $scheduleItems = $this->calculateScheduleDates($lopHocs, $tuNgay, $denNgay);
        
        // Lấy danh sách học sinh đã đăng ký các lớp của giảng viên
        $hocSinhList = [];
        $lopIds = array_unique(array_column($scheduleItems, 'id_lop'));
        foreach ($lopIds as $idLop) {
            $hocSinh = $this->model->getHocSinhByLop($idLop);
            foreach ($hocSinh as &$hs) {
                $hs['id_lop'] = $idLop;
            }
            $hocSinhList = array_merge($hocSinhList, $hocSinh);
        }
        
        // Truyền biến vào view
        $data = [
            'scheduleItems' => $scheduleItems,
            'hocSinhList' => $hocSinhList,
            'tuNgay' => $tuNgay,
            'denNgay' => $denNgay
        ];
        extract($data);
        
        require __DIR__ . '/../views/giang_vien/dashboard.php';
    }
    
    // Helper function để tính ngày cụ thể từ thứ trong tuần, chỉ hiển thị nếu có ngày bắt đầu/kết thúc
    // Lọc theo khoảng thời gian nếu có
    private function calculateScheduleDates($lopHocs, $tuNgay = '', $denNgay = '') {
        // Map thứ trong tuần sang số
        $thuMap = [
            'Thứ 2' => 1,
            'Thứ 3' => 2,
            'Thứ 4' => 3,
            'Thứ 5' => 4,
            'Thứ 6' => 5,
            'Thứ 7' => 6,
            'Chủ nhật' => 7
        ];
        
        // Xác định khoảng thời gian lọc (nếu có)
        $hasFilter = !empty($tuNgay) || !empty($denNgay);
        $filterStartDate = null;
        $filterEndDate = null;
        
        if ($hasFilter) {
            if ($tuNgay) {
                $filterStartDate = new DateTime($tuNgay);
                $filterStartDate->setTime(0, 0, 0);
            }
            if ($denNgay) {
                $filterEndDate = new DateTime($denNgay);
                $filterEndDate->setTime(23, 59, 59);
            }
            // Nếu chỉ có một trong hai, tự động điền ngày còn lại
            if ($tuNgay && !$denNgay) {
                $filterEndDate = clone $filterStartDate;
                $filterEndDate->setTime(23, 59, 59);
            }
            if (!$tuNgay && $denNgay) {
                $filterStartDate = clone $filterEndDate;
                $filterStartDate->setTime(0, 0, 0);
            }
        }
        
        $allScheduleItems = [];
        
        foreach ($lopHocs as $lop) {
            // Kiểm tra có ca học không
            if (empty($lop['ca_hoc'])) {
                continue;
            }
            
            // Kiểm tra lớp có ngày bắt đầu và kết thúc không
            $hasDateRange = !empty($lop['ngay_bat_dau']) && !empty($lop['ngay_ket_thuc']);
            
            foreach ($lop['ca_hoc'] as $ca) {
                // Ưu tiên sử dụng ngay_hoc từ database nếu có
                $ngayHocFromDB = $ca['ngay_hoc'] ?? null;
                
                // Nếu có ngay_hoc trong database, sử dụng trực tiếp
                if (!empty($ngayHocFromDB)) {
                    $ngayHocObj = new DateTime($ngayHocFromDB);
                    
                    // Kiểm tra filter nếu có
                    if ($hasFilter) {
                        if ($ngayHocObj < $filterStartDate || $ngayHocObj > $filterEndDate) {
                            continue; // Bỏ qua nếu không nằm trong khoảng lọc
                        }
                    }
                    
                    $scheduleItem = [
                        'id_lop' => $lop['id_lop'],
                        'ten_lop' => $lop['ten_lop'],
                        'ten_khoa_hoc' => $lop['ten_khoa_hoc'],
                        'mo_ta_lop' => $lop['mo_ta_lop'] ?? '',
                        'so_luong_toi_da' => $lop['so_luong_toi_da'] ?? 0,
                        'ngay_bat_dau_lop' => $lop['ngay_bat_dau'] ?? null,
                        'ngay_ket_thuc_lop' => $lop['ngay_ket_thuc'] ?? null,
                        'ngay_bat_dau_formatted' => !empty($lop['ngay_bat_dau']) ? date('d/m/Y', strtotime($lop['ngay_bat_dau'])) : 'Chưa có',
                        'ngay_ket_thuc_formatted' => !empty($lop['ngay_ket_thuc']) ? date('d/m/Y', strtotime($lop['ngay_ket_thuc'])) : 'Chưa có',
                        'ngay_hoc' => $ngayHocFromDB,
                        'ngay_hoc_formatted' => $ngayHocObj->format('d/m/Y'),
                        'thu_trong_tuan' => $ca['thu_trong_tuan'] ?? '',
                        'ten_ca' => $ca['ten_ca'] ?? '',
                        'gio_bat_dau' => $ca['gio_bat_dau'] ?? '',
                        'gio_ket_thuc' => $ca['gio_ket_thuc'] ?? '',
                        'ten_phong' => $ca['ten_phong'] ?? '',
                        'suc_chua' => $ca['suc_chua'] ?? 0,
                        'id_ca_hoc' => $ca['id_ca_hoc'] ?? 0
                    ];
                    $allScheduleItems[] = $scheduleItem;
                    continue; // Bỏ qua phần tính toán từ thứ
                }
                
                // Nếu không có ngay_hoc, tính toán từ thứ trong tuần (logic cũ)
                $thu = $ca['thu_trong_tuan'] ?? '';
                if (!isset($thuMap[$thu])) {
                    continue;
                }
                
                // Nếu lớp có ngày bắt đầu/kết thúc, tính toán ngày cụ thể
                if ($hasDateRange) {
                    $ngayBatDau = new DateTime($lop['ngay_bat_dau']);
                    $ngayKetThuc = new DateTime($lop['ngay_ket_thuc']);
                    
                    // Tính toán ngày học cụ thể
                    $dayOfWeekNumber = $thuMap[$thu]; // 1 = Monday, 7 = Sunday
                    
                    // Tìm ngày đầu tiên có thứ tương ứng
                    $firstOccurrence = clone $ngayBatDau;
                    $currentDayOfWeek = (int)$firstOccurrence->format('N'); // 1 = Monday, 7 = Sunday
                    
                    // Tính số ngày cần cộng để đến thứ cần tìm
                    if ($currentDayOfWeek <= $dayOfWeekNumber) {
                        $daysToAdd = $dayOfWeekNumber - $currentDayOfWeek;
                    } else {
                        $daysToAdd = 7 - ($currentDayOfWeek - $dayOfWeekNumber);
                    }
                    
                    $firstOccurrence->modify("+{$daysToAdd} days");
                    
                    // Nếu ngày đầu tiên vượt quá ngày kết thúc, bỏ qua
                    if ($firstOccurrence > $ngayKetThuc) {
                        continue;
                    }
                    
                    // Nếu không có filter, hiển thị tất cả các ngày học trong khoảng thời gian
                    if (!$hasFilter) {
                        // Duyệt qua tất cả các ngày có thứ này trong khoảng thời gian
                        $ngayHoc = clone $firstOccurrence;
                        while ($ngayHoc <= $ngayKetThuc) {
                            $scheduleItem = [
                                'id_lop' => $lop['id_lop'],
                                'ten_lop' => $lop['ten_lop'],
                                'ten_khoa_hoc' => $lop['ten_khoa_hoc'],
                                'mo_ta_lop' => $lop['mo_ta_lop'] ?? '',
                                'so_luong_toi_da' => $lop['so_luong_toi_da'] ?? 0,
                                'ngay_bat_dau_lop' => $lop['ngay_bat_dau'],
                                'ngay_ket_thuc_lop' => $lop['ngay_ket_thuc'],
                                'ngay_bat_dau_formatted' => date('d/m/Y', strtotime($lop['ngay_bat_dau'])),
                                'ngay_ket_thuc_formatted' => date('d/m/Y', strtotime($lop['ngay_ket_thuc'])),
                                'ngay_hoc' => $ngayHoc->format('Y-m-d'),
                                'ngay_hoc_formatted' => $ngayHoc->format('d/m/Y'),
                                'thu_trong_tuan' => $ca['thu_trong_tuan'],
                                'ten_ca' => $ca['ten_ca'] ?? '',
                                'gio_bat_dau' => $ca['gio_bat_dau'] ?? '',
                                'gio_ket_thuc' => $ca['gio_ket_thuc'] ?? '',
                                'ten_phong' => $ca['ten_phong'] ?? '',
                                'suc_chua' => $ca['suc_chua'] ?? 0,
                                'id_ca_hoc' => $ca['id_ca_hoc'] ?? 0
                            ];
                            $allScheduleItems[] = $scheduleItem;
                            
                            // Chuyển sang tuần tiếp theo (cùng thứ)
                            $ngayHoc->modify('+7 days');
                        }
                    } else {
                        // Nếu có filter, tính toán các ngày cụ thể trong khoảng thời gian
                        // Duyệt qua tất cả các ngày có thứ này trong khoảng thời gian
                        $ngayHoc = clone $firstOccurrence;
                        while ($ngayHoc <= $ngayKetThuc) {
                            // Kiểm tra ngày có nằm trong khoảng lọc không
                            if ($ngayHoc < $filterStartDate || $ngayHoc > $filterEndDate) {
                                $ngayHoc->modify('+7 days');
                                continue; // Bỏ qua nếu không nằm trong khoảng lọc
                            }
                            
                            $scheduleItem = [
                                'id_lop' => $lop['id_lop'],
                                'ten_lop' => $lop['ten_lop'],
                                'ten_khoa_hoc' => $lop['ten_khoa_hoc'],
                                'mo_ta_lop' => $lop['mo_ta_lop'] ?? '',
                                'so_luong_toi_da' => $lop['so_luong_toi_da'] ?? 0,
                                'ngay_bat_dau_lop' => $lop['ngay_bat_dau'],
                                'ngay_ket_thuc_lop' => $lop['ngay_ket_thuc'],
                                'ngay_bat_dau_formatted' => date('d/m/Y', strtotime($lop['ngay_bat_dau'])),
                                'ngay_ket_thuc_formatted' => date('d/m/Y', strtotime($lop['ngay_ket_thuc'])),
                                'ngay_hoc' => $ngayHoc->format('Y-m-d'),
                                'ngay_hoc_formatted' => $ngayHoc->format('d/m/Y'),
                                'thu_trong_tuan' => $ca['thu_trong_tuan'],
                                'ten_ca' => $ca['ten_ca'] ?? '',
                                'gio_bat_dau' => $ca['gio_bat_dau'] ?? '',
                                'gio_ket_thuc' => $ca['gio_ket_thuc'] ?? '',
                                'ten_phong' => $ca['ten_phong'] ?? '',
                                'suc_chua' => $ca['suc_chua'] ?? 0,
                                'id_ca_hoc' => $ca['id_ca_hoc'] ?? 0
                            ];
                            $allScheduleItems[] = $scheduleItem;
                            
                            // Chuyển sang tuần tiếp theo (cùng thứ)
                            $ngayHoc->modify('+7 days');
                        }
                    }
                } else {
                    // Nếu lớp không có ngày bắt đầu/kết thúc, vẫn hiển thị ca học nhưng không có ngày cụ thể
                    $scheduleItem = [
                        'id_lop' => $lop['id_lop'],
                        'ten_lop' => $lop['ten_lop'],
                        'ten_khoa_hoc' => $lop['ten_khoa_hoc'],
                        'mo_ta_lop' => $lop['mo_ta_lop'] ?? '',
                        'so_luong_toi_da' => $lop['so_luong_toi_da'] ?? 0,
                        'ngay_bat_dau_lop' => null,
                        'ngay_ket_thuc_lop' => null,
                        'ngay_bat_dau_formatted' => 'Chưa có',
                        'ngay_ket_thuc_formatted' => 'Chưa có',
                        'ngay_hoc' => null,
                        'ngay_hoc_formatted' => null,
                        'thu_trong_tuan' => $ca['thu_trong_tuan'],
                        'ten_ca' => $ca['ten_ca'] ?? '',
                        'gio_bat_dau' => $ca['gio_bat_dau'] ?? '',
                        'gio_ket_thuc' => $ca['gio_ket_thuc'] ?? '',
                        'ten_phong' => $ca['ten_phong'] ?? '',
                        'suc_chua' => $ca['suc_chua'] ?? 0,
                        'id_ca_hoc' => $ca['id_ca_hoc'] ?? 0
                    ];
                    $allScheduleItems[] = $scheduleItem;
                }
            }
        }
        
        // Loại bỏ trùng lặp dựa trên id_lop + id_ca_hoc + ngay_hoc
        $uniqueScheduleItems = [];
        $seenKeys = [];
        
        foreach ($allScheduleItems as $item) {
            // Tạo key duy nhất để kiểm tra trùng lặp
            $uniqueKey = $item['id_lop'] . '_' . $item['id_ca_hoc'] . '_' . ($item['ngay_hoc'] ?? 'no_date');
            
            if (!isset($seenKeys[$uniqueKey])) {
                $seenKeys[$uniqueKey] = true;
                $uniqueScheduleItems[] = $item;
            }
        }
        
        // Sắp xếp theo ngày và giờ
        usort($uniqueScheduleItems, function($a, $b) {
            // Nếu có ngày học, sắp xếp theo ngày
            if (!empty($a['ngay_hoc']) && !empty($b['ngay_hoc'])) {
                if ($a['ngay_hoc'] == $b['ngay_hoc']) {
                    return strcmp($a['gio_bat_dau'], $b['gio_bat_dau']);
                }
                return strcmp($a['ngay_hoc'], $b['ngay_hoc']);
            }
            // Nếu không có ngày học, sắp xếp theo tên lớp
            return strcmp($a['ten_lop'], $b['ten_lop']);
        });
        
        return $uniqueScheduleItems;
    }

    // ===========================================
    //  XEM LỊCH HỌC CỦA GIẢNG VIÊN (action = myClasses)
    // ===========================================
    public function myClasses()
    {
        $this->checkGiangVienLogin();
        
        $id_giang_vien = $_SESSION['giang_vien_id'];
        
        // Lấy filter ngày từ GET
        $filter_ngay = $_GET['filter_ngay'] ?? null;
        if (!empty($filter_ngay)) {
            // Validate ngày
            $date = DateTime::createFromFormat('Y-m-d', $filter_ngay);
            if (!$date || $date->format('Y-m-d') !== $filter_ngay) {
                $filter_ngay = null;
            }
        }
        
        $caHocs = $this->model->getCaHocByGiangVien($id_giang_vien, $filter_ngay);
        
        require __DIR__ . '/../views/giang_vien/my_classes.php';
    }

    // ===========================================
    //  DANH SÁCH HỌC SINH (action = listHocSinh)
    // ===========================================
    public function listHocSinh()
    {
        $this->checkGiangVienLogin();
        $id_giang_vien = $_SESSION['giang_vien_id'];
        
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        
        $total = $this->model->countHocSinhByGiangVien($id_giang_vien, $search);
        $totalPages = ceil($total / $limit);
        $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
        
        $hocSinh = $this->model->getHocSinhByGiangVien($id_giang_vien, $page, $limit, $search);
        
        require __DIR__ . '/../views/giang_vien/list_hoc_sinh.php';
    }

    // ===========================================
    //  XEM CHI TIẾT LỚP HỌC CỦA HỌC SINH
    // ===========================================
    public function viewHocSinhDetail()
    {
        $this->checkGiangVienLogin();
        $id_giang_vien = $_SESSION['giang_vien_id'];
        $id_hoc_sinh = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if (!$id_hoc_sinh) {
            $_SESSION['error'] = 'ID học sinh không hợp lệ!';
            header('Location: ?act=giang-vien-list-hoc-sinh');
            exit;
        }

        $hocSinh = $this->model->getNguoiDungById($id_hoc_sinh);
        if (!$hocSinh || $hocSinh['vai_tro'] !== 'hoc_sinh') {
            $_SESSION['error'] = 'Không tìm thấy học sinh hoặc học sinh không tồn tại!';
            header('Location: ?act=giang-vien-list-hoc-sinh');
            exit;
        }

        // Lấy các lớp học mà học sinh này đã đăng ký VÀ giảng viên này đang dạy
        $lopHocs = $this->model->getLopHocDetailByHocSinhAndGiangVien($id_hoc_sinh, $id_giang_vien);

        $data = [
            'hocSinh' => $hocSinh,
            'lopHocs' => $lopHocs
        ];

        require __DIR__ . '/../views/giang_vien/hoc_sinh_detail.php';
    }
    
    // ===========================================
    //  XEM HỌC SINH TRONG LỚP
    // ===========================================
    public function viewHocSinhTrongLop()
    {
        $this->checkGiangVienLogin();
        $id_giang_vien = $_SESSION['giang_vien_id'];
        $id_lop = isset($_GET['id_lop']) ? (int)$_GET['id_lop'] : 0;

        if (!$id_lop) {
            $_SESSION['error'] = 'ID lớp học không hợp lệ!';
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }

        // Kiểm tra lớp này có thuộc giảng viên không
        $lopHocs = $this->model->getLopHocByGiangVien($id_giang_vien);
        $lopInfo = null;
        foreach ($lopHocs as $lop) {
            if ($lop['id_lop'] == $id_lop) {
                $lopInfo = $lop;
                break;
            }
        }

        if (!$lopInfo) {
            $_SESSION['error'] = 'Bạn không có quyền xem lớp học này!';
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }

        // Lấy danh sách học sinh trong lớp
        $hocSinh = $this->model->getHocSinhByLop($id_lop);

        require __DIR__ . '/../views/giang_vien/hoc_sinh_trong_lop.php';
    }

    // ===========================================
    //  XEM THÔNG TIN CÁ NHÂN (action = profile)
    // ===========================================
    public function profile()
    {
        $this->checkGiangVienLogin();
        
        $id_giang_vien = $_SESSION['giang_vien_id'] ?? 0;
        if (!$id_giang_vien) {
            header('Location: ?act=giang-vien-login');
            exit;
        }
        
        $user = $this->model->getNguoiDungById($id_giang_vien);
        if (!$user) {
            $_SESSION['error'] = 'Không tìm thấy thông tin người dùng!';
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
        require __DIR__ . '/../views/giang_vien/profile.php';
    }

    // ===========================================
    //  YÊU CẦU ĐỔI LỊCH
    // ===========================================
    
    // Form yêu cầu đổi lịch
    public function yeuCauDoiLich()
    {
        $this->checkGiangVienLogin();
        
        $id_giang_vien = $_SESSION['giang_vien_id'];
        $id_ca_hoc = isset($_GET['id_ca_hoc']) ? (int)$_GET['id_ca_hoc'] : 0;
        
        if (!$id_ca_hoc) {
            $_SESSION['error'] = 'ID ca học không hợp lệ!';
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
        // Lấy thông tin ca học
        $caHoc = $this->model->getCaHocById($id_ca_hoc);
        if (!$caHoc || $caHoc['id_giang_vien'] != $id_giang_vien) {
            $_SESSION['error'] = 'Bạn không có quyền đổi lịch này!';
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
        // Lấy danh sách ca mặc định và phòng học
        $caMacDinhList = $this->model->getCaMacDinhList();
        $phongHocList = $this->model->getPhongHocList();
        
        $data = [
            'caHoc' => $caHoc,
            'caMacDinhList' => $caMacDinhList,
            'phongHocList' => $phongHocList
        ];
        extract($data);
        
        require __DIR__ . '/../views/giang_vien/yeu_cau_doi_lich.php';
    }
    
    // Xử lý yêu cầu đổi lịch
    public function processYeuCauDoiLich()
    {
        $this->checkGiangVienLogin();
        
        $id_giang_vien = $_SESSION['giang_vien_id'];
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
        $id_ca_hoc_cu = isset($_POST['id_ca_hoc_cu']) ? (int)$_POST['id_ca_hoc_cu'] : 0;
        $id_lop = isset($_POST['id_lop']) ? (int)$_POST['id_lop'] : 0;
        $thu_trong_tuan_moi = trim($_POST['thu_trong_tuan_moi'] ?? '');
        $id_ca_moi = isset($_POST['id_ca_moi']) ? (int)$_POST['id_ca_moi'] : 0;
        $id_phong_moi = isset($_POST['id_phong_moi']) ? (int)$_POST['id_phong_moi'] : 0;
        $ngay_doi = trim($_POST['ngay_doi'] ?? '');
        $ngay_doi = !empty($ngay_doi) ? $ngay_doi : null;
        $ly_do = trim($_POST['ly_do'] ?? '');
        
        // Validation cơ bản
        if (!$id_ca_hoc_cu || !$id_lop || !$id_ca_moi || !$id_phong_moi) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            header('Location: ?act=giang-vien-yeu-cau-doi-lich&id_ca_hoc=' . $id_ca_hoc_cu);
            exit;
        }
        
        // Kiểm tra lý do đổi lịch bắt buộc
        if (empty($ly_do)) {
            $_SESSION['error'] = 'Vui lòng nhập lý do đổi lịch!';
            header('Location: ?act=giang-vien-yeu-cau-doi-lich&id_ca_hoc=' . $id_ca_hoc_cu);
            exit;
        }
        
        // Kiểm tra ca học có thuộc giảng viên không
        $caHoc = $this->model->getCaHocById($id_ca_hoc_cu);
        if (!$caHoc || $caHoc['id_giang_vien'] != $id_giang_vien) {
            $_SESSION['error'] = 'Bạn không có quyền đổi lịch này!';
            header('Location: ?act=giang-vien-dashboard');
            exit;
        }
        
        // Xử lý thứ trong tuần:
        // - Nếu có ngày đổi: tự động tính thứ từ ngày
        // - Nếu không có ngày đổi: dùng thứ từ form (đổi toàn bộ lịch)
        if ($ngay_doi) {
            // Tự động tính thứ từ ngày
            $date = new DateTime($ngay_doi);
            $thu = (int)$date->format('N'); // 1 = Monday, 7 = Sunday
            $thuMap = [
                1 => 'Thứ 2',
                2 => 'Thứ 3',
                3 => 'Thứ 4',
                4 => 'Thứ 5',
                5 => 'Thứ 6',
                6 => 'Thứ 7',
                7 => 'Chủ nhật'
            ];
            $thu_trong_tuan_moi = $thuMap[$thu] ?? '';
        } else {
            // Nếu không có ngày đổi, cần có thứ để kiểm tra trùng
            if (empty($thu_trong_tuan_moi)) {
                $_SESSION['error'] = 'Vui lòng chọn thứ trong tuần khi đổi toàn bộ lịch!';
                header('Location: ?act=giang-vien-yeu-cau-doi-lich&id_ca_hoc=' . $id_ca_hoc_cu);
                exit;
            }
        }
        
        // Kiểm tra trùng lịch trước khi cho phép đổi
        $trungLich = $this->model->kiemTraTrungLich(
            $id_giang_vien,
            $thu_trong_tuan_moi,
            $id_ca_moi,
            $id_phong_moi,
            $ngay_doi,
            $id_ca_hoc_cu
        );
        
        if (!empty($trungLich)) {
            // Tạo thông báo lỗi chi tiết
            $thongBaoLoi = 'Không thể đổi lịch! Lịch mới bị trùng với: ';
            $danhSachTrung = [];
            foreach ($trungLich as $tl) {
                $tenLop = $tl['ten_lop'] ?? 'N/A';
                $tenGiangVien = $tl['ten_giang_vien'] ?? 'N/A';
                $thuTrung = $tl['thu_trong_tuan'] ?? '';
                $ngayTrung = !empty($tl['ngay_hoc']) ? date('d/m/Y', strtotime($tl['ngay_hoc'])) : '';
                if ($ngayTrung) {
                    $danhSachTrung[] = "Lớp '$tenLop' (GV: $tenGiangVien) vào ngày $ngayTrung ($thuTrung)";
                } else {
                    $danhSachTrung[] = "Lớp '$tenLop' (GV: $tenGiangVien) vào $thuTrung";
                }
            }
            $thongBaoLoi .= implode(', ', $danhSachTrung);
            
            $_SESSION['error'] = $thongBaoLoi;
            header('Location: ?act=giang-vien-yeu-cau-doi-lich&id_ca_hoc=' . $id_ca_hoc_cu);
            exit;
        }
        
        // Tạo yêu cầu
        $data = [
            'id_giang_vien' => $id_giang_vien,
            'id_ca_hoc_cu' => $id_ca_hoc_cu,
            'id_lop' => $id_lop,
            'thu_trong_tuan_moi' => $thu_trong_tuan_moi,
            'id_ca_moi' => $id_ca_moi,
            'id_phong_moi' => $id_phong_moi,
            'ngay_doi' => !empty($ngay_doi) ? $ngay_doi : null,
            'ly_do' => $ly_do
        ];
        
        if ($this->model->taoYeuCauDoiLich($data)) {
            $_SESSION['success'] = 'Yêu cầu đổi lịch đã được gửi thành công! Vui lòng chờ admin duyệt.';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi gửi yêu cầu!';
        }
        
        header('Location: ?act=giang-vien-danh-sach-yeu-cau-doi-lich');
        exit;
    }
    
    // Danh sách yêu cầu đổi lịch của giảng viên
    public function danhSachYeuCauDoiLich()
    {
        $this->checkGiangVienLogin();
        
        $id_giang_vien = $_SESSION['giang_vien_id'];
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 10;
        
        $yeuCauList = $this->model->getYeuCauDoiLichByGiangVien($id_giang_vien, $page, $limit);
        $total = $this->model->countYeuCauDoiLich();
        
        require __DIR__ . '/../views/giang_vien/danh_sach_yeu_cau_doi_lich.php';
    }
}

