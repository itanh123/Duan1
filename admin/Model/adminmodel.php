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

    // Lấy danh sách danh mục
    public function getDanhMuc()
    {
        $sql = "SELECT * FROM danh_muc WHERE trang_thai = 1 ORDER BY ten_danh_muc";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
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
