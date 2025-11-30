<?php
class adminmodel
{
    public $conn;

    public function __construct()
    {
        $this->conn = connectDB();
    }

    // Lấy danh sách khóa học
    public function getKhoaHoc($page = 1, $limit = 10, $search = '', $id_danh_muc = '')
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT kh.*, dm.ten_danh_muc 
                FROM khoa_hoc kh 
                LEFT JOIN danh_muc dm ON kh.id_danh_muc = dm.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND kh.ten_khoa_hoc LIKE :search";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_danh_muc)) {
            $sql .= " AND kh.id_danh_muc = :id_danh_muc";
            $params[':id_danh_muc'] = $id_danh_muc;
        }

        $sql .= " ORDER BY kh.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số khóa học
    public function countKhoaHoc($search = '', $id_danh_muc = '')
    {
        $sql = "SELECT COUNT(*) as total FROM khoa_hoc WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND ten_khoa_hoc LIKE :search";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_danh_muc)) {
            $sql .= " AND id_danh_muc = :id_danh_muc";
            $params[':id_danh_muc'] = $id_danh_muc;
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin một khóa học theo ID
    public function getKhoaHocById($id)
    {
        $sql = "SELECT kh.*, dm.ten_danh_muc 
                FROM khoa_hoc kh 
                LEFT JOIN danh_muc dm ON kh.id_danh_muc = dm.id 
                WHERE kh.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Thêm khóa học mới
    public function addKhoaHoc($data)
    {
        $sql = "INSERT INTO khoa_hoc (id_danh_muc, ten_khoa_hoc, mo_ta, gia, hinh_anh, trang_thai) 
                VALUES (:id_danh_muc, :ten_khoa_hoc, :mo_ta, :gia, :hinh_anh, :trang_thai)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_danh_muc', $data['id_danh_muc'], PDO::PARAM_INT);
        $stmt->bindValue(':ten_khoa_hoc', $data['ten_khoa_hoc']);
        $stmt->bindValue(':mo_ta', $data['mo_ta']);
        $stmt->bindValue(':gia', $data['gia']);
        $stmt->bindValue(':hinh_anh', $data['hinh_anh'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Cập nhật khóa học
    public function updateKhoaHoc($id, $data)
    {
        $sql = "UPDATE khoa_hoc 
                SET id_danh_muc = :id_danh_muc, 
                    ten_khoa_hoc = :ten_khoa_hoc, 
                    mo_ta = :mo_ta, 
                    gia = :gia, 
                    hinh_anh = :hinh_anh, 
                    trang_thai = :trang_thai 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_danh_muc', $data['id_danh_muc'], PDO::PARAM_INT);
        $stmt->bindValue(':ten_khoa_hoc', $data['ten_khoa_hoc']);
        $stmt->bindValue(':mo_ta', $data['mo_ta']);
        $stmt->bindValue(':gia', $data['gia']);
        $stmt->bindValue(':hinh_anh', $data['hinh_anh'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Xóa khóa học
    public function deleteKhoaHoc($id)
    {
        $sql = "DELETE FROM khoa_hoc WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Lấy danh sách danh mục (chỉ lấy danh mục đang hoạt động - dùng cho dropdown)
    public function getDanhMuc()
    {
        $sql = "SELECT * FROM danh_muc WHERE trang_thai = 1 ORDER BY ten_danh_muc";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ===========================================
    //  QUẢN LÝ DANH MỤC
    // ===========================================

    // Lấy danh sách danh mục (quản lý - lấy tất cả)
    public function getDanhMucList($page = 1, $limit = 10, $search = '')
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT * FROM danh_muc";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE ten_danh_muc LIKE :search";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số danh mục
    public function countDanhMuc($search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM danh_muc";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE ten_danh_muc LIKE :search";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin một danh mục theo ID
    public function getDanhMucById($id)
    {
        $sql = "SELECT * FROM danh_muc WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Thêm danh mục mới
    public function addDanhMuc($data)
    {
        $sql = "INSERT INTO danh_muc (ten_danh_muc, mo_ta, trang_thai) 
                VALUES (:ten_danh_muc, :mo_ta, :trang_thai)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ten_danh_muc', $data['ten_danh_muc']);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Cập nhật danh mục
    public function updateDanhMuc($id, $data)
    {
        $sql = "UPDATE danh_muc 
                SET ten_danh_muc = :ten_danh_muc, 
                    mo_ta = :mo_ta, 
                    trang_thai = :trang_thai 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':ten_danh_muc', $data['ten_danh_muc']);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Xóa danh mục
    public function deleteDanhMuc($id)
    {
        // Kiểm tra xem danh mục có đang được sử dụng trong khóa học không
        $sqlCheck = "SELECT COUNT(*) as total FROM khoa_hoc WHERE id_danh_muc = :id";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        $result = $stmtCheck->fetch();
        
        if ($result['total'] > 0) {
            return false; // Không thể xóa vì đang có khóa học sử dụng
        }

        $sql = "DELETE FROM danh_muc WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Kiểm tra tên danh mục đã tồn tại chưa (trừ ID hiện tại)
    public function checkDanhMucExists($ten_danh_muc, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM danh_muc WHERE ten_danh_muc = :ten_danh_muc";
        $params = [':ten_danh_muc' => $ten_danh_muc];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    // ===========================================
    //  QUẢN LÝ GIẢNG VIÊN
    // ===========================================

    // Lấy danh sách giảng viên
    public function getGiangVien($page = 1, $limit = 10, $search = '')
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT nd.* FROM nguoi_dung nd WHERE nd.vai_tro = 'giang_vien'";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nd.ma_nguoi_dung LIKE :search OR nd.ho_ten LIKE :search OR nd.email LIKE :search OR nd.so_dien_thoai LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY nd.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số giảng viên
    public function countGiangVien($search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE vai_tro = 'giang_vien'";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ma_nguoi_dung LIKE :search OR ho_ten LIKE :search OR email LIKE :search OR so_dien_thoai LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin một giảng viên theo ID
    public function getGiangVienById($id)
    {
        $sql = "SELECT * FROM nguoi_dung WHERE id = :id AND vai_tro = 'giang_vien'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Thêm giảng viên mới
    public function addGiangVien($data)
    {
        $sql = "INSERT INTO nguoi_dung (ho_ten, email, mat_khau, so_dien_thoai, dia_chi, vai_tro, trang_thai) 
                VALUES (:ho_ten, :email, :mat_khau, :so_dien_thoai, :dia_chi, 'giang_vien', :trang_thai)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ho_ten', $data['ho_ten']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':mat_khau', $data['mat_khau']);
        $stmt->bindValue(':so_dien_thoai', $data['so_dien_thoai'] ?? null);
        $stmt->bindValue(':dia_chi', $data['dia_chi'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Cập nhật giảng viên
    public function updateGiangVien($id, $data)
    {
        $sql = "UPDATE nguoi_dung 
                SET ho_ten = :ho_ten, 
                    email = :email, 
                    so_dien_thoai = :so_dien_thoai, 
                    dia_chi = :dia_chi, 
                    trang_thai = :trang_thai";
        
        // Chỉ cập nhật mật khẩu nếu có
        if (!empty($data['mat_khau'])) {
            $sql .= ", mat_khau = :mat_khau";
        }
        
        $sql .= " WHERE id = :id AND vai_tro = 'giang_vien'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':ho_ten', $data['ho_ten']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':so_dien_thoai', $data['so_dien_thoai'] ?? null);
        $stmt->bindValue(':dia_chi', $data['dia_chi'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
        
        if (!empty($data['mat_khau'])) {
            $stmt->bindValue(':mat_khau', $data['mat_khau']);
        }
        
        return $stmt->execute();
    }

    // Xóa giảng viên
    public function deleteGiangVien($id)
    {
        $sql = "DELETE FROM nguoi_dung WHERE id = :id AND vai_tro = 'giang_vien'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===========================================
    //  QUẢN LÝ LỚP HỌC
    // ===========================================

    // Lấy danh sách lớp học
    public function getLopHoc($page = 1, $limit = 10, $search = '', $id_khoa_hoc = '')
    {
        $offset = ($page - 1) * $limit;
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        // Query không JOIN giảng viên (giảng viên được quản lý trong ca học)
        $sql = "SELECT lh.*, kh.ten_khoa_hoc 
                FROM lop_hoc lh 
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id 
                WHERE 1=1";
        
        $params = [];

        if (!empty($search)) {
            $sql .= " AND lh.ten_lop LIKE :search";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_khoa_hoc)) {
            $sql .= " AND lh.id_khoa_hoc = :id_khoa_hoc";
            $params[':id_khoa_hoc'] = $id_khoa_hoc;
        }

        $sql .= " ORDER BY lh.id DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Kiểm tra xem cột có tồn tại trong bảng không
    private function checkColumnExists($table, $column)
    {
        try {
            $sql = "SHOW COLUMNS FROM `$table` LIKE :column";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':column', $column);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    // Đếm tổng số lớp học
    public function countLopHoc($search = '', $id_khoa_hoc = '')
    {
        $sql = "SELECT COUNT(*) as total FROM lop_hoc WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND ten_lop LIKE :search";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_khoa_hoc)) {
            $sql .= " AND id_khoa_hoc = :id_khoa_hoc";
            $params[':id_khoa_hoc'] = $id_khoa_hoc;
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin một lớp học theo ID
    public function getLopHocById($id)
    {
        // Query không JOIN giảng viên (giảng viên được quản lý trong ca học)
        $sql = "SELECT lh.*, kh.ten_khoa_hoc 
                FROM lop_hoc lh 
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id 
                WHERE lh.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy danh sách giảng viên (để chọn trong form)
    public function getGiangVienList()
    {
        $sql = "SELECT id, ho_ten, email FROM nguoi_dung WHERE vai_tro = 'giang_vien' AND trang_thai = 1 ORDER BY ho_ten";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy danh sách giảng viên cho client (chỉ hiển thị thông tin công khai)
    public function getGiangVienForClient($page = 1, $limit = 12, $search = '')
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT nd.id, nd.ho_ten, nd.email, nd.so_dien_thoai, nd.dia_chi
                FROM nguoi_dung nd
                INNER JOIN nguoi_dung_vai_tro ndvt ON nd.id = ndvt.id_nguoi_dung
                WHERE ndvt.vai_tro = 'giang_vien' AND nd.trang_thai = 1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nd.ho_ten LIKE :search OR nd.email LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY nd.ho_ten ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số giảng viên cho client
    public function countGiangVienForClient($search = '')
    {
        $sql = "SELECT COUNT(DISTINCT nd.id) as total
                FROM nguoi_dung nd
                INNER JOIN nguoi_dung_vai_tro ndvt ON nd.id = ndvt.id_nguoi_dung
                WHERE ndvt.vai_tro = 'giang_vien' AND nd.trang_thai = 1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nd.ho_ten LIKE :search OR nd.email LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)$result['total'];
    }

    // Thêm lớp học mới
    public function addLopHoc($data)
    {
        // Thêm lớp học không có id_giang_vien (giảng viên sẽ được phân công trong ca học)
        $sql = "INSERT INTO lop_hoc (id_khoa_hoc, ten_lop, mo_ta, so_luong_toi_da, trang_thai) 
                VALUES (:id_khoa_hoc, :ten_lop, :mo_ta, :so_luong_toi_da, :trang_thai)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_khoa_hoc', $data['id_khoa_hoc'], PDO::PARAM_INT);
        $stmt->bindValue(':ten_lop', $data['ten_lop']);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':so_luong_toi_da', $data['so_luong_toi_da'] ?? null, PDO::PARAM_INT);
        // Đảm bảo trang_thai là một trong các giá trị ENUM hợp lệ
        $trang_thai = $data['trang_thai'] ?? 'Chưa khai giảng';
        $validTrangThai = ['Chưa khai giảng', 'Đang học', 'Kết thúc'];
        if (!in_array($trang_thai, $validTrangThai)) {
            $trang_thai = 'Chưa khai giảng'; // Mặc định
        }
        $stmt->bindValue(':trang_thai', $trang_thai, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Cập nhật lớp học
    public function updateLopHoc($id, $data)
    {
        // Đảm bảo trang_thai là một trong các giá trị ENUM hợp lệ
        $trang_thai = $data['trang_thai'] ?? 'Chưa khai giảng';
        $validTrangThai = ['Chưa khai giảng', 'Đang học', 'Kết thúc'];
        if (!in_array($trang_thai, $validTrangThai)) {
            $trang_thai = 'Chưa khai giảng'; // Mặc định
        }
        
        // Cập nhật lớp học không có id_giang_vien (giảng viên sẽ được phân công trong ca học)
        $sql = "UPDATE lop_hoc 
                SET id_khoa_hoc = :id_khoa_hoc, 
                    ten_lop = :ten_lop, 
                    mo_ta = :mo_ta, 
                    so_luong_toi_da = :so_luong_toi_da, 
                    trang_thai = :trang_thai 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_khoa_hoc', $data['id_khoa_hoc'], PDO::PARAM_INT);
        $stmt->bindValue(':ten_lop', $data['ten_lop']);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':so_luong_toi_da', $data['so_luong_toi_da'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':trang_thai', $trang_thai, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // ===========================================
    //  QUẢN LÝ CA HỌC
    // ===========================================

    // Lấy danh sách ca học
    public function getCaHoc($page = 1, $limit = 10, $search = '', $id_lop = '')
    {
        $offset = ($page - 1) * $limit;
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT ch.*, lh.ten_lop, kh.ten_khoa_hoc, nd.ho_ten as ten_giang_vien, 
                       cmd.ten_ca, cmd.gio_bat_dau, cmd.gio_ket_thuc,
                       ph.ten_phong
                FROM ca_hoc ch 
                LEFT JOIN lop_hoc lh ON ch.id_lop = lh.id 
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id 
                LEFT JOIN nguoi_dung nd ON ch.id_giang_vien = nd.id 
                LEFT JOIN ca_mac_dinh cmd ON ch.id_ca = cmd.id
                LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ph.ten_phong LIKE :search OR ch.ghi_chu LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_lop)) {
            $sql .= " AND ch.id_lop = :id_lop";
            $params[':id_lop'] = $id_lop;
        }

        $sql .= " ORDER BY ch.id_lop, ch.thu_trong_tuan, cmd.gio_bat_dau LIMIT $limit OFFSET $offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số ca học
    public function countCaHoc($search = '', $id_lop = '')
    {
        $sql = "SELECT COUNT(*) as total 
                FROM ca_hoc ch
                LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ph.ten_phong LIKE :search OR ch.ghi_chu LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_lop)) {
            $sql .= " AND id_lop = :id_lop";
            $params[':id_lop'] = $id_lop;
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin một ca học theo ID
    public function getCaHocById($id)
    {
        $sql = "SELECT ch.*, lh.ten_lop, kh.ten_khoa_hoc, nd.ho_ten as ten_giang_vien,
                       cmd.ten_ca, cmd.gio_bat_dau, cmd.gio_ket_thuc,
                       ph.ten_phong
                FROM ca_hoc ch 
                LEFT JOIN lop_hoc lh ON ch.id_lop = lh.id 
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id 
                LEFT JOIN nguoi_dung nd ON ch.id_giang_vien = nd.id 
                LEFT JOIN ca_mac_dinh cmd ON ch.id_ca = cmd.id
                LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                WHERE ch.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy danh sách lớp học (để chọn trong form)
    public function getLopHocList()
    {
        $sql = "SELECT id, ten_lop FROM lop_hoc ORDER BY ten_lop";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy danh sách ca mặc định (để chọn trong form)
    public function getCaMacDinhList()
    {
        $sql = "SELECT id, ten_ca, gio_bat_dau, gio_ket_thuc FROM ca_mac_dinh ORDER BY gio_bat_dau";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy danh sách phòng học (để chọn trong form)
    public function getPhongHocList()
    {
        $sql = "SELECT id, ten_phong, suc_chua FROM phong_hoc WHERE trang_thai = 'Sử dụng' ORDER BY ten_phong";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Kiểm tra trùng ca học (cùng ca, cùng thứ thì phải khác giảng viên và khác phòng)
    public function checkTrungCaHoc($id_ca, $thu_trong_tuan, $id_giang_vien, $id_phong, $excludeId = null)
    {
        // Kiểm tra trùng giảng viên (nếu có chọn giảng viên)
        if (!empty($id_giang_vien)) {
            $sql = "SELECT ch.*, lh.ten_lop, nd.ho_ten as ten_giang_vien, ph.ten_phong
                    FROM ca_hoc ch
                    LEFT JOIN lop_hoc lh ON ch.id_lop = lh.id
                    LEFT JOIN nguoi_dung nd ON ch.id_giang_vien = nd.id
                    LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                    WHERE ch.id_ca = :id_ca 
                    AND ch.thu_trong_tuan = :thu_trong_tuan
                    AND ch.id_giang_vien = :id_giang_vien";
            
            $params = [
                ':id_ca' => $id_ca,
                ':thu_trong_tuan' => $thu_trong_tuan,
                ':id_giang_vien' => $id_giang_vien
            ];
            
            if ($excludeId) {
                $sql .= " AND ch.id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result) {
                return [
                    'trung' => true,
                    'loi' => 'giang_vien',
                    'thong_tin' => $result
                ];
            }
        }
        
        // Kiểm tra trùng phòng học (nếu có chọn phòng)
        if (!empty($id_phong)) {
            $sql = "SELECT ch.*, lh.ten_lop, nd.ho_ten as ten_giang_vien, ph.ten_phong
                    FROM ca_hoc ch
                    LEFT JOIN lop_hoc lh ON ch.id_lop = lh.id
                    LEFT JOIN nguoi_dung nd ON ch.id_giang_vien = nd.id
                    LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                    WHERE ch.id_ca = :id_ca 
                    AND ch.thu_trong_tuan = :thu_trong_tuan
                    AND ch.id_phong = :id_phong";
            
            $params = [
                ':id_ca' => $id_ca,
                ':thu_trong_tuan' => $thu_trong_tuan,
                ':id_phong' => $id_phong
            ];
            
            if ($excludeId) {
                $sql .= " AND ch.id != :exclude_id";
                $params[':exclude_id'] = $excludeId;
            }
            
            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result) {
                return [
                    'trung' => true,
                    'loi' => 'phong',
                    'thong_tin' => $result
                ];
            }
        }
        
        return ['trung' => false];
    }

    // Thêm ca học mới
    public function addCaHoc($data)
    {
        $sql = "INSERT INTO ca_hoc (id_lop, id_giang_vien, id_ca, thu_trong_tuan, id_phong, ghi_chu) 
                VALUES (:id_lop, :id_giang_vien, :id_ca, :thu_trong_tuan, :id_phong, :ghi_chu)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_lop', $data['id_lop'], PDO::PARAM_INT);
        $stmt->bindValue(':id_giang_vien', !empty($data['id_giang_vien']) ? $data['id_giang_vien'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':id_ca', $data['id_ca'], PDO::PARAM_INT);
        $stmt->bindValue(':thu_trong_tuan', $data['thu_trong_tuan'], PDO::PARAM_STR);
        $stmt->bindValue(':id_phong', $data['id_phong'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':ghi_chu', $data['ghi_chu'] ?? null);
        return $stmt->execute();
    }

    // Cập nhật ca học
    public function updateCaHoc($id, $data)
    {
        $sql = "UPDATE ca_hoc 
                SET id_lop = :id_lop, 
                    id_giang_vien = :id_giang_vien, 
                    id_ca = :id_ca,
                    thu_trong_tuan = :thu_trong_tuan, 
                    id_phong = :id_phong, 
                    ghi_chu = :ghi_chu 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_lop', $data['id_lop'], PDO::PARAM_INT);
        $stmt->bindValue(':id_giang_vien', !empty($data['id_giang_vien']) ? $data['id_giang_vien'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':id_ca', $data['id_ca'], PDO::PARAM_INT);
        $stmt->bindValue(':thu_trong_tuan', $data['thu_trong_tuan'], PDO::PARAM_STR);
        $stmt->bindValue(':id_phong', $data['id_phong'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':ghi_chu', $data['ghi_chu'] ?? null);
        return $stmt->execute();
    }

    // Xóa ca học
    public function deleteCaHoc($id)
    {
        $sql = "DELETE FROM ca_hoc WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Đếm số lượng đăng ký hiện tại của lớp học (chỉ đếm đăng ký đã xác nhận)
    // Lấy danh sách học sinh đã đăng ký lớp học
    public function getHocSinhByLop($id_lop)
    {
        $sql = "SELECT dk.id as id_dang_ky,
                       dk.trang_thai as trang_thai_dang_ky,
                       dk.ngay_dang_ky,
                       nd.id as id_hoc_sinh,
                       nd.ho_ten,
                       nd.email,
                       nd.so_dien_thoai,
                       nd.dia_chi
                FROM dang_ky dk
                INNER JOIN nguoi_dung nd ON dk.id_hoc_sinh = nd.id
                WHERE dk.id_lop = :id_lop
                AND dk.trang_thai = 'Đã xác nhận'
                ORDER BY dk.ngay_dang_ky DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_lop', $id_lop, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function countDangKyByLop($id_lop)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM dang_ky 
                WHERE id_lop = :id_lop 
                AND trang_thai = 'Đã xác nhận'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_lop', $id_lop, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int)$result['total'];
    }

    // Lấy sức chứa phòng học nhỏ nhất của lớp học (lớp có thể có nhiều ca học với nhiều phòng)
    public function getSucChuaPhongHocNhoNhatByLop($id_lop)
    {
        $sql = "SELECT MIN(ph.suc_chua) as suc_chua_nho_nhat, 
                       GROUP_CONCAT(DISTINCT ph.ten_phong SEPARATOR ', ') as danh_sach_phong
                FROM ca_hoc ch
                INNER JOIN phong_hoc ph ON ch.id_phong = ph.id
                WHERE ch.id_lop = :id_lop
                AND ph.trang_thai = 'Sử dụng'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_lop', $id_lop, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result && $result['suc_chua_nho_nhat'] !== null) {
            return [
                'suc_chua' => (int)$result['suc_chua_nho_nhat'],
                'danh_sach_phong' => $result['danh_sach_phong']
            ];
        }
        
        return null; // Lớp học chưa có phòng học được phân công
    }

    // Lấy danh sách lớp học mà học sinh đã đăng ký (với thông tin ca học, phòng học)
    public function getLopHocByHocSinh($id_hoc_sinh)
    {
        $sql = "SELECT dk.id as id_dang_ky,
                       dk.trang_thai as trang_thai_dang_ky,
                       dk.ngay_dang_ky,
                       lh.id as id_lop,
                       lh.ten_lop,
                       lh.mo_ta as mo_ta_lop,
                       lh.so_luong_toi_da,
                       lh.trang_thai as trang_thai_lop,
                       kh.id as id_khoa_hoc,
                       kh.ten_khoa_hoc,
                       kh.gia,
                       kh.hinh_anh
                FROM dang_ky dk
                INNER JOIN lop_hoc lh ON dk.id_lop = lh.id
                INNER JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                WHERE dk.id_hoc_sinh = :id_hoc_sinh
                AND dk.trang_thai = 'Đã xác nhận'
                ORDER BY dk.ngay_dang_ky DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_hoc_sinh', $id_hoc_sinh, PDO::PARAM_INT);
        $stmt->execute();
        $lopHocs = $stmt->fetchAll();
        
        // Lấy thông tin ca học và phòng học cho mỗi lớp
        foreach ($lopHocs as &$lop) {
            $sqlCaHoc = "SELECT ch.id as id_ca_hoc,
                                ch.thu_trong_tuan,
                                cmd.ten_ca,
                                cmd.gio_bat_dau,
                                cmd.gio_ket_thuc,
                                ph.ten_phong,
                                ph.suc_chua,
                                nd.ho_ten as ten_giang_vien
                         FROM ca_hoc ch
                         LEFT JOIN ca_mac_dinh cmd ON ch.id_ca = cmd.id
                         LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                         LEFT JOIN nguoi_dung nd ON ch.id_giang_vien = nd.id
                         WHERE ch.id_lop = :id_lop
                         ORDER BY ch.thu_trong_tuan, cmd.gio_bat_dau";
            
            $stmtCaHoc = $this->conn->prepare($sqlCaHoc);
            $stmtCaHoc->bindValue(':id_lop', $lop['id_lop'], PDO::PARAM_INT);
            $stmtCaHoc->execute();
            $lop['ca_hoc'] = $stmtCaHoc->fetchAll();
        }
        
        return $lopHocs;
    }

    // Lấy danh sách lớp học mà giảng viên đang dạy (với thông tin ca học)
    public function getLopHocByGiangVien($id_giang_vien, $filterDate = '')
    {
        // Map thứ trong tuần
        $thuMap = [
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            7 => 'Chủ nhật'
        ];
        
        // Tính thứ trong tuần từ filterDate nếu có
        $filterThu = '';
        if ($filterDate) {
            $filterDateObj = new DateTime($filterDate);
            $dayOfWeek = (int)$filterDateObj->format('N'); // 1 = Monday, 7 = Sunday
            if (isset($thuMap[$dayOfWeek])) {
                $filterThu = $thuMap[$dayOfWeek];
            }
        }
        
        $sql = "SELECT DISTINCT
                       lh.id as id_lop,
                       lh.ten_lop,
                       lh.mo_ta as mo_ta_lop,
                       lh.so_luong_toi_da,
                       lh.trang_thai as trang_thai_lop,
                       lh.ngay_bat_dau,
                       lh.ngay_ket_thuc,
                       kh.id as id_khoa_hoc,
                       kh.ten_khoa_hoc,
                       kh.gia,
                       kh.hinh_anh
                FROM ca_hoc ch
                INNER JOIN lop_hoc lh ON ch.id_lop = lh.id
                INNER JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                WHERE ch.id_giang_vien = :id_giang_vien
                AND lh.ngay_bat_dau IS NOT NULL
                AND lh.ngay_ket_thuc IS NOT NULL
                ORDER BY lh.ten_lop";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_giang_vien', $id_giang_vien, PDO::PARAM_INT);
        $stmt->execute();
        $lopHocs = $stmt->fetchAll();
        
        // Lấy thông tin ca học cho mỗi lớp
        foreach ($lopHocs as &$lop) {
            $sqlCaHoc = "SELECT ch.id as id_ca_hoc,
                                ch.thu_trong_tuan,
                                cmd.ten_ca,
                                cmd.gio_bat_dau,
                                cmd.gio_ket_thuc,
                                ph.ten_phong,
                                ph.suc_chua
                         FROM ca_hoc ch
                         LEFT JOIN ca_mac_dinh cmd ON ch.id_ca = cmd.id
                         LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                         WHERE ch.id_lop = :id_lop
                         AND ch.id_giang_vien = :id_giang_vien";
            
            // Nếu có filter ngày, chỉ lấy ca học của thứ tương ứng
            if ($filterThu) {
                $sqlCaHoc .= " AND ch.thu_trong_tuan = :thu_trong_tuan";
            }
            
            $sqlCaHoc .= " ORDER BY ch.thu_trong_tuan, cmd.gio_bat_dau";
            
            $stmtCaHoc = $this->conn->prepare($sqlCaHoc);
            $stmtCaHoc->bindValue(':id_lop', $lop['id_lop'], PDO::PARAM_INT);
            $stmtCaHoc->bindValue(':id_giang_vien', $id_giang_vien, PDO::PARAM_INT);
            if ($filterThu) {
                $stmtCaHoc->bindValue(':thu_trong_tuan', $filterThu);
            }
            $stmtCaHoc->execute();
            $lop['ca_hoc'] = $stmtCaHoc->fetchAll();
        }
        
        return $lopHocs;
    }

    // Lấy thông tin chi tiết lớp học của một học sinh cụ thể (dùng trong admin)
    public function getLopHocDetailByHocSinh($id_hoc_sinh)
    {
        $sql = "SELECT dk.id as id_dang_ky,
                       dk.trang_thai as trang_thai_dang_ky,
                       dk.ngay_dang_ky,
                       lh.id as id_lop,
                       lh.ten_lop,
                       lh.mo_ta as mo_ta_lop,
                       lh.so_luong_toi_da,
                       lh.trang_thai as trang_thai_lop,
                       kh.id as id_khoa_hoc,
                       kh.ten_khoa_hoc,
                       kh.gia,
                       kh.hinh_anh
                FROM dang_ky dk
                INNER JOIN lop_hoc lh ON dk.id_lop = lh.id
                INNER JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                WHERE dk.id_hoc_sinh = :id_hoc_sinh
                ORDER BY dk.ngay_dang_ky DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_hoc_sinh', $id_hoc_sinh, PDO::PARAM_INT);
        $stmt->execute();
        $lopHocs = $stmt->fetchAll();
        
        // Lấy thông tin ca học và phòng học cho mỗi lớp
        foreach ($lopHocs as &$lop) {
            $sqlCaHoc = "SELECT ch.id as id_ca_hoc,
                                ch.thu_trong_tuan,
                                cmd.ten_ca,
                                cmd.gio_bat_dau,
                                cmd.gio_ket_thuc,
                                ph.ten_phong,
                                ph.suc_chua,
                                nd.ho_ten as ten_giang_vien
                         FROM ca_hoc ch
                         LEFT JOIN ca_mac_dinh cmd ON ch.id_ca = cmd.id
                         LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                         LEFT JOIN nguoi_dung nd ON ch.id_giang_vien = nd.id
                         WHERE ch.id_lop = :id_lop
                         ORDER BY ch.thu_trong_tuan, cmd.gio_bat_dau";
            
            $stmtCaHoc = $this->conn->prepare($sqlCaHoc);
            $stmtCaHoc->bindValue(':id_lop', $lop['id_lop'], PDO::PARAM_INT);
            $stmtCaHoc->execute();
            $lop['ca_hoc'] = $stmtCaHoc->fetchAll();
        }
        
        return $lopHocs;
    }

    // Lấy thông tin chi tiết lớp học của một giảng viên cụ thể (dùng trong admin)
    public function getLopHocDetailByGiangVien($id_giang_vien)
    {
        $sql = "SELECT DISTINCT
                       lh.id as id_lop,
                       lh.ten_lop,
                       lh.mo_ta as mo_ta_lop,
                       lh.so_luong_toi_da,
                       lh.trang_thai as trang_thai_lop,
                       kh.id as id_khoa_hoc,
                       kh.ten_khoa_hoc,
                       kh.gia,
                       kh.hinh_anh
                FROM ca_hoc ch
                INNER JOIN lop_hoc lh ON ch.id_lop = lh.id
                INNER JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                WHERE ch.id_giang_vien = :id_giang_vien
                ORDER BY lh.ten_lop";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_giang_vien', $id_giang_vien, PDO::PARAM_INT);
        $stmt->execute();
        $lopHocs = $stmt->fetchAll();
        
        // Lấy thông tin ca học cho mỗi lớp
        foreach ($lopHocs as &$lop) {
            $sqlCaHoc = "SELECT ch.id as id_ca_hoc,
                                ch.thu_trong_tuan,
                                cmd.ten_ca,
                                cmd.gio_bat_dau,
                                cmd.gio_ket_thuc,
                                ph.ten_phong,
                                ph.suc_chua
                         FROM ca_hoc ch
                         LEFT JOIN ca_mac_dinh cmd ON ch.id_ca = cmd.id
                         LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                         WHERE ch.id_lop = :id_lop
                         AND ch.id_giang_vien = :id_giang_vien
                         ORDER BY ch.thu_trong_tuan, cmd.gio_bat_dau";
            
            $stmtCaHoc = $this->conn->prepare($sqlCaHoc);
            $stmtCaHoc->bindValue(':id_lop', $lop['id_lop'], PDO::PARAM_INT);
            $stmtCaHoc->bindValue(':id_giang_vien', $id_giang_vien, PDO::PARAM_INT);
            $stmtCaHoc->execute();
            $lop['ca_hoc'] = $stmtCaHoc->fetchAll();
        }
        
        return $lopHocs;
    }

    // Xóa lớp học
    public function deleteLopHoc($id)
    {
        // Kiểm tra xem lớp học có đang được sử dụng trong đăng ký không
        $sqlCheck = "SELECT COUNT(*) as total FROM dang_ky WHERE id_lop = :id";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        $result = $stmtCheck->fetch();
        
        if ($result['total'] > 0) {
            return false; // Không thể xóa vì đang có đăng ký
        }

        $sql = "DELETE FROM lop_hoc WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===========================================
    //  QUẢN LÝ ĐĂNG KÝ
    // ===========================================

    // Lấy danh sách đăng ký
    public function getDangKy($page = 1, $limit = 10, $search = '', $id_lop = '', $trang_thai = '')
    {
        $offset = ($page - 1) * $limit;
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT dk.*, 
                       nd.ho_ten as ten_hoc_sinh, nd.email as email_hoc_sinh, nd.so_dien_thoai,
                       lh.ten_lop, 
                       kh.ten_khoa_hoc
                FROM dang_ky dk 
                LEFT JOIN nguoi_dung nd ON dk.id_hoc_sinh = nd.id 
                LEFT JOIN lop_hoc lh ON dk.id_lop = lh.id 
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nd.ho_ten LIKE :search OR nd.email LIKE :search OR lh.ten_lop LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_lop)) {
            $sql .= " AND dk.id_lop = :id_lop";
            $params[':id_lop'] = $id_lop;
        }

        if (!empty($trang_thai)) {
            $sql .= " AND dk.trang_thai = :trang_thai";
            $params[':trang_thai'] = $trang_thai;
        }

        $sql .= " ORDER BY dk.ngay_dang_ky DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số đăng ký
    public function countDangKy($search = '', $id_lop = '', $trang_thai = '')
    {
        $sql = "SELECT COUNT(*) as total 
                FROM dang_ky dk 
                LEFT JOIN nguoi_dung nd ON dk.id_hoc_sinh = nd.id 
                LEFT JOIN lop_hoc lh ON dk.id_lop = lh.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nd.ho_ten LIKE :search OR nd.email LIKE :search OR lh.ten_lop LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_lop)) {
            $sql .= " AND dk.id_lop = :id_lop";
            $params[':id_lop'] = $id_lop;
        }

        if (!empty($trang_thai)) {
            $sql .= " AND dk.trang_thai = :trang_thai";
            $params[':trang_thai'] = $trang_thai;
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin một đăng ký theo ID
    public function getDangKyById($id)
    {
        $sql = "SELECT dk.*, 
                       nd.ho_ten as ten_hoc_sinh, nd.email as email_hoc_sinh, nd.so_dien_thoai, nd.dia_chi,
                       lh.ten_lop, lh.so_luong_toi_da,
                       kh.ten_khoa_hoc, kh.gia
                FROM dang_ky dk 
                LEFT JOIN nguoi_dung nd ON dk.id_hoc_sinh = nd.id 
                LEFT JOIN lop_hoc lh ON dk.id_lop = lh.id 
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id 
                WHERE dk.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Cập nhật đăng ký
    public function updateDangKy($id, $data)
    {
        $sql = "UPDATE dang_ky 
                SET trang_thai = :trang_thai 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':trang_thai', $data['trang_thai'], PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Xóa đăng ký
    public function deleteDangKy($id)
    {
        $sql = "DELETE FROM dang_ky WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Thống kê tổng quan
    public function getThongKe()
    {
        $stats = [];
        
        // Tổng số khóa học
        $sql = "SELECT COUNT(*) as total FROM khoa_hoc";
        $stmt = $this->conn->query($sql);
        $stats['tong_khoa_hoc'] = $stmt->fetch()['total'];
        
        // Tổng số học sinh
        $sql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE vai_tro = 'hoc_sinh'";
        $stmt = $this->conn->query($sql);
        $stats['tong_hoc_sinh'] = $stmt->fetch()['total'];
        
        // Tổng số giảng viên
        $sql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE vai_tro = 'giang_vien'";
        $stmt = $this->conn->query($sql);
        $stats['tong_giang_vien'] = $stmt->fetch()['total'];
        
        // Tổng số đăng ký
        $sql = "SELECT COUNT(*) as total FROM dang_ky";
        $stmt = $this->conn->query($sql);
        $stats['tong_dang_ky'] = $stmt->fetch()['total'];
        
        // Tổng số đăng ký đã xác nhận
        $sql = "SELECT COUNT(*) as total FROM dang_ky WHERE trang_thai = 'Đã xác nhận'";
        $stmt = $this->conn->query($sql);
        $stats['dang_ky_da_xac_nhan'] = $stmt->fetch()['total'];
        
        // Tổng doanh thu
        $sql = "SELECT COALESCE(SUM(so_tien), 0) as total FROM thanh_toan WHERE trang_thai = 'Thành công'";
        $stmt = $this->conn->query($sql);
        $stats['tong_doanh_thu'] = $stmt->fetch()['total'];
        
        // Tổng số lớp học
        $sql = "SELECT COUNT(*) as total FROM lop_hoc";
        $stmt = $this->conn->query($sql);
        $stats['tong_lop_hoc'] = $stmt->fetch()['total'];
        
        // Tổng số danh mục
        $sql = "SELECT COUNT(*) as total FROM danh_muc";
        $stmt = $this->conn->query($sql);
        $stats['tong_danh_muc'] = $stmt->fetch()['total'];
        
        return $stats;
    }

    // Lấy danh sách đăng ký mới nhất
    public function getDangKyMoiNhat($limit = 5)
    {
        $sql = "SELECT dk.*, nd.ho_ten, nd.email, lh.ten_lop, kh.ten_khoa_hoc 
                FROM dang_ky dk
                LEFT JOIN nguoi_dung nd ON dk.id_hoc_sinh = nd.id
                LEFT JOIN lop_hoc lh ON dk.id_lop = lh.id
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                ORDER BY dk.ngay_dang_ky DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy danh sách thanh toán mới nhất
    public function getThanhToanMoiNhat($limit = 5)
    {
        $sql = "SELECT tt.*, nd.ho_ten, nd.email
                FROM thanh_toan tt
                LEFT JOIN nguoi_dung nd ON tt.id_hoc_sinh = nd.id
                ORDER BY tt.ngay_thanh_toan DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đăng nhập - Kiểm tra email và mật khẩu
    public function login($email, $password, $vai_tro = 'admin')
    {
        $sql = "SELECT * FROM nguoi_dung 
                WHERE email = :email 
                AND vai_tro = :vai_tro 
                AND trang_thai = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':vai_tro', $vai_tro);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            // Kiểm tra mật khẩu (có thể là plain text hoặc đã hash)
            // Nếu mật khẩu trong DB là plain text
            if ($user['mat_khau'] === $password) {
                return $user;
            }
            // Nếu mật khẩu đã được hash bằng password_verify
            if (password_verify($password, $user['mat_khau'])) {
                return $user;
            }
        }

        return false;
    }

    // Đăng nhập theo email (không kiểm tra vai trò - dùng cho nhiều vai trò)
    public function loginByEmail($email, $password)
    {
        $sql = "SELECT * FROM nguoi_dung 
                WHERE email = :email 
                AND trang_thai = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            // Kiểm tra mật khẩu (có thể là plain text hoặc đã hash)
            // Nếu mật khẩu trong DB là plain text
            if ($user['mat_khau'] === $password) {
                return $user;
            }
            // Nếu mật khẩu đã được hash bằng password_verify
            if (password_verify($password, $user['mat_khau'])) {
                return $user;
            }
        }
        return false;
    }

    // Lấy thông tin người dùng theo ID
    public function getUserById($id)
    {
        $sql = "SELECT * FROM nguoi_dung WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // ===========================================
    //  QUẢN LÝ HỌC SINH
    // ===========================================

    // Lấy danh sách học sinh
    public function getHocSinh($page = 1, $limit = 10, $search = '')
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT nd.* FROM nguoi_dung nd WHERE nd.vai_tro = 'hoc_sinh'";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nd.ma_nguoi_dung LIKE :search OR nd.ho_ten LIKE :search OR nd.email LIKE :search OR nd.so_dien_thoai LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY nd.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số học sinh
    public function countHocSinh($search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE vai_tro = 'hoc_sinh'";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ma_nguoi_dung LIKE :search OR ho_ten LIKE :search OR email LIKE :search OR so_dien_thoai LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin một học sinh theo ID
    public function getHocSinhById($id)
    {
        $sql = "SELECT * FROM nguoi_dung WHERE id = :id AND vai_tro = 'hoc_sinh'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy danh sách học sinh đã đăng ký các lớp của một giảng viên
    public function getHocSinhByGiangVien($id_giang_vien, $page = 1, $limit = 10, $search = '')
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT DISTINCT nd.id as id_hoc_sinh, nd.ma_nguoi_dung, nd.ho_ten, nd.email, nd.so_dien_thoai, nd.dia_chi, nd.trang_thai, nd.ngay_tao,
                       GROUP_CONCAT(DISTINCT CONCAT(lh.ten_lop, ' (', kh.ten_khoa_hoc, ')') SEPARATOR '; ') as cac_lop_da_dang_ky
                FROM nguoi_dung nd
                INNER JOIN dang_ky dk ON nd.id = dk.id_hoc_sinh
                INNER JOIN lop_hoc lh ON dk.id_lop = lh.id
                INNER JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                INNER JOIN ca_hoc ch ON lh.id = ch.id_lop
                WHERE ch.id_giang_vien = :id_giang_vien
                  AND dk.trang_thai = 'Đã xác nhận'"; // Chỉ lấy học sinh đã xác nhận
        $params = [':id_giang_vien' => $id_giang_vien];

        if (!empty($search)) {
            $sql .= " AND (nd.ma_nguoi_dung LIKE :search OR nd.ho_ten LIKE :search OR nd.email LIKE :search OR nd.so_dien_thoai LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " GROUP BY nd.id ORDER BY nd.ho_ten ASC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số học sinh đã đăng ký các lớp của một giảng viên
    public function countHocSinhByGiangVien($id_giang_vien, $search = '')
    {
        $sql = "SELECT COUNT(DISTINCT nd.id) as total
                FROM nguoi_dung nd
                INNER JOIN dang_ky dk ON nd.id = dk.id_hoc_sinh
                INNER JOIN lop_hoc lh ON dk.id_lop = lh.id
                INNER JOIN ca_hoc ch ON lh.id = ch.id_lop
                WHERE ch.id_giang_vien = :id_giang_vien
                  AND dk.trang_thai = 'Đã xác nhận'";
        $params = [':id_giang_vien' => $id_giang_vien];

        if (!empty($search)) {
            $sql .= " AND (nd.ma_nguoi_dung LIKE :search OR nd.ho_ten LIKE :search OR nd.email LIKE :search OR nd.so_dien_thoai LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin chi tiết lớp học của một học sinh mà giảng viên đang dạy
    public function getLopHocDetailByHocSinhAndGiangVien($id_hoc_sinh, $id_giang_vien)
    {
        $sql = "SELECT dk.id as id_dang_ky,
                       dk.trang_thai as trang_thai_dang_ky,
                       dk.ngay_dang_ky,
                       lh.id as id_lop,
                       lh.ten_lop,
                       lh.mo_ta as mo_ta_lop,
                       lh.so_luong_toi_da,
                       lh.trang_thai as trang_thai_lop,
                       kh.id as id_khoa_hoc,
                       kh.ten_khoa_hoc,
                       kh.gia,
                       kh.hinh_anh
                FROM dang_ky dk
                INNER JOIN lop_hoc lh ON dk.id_lop = lh.id
                INNER JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                INNER JOIN ca_hoc ch ON lh.id = ch.id_lop
                WHERE dk.id_hoc_sinh = :id_hoc_sinh
                  AND ch.id_giang_vien = :id_giang_vien
                ORDER BY dk.ngay_dang_ky DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_hoc_sinh', $id_hoc_sinh, PDO::PARAM_INT);
        $stmt->bindValue(':id_giang_vien', $id_giang_vien, PDO::PARAM_INT);
        $stmt->execute();
        $lopHocs = $stmt->fetchAll();
        
        // Lấy thông tin ca học và phòng học cho mỗi lớp
        foreach ($lopHocs as &$lop) {
            $sqlCaHoc = "SELECT ch.id as id_ca_hoc,
                                ch.thu_trong_tuan,
                                cmd.ten_ca,
                                cmd.gio_bat_dau,
                                cmd.gio_ket_thuc,
                                ph.ten_phong,
                                ph.suc_chua,
                                nd.ho_ten as ten_giang_vien
                         FROM ca_hoc ch
                         LEFT JOIN ca_mac_dinh cmd ON ch.id_ca = cmd.id
                         LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                         LEFT JOIN nguoi_dung nd ON ch.id_giang_vien = nd.id
                         WHERE ch.id_lop = :id_lop
                           AND ch.id_giang_vien = :id_giang_vien
                         ORDER BY ch.thu_trong_tuan, cmd.gio_bat_dau";
            
            $stmtCaHoc = $this->conn->prepare($sqlCaHoc);
            $stmtCaHoc->bindValue(':id_lop', $lop['id_lop'], PDO::PARAM_INT);
            $stmtCaHoc->bindValue(':id_giang_vien', $id_giang_vien, PDO::PARAM_INT);
            $stmtCaHoc->execute();
            $lop['ca_hoc'] = $stmtCaHoc->fetchAll();
        }
        
        return $lopHocs;
    }

    // Thêm học sinh mới
    public function addHocSinh($data)
    {
        try {
            $sql = "INSERT INTO nguoi_dung (ho_ten, email, mat_khau, so_dien_thoai, dia_chi, vai_tro, trang_thai, ngay_tao) 
                    VALUES (:ho_ten, :email, :mat_khau, :so_dien_thoai, :dia_chi, 'hoc_sinh', :trang_thai, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':ho_ten', $data['ho_ten']);
            $stmt->bindValue(':email', $data['email']);
            $stmt->bindValue(':mat_khau', $data['mat_khau']);
            $stmt->bindValue(':so_dien_thoai', $data['so_dien_thoai'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':dia_chi', $data['dia_chi'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Lấy ID vừa tạo và thêm vào bảng nguoi_dung_vai_tro
                $id_nguoi_dung = $this->conn->lastInsertId();
                
                // Thêm vai trò vào bảng nguoi_dung_vai_tro
                $sql_vai_tro = "INSERT INTO nguoi_dung_vai_tro (id_nguoi_dung, vai_tro) 
                                VALUES (:id_nguoi_dung, 'hoc_sinh') 
                                ON DUPLICATE KEY UPDATE vai_tro = 'hoc_sinh'";
                $stmt_vai_tro = $this->conn->prepare($sql_vai_tro);
                $stmt_vai_tro->bindValue(':id_nguoi_dung', $id_nguoi_dung, PDO::PARAM_INT);
                $stmt_vai_tro->execute();
                
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Lỗi thêm học sinh: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật học sinh
    public function updateHocSinh($id, $data)
    {
        $sql = "UPDATE nguoi_dung 
                SET ho_ten = :ho_ten, 
                    email = :email, 
                    so_dien_thoai = :so_dien_thoai, 
                    dia_chi = :dia_chi, 
                    trang_thai = :trang_thai";
        
        // Chỉ cập nhật mật khẩu nếu có
        if (!empty($data['mat_khau'])) {
            $sql .= ", mat_khau = :mat_khau";
        }
        
        $sql .= " WHERE id = :id AND vai_tro = 'hoc_sinh'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':ho_ten', $data['ho_ten']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':so_dien_thoai', $data['so_dien_thoai'] ?? null);
        $stmt->bindValue(':dia_chi', $data['dia_chi'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
        
        if (!empty($data['mat_khau'])) {
            $stmt->bindValue(':mat_khau', $data['mat_khau']);
        }
        
        return $stmt->execute();
    }

    // Xóa học sinh
    public function deleteHocSinh($id)
    {
        $sql = "DELETE FROM nguoi_dung WHERE id = :id AND vai_tro = 'hoc_sinh'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Kiểm tra email đã tồn tại chưa (theo vai trò)
    public function checkEmailExistsByRole($email, $vai_tro = 'hoc_sinh')
    {
        $sql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE email = :email AND vai_tro = :vai_tro";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':vai_tro', $vai_tro);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    // Kiểm tra email đã tồn tại chưa (trừ ID hiện tại)
    public function checkEmailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE email = :email";
        $params = [':email' => $email];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    // ===========================================
    //  QUẢN LÝ BÌNH LUẬN
    // ===========================================

    // Lấy danh sách bình luận
    public function getBinhLuan($page = 1, $limit = 10, $search = '', $id_khoa_hoc = '', $trang_thai = '')
    {
        $offset = ($page - 1) * $limit;
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT bl.*, 
                       kh.ten_khoa_hoc,
                       nd.ho_ten as ten_hoc_sinh, nd.email as email_hoc_sinh
                FROM binh_luan bl 
                LEFT JOIN khoa_hoc kh ON bl.id_khoa_hoc = kh.id 
                LEFT JOIN nguoi_dung nd ON bl.id_hoc_sinh = nd.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (bl.noi_dung LIKE :search OR nd.ho_ten LIKE :search OR kh.ten_khoa_hoc LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_khoa_hoc)) {
            $sql .= " AND bl.id_khoa_hoc = :id_khoa_hoc";
            $params[':id_khoa_hoc'] = $id_khoa_hoc;
        }

        if (!empty($trang_thai)) {
            $sql .= " AND bl.trang_thai = :trang_thai";
            $params[':trang_thai'] = $trang_thai;
        }

        $sql .= " ORDER BY bl.ngay_tao DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số bình luận
    public function countBinhLuan($search = '', $id_khoa_hoc = '', $trang_thai = '')
    {
        $sql = "SELECT COUNT(*) as total 
                FROM binh_luan bl 
                LEFT JOIN khoa_hoc kh ON bl.id_khoa_hoc = kh.id 
                LEFT JOIN nguoi_dung nd ON bl.id_hoc_sinh = nd.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (bl.noi_dung LIKE :search OR nd.ho_ten LIKE :search OR kh.ten_khoa_hoc LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_khoa_hoc)) {
            $sql .= " AND bl.id_khoa_hoc = :id_khoa_hoc";
            $params[':id_khoa_hoc'] = $id_khoa_hoc;
        }

        if (!empty($trang_thai)) {
            $sql .= " AND bl.trang_thai = :trang_thai";
            $params[':trang_thai'] = $trang_thai;
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin một bình luận theo ID
    public function getBinhLuanById($id)
    {
        $sql = "SELECT bl.*, 
                       kh.ten_khoa_hoc,
                       nd.ho_ten as ten_hoc_sinh, nd.email as email_hoc_sinh
                FROM binh_luan bl 
                LEFT JOIN khoa_hoc kh ON bl.id_khoa_hoc = kh.id 
                LEFT JOIN nguoi_dung nd ON bl.id_hoc_sinh = nd.id 
                WHERE bl.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Cập nhật bình luận
    public function updateBinhLuan($id, $data)
    {
        $sql = "UPDATE binh_luan 
                SET noi_dung = :noi_dung, 
                    danh_gia = :danh_gia,
                    trang_thai = :trang_thai 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':noi_dung', $data['noi_dung']);
        $stmt->bindValue(':danh_gia', $data['danh_gia'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':trang_thai', $data['trang_thai'], PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Xóa bình luận
    public function deleteBinhLuan($id)
    {
        $sql = "DELETE FROM binh_luan WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===========================================
    //  QUẢN LÝ PHÒNG HỌC
    // ===========================================

    // Lấy danh sách phòng học
    public function getPhongHoc($page = 1, $limit = 10, $search = '', $trang_thai = '')
    {
        $offset = ($page - 1) * $limit;
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT * FROM phong_hoc WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ten_phong LIKE :search OR mo_ta LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($trang_thai)) {
            $sql .= " AND trang_thai = :trang_thai";
            $params[':trang_thai'] = $trang_thai;
        }

        $sql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số phòng học
    public function countPhongHoc($search = '', $trang_thai = '')
    {
        $sql = "SELECT COUNT(*) as total FROM phong_hoc WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ten_phong LIKE :search OR mo_ta LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($trang_thai)) {
            $sql .= " AND trang_thai = :trang_thai";
            $params[':trang_thai'] = $trang_thai;
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin một phòng học theo ID
    public function getPhongHocById($id)
    {
        $sql = "SELECT * FROM phong_hoc WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Thêm phòng học mới
    public function addPhongHoc($data)
    {
        $sql = "INSERT INTO phong_hoc (ten_phong, suc_chua, mo_ta, trang_thai) 
                VALUES (:ten_phong, :suc_chua, :mo_ta, :trang_thai)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ten_phong', $data['ten_phong']);
        $stmt->bindValue(':suc_chua', $data['suc_chua'] ?? 30, PDO::PARAM_INT);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 'Sử dụng', PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Cập nhật phòng học
    public function updatePhongHoc($id, $data)
    {
        $sql = "UPDATE phong_hoc 
                SET ten_phong = :ten_phong, 
                    suc_chua = :suc_chua, 
                    mo_ta = :mo_ta,
                    trang_thai = :trang_thai 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':ten_phong', $data['ten_phong']);
        $stmt->bindValue(':suc_chua', $data['suc_chua'] ?? 30, PDO::PARAM_INT);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 'Sử dụng', PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Xóa phòng học
    public function deletePhongHoc($id)
    {
        // Kiểm tra xem phòng học có đang được sử dụng trong ca học không
        $sqlCheck = "SELECT COUNT(*) as total FROM ca_hoc WHERE id_phong = :id";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bindValue(':id', $id, PDO::PARAM_INT);
        $stmtCheck->execute();
        $result = $stmtCheck->fetch();
        
        if ($result['total'] > 0) {
            return false; // Không thể xóa vì đang được sử dụng
        }

        $sql = "DELETE FROM phong_hoc WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Kiểm tra tên phòng học đã tồn tại chưa (trừ ID hiện tại)
    public function checkPhongHocExists($ten_phong, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as total FROM phong_hoc WHERE ten_phong = :ten_phong";
        $params = [':ten_phong' => $ten_phong];
        
        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    // Kiểm tra người dùng có quyền không (luôn trả về true vì đã bỏ phân quyền)
    public function hasPermission($id_nguoi_dung, $ten_quyen)
    {
        return true;
    }

    // Lấy danh sách admin (có quyền)
    public function getAdmin($page = 1, $limit = 10, $search = '')
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT nd.* FROM nguoi_dung nd WHERE nd.vai_tro = 'admin'";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nd.ho_ten LIKE :search OR nd.email LIKE :search OR nd.so_dien_thoai LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY nd.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số admin
    public function countAdmin($search = '')
    {
        $sql = "SELECT COUNT(DISTINCT nd.id) as total 
                FROM nguoi_dung nd
                WHERE nd.vai_tro = 'admin'";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nd.ho_ten LIKE :search OR nd.email LIKE :search OR nd.so_dien_thoai LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin một người dùng theo ID
    public function getNguoiDungById($id)
    {
        $sql = "SELECT * FROM nguoi_dung WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy danh sách vai trò của một người dùng
    public function getVaiTroByNguoiDung($id_nguoi_dung)
    {
        $sql = "SELECT vai_tro FROM nguoi_dung_vai_tro WHERE id_nguoi_dung = :id_nguoi_dung";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_nguoi_dung', $id_nguoi_dung, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        return array_column($results, 'vai_tro');
    }

    // Kiểm tra người dùng có vai trò không
    public function hasVaiTro($id_nguoi_dung, $vai_tro)
    {
        $sql = "SELECT COUNT(*) as total FROM nguoi_dung_vai_tro 
                WHERE id_nguoi_dung = :id_nguoi_dung AND vai_tro = :vai_tro";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_nguoi_dung', $id_nguoi_dung, PDO::PARAM_INT);
        $stmt->bindValue(':vai_tro', $vai_tro);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] > 0;
    }

    // ===========================================
    //  QUẢN LÝ TÀI KHOẢN NGƯỜI DÙNG
    // ===========================================

    // Lấy danh sách tất cả tài khoản
    public function getAllTaiKhoan($page = 1, $limit = 10, $search = '', $trang_thai = '')
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT nd.*, 
                       GROUP_CONCAT(DISTINCT ndvt.vai_tro SEPARATOR ', ') as danh_sach_vai_tro
                FROM nguoi_dung nd
                LEFT JOIN nguoi_dung_vai_tro ndvt ON nd.id = ndvt.id_nguoi_dung
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nd.ho_ten LIKE :search OR nd.email LIKE :search OR nd.so_dien_thoai LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if ($trang_thai !== '') {
            $sql .= " AND nd.trang_thai = :trang_thai";
            $params[':trang_thai'] = (int)$trang_thai;
        }

        $sql .= " GROUP BY nd.id ORDER BY nd.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        foreach ($results as &$result) {
            $result['vai_tro_list'] = !empty($result['danh_sach_vai_tro']) 
                ? explode(', ', $result['danh_sach_vai_tro']) 
                : [];
        }
        
        return $results;
    }

    // Đếm tổng số tài khoản
    public function countAllTaiKhoan($search = '', $trang_thai = '')
    {
        $sql = "SELECT COUNT(DISTINCT nd.id) as total 
                FROM nguoi_dung nd
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nd.ho_ten LIKE :search OR nd.email LIKE :search OR nd.so_dien_thoai LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if ($trang_thai !== '') {
            $sql .= " AND nd.trang_thai = :trang_thai";
            $params[':trang_thai'] = (int)$trang_thai;
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy thông tin tài khoản theo ID (đầy đủ)
    public function getTaiKhoanById($id)
    {
        $sql = "SELECT nd.*, 
                       GROUP_CONCAT(DISTINCT ndvt.vai_tro SEPARATOR ', ') as danh_sach_vai_tro
                FROM nguoi_dung nd
                LEFT JOIN nguoi_dung_vai_tro ndvt ON nd.id = ndvt.id_nguoi_dung
                WHERE nd.id = :id
                GROUP BY nd.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result) {
            $result['vai_tro_list'] = !empty($result['danh_sach_vai_tro']) 
                ? explode(', ', $result['danh_sach_vai_tro']) 
                : [];
        }
        
        return $result;
    }

    // Cập nhật thông tin tài khoản
    public function updateTaiKhoan($id, $data)
    {
        $sql = "UPDATE nguoi_dung 
                SET ho_ten = :ho_ten, 
                    email = :email, 
                    so_dien_thoai = :so_dien_thoai, 
                    dia_chi = :dia_chi,
                    trang_thai = :trang_thai";
        
        $params = [
            ':ho_ten' => $data['ho_ten'],
            ':email' => $data['email'],
            ':so_dien_thoai' => $data['so_dien_thoai'] ?? null,
            ':dia_chi' => $data['dia_chi'] ?? null,
            ':trang_thai' => $data['trang_thai'] ?? 1,
            ':id' => $id
        ];

        // Nếu có mật khẩu mới thì cập nhật
        if (!empty($data['mat_khau'])) {
            $sql .= ", mat_khau = :mat_khau";
            $params[':mat_khau'] = password_hash($data['mat_khau'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        return $stmt->execute();
    }

    // Toggle trạng thái tài khoản (ban/mở ban)
    public function toggleTaiKhoanStatus($id)
    {
        // Lấy trạng thái hiện tại
        $sql = "SELECT trang_thai FROM nguoi_dung WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if (!$result) {
            return false;
        }

        // Đảo ngược trạng thái (1 -> 0, 0 -> 1)
        $newStatus = $result['trang_thai'] == 1 ? 0 : 1;

        // Cập nhật
        $sql = "UPDATE nguoi_dung SET trang_thai = :trang_thai WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':trang_thai', $newStatus, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ===========================================
    //  YÊU CẦU ĐỔI LỊCH
    // ===========================================

    // Kiểm tra và tạo bảng yeu_cau_doi_lich nếu chưa tồn tại
    private function ensureYeuCauDoiLichTable()
    {
        try {
            // Kiểm tra bảng có tồn tại không
            $checkSql = "SHOW TABLES LIKE 'yeu_cau_doi_lich'";
            $stmt = $this->conn->query($checkSql);
            if ($stmt->rowCount() > 0) {
                return true; // Bảng đã tồn tại
            }
            
            // Tạo bảng nếu chưa tồn tại
            $createSql = "CREATE TABLE IF NOT EXISTS `yeu_cau_doi_lich` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `id_giang_vien` int(11) NOT NULL,
              `id_ca_hoc_cu` int(11) NOT NULL COMMENT 'ID ca học hiện tại cần đổi',
              `id_lop` int(11) NOT NULL,
              `thu_trong_tuan_moi` varchar(20) NOT NULL COMMENT 'Thứ trong tuần mới (Thứ 2, Thứ 3, ...)',
              `id_ca_moi` int(11) DEFAULT NULL COMMENT 'ID ca mặc định mới',
              `id_phong_moi` int(11) DEFAULT NULL COMMENT 'ID phòng học mới',
              `ngay_doi` date DEFAULT NULL COMMENT 'Ngày cụ thể cần đổi (nếu đổi một ngày cụ thể)',
              `ly_do` text DEFAULT NULL COMMENT 'Lý do đổi lịch',
              `trang_thai` enum('cho_duyet','da_duyet','tu_choi') DEFAULT 'cho_duyet',
              `ghi_chu_admin` text DEFAULT NULL COMMENT 'Ghi chú của admin khi duyệt/từ chối',
              `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
              `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `id_giang_vien` (`id_giang_vien`),
              KEY `id_ca_hoc_cu` (`id_ca_hoc_cu`),
              KEY `id_lop` (`id_lop`),
              KEY `trang_thai` (`trang_thai`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->conn->exec($createSql);
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi khi tạo bảng yeu_cau_doi_lich: " . $e->getMessage());
            return false;
        }
    }

    // Tạo yêu cầu đổi lịch
    public function taoYeuCauDoiLich($data)
    {
        // Đảm bảo bảng tồn tại
        $this->ensureYeuCauDoiLichTable();
        
        try {
            $sql = "INSERT INTO yeu_cau_doi_lich 
                    (id_giang_vien, id_ca_hoc_cu, id_lop, thu_trong_tuan_moi, id_ca_moi, id_phong_moi, ngay_doi, ly_do, trang_thai) 
                    VALUES (:id_giang_vien, :id_ca_hoc_cu, :id_lop, :thu_trong_tuan_moi, :id_ca_moi, :id_phong_moi, :ngay_doi, :ly_do, 'cho_duyet')";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_giang_vien', $data['id_giang_vien'], PDO::PARAM_INT);
            $stmt->bindValue(':id_ca_hoc_cu', $data['id_ca_hoc_cu'], PDO::PARAM_INT);
            $stmt->bindValue(':id_lop', $data['id_lop'], PDO::PARAM_INT);
            $stmt->bindValue(':thu_trong_tuan_moi', $data['thu_trong_tuan_moi']);
            $stmt->bindValue(':id_ca_moi', $data['id_ca_moi'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':id_phong_moi', $data['id_phong_moi'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':ngay_doi', $data['ngay_doi'] ?? null);
            $stmt->bindValue(':ly_do', $data['ly_do'] ?? null);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi khi tạo yêu cầu đổi lịch: " . $e->getMessage());
            throw new Exception("Có lỗi xảy ra khi tạo yêu cầu đổi lịch: " . $e->getMessage());
        }
    }

    // Lấy danh sách yêu cầu đổi lịch (cho admin)
    public function getYeuCauDoiLich($page = 1, $limit = 10, $trang_thai = '')
    {
        // Đảm bảo bảng tồn tại
        $this->ensureYeuCauDoiLichTable();
        $offset = ($page - 1) * $limit;
        $sql = "SELECT yc.*, 
                       nd.ho_ten as ten_giang_vien,
                       lh.ten_lop,
                       kh.ten_khoa_hoc,
                       ch_cu.thu_trong_tuan as thu_cu,
                       cmd_cu.ten_ca as ten_ca_cu,
                       cmd_cu.gio_bat_dau as gio_bat_dau_cu,
                       cmd_cu.gio_ket_thuc as gio_ket_thuc_cu,
                       ph_cu.ten_phong as ten_phong_cu,
                       cmd_moi.ten_ca as ten_ca_moi,
                       cmd_moi.gio_bat_dau as gio_bat_dau_moi,
                       cmd_moi.gio_ket_thuc as gio_ket_thuc_moi,
                       ph_moi.ten_phong as ten_phong_moi
                FROM yeu_cau_doi_lich yc
                LEFT JOIN nguoi_dung nd ON yc.id_giang_vien = nd.id
                LEFT JOIN lop_hoc lh ON yc.id_lop = lh.id
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                LEFT JOIN ca_hoc ch_cu ON yc.id_ca_hoc_cu = ch_cu.id
                LEFT JOIN ca_mac_dinh cmd_cu ON ch_cu.id_ca = cmd_cu.id
                LEFT JOIN phong_hoc ph_cu ON ch_cu.id_phong = ph_cu.id
                LEFT JOIN ca_mac_dinh cmd_moi ON yc.id_ca_moi = cmd_moi.id
                LEFT JOIN phong_hoc ph_moi ON yc.id_phong_moi = ph_moi.id
                WHERE 1=1";
        
        $params = [];
        if (!empty($trang_thai)) {
            $sql .= " AND yc.trang_thai = :trang_thai";
            $params[':trang_thai'] = $trang_thai;
        }
        
        $sql .= " ORDER BY yc.ngay_tao DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số yêu cầu đổi lịch
    public function countYeuCauDoiLich($trang_thai = '')
    {
        // Đảm bảo bảng tồn tại
        $this->ensureYeuCauDoiLichTable();
        $sql = "SELECT COUNT(*) as total FROM yeu_cau_doi_lich WHERE 1=1";
        $params = [];
        
        if (!empty($trang_thai)) {
            $sql .= " AND trang_thai = :trang_thai";
            $params[':trang_thai'] = $trang_thai;
        }
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // Lấy chi tiết yêu cầu đổi lịch
    public function getYeuCauDoiLichById($id)
    {
        // Đảm bảo bảng tồn tại
        $this->ensureYeuCauDoiLichTable();
        $sql = "SELECT yc.*, 
                       nd.ho_ten as ten_giang_vien,
                       lh.ten_lop,
                       kh.ten_khoa_hoc,
                       ch_cu.thu_trong_tuan as thu_cu,
                       cmd_cu.ten_ca as ten_ca_cu,
                       cmd_cu.gio_bat_dau as gio_bat_dau_cu,
                       cmd_cu.gio_ket_thuc as gio_ket_thuc_cu,
                       ph_cu.ten_phong as ten_phong_cu,
                       cmd_moi.ten_ca as ten_ca_moi,
                       cmd_moi.gio_bat_dau as gio_bat_dau_moi,
                       cmd_moi.gio_ket_thuc as gio_ket_thuc_moi,
                       ph_moi.ten_phong as ten_phong_moi
                FROM yeu_cau_doi_lich yc
                LEFT JOIN nguoi_dung nd ON yc.id_giang_vien = nd.id
                LEFT JOIN lop_hoc lh ON yc.id_lop = lh.id
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                LEFT JOIN ca_hoc ch_cu ON yc.id_ca_hoc_cu = ch_cu.id
                LEFT JOIN ca_mac_dinh cmd_cu ON ch_cu.id_ca = cmd_cu.id
                LEFT JOIN phong_hoc ph_cu ON ch_cu.id_phong = ph_cu.id
                LEFT JOIN ca_mac_dinh cmd_moi ON yc.id_ca_moi = cmd_moi.id
                LEFT JOIN phong_hoc ph_moi ON yc.id_phong_moi = ph_moi.id
                WHERE yc.id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy yêu cầu đổi lịch của giảng viên
    public function getYeuCauDoiLichByGiangVien($id_giang_vien, $page = 1, $limit = 10)
    {
        // Đảm bảo bảng tồn tại
        $this->ensureYeuCauDoiLichTable();
        $offset = ($page - 1) * $limit;
        $sql = "SELECT yc.*, 
                       lh.ten_lop,
                       kh.ten_khoa_hoc,
                       ch_cu.thu_trong_tuan as thu_cu,
                       cmd_cu.ten_ca as ten_ca_cu,
                       cmd_cu.gio_bat_dau as gio_bat_dau_cu,
                       cmd_cu.gio_ket_thuc as gio_ket_thuc_cu,
                       ph_cu.ten_phong as ten_phong_cu,
                       cmd_moi.ten_ca as ten_ca_moi,
                       cmd_moi.gio_bat_dau as gio_bat_dau_moi,
                       cmd_moi.gio_ket_thuc as gio_ket_thuc_moi,
                       ph_moi.ten_phong as ten_phong_moi
                FROM yeu_cau_doi_lich yc
                LEFT JOIN lop_hoc lh ON yc.id_lop = lh.id
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                LEFT JOIN ca_hoc ch_cu ON yc.id_ca_hoc_cu = ch_cu.id
                LEFT JOIN ca_mac_dinh cmd_cu ON ch_cu.id_ca = cmd_cu.id
                LEFT JOIN phong_hoc ph_cu ON ch_cu.id_phong = ph_cu.id
                LEFT JOIN ca_mac_dinh cmd_moi ON yc.id_ca_moi = cmd_moi.id
                LEFT JOIN phong_hoc ph_moi ON yc.id_phong_moi = ph_moi.id
                WHERE yc.id_giang_vien = :id_giang_vien
                ORDER BY yc.ngay_tao DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_giang_vien', $id_giang_vien, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Kiểm tra trùng lịch khi đổi lịch
    public function kiemTraTrungLich($id_giang_vien, $thu_trong_tuan, $id_ca, $id_phong, $ngay_doi = null, $id_ca_hoc_bo_qua = null)
    {
        // Nếu có ngày cụ thể, kiểm tra trùng trong ngày đó
        if ($ngay_doi) {
            // Tính thứ trong tuần của ngày đó
            $ngayObj = new DateTime($ngay_doi);
            $thuCuaNgay = (int)$ngayObj->format('N'); // 1 = Monday, 7 = Sunday
            $thuMap = [
                1 => 'Thứ 2',
                2 => 'Thứ 3',
                3 => 'Thứ 4',
                4 => 'Thứ 5',
                5 => 'Thứ 6',
                6 => 'Thứ 7',
                7 => 'Chủ nhật'
            ];
            $thuTrongTuan = $thuMap[$thuCuaNgay] ?? $thu_trong_tuan;
        } else {
            $thuTrongTuan = $thu_trong_tuan;
        }
        
        $sql = "SELECT ch.*, lh.ngay_bat_dau, lh.ngay_ket_thuc
                FROM ca_hoc ch
                INNER JOIN lop_hoc lh ON ch.id_lop = lh.id
                WHERE ch.id_giang_vien = :id_giang_vien
                AND ch.thu_trong_tuan = :thu_trong_tuan
                AND ch.id_ca = :id_ca
                AND ch.id_phong = :id_phong";
        
        $params = [
            ':id_giang_vien' => $id_giang_vien,
            ':thu_trong_tuan' => $thuTrongTuan,
            ':id_ca' => $id_ca,
            ':id_phong' => $id_phong
        ];
        
        // Bỏ qua ca học hiện tại nếu đang đổi
        if ($id_ca_hoc_bo_qua) {
            $sql .= " AND ch.id != :id_ca_hoc_bo_qua";
            $params[':id_ca_hoc_bo_qua'] = $id_ca_hoc_bo_qua;
        }
        
        // Nếu có ngày cụ thể, kiểm tra xem ngày đó có nằm trong khoảng thời gian của lớp không
        if ($ngay_doi) {
            $sql .= " AND lh.ngay_bat_dau <= :ngay_doi AND lh.ngay_ket_thuc >= :ngay_doi";
            $params[':ngay_doi'] = $ngay_doi;
        }
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Duyệt yêu cầu đổi lịch
    public function duyetYeuCauDoiLich($id, $ghi_chu = null)
    {
        // Đảm bảo bảng tồn tại
        $this->ensureYeuCauDoiLichTable();
        
        // Lấy thông tin yêu cầu
        $yeuCau = $this->getYeuCauDoiLichById($id);
        if (!$yeuCau || $yeuCau['trang_thai'] != 'cho_duyet') {
            return false;
        }
        
        // Kiểm tra trùng lịch
        $trungLich = $this->kiemTraTrungLich(
            $yeuCau['id_giang_vien'],
            $yeuCau['thu_trong_tuan_moi'],
            $yeuCau['id_ca_moi'],
            $yeuCau['id_phong_moi'],
            $yeuCau['ngay_doi'],
            $yeuCau['id_ca_hoc_cu']
        );
        
        if (!empty($trungLich)) {
            return ['error' => 'Lịch mới bị trùng với lịch khác!'];
        }
        
        // Bắt đầu transaction
        $this->conn->beginTransaction();
        
        try {
            // Cập nhật ca học cũ
            $sqlUpdate = "UPDATE ca_hoc 
                         SET thu_trong_tuan = :thu_moi, 
                             id_ca = :id_ca_moi, 
                             id_phong = :id_phong_moi
                         WHERE id = :id_ca_hoc_cu";
            $stmt = $this->conn->prepare($sqlUpdate);
            $stmt->bindValue(':thu_moi', $yeuCau['thu_trong_tuan_moi']);
            $stmt->bindValue(':id_ca_moi', $yeuCau['id_ca_moi'], PDO::PARAM_INT);
            $stmt->bindValue(':id_phong_moi', $yeuCau['id_phong_moi'], PDO::PARAM_INT);
            $stmt->bindValue(':id_ca_hoc_cu', $yeuCau['id_ca_hoc_cu'], PDO::PARAM_INT);
            $stmt->execute();
            
            // Cập nhật trạng thái yêu cầu
            $sqlYeuCau = "UPDATE yeu_cau_doi_lich 
                         SET trang_thai = 'da_duyet', 
                             ghi_chu_admin = :ghi_chu
                         WHERE id = :id";
            $stmt = $this->conn->prepare($sqlYeuCau);
            $stmt->bindValue(':ghi_chu', $ghi_chu);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['error' => $e->getMessage()];
        }
    }

    // Từ chối yêu cầu đổi lịch
    public function tuChoiYeuCauDoiLich($id, $ghi_chu = null)
    {
        // Đảm bảo bảng tồn tại
        $this->ensureYeuCauDoiLichTable();
        
        $sql = "UPDATE yeu_cau_doi_lich 
                SET trang_thai = 'tu_choi', 
                    ghi_chu_admin = :ghi_chu
                WHERE id = :id AND trang_thai = 'cho_duyet'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ghi_chu', $ghi_chu);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}


?>
