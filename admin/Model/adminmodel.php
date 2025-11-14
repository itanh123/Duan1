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
        $sql = "SELECT * FROM nguoi_dung WHERE vai_tro = 'giang_vien'";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ho_ten LIKE :search OR email LIKE :search OR sdt LIKE :search)";
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

    // Đếm tổng số giảng viên
    public function countGiangVien($search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE vai_tro = 'giang_vien'";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ho_ten LIKE :search OR email LIKE :search OR sdt LIKE :search)";
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
        $sql = "INSERT INTO nguoi_dung (ho_ten, email, mat_khau, sdt, dia_chi, vai_tro, trang_thai) 
                VALUES (:ho_ten, :email, :mat_khau, :sdt, :dia_chi, 'giang_vien', :trang_thai)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ho_ten', $data['ho_ten']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':mat_khau', $data['mat_khau']);
        $stmt->bindValue(':sdt', $data['sdt'] ?? null);
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
                    sdt = :sdt, 
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
        $stmt->bindValue(':sdt', $data['sdt'] ?? null);
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
                       cmd.ten_ca, cmd.gio_bat_dau, cmd.gio_ket_thuc
                FROM ca_hoc ch 
                LEFT JOIN lop_hoc lh ON ch.id_lop = lh.id 
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id 
                LEFT JOIN nguoi_dung nd ON ch.id_giang_vien = nd.id 
                LEFT JOIN ca_mac_dinh cmd ON ch.id_ca = cmd.id
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ch.phong_hoc LIKE :search OR ch.ghi_chu LIKE :search)";
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
        $sql = "SELECT COUNT(*) as total FROM ca_hoc WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (phong_hoc LIKE :search OR ghi_chu LIKE :search)";
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
                       cmd.ten_ca, cmd.gio_bat_dau, cmd.gio_ket_thuc
                FROM ca_hoc ch 
                LEFT JOIN lop_hoc lh ON ch.id_lop = lh.id 
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id 
                LEFT JOIN nguoi_dung nd ON ch.id_giang_vien = nd.id 
                LEFT JOIN ca_mac_dinh cmd ON ch.id_ca = cmd.id
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

    // Thêm ca học mới
    public function addCaHoc($data)
    {
        $sql = "INSERT INTO ca_hoc (id_lop, id_giang_vien, id_ca, thu_trong_tuan, phong_hoc, ghi_chu) 
                VALUES (:id_lop, :id_giang_vien, :id_ca, :thu_trong_tuan, :phong_hoc, :ghi_chu)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_lop', $data['id_lop'], PDO::PARAM_INT);
        $stmt->bindValue(':id_giang_vien', !empty($data['id_giang_vien']) ? $data['id_giang_vien'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':id_ca', $data['id_ca'], PDO::PARAM_INT);
        $stmt->bindValue(':thu_trong_tuan', $data['thu_trong_tuan'], PDO::PARAM_STR);
        $stmt->bindValue(':phong_hoc', $data['phong_hoc'] ?? null);
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
                    phong_hoc = :phong_hoc, 
                    ghi_chu = :ghi_chu 
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_lop', $data['id_lop'], PDO::PARAM_INT);
        $stmt->bindValue(':id_giang_vien', !empty($data['id_giang_vien']) ? $data['id_giang_vien'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':id_ca', $data['id_ca'], PDO::PARAM_INT);
        $stmt->bindValue(':thu_trong_tuan', $data['thu_trong_tuan'], PDO::PARAM_STR);
        $stmt->bindValue(':phong_hoc', $data['phong_hoc'] ?? null);
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
        $sql = "SELECT * FROM nguoi_dung WHERE vai_tro = 'hoc_sinh'";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ho_ten LIKE :search OR email LIKE :search OR sdt LIKE :search)";
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

    // Đếm tổng số học sinh
    public function countHocSinh($search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM nguoi_dung WHERE vai_tro = 'hoc_sinh'";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (ho_ten LIKE :search OR email LIKE :search OR sdt LIKE :search)";
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

    // Thêm học sinh mới
    public function addHocSinh($data)
    {
        $sql = "INSERT INTO nguoi_dung (ho_ten, email, mat_khau, sdt, dia_chi, vai_tro, trang_thai) 
                VALUES (:ho_ten, :email, :mat_khau, :sdt, :dia_chi, 'hoc_sinh', :trang_thai)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ho_ten', $data['ho_ten']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':mat_khau', $data['mat_khau']);
        $stmt->bindValue(':sdt', $data['sdt'] ?? null);
        $stmt->bindValue(':dia_chi', $data['dia_chi'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Cập nhật học sinh
    public function updateHocSinh($id, $data)
    {
        $sql = "UPDATE nguoi_dung 
                SET ho_ten = :ho_ten, 
                    email = :email, 
                    sdt = :sdt, 
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
        $stmt->bindValue(':sdt', $data['sdt'] ?? null);
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
}


?>
