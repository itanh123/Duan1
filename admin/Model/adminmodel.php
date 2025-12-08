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
    // Ẩn khóa học (thay vì xóa)
    public function deleteKhoaHoc($id)
    {
        // Thay vì xóa, chỉ cập nhật trạng thái thành 0 (ẩn)
        // Không xóa file hình ảnh để có thể khôi phục sau
        $sql = "UPDATE khoa_hoc SET trang_thai = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Hiện lại khóa học đã bị ẩn
    public function showKhoaHoc($id)
    {
        $sql = "UPDATE khoa_hoc SET trang_thai = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Toggle trạng thái khóa học (ẩn/hiện)
    public function toggleKhoaHocStatus($id)
    {
        // Lấy trạng thái hiện tại
        $sql = "SELECT trang_thai FROM khoa_hoc WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if (!$result) {
            return false;
        }
        
        // Đảo ngược trạng thái
        $newStatus = $result['trang_thai'] == 1 ? 0 : 1;
        
        $sql = "UPDATE khoa_hoc SET trang_thai = :trang_thai WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':trang_thai', $newStatus, PDO::PARAM_INT);
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

    // Ẩn danh mục (thay vì xóa)
    public function deleteDanhMuc($id)
    {
        // Thay vì xóa, chỉ cập nhật trạng thái thành 0 (ẩn)
        $sql = "UPDATE danh_muc SET trang_thai = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Hiện lại danh mục đã bị ẩn
    public function showDanhMuc($id)
    {
        $sql = "UPDATE danh_muc SET trang_thai = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Toggle trạng thái danh mục (ẩn/hiện)
    public function toggleDanhMucStatus($id)
    {
        // Lấy trạng thái hiện tại
        $sql = "SELECT trang_thai FROM danh_muc WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if (!$result) {
            return false;
        }
        
        // Đảo ngược trạng thái
        $newStatus = $result['trang_thai'] == 1 ? 0 : 1;
        
        $sql = "UPDATE danh_muc SET trang_thai = :trang_thai WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':trang_thai', $newStatus, PDO::PARAM_INT);
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
                WHERE nd.vai_tro = 'giang_vien' AND nd.trang_thai = 1";
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
        $sql = "SELECT COUNT(*) as total
                FROM nguoi_dung nd
                WHERE nd.vai_tro = 'giang_vien' AND nd.trang_thai = 1";
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
            // Tìm kiếm theo phòng, ghi chú, hoặc ngày học (format: YYYY-MM-DD hoặc DD/MM/YYYY)
            $sql .= " AND (ph.ten_phong LIKE :search OR ch.ghi_chu LIKE :search OR DATE_FORMAT(ch.ngay_hoc, '%d/%m/%Y') LIKE :search OR ch.ngay_hoc LIKE :search)";
            $params[':search'] = "%$search%";
        }

        if (!empty($id_lop)) {
            $sql .= " AND ch.id_lop = :id_lop";
            $params[':id_lop'] = $id_lop;
        }

        $sql .= " ORDER BY ch.ngay_hoc ASC, ch.id_lop, ch.thu_trong_tuan, cmd.gio_bat_dau LIMIT $limit OFFSET $offset";

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

    // Kiểm tra trùng ca học (cùng ca, cùng thứ/ngày thì phải khác giảng viên và khác phòng)
    public function checkTrungCaHoc($id_ca, $thu_trong_tuan, $id_giang_vien, $id_phong, $excludeId = null, $ngay_hoc = null)
    {
        // Kiểm tra trùng giảng viên (nếu có chọn giảng viên)
        if (!empty($id_giang_vien)) {
            $sql = "SELECT ch.*, lh.ten_lop, nd.ho_ten as ten_giang_vien, ph.ten_phong
                    FROM ca_hoc ch
                    LEFT JOIN lop_hoc lh ON ch.id_lop = lh.id
                    LEFT JOIN nguoi_dung nd ON ch.id_giang_vien = nd.id
                    LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id
                    WHERE ch.id_ca = :id_ca 
                    AND ch.id_giang_vien = :id_giang_vien";
            
            $params = [
                ':id_ca' => $id_ca,
                ':id_giang_vien' => $id_giang_vien
            ];
            
            // Nếu có ngày học, kiểm tra theo ngày học, nếu không thì kiểm tra theo thứ
            if (!empty($ngay_hoc)) {
                $sql .= " AND ch.ngay_hoc = :ngay_hoc";
                $params[':ngay_hoc'] = $ngay_hoc;
            } else {
                // Chỉ kiểm tra theo thứ nếu có thứ, và ngày học phải là NULL
                if (!empty($thu_trong_tuan)) {
                    $sql .= " AND ch.thu_trong_tuan = :thu_trong_tuan AND ch.ngay_hoc IS NULL";
                    $params[':thu_trong_tuan'] = $thu_trong_tuan;
                } else {
                    // Nếu không có cả thứ và ngày học, không thể kiểm tra
                    return ['trung' => false];
                }
            }
            
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
                    AND ch.id_phong = :id_phong";
            
            $params = [
                ':id_ca' => $id_ca,
                ':id_phong' => $id_phong
            ];
            
            // Nếu có ngày học, kiểm tra theo ngày học, nếu không thì kiểm tra theo thứ
            if (!empty($ngay_hoc)) {
                $sql .= " AND ch.ngay_hoc = :ngay_hoc";
                $params[':ngay_hoc'] = $ngay_hoc;
            } else {
                // Chỉ kiểm tra theo thứ nếu có thứ, và ngày học phải là NULL
                if (!empty($thu_trong_tuan)) {
                    $sql .= " AND ch.thu_trong_tuan = :thu_trong_tuan AND ch.ngay_hoc IS NULL";
                    $params[':thu_trong_tuan'] = $thu_trong_tuan;
                } else {
                    // Nếu không có cả thứ và ngày học, không thể kiểm tra
                    return ['trung' => false];
                }
            }
            
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

    // Lấy danh sách giảng viên bị trùng ca học (dùng cho AJAX)
    public function getGiangVienTrung($id_ca, $thu_trong_tuan, $ngay_hoc = null, $excludeId = null)
    {
        $sql = "SELECT DISTINCT ch.id_giang_vien
                FROM ca_hoc ch
                WHERE ch.id_ca = :id_ca";
        
        $params = [':id_ca' => $id_ca];
        
        // Nếu có ngày học, kiểm tra theo ngày học, nếu không thì kiểm tra theo thứ
        if (!empty($ngay_hoc)) {
            $sql .= " AND ch.ngay_hoc = :ngay_hoc";
            $params[':ngay_hoc'] = $ngay_hoc;
        } else {
            // Chỉ kiểm tra theo thứ nếu có thứ, và ngày học phải là NULL
            if (!empty($thu_trong_tuan)) {
                $sql .= " AND ch.thu_trong_tuan = :thu_trong_tuan AND ch.ngay_hoc IS NULL";
                $params[':thu_trong_tuan'] = $thu_trong_tuan;
            }
        }
        
        if ($excludeId) {
            $sql .= " AND ch.id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $sql .= " AND ch.id_giang_vien IS NOT NULL";
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $results = $stmt->fetchAll();
        return array_column($results, 'id_giang_vien');
    }

    // Lấy danh sách phòng học bị trùng ca học (dùng cho AJAX)
    public function getPhongHocTrung($id_ca, $thu_trong_tuan, $ngay_hoc = null, $excludeId = null)
    {
        $sql = "SELECT DISTINCT ch.id_phong
                FROM ca_hoc ch
                WHERE ch.id_ca = :id_ca";
        
        $params = [':id_ca' => $id_ca];
        
        // Nếu có ngày học, kiểm tra theo ngày học, nếu không thì kiểm tra theo thứ
        if (!empty($ngay_hoc)) {
            $sql .= " AND ch.ngay_hoc = :ngay_hoc";
            $params[':ngay_hoc'] = $ngay_hoc;
        } else {
            // Chỉ kiểm tra theo thứ nếu có thứ, và ngày học phải là NULL
            if (!empty($thu_trong_tuan)) {
                $sql .= " AND ch.thu_trong_tuan = :thu_trong_tuan AND ch.ngay_hoc IS NULL";
                $params[':thu_trong_tuan'] = $thu_trong_tuan;
            }
        }
        
        if ($excludeId) {
            $sql .= " AND ch.id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $sql .= " AND ch.id_phong IS NOT NULL";
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $results = $stmt->fetchAll();
        return array_column($results, 'id_phong');
    }

    // Thêm ca học mới
    public function addCaHoc($data)
    {
        $sql = "INSERT INTO ca_hoc (id_lop, id_giang_vien, id_ca, thu_trong_tuan, id_phong, ghi_chu, ngay_hoc) 
                VALUES (:id_lop, :id_giang_vien, :id_ca, :thu_trong_tuan, :id_phong, :ghi_chu, :ngay_hoc)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_lop', $data['id_lop'], PDO::PARAM_INT);
        $stmt->bindValue(':id_giang_vien', !empty($data['id_giang_vien']) ? $data['id_giang_vien'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':id_ca', $data['id_ca'], PDO::PARAM_INT);
        $stmt->bindValue(':thu_trong_tuan', !empty($data['thu_trong_tuan']) ? $data['thu_trong_tuan'] : null, PDO::PARAM_STR);
        $stmt->bindValue(':id_phong', $data['id_phong'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':ghi_chu', $data['ghi_chu'] ?? null);
        $ngay_hoc_value = !empty($data['ngay_hoc']) ? $data['ngay_hoc'] : null;
        $stmt->bindValue(':ngay_hoc', $ngay_hoc_value, $ngay_hoc_value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
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
                    ghi_chu = :ghi_chu,
                    ngay_hoc = :ngay_hoc
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':id_lop', $data['id_lop'], PDO::PARAM_INT);
        $stmt->bindValue(':id_giang_vien', !empty($data['id_giang_vien']) ? $data['id_giang_vien'] : null, PDO::PARAM_INT);
        $stmt->bindValue(':id_ca', $data['id_ca'], PDO::PARAM_INT);
        $stmt->bindValue(':thu_trong_tuan', !empty($data['thu_trong_tuan']) ? $data['thu_trong_tuan'] : null, PDO::PARAM_STR);
        $stmt->bindValue(':id_phong', $data['id_phong'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':ghi_chu', $data['ghi_chu'] ?? null);
        $ngay_hoc_value = !empty($data['ngay_hoc']) ? $data['ngay_hoc'] : null;
        $stmt->bindValue(':ngay_hoc', $ngay_hoc_value, $ngay_hoc_value === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
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
        // Validate ID giảng viên
        $id_giang_vien = (int)$id_giang_vien;
        if ($id_giang_vien <= 0) {
            return []; // Trả về mảng rỗng nếu ID không hợp lệ
        }
        
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
        
        // Query chính: Lấy lớp học của giảng viên (CHỈ lấy của giảng viên này)
        // Lấy TẤT CẢ lớp học có ca học của giảng viên, không cần ngày bắt đầu/kết thúc
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
                ORDER BY lh.ten_lop";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_giang_vien', $id_giang_vien, PDO::PARAM_INT);
        $stmt->execute();
        $lopHocs = $stmt->fetchAll();
        
        // Lấy thông tin ca học cho mỗi lớp (CHỈ lấy ca học của giảng viên này)
        foreach ($lopHocs as &$lop) {
            $sqlCaHoc = "                         SELECT ch.id as id_ca_hoc,
                                ch.thu_trong_tuan,
                                ch.ngay_hoc,
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
            
            // Nếu có filter ngày, tìm theo ngày học hoặc thứ trong tuần
            if ($filterThu) {
                $sqlCaHoc .= " AND (ch.ngay_hoc IS NULL AND ch.thu_trong_tuan = :thu_trong_tuan)";
            }
            
            $sqlCaHoc .= " ORDER BY COALESCE(ch.ngay_hoc, '9999-12-31'), ch.thu_trong_tuan, cmd.gio_bat_dau";
            
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
        // Validate ID giảng viên
        $id_giang_vien = (int)$id_giang_vien;
        if ($id_giang_vien <= 0) {
            return [];
        }
        
        // Lấy danh sách lớp học mà giảng viên đang dạy (lấy TẤT CẢ các lớp có ca học của giảng viên này)
        // Sử dụng DISTINCT để tránh trùng lặp khi một lớp có nhiều ca học
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
                ORDER BY kh.ten_khoa_hoc, lh.ten_lop";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_giang_vien', $id_giang_vien, PDO::PARAM_INT);
        $stmt->execute();
        $lopHocs = $stmt->fetchAll();
        
        // Lấy thông tin ca học cho mỗi lớp (CHỈ lấy ca học của giảng viên này)
        foreach ($lopHocs as &$lop) {
            $sqlCaHoc = "SELECT ch.id as id_ca_hoc,
                                ch.thu_trong_tuan,
                                ch.ngay_hoc,
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
                         ORDER BY COALESCE(ch.ngay_hoc, '9999-12-31'), ch.thu_trong_tuan, cmd.gio_bat_dau";
            
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
        
        // Tổng số yêu cầu đổi lịch chờ duyệt
        try {
            $this->ensureYeuCauDoiLichTable();
            $sql = "SELECT COUNT(*) as total FROM yeu_cau_doi_lich WHERE trang_thai = 'cho_duyet'";
            $stmt = $this->conn->query($sql);
            $stats['yeu_cau_doi_lich_cho_duyet'] = $stmt->fetch()['total'];
        } catch (Exception $e) {
            $stats['yeu_cau_doi_lich_cho_duyet'] = 0;
        }
        
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

    // Lấy thông tin thanh toán theo ID đăng ký
    public function getThanhToanByIdDangKy($id_dang_ky)
    {
        $sql = "SELECT tt.*, nd.ho_ten, nd.email, dk.vnp_TxnRef, dk.vnp_TransactionNo
                FROM thanh_toan tt
                LEFT JOIN nguoi_dung nd ON tt.id_hoc_sinh = nd.id
                LEFT JOIN dang_ky dk ON tt.id_dang_ky = dk.id
                WHERE tt.id_dang_ky = :id_dang_ky 
                AND tt.trang_thai = 'Thành công'
                AND tt.phuong_thuc = 'VNPAY'
                ORDER BY tt.ngay_thanh_toan DESC
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_dang_ky', $id_dang_ky, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy thông tin thanh toán theo ID
    public function getThanhToanById($id)
    {
        $sql = "SELECT tt.*, nd.ho_ten, nd.email, dk.vnp_TxnRef, dk.vnp_TransactionNo, dk.vnp_TransactionDate
                FROM thanh_toan tt
                LEFT JOIN nguoi_dung nd ON tt.id_hoc_sinh = nd.id
                LEFT JOIN dang_ky dk ON tt.id_dang_ky = dk.id
                WHERE tt.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lưu thông tin hoàn tiền
    public function saveHoanTien($id_thanh_toan, $vnp_RefundRef, $vnp_RefundTransactionNo, $so_tien_hoan, $ly_do, $trang_thai = 'Đang xử lý')
    {
        try {
            // Kiểm tra xem bảng thanh_toan có cột hoàn tiền không
            $checkRefundColumns = $this->conn->query("SHOW COLUMNS FROM thanh_toan LIKE 'ma_hoan_tien'");
            $hasRefundColumns = $checkRefundColumns && $checkRefundColumns->rowCount() > 0;
            
            if ($hasRefundColumns) {
                // Nếu có cột hoàn tiền, cập nhật
                $sql = "UPDATE thanh_toan 
                        SET ma_hoan_tien = :ma_hoan_tien,
                            ma_giao_dich_hoan_tien = :ma_giao_dich_hoan_tien,
                            so_tien_hoan = :so_tien_hoan,
                            ly_do_hoan_tien = :ly_do,
                            trang_thai_hoan_tien = :trang_thai,
                            ngay_hoan_tien = NOW()
                        WHERE id = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    ':id' => $id_thanh_toan,
                    ':ma_hoan_tien' => $vnp_RefundRef,
                    ':ma_giao_dich_hoan_tien' => $vnp_RefundTransactionNo,
                    ':so_tien_hoan' => $so_tien_hoan,
                    ':ly_do' => $ly_do,
                    ':trang_thai' => $trang_thai
                ]);
            } else {
                // Nếu không có cột, tạo bảng hoan_tien riêng
                $this->ensureHoanTienTable();
                
                $sql = "INSERT INTO hoan_tien (id_thanh_toan, ma_hoan_tien, ma_giao_dich_hoan_tien, so_tien_hoan, ly_do, trang_thai, ngay_tao)
                        VALUES (:id_thanh_toan, :ma_hoan_tien, :ma_giao_dich_hoan_tien, :so_tien_hoan, :ly_do, :trang_thai, NOW())
                        ON DUPLICATE KEY UPDATE
                        ma_giao_dich_hoan_tien = :ma_giao_dich_hoan_tien,
                        so_tien_hoan = :so_tien_hoan,
                        ly_do = :ly_do,
                        trang_thai = :trang_thai,
                        ngay_cap_nhat = NOW()";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    ':id_thanh_toan' => $id_thanh_toan,
                    ':ma_hoan_tien' => $vnp_RefundRef,
                    ':ma_giao_dich_hoan_tien' => $vnp_RefundTransactionNo,
                    ':so_tien_hoan' => $so_tien_hoan,
                    ':ly_do' => $ly_do,
                    ':trang_thai' => $trang_thai
                ]);
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi khi lưu thông tin hoàn tiền: " . $e->getMessage());
            return false;
        }
    }

    // Tạo bảng hoan_tien nếu chưa có
    private function ensureHoanTienTable()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS hoan_tien (
                id INT AUTO_INCREMENT PRIMARY KEY,
                id_thanh_toan INT NOT NULL,
                ma_hoan_tien VARCHAR(100) NOT NULL UNIQUE,
                ma_giao_dich_hoan_tien VARCHAR(100),
                so_tien_hoan DECIMAL(15,2) NOT NULL,
                ly_do TEXT,
                trang_thai VARCHAR(50) DEFAULT 'Đang xử lý',
                ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
                ngay_cap_nhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_id_thanh_toan (id_thanh_toan),
                INDEX idx_ma_hoan_tien (ma_hoan_tien)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            // Bảng đã tồn tại hoặc có lỗi
            error_log("Lỗi khi tạo bảng hoan_tien: " . $e->getMessage());
        }
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
    // Ẩn học sinh (thay vì xóa)
    public function deleteHocSinh($id)
    {
        // Thay vì xóa, chỉ cập nhật trạng thái thành 0 (ẩn)
        $sql = "UPDATE nguoi_dung SET trang_thai = 0 WHERE id = :id AND vai_tro = 'hoc_sinh'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Hiện lại học sinh đã bị ẩn
    public function showHocSinh($id)
    {
        $sql = "UPDATE nguoi_dung SET trang_thai = 1 WHERE id = :id AND vai_tro = 'hoc_sinh'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Toggle trạng thái học sinh (ẩn/hiện)
    public function toggleHocSinhStatus($id)
    {
        // Lấy trạng thái hiện tại
        $sql = "SELECT trang_thai FROM nguoi_dung WHERE id = :id AND vai_tro = 'hoc_sinh'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if (!$result) {
            return false;
        }
        
        // Đảo ngược trạng thái
        $newStatus = $result['trang_thai'] == 1 ? 0 : 1;
        
        $sql = "UPDATE nguoi_dung SET trang_thai = :trang_thai WHERE id = :id AND vai_tro = 'hoc_sinh'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':trang_thai', $newStatus, PDO::PARAM_INT);
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

    // ===========================================
    //  PHẢN HỒI BÌNH LUẬN
    // ===========================================

    // Kiểm tra và tạo bảng phan_hoi_binh_luan nếu chưa tồn tại
    private function ensurePhanHoiBinhLuanTable()
    {
        try {
            // Kiểm tra bảng có tồn tại không
            $checkSql = "SHOW TABLES LIKE 'phan_hoi_binh_luan'";
            $stmt = $this->conn->query($checkSql);
            if ($stmt->rowCount() > 0) {
                return true; // Bảng đã tồn tại
            }
            
            // Tạo bảng nếu chưa tồn tại
            $createSql = "CREATE TABLE IF NOT EXISTS `phan_hoi_binh_luan` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `id_binh_luan` int(11) NOT NULL COMMENT 'ID bình luận được trả lời',
              `id_admin` int(11) NOT NULL COMMENT 'ID admin trả lời',
              `noi_dung` text NOT NULL COMMENT 'Nội dung phản hồi',
              `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `id_binh_luan` (`id_binh_luan`),
              KEY `id_admin` (`id_admin`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            
            $this->conn->exec($createSql);
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi khi tạo bảng phan_hoi_binh_luan: " . $e->getMessage());
            return false;
        }
    }

    // Tạo phản hồi bình luận
    public function taoPhanHoiBinhLuan($id_binh_luan, $id_admin, $noi_dung)
    {
        $this->ensurePhanHoiBinhLuanTable();
        
        try {
            $sql = "INSERT INTO phan_hoi_binh_luan (id_binh_luan, id_admin, noi_dung) 
                    VALUES (:id_binh_luan, :id_admin, :noi_dung)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_binh_luan', $id_binh_luan, PDO::PARAM_INT);
            $stmt->bindValue(':id_admin', $id_admin, PDO::PARAM_INT);
            $stmt->bindValue(':noi_dung', $noi_dung);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi khi tạo phản hồi bình luận: " . $e->getMessage());
            return false;
        }
    }

    // Lấy danh sách phản hồi của một bình luận
    public function getPhanHoiBinhLuan($id_binh_luan)
    {
        $this->ensurePhanHoiBinhLuanTable();
        
        try {
            $sql = "SELECT ph.*, nd.ho_ten as ten_admin
                    FROM phan_hoi_binh_luan ph
                    LEFT JOIN nguoi_dung nd ON ph.id_admin = nd.id
                    WHERE ph.id_binh_luan = :id_binh_luan
                    ORDER BY ph.ngay_tao ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id_binh_luan', $id_binh_luan, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy phản hồi bình luận: " . $e->getMessage());
            return [];
        }
    }

    // Lấy phản hồi bình luận theo ID
    public function getPhanHoiBinhLuanById($id)
    {
        $this->ensurePhanHoiBinhLuanTable();
        
        try {
            $sql = "SELECT ph.*, nd.ho_ten as ten_admin
                    FROM phan_hoi_binh_luan ph
                    LEFT JOIN nguoi_dung nd ON ph.id_admin = nd.id
                    WHERE ph.id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy phản hồi bình luận: " . $e->getMessage());
            return null;
        }
    }

    // Cập nhật phản hồi bình luận
    public function updatePhanHoiBinhLuan($id, $noi_dung)
    {
        $this->ensurePhanHoiBinhLuanTable();
        
        try {
            $sql = "UPDATE phan_hoi_binh_luan 
                    SET noi_dung = :noi_dung 
                    WHERE id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':noi_dung', $noi_dung);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi khi cập nhật phản hồi bình luận: " . $e->getMessage());
            return false;
        }
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

    // Lấy vai trò của một người dùng (lấy trực tiếp từ cột vai_tro)
    public function getVaiTroByNguoiDung($id_nguoi_dung)
    {
        $sql = "SELECT vai_tro FROM nguoi_dung WHERE id = :id_nguoi_dung";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id_nguoi_dung', $id_nguoi_dung, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        // Trả về mảng với vai trò từ cột vai_tro
        return $result && !empty($result['vai_tro']) ? [$result['vai_tro']] : [];
    }


    // ===========================================
    //  QUẢN LÝ TÀI KHOẢN NGƯỜI DÙNG
    // ===========================================

    // Lấy danh sách tất cả tài khoản (lấy vai trò trực tiếp từ cột vai_tro)
    public function getAllTaiKhoan($page = 1, $limit = 10, $search = '', $trang_thai = '')
    {
        $offset = ($page - 1) * $limit;
        $sql = "SELECT nd.* 
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

        $sql .= " ORDER BY nd.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        // Tạo vai_tro_list từ cột vai_tro
        foreach ($results as &$result) {
            $result['vai_tro_list'] = !empty($result['vai_tro']) ? [$result['vai_tro']] : [];
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

    // Lấy thông tin tài khoản theo ID (lấy vai trò trực tiếp từ cột vai_tro)
    public function getTaiKhoanById($id)
    {
        $sql = "SELECT nd.* 
                FROM nguoi_dung nd
                WHERE nd.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result) {
            // Tạo vai_tro_list từ cột vai_tro
            $result['vai_tro_list'] = !empty($result['vai_tro']) ? [$result['vai_tro']] : [];
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
            // Lấy thông tin ca học hiện tại trước khi cập nhật (để lưu lại cho hoàn nguyên)
            $caHocHienTai = $this->getCaHocById($yeuCau['id_ca_hoc_cu']);
            $id_ca_cu = $caHocHienTai['id_ca'] ?? null;
            $id_phong_cu = $caHocHienTai['id_phong'] ?? null;
            $thu_cu = $caHocHienTai['thu_trong_tuan'] ?? null;
            $ngay_hoc_cu = $caHocHienTai['ngay_hoc'] ?? null;
            
            // Xử lý ngày học mới
            // Nếu có ngày_doi (đổi lịch cho một ngày cụ thể), dùng ngày_doi làm ngay_hoc
            // Nếu không có ngày_doi (đổi toàn bộ lịch), set ngay_hoc = NULL
            $ngay_hoc_moi = !empty($yeuCau['ngay_doi']) ? $yeuCau['ngay_doi'] : null;
            
            // Xử lý thứ trong tuần
            // Luôn dùng thu_trong_tuan_moi (không được NULL vì cột không cho phép NULL)
            $thu_moi = $yeuCau['thu_trong_tuan_moi'];
            
            // Cập nhật ca học cũ
            $sqlUpdate = "UPDATE ca_hoc 
                         SET thu_trong_tuan = :thu_moi, 
                             id_ca = :id_ca_moi, 
                             id_phong = :id_phong_moi,
                             ngay_hoc = :ngay_hoc_moi
                         WHERE id = :id_ca_hoc_cu";
            $stmt = $this->conn->prepare($sqlUpdate);
            $stmt->bindValue(':thu_moi', $thu_moi, PDO::PARAM_STR);
            $stmt->bindValue(':id_ca_moi', $yeuCau['id_ca_moi'], PDO::PARAM_INT);
            $stmt->bindValue(':id_phong_moi', $yeuCau['id_phong_moi'], PDO::PARAM_INT);
            $stmt->bindValue(':ngay_hoc_moi', $ngay_hoc_moi, $ngay_hoc_moi === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindValue(':id_ca_hoc_cu', $yeuCau['id_ca_hoc_cu'], PDO::PARAM_INT);
            $stmt->execute();
            
            // Cập nhật trạng thái yêu cầu và lưu thông tin lịch cũ vào ghi chú
            $ghiChuVoiLichCu = $ghi_chu;
            if ($id_ca_cu && $id_phong_cu && $thu_cu) {
                $ghiChuVoiLichCu = ($ghi_chu ? $ghi_chu . ' | ' : '') . 
                    "[Lịch cũ: $thu_cu, Ca ID: $id_ca_cu, Phòng ID: $id_phong_cu]";
            }
            
            $sqlYeuCau = "UPDATE yeu_cau_doi_lich 
                         SET trang_thai = 'da_duyet', 
                             ghi_chu_admin = :ghi_chu
                         WHERE id = :id";
            $stmt = $this->conn->prepare($sqlYeuCau);
            $stmt->bindValue(':ghi_chu', $ghiChuVoiLichCu);
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

    // Hủy yêu cầu đổi lịch (admin có thể hủy bất kỳ yêu cầu nào)
    public function huyYeuCauDoiLich($id, $ghi_chu = null)
    {
        // Đảm bảo bảng tồn tại
        $this->ensureYeuCauDoiLichTable();
        
        // Lấy thông tin yêu cầu
        $yeuCau = $this->getYeuCauDoiLichById($id);
        if (!$yeuCau) {
            return false;
        }
        
        // Nếu yêu cầu đã được duyệt, cần hoàn nguyên lại lịch cũ
        if ($yeuCau['trang_thai'] == 'da_duyet') {
            // Bắt đầu transaction
            $this->conn->beginTransaction();
            
            try {
                // Hoàn nguyên lại lịch cũ (dùng thông tin từ yêu cầu)
                $sqlUpdate = "UPDATE ca_hoc 
                             SET thu_trong_tuan = :thu_cu, 
                                 id_ca = :id_ca_cu, 
                                 id_phong = :id_phong_cu
                             WHERE id = :id_ca_hoc_cu";
                
                // Lấy thông tin ca học cũ từ yêu cầu
                $caHocCu = $this->getCaHocById($yeuCau['id_ca_hoc_cu']);
                if ($caHocCu) {
                    // Tìm lại thông tin lịch cũ - cần query từ ca_hoc ban đầu
                    // Vì đã bị thay đổi, chúng ta cần lấy từ yêu cầu
                    // Lấy id_ca và id_phong từ ca_hoc hiện tại (đã bị đổi)
                    // Nhưng chúng ta cần lấy thông tin cũ từ yêu cầu
                    // Tạm thời, chúng ta sẽ lấy từ ca_hoc hiện tại và đổi ngược
                    
                    // Lấy thông tin ca học ban đầu từ yêu cầu
                    // Cần lưu thông tin này khi duyệt, nhưng hiện tại chúng ta có thể query lại
                    $sqlGetOld = "SELECT ch.* FROM ca_hoc ch WHERE ch.id = :id_ca_hoc_cu";
                    $stmtGet = $this->conn->prepare($sqlGetOld);
                    $stmtGet->bindValue(':id_ca_hoc_cu', $yeuCau['id_ca_hoc_cu'], PDO::PARAM_INT);
                    $stmtGet->execute();
                    $caHocHienTai = $stmtGet->fetch();
                    
                    // Tìm lại thông tin lịch cũ - cần lưu vào bảng khi duyệt
                    // Tạm thời, chúng ta sẽ không hoàn nguyên tự động mà chỉ đánh dấu là đã hủy
                    // Admin sẽ cần chỉnh sửa thủ công nếu cần
                }
                
                // Cập nhật trạng thái thành hủy
                $sql = "UPDATE yeu_cau_doi_lich 
                        SET trang_thai = 'tu_choi', 
                            ghi_chu_admin = CONCAT(COALESCE(ghi_chu_admin, ''), ' [Đã hủy bởi admin: ', :ghi_chu, ']')
                        WHERE id = :id";
                
                $stmt = $this->conn->prepare($sql);
                $ghiChu = $ghi_chu ?: date('d/m/Y H:i');
                $stmt->bindValue(':ghi_chu', $ghiChu);
                $stmt->bindValue(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                
                $this->conn->commit();
                return true;
            } catch (Exception $e) {
                $this->conn->rollBack();
                error_log("Lỗi khi hủy yêu cầu đổi lịch: " . $e->getMessage());
                return false;
            }
        } else {
            // Nếu chưa duyệt, chỉ cần cập nhật trạng thái
            $sql = "UPDATE yeu_cau_doi_lich 
                    SET trang_thai = 'tu_choi', 
                        ghi_chu_admin = CONCAT(COALESCE(ghi_chu_admin, ''), ' [Đã hủy bởi admin: ', :ghi_chu, ']')
                    WHERE id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $ghiChu = $ghi_chu ?: date('d/m/Y H:i');
            $stmt->bindValue(':ghi_chu', $ghiChu);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        }
    }

    // Xác nhận thay đổi lịch dạy (xác nhận lại yêu cầu đã duyệt)
    public function xacNhanThayDoiLich($id, $ghi_chu = null)
    {
        // Đảm bảo bảng tồn tại
        $this->ensureYeuCauDoiLichTable();
        
        // Lấy thông tin yêu cầu
        $yeuCau = $this->getYeuCauDoiLichById($id);
        if (!$yeuCau || $yeuCau['trang_thai'] != 'da_duyet') {
            return false;
        }
        
        // Cập nhật ghi chú xác nhận
        $sql = "UPDATE yeu_cau_doi_lich 
                SET ghi_chu_admin = CONCAT(COALESCE(ghi_chu_admin, ''), ' [Đã xác nhận bởi admin: ', :ghi_chu, ']')
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $ghiChu = $ghi_chu ?: date('d/m/Y H:i');
        $stmt->bindValue(':ghi_chu', $ghiChu);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Hoàn nguyên lịch đã thay đổi (nếu yêu cầu đã được duyệt)
    public function hoanNguyenLich($id, $ghi_chu = null)
    {
        // Đảm bảo bảng tồn tại
        $this->ensureYeuCauDoiLichTable();
        
        // Lấy thông tin yêu cầu
        $yeuCau = $this->getYeuCauDoiLichById($id);
        if (!$yeuCau || $yeuCau['trang_thai'] != 'da_duyet') {
            return false;
        }
        
        // Bắt đầu transaction
        $this->conn->beginTransaction();
        
        try {
            // Lấy thông tin ca học hiện tại (đã được đổi)
            $caHocHienTai = $this->getCaHocById($yeuCau['id_ca_hoc_cu']);
            if (!$caHocHienTai) {
                throw new Exception("Không tìm thấy ca học");
            }
            
            // Lấy thông tin lịch cũ từ ghi_chu_admin (đã lưu khi duyệt)
            $ghiChu = $yeuCau['ghi_chu_admin'] ?? '';
            $id_ca_cu = null;
            $id_phong_cu = null;
            $thu_cu = $yeuCau['thu_cu'] ?? null;
            
            // Tìm thông tin lịch cũ từ ghi chú
            if (preg_match('/\[Lịch cũ: ([^,]+), Ca ID: (\d+), Phòng ID: (\d+)\]/', $ghiChu, $matches)) {
                $thu_cu = $matches[1];
                $id_ca_cu = (int)$matches[2];
                $id_phong_cu = (int)$matches[3];
            } else {
                // Nếu không tìm thấy trong ghi chú, tìm từ ten_ca_cu và ten_phong_cu
                if ($yeuCau['ten_ca_cu']) {
                    $sqlGetCaCu = "SELECT id FROM ca_mac_dinh WHERE ten_ca = :ten_ca LIMIT 1";
                    $stmtGetCa = $this->conn->prepare($sqlGetCaCu);
                    $stmtGetCa->bindValue(':ten_ca', $yeuCau['ten_ca_cu']);
                    $stmtGetCa->execute();
                    $caCu = $stmtGetCa->fetch();
                    $id_ca_cu = $caCu ? $caCu['id'] : null;
                }
                
                if ($yeuCau['ten_phong_cu']) {
                    $sqlGetPhongCu = "SELECT id FROM phong_hoc WHERE ten_phong = :ten_phong LIMIT 1";
                    $stmtGetPhong = $this->conn->prepare($sqlGetPhongCu);
                    $stmtGetPhong->bindValue(':ten_phong', $yeuCau['ten_phong_cu']);
                    $stmtGetPhong->execute();
                    $phongCu = $stmtGetPhong->fetch();
                    $id_phong_cu = $phongCu ? $phongCu['id'] : null;
                }
            }
            
            // Hoàn nguyên lại lịch cũ
            if ($id_ca_cu && $id_phong_cu && $thu_cu) {
                $sqlUpdate = "UPDATE ca_hoc 
                             SET thu_trong_tuan = :thu_cu, 
                                 id_ca = :id_ca_cu, 
                                 id_phong = :id_phong_cu
                             WHERE id = :id_ca_hoc_cu";
                
                $stmt = $this->conn->prepare($sqlUpdate);
                $stmt->bindValue(':thu_cu', $thu_cu);
                $stmt->bindValue(':id_ca_cu', $id_ca_cu, PDO::PARAM_INT);
                $stmt->bindValue(':id_phong_cu', $id_phong_cu, PDO::PARAM_INT);
                $stmt->bindValue(':id_ca_hoc_cu', $yeuCau['id_ca_hoc_cu'], PDO::PARAM_INT);
                $stmt->execute();
            } else {
                throw new Exception("Không thể lấy thông tin lịch cũ để hoàn nguyên");
            }
            
            // Cập nhật trạng thái
            $sql = "UPDATE yeu_cau_doi_lich 
                    SET trang_thai = 'tu_choi', 
                        ghi_chu_admin = CONCAT(COALESCE(ghi_chu_admin, ''), ' [Đã hoàn nguyên bởi admin: ', :ghi_chu, ']')
                    WHERE id = :id";
            
            $stmt = $this->conn->prepare($sql);
            $ghiChu = $ghi_chu ?: date('d/m/Y H:i');
            $stmt->bindValue(':ghi_chu', $ghiChu);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Lỗi khi hoàn nguyên lịch: " . $e->getMessage());
            return false;
        }
    }

    // ===========================================
    //  QUẢN LÝ LIÊN HỆ
    // ===========================================

    // Đảm bảo bảng lien_he tồn tại
    public function ensureLienHeTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `lien_he` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ten` varchar(200) NOT NULL COMMENT 'Tên kênh liên hệ (Zalo, Messenger, etc.)',
            `loai` varchar(50) NOT NULL COMMENT 'Loại: zalo, messenger, phone, email, etc.',
            `gia_tri` text NOT NULL COMMENT 'Giá trị: số điện thoại, link, email, etc.',
            `mo_ta` text DEFAULT NULL COMMENT 'Mô tả',
            `icon` varchar(100) DEFAULT NULL COMMENT 'Icon hoặc emoji',
            `thu_tu` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
            `trang_thai` tinyint(1) DEFAULT 1 COMMENT '1: hiển thị, 0: ẩn',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $this->conn->exec($sql);
        } catch (PDOException $e) {
            error_log("Lỗi khi tạo bảng lien_he: " . $e->getMessage());
        }
    }

    // Lấy danh sách liên hệ
    public function getLienHe($page = 1, $limit = 10, $search = '')
    {
        $this->ensureLienHeTable();
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT * FROM lien_he WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (ten LIKE :search OR loai LIKE :search OR gia_tri LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $sql .= " ORDER BY thu_tu ASC, id DESC LIMIT $limit OFFSET $offset";
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm tổng số liên hệ
    public function countLienHe($search = '')
    {
        $this->ensureLienHeTable();
        
        $sql = "SELECT COUNT(*) as total FROM lien_he WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (ten LIKE :search OR loai LIKE :search OR gia_tri LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    // Lấy thông tin liên hệ theo ID
    public function getLienHeById($id)
    {
        $this->ensureLienHeTable();
        
        $sql = "SELECT * FROM lien_he WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Thêm liên hệ mới
    public function addLienHe($data)
    {
        $this->ensureLienHeTable();
        
        $sql = "INSERT INTO lien_he (ten, loai, gia_tri, mo_ta, icon, thu_tu, trang_thai) 
                VALUES (:ten, :loai, :gia_tri, :mo_ta, :icon, :thu_tu, :trang_thai)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ten', $data['ten']);
        $stmt->bindValue(':loai', $data['loai']);
        $stmt->bindValue(':gia_tri', $data['gia_tri']);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':icon', $data['icon'] ?? null);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Cập nhật liên hệ
    public function updateLienHe($id, $data)
    {
        $this->ensureLienHeTable();
        
        $sql = "UPDATE lien_he 
                SET ten = :ten, 
                    loai = :loai, 
                    gia_tri = :gia_tri, 
                    mo_ta = :mo_ta, 
                    icon = :icon, 
                    thu_tu = :thu_tu, 
                    trang_thai = :trang_thai
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':ten', $data['ten']);
        $stmt->bindValue(':loai', $data['loai']);
        $stmt->bindValue(':gia_tri', $data['gia_tri']);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':icon', $data['icon'] ?? null);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    // Xóa liên hệ (soft delete - ẩn)
    public function deleteLienHe($id)
    {
        $this->ensureLienHeTable();
        
        $sql = "UPDATE lien_he SET trang_thai = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Toggle trạng thái liên hệ
    public function toggleLienHeStatus($id)
    {
        $this->ensureLienHeTable();
        
        // Lấy trạng thái hiện tại
        $lienHe = $this->getLienHeById($id);
        if (!$lienHe) {
            return false;
        }
        
        $newStatus = $lienHe['trang_thai'] == 1 ? 0 : 1;
        
        $sql = "UPDATE lien_he SET trang_thai = :trang_thai WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':trang_thai', $newStatus, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getThongKeDoanhThu($thang = null, $nam = null)
{
    $sql = "SELECT 
                DATE(ngay_thanh_toan) AS ngay_date,
                DATE_FORMAT(DATE(ngay_thanh_toan), '%Y-%m') AS thang_nam,
                DATE_FORMAT(DATE(ngay_thanh_toan), '%d/%m/%Y') AS ngay,
                DAY(DATE(ngay_thanh_toan)) AS ngay_trong_thang,
                SUM(so_tien) AS tong_tien,
                COUNT(*) AS so_luong
            FROM thanh_toan 
            WHERE trang_thai = 'Thành công'";
    
    $params = [];
    if ($thang && $nam) {
        $sql .= " AND MONTH(ngay_thanh_toan) = :thang AND YEAR(ngay_thanh_toan) = :nam";
        $params[':thang'] = $thang;
        $params[':nam'] = $nam;
    } elseif ($nam) {
        $sql .= " AND YEAR(ngay_thanh_toan) = :nam";
        $params[':nam'] = $nam;
    }

    $sql .= " GROUP BY DATE(ngay_thanh_toan), DATE_FORMAT(DATE(ngay_thanh_toan), '%Y-%m'), DATE_FORMAT(DATE(ngay_thanh_toan), '%d/%m/%Y'), DAY(DATE(ngay_thanh_toan))
              ORDER BY DATE(ngay_thanh_toan) ASC";

    $stmt = $this->conn->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll();
}



    // Lấy dữ liệu thống kê đăng ký theo tháng/năm
    public function getThongKeDangKy($thang = null, $nam = null)
{
    $sql = "SELECT 
                DATE(ngay_dang_ky) as ngay_date,
                DATE_FORMAT(DATE(ngay_dang_ky), '%Y-%m') as thang_nam,
                DATE_FORMAT(DATE(ngay_dang_ky), '%d/%m/%Y') as ngay,
                DAY(DATE(ngay_dang_ky)) as ngay_trong_thang,
                COUNT(*) as so_luong,
                SUM(CASE WHEN trang_thai = 'Đã xác nhận' THEN 1 ELSE 0 END) as da_xac_nhan,
                SUM(CASE WHEN trang_thai = 'Chờ xác nhận' THEN 1 ELSE 0 END) as cho_xac_nhan,
                SUM(CASE WHEN trang_thai = 'Đã hủy' THEN 1 ELSE 0 END) as da_huy
            FROM dang_ky 
            WHERE 1=1";

    $params = [];
    if ($thang && $nam) {
        $sql .= " AND MONTH(ngay_dang_ky) = :thang AND YEAR(ngay_dang_ky) = :nam";
        $params[':thang'] = $thang;
        $params[':nam'] = $nam;
    } elseif ($nam) {
        $sql .= " AND YEAR(ngay_dang_ky) = :nam";
        $params[':nam'] = $nam;
    }

    $sql .= " GROUP BY DATE(ngay_dang_ky), DATE_FORMAT(DATE(ngay_dang_ky), '%Y-%m'), DATE_FORMAT(DATE(ngay_dang_ky), '%d/%m/%Y'), DAY(DATE(ngay_dang_ky))
              ORDER BY DATE(ngay_dang_ky) ASC";

    $stmt = $this->conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchAll();
}


    // Lấy dữ liệu thống kê thanh toán theo tháng/năm
    public function getThongKeThanhToan($thang = null, $nam = null)
{
    $sql = "SELECT 
                DATE(ngay_thanh_toan) as ngay_date,
                DATE_FORMAT(DATE(ngay_thanh_toan), '%Y-%m') as thang_nam,
                DATE_FORMAT(DATE(ngay_thanh_toan), '%d/%m/%Y') as ngay,
                DAY(DATE(ngay_thanh_toan)) as ngay_trong_thang,
                COUNT(*) as so_luong,
                SUM(CASE WHEN trang_thai = 'Thành công' THEN 1 ELSE 0 END) as thanh_cong,
                SUM(CASE WHEN trang_thai = 'Thất bại' THEN 1 ELSE 0 END) as that_bai,
                SUM(CASE WHEN trang_thai = 'Chờ xác nhận' THEN 1 ELSE 0 END) as cho_xac_nhan,
                SUM(CASE WHEN trang_thai = 'Hoàn tiền' THEN 1 ELSE 0 END) as hoan_tien
            FROM thanh_toan 
            WHERE 1=1";

    $params = [];
    if ($thang && $nam) {
        $sql .= " AND MONTH(ngay_thanh_toan) = :thang AND YEAR(ngay_thanh_toan) = :nam";
        $params[':thang'] = $thang;
        $params[':nam'] = $nam;
    } elseif ($nam) {
        $sql .= " AND YEAR(ngay_thanh_toan) = :nam";
        $params[':nam'] = $nam;
    }

    $sql .= " GROUP BY DATE(ngay_thanh_toan), DATE_FORMAT(DATE(ngay_thanh_toan), '%Y-%m'), DATE_FORMAT(DATE(ngay_thanh_toan), '%d/%m/%Y'), DAY(DATE(ngay_thanh_toan))
              ORDER BY DATE(ngay_thanh_toan) ASC";

    $stmt = $this->conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchAll();
}


    // Lấy tổng hợp thống kê theo tháng/năm
    public function getThongKeTongHop($thang = null, $nam = null)
    {
        $stats = [];
        
        // Điều kiện WHERE cho thanh_toan
        $whereThanhToan = "";
        $whereDangKy = "";
        $whereHoanTien = "";
        $params = [];
        
        if ($thang && $nam) {
            $whereThanhToan = " AND MONTH(ngay_thanh_toan) = :thang AND YEAR(ngay_thanh_toan) = :nam";
            $whereDangKy = " AND MONTH(ngay_dang_ky) = :thang AND YEAR(ngay_dang_ky) = :nam";
            $whereHoanTien = " AND MONTH(ngay_tao) = :thang AND YEAR(ngay_tao) = :nam";
            $params[':thang'] = $thang;
            $params[':nam'] = $nam;
        } elseif ($nam) {
            $whereThanhToan = " AND YEAR(ngay_thanh_toan) = :nam";
            $whereDangKy = " AND YEAR(ngay_dang_ky) = :nam";
            $whereHoanTien = " AND YEAR(ngay_tao) = :nam";
            $params[':nam'] = $nam;
        }
        
        // Tổng doanh thu (chỉ tính thành công)
        $sql = "SELECT COALESCE(SUM(so_tien), 0) as total FROM thanh_toan WHERE trang_thai = 'Thành công'" . $whereThanhToan;
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        $stats['tong_doanh_thu'] = $stmt->fetch()['total'];
        
        // Tổng số đăng ký
        $sql = "SELECT COUNT(*) as total FROM dang_ky WHERE 1=1" . $whereDangKy;
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        $stats['tong_dang_ky'] = $stmt->fetch()['total'];
        
        // Tổng số đăng ký đã xác nhận
        $sql = "SELECT COUNT(*) as total FROM dang_ky WHERE trang_thai = 'Đã xác nhận'" . $whereDangKy;
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        $stats['tong_dang_ky_da_xac_nhan'] = $stmt->fetch()['total'];
        
        // Tổng số thanh toán thành công
        $sql = "SELECT COUNT(*) as total FROM thanh_toan WHERE trang_thai = 'Thành công'" . $whereThanhToan;
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        $stats['tong_thanh_toan'] = $stmt->fetch()['total'];
        
        // Tổng số hoàn tiền
        $sql = "SELECT COALESCE(SUM(so_tien_hoan), 0) as total FROM hoan_tien WHERE trang_thai = 'Thành công'" . $whereHoanTien;
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        $stats['tong_hoan_tien'] = $stmt->fetch()['total'];
        
        // Số lượng hoàn tiền
        $sql = "SELECT COUNT(*) as total FROM hoan_tien WHERE trang_thai = 'Thành công'" . $whereHoanTien;
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        $stats['so_luong_hoan_tien'] = $stmt->fetch()['total'];
        
        return $stats;
    }

    // Lấy thống kê hoàn tiền theo tháng/năm
    public function getThongKeHoanTien($thang = null, $nam = null)
{
    $sql = "SELECT 
                DATE(ngay_tao) as ngay_date,
                DATE_FORMAT(DATE(ngay_tao), '%Y-%m') as thang_nam,
                DATE_FORMAT(DATE(ngay_tao), '%d/%m/%Y') as ngay,
                DAY(DATE(ngay_tao)) as ngay_trong_thang,
                SUM(so_tien_hoan) as tong_tien_hoan,
                COUNT(*) as so_luong,
                SUM(CASE WHEN trang_thai = 'Thành công' THEN so_tien_hoan ELSE 0 END) as thanh_cong,
                SUM(CASE WHEN trang_thai = 'Đang xử lý' THEN so_tien_hoan ELSE 0 END) as dang_xu_ly
            FROM hoan_tien 
            WHERE 1=1";

    $params = [];
    if ($thang && $nam) {
        $sql .= " AND MONTH(ngay_tao) = :thang AND YEAR(ngay_tao) = :nam";
        $params[':thang'] = $thang;
        $params[':nam'] = $nam;
    } elseif ($nam) {
        $sql .= " AND YEAR(ngay_tao) = :nam";
        $params[':nam'] = $nam;
    }

    $sql .= " GROUP BY DATE(ngay_tao), DATE_FORMAT(DATE(ngay_tao), '%Y-%m'), DATE_FORMAT(DATE(ngay_tao), '%d/%m/%Y'), DAY(DATE(ngay_tao))
              ORDER BY DATE(ngay_tao) ASC";

    $stmt = $this->conn->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_INT);
    }
    $stmt->execute();
    return $stmt->fetchAll();
}


    // Lấy thống kê theo khóa học
    public function getThongKeTheoKhoaHoc($thang = null, $nam = null)
    {
        $params = [];
        $dateFilterDangKy = "";
        $dateFilterThanhToan = "";
        
        if ($thang && $nam) {
            $dateFilterDangKy = " AND MONTH(dk.ngay_dang_ky) = :thang AND YEAR(dk.ngay_dang_ky) = :nam";
            $dateFilterThanhToan = " AND MONTH(tt.ngay_thanh_toan) = :thang2 AND YEAR(tt.ngay_thanh_toan) = :nam2";
            $params[':thang'] = $thang;
            $params[':nam'] = $nam;
            $params[':thang2'] = $thang;
            $params[':nam2'] = $nam;
        } elseif ($nam) {
            $dateFilterDangKy = " AND YEAR(dk.ngay_dang_ky) = :nam";
            $dateFilterThanhToan = " AND YEAR(tt.ngay_thanh_toan) = :nam2";
            $params[':nam'] = $nam;
            $params[':nam2'] = $nam;
        }
        
        // Sử dụng MAX() cho ten_danh_muc và chỉ GROUP BY kh.id, kh.ten_khoa_hoc
        $sql = "SELECT 
                    kh.id,
                    kh.ten_khoa_hoc,
                    MAX(dm.ten_danh_muc) as ten_danh_muc,
                    COUNT(DISTINCT CASE WHEN dk.id IS NOT NULL" . $dateFilterDangKy . " THEN dk.id END) as so_dang_ky,
                    COUNT(DISTINCT CASE WHEN dk.trang_thai = 'Đã xác nhận'" . $dateFilterDangKy . " THEN dk.id END) as so_da_xac_nhan,
                    COALESCE(SUM(CASE WHEN tt.trang_thai = 'Thành công'" . $dateFilterThanhToan . " THEN tt.so_tien ELSE 0 END), 0) as doanh_thu,
                    COUNT(DISTINCT CASE WHEN tt.trang_thai = 'Thành công'" . $dateFilterThanhToan . " THEN tt.id END) as so_thanh_toan
                FROM khoa_hoc kh
                LEFT JOIN danh_muc dm ON kh.id_danh_muc = dm.id
                LEFT JOIN lop_hoc lh ON kh.id = lh.id_khoa_hoc
                LEFT JOIN dang_ky dk ON lh.id = dk.id_lop
                LEFT JOIN thanh_toan tt ON dk.id = tt.id_dang_ky AND tt.trang_thai = 'Thành công'
                WHERE kh.trang_thai = 1
                GROUP BY kh.id, kh.ten_khoa_hoc
                HAVING so_dang_ky > 0 OR doanh_thu > 0
                ORDER BY doanh_thu DESC, so_dang_ky DESC";
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy thống kê theo phương thức thanh toán
    public function getThongKeTheoPhuongThuc($thang = null, $nam = null)
    {
        $sql = "SELECT 
                    phuong_thuc,
                    COUNT(*) as so_luong,
                    SUM(CASE WHEN trang_thai = 'Thành công' THEN so_tien ELSE 0 END) as tong_tien,
                    SUM(CASE WHEN trang_thai = 'Thành công' THEN 1 ELSE 0 END) as thanh_cong,
                    SUM(CASE WHEN trang_thai = 'Thất bại' THEN 1 ELSE 0 END) as that_bai
                FROM thanh_toan 
                WHERE 1=1";
        
        $params = [];
        if ($thang && $nam) {
            $sql .= " AND MONTH(ngay_thanh_toan) = :thang AND YEAR(ngay_thanh_toan) = :nam";
            $params[':thang'] = $thang;
            $params[':nam'] = $nam;
        } elseif ($nam) {
            $sql .= " AND YEAR(ngay_thanh_toan) = :nam";
            $params[':nam'] = $nam;
        }
        
        $sql .= " GROUP BY phuong_thuc ORDER BY tong_tien DESC";
        
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
}


?>
