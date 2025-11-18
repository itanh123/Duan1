<?php
require_once './Commons/function.php';

class LopHoc {

    private $db;

    public function __construct() {
        $this->db = connectDB();
    }

    /**
     * Lấy chi tiết lớp học theo ID
     * Bao gồm: thông tin lớp, khóa học, ca học, số lượng đăng ký
     */
    public function getChiTietLopHoc($id) {
        $sql = "SELECT 
                    lh.id,
                    lh.id_khoa_hoc,
                    lh.ten_lop,
                    lh.so_luong_toi_da,
                    lh.mo_ta,
                    lh.ngay_bat_dau,
                    lh.ngay_ket_thuc,
                    lh.trang_thai,
                    kh.ten_khoa_hoc,
                    kh.mo_ta AS mo_ta_khoa_hoc,
                    kh.gia,
                    kh.hinh_anh,
                    kh.trang_thai AS trang_thai_khoa_hoc,
                    dm.ten_danh_muc,
                    dm.duong_dan AS duong_dan_danh_muc,
                    COUNT(DISTINCT dk.id) AS so_luong_dang_ky,
                    COUNT(DISTINCT CASE WHEN dk.trang_thai = 'Đã xác nhận' THEN dk.id END) AS so_luong_da_xac_nhan
                FROM lop_hoc lh
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                LEFT JOIN danh_muc dm ON kh.id_danh_muc = dm.id
                LEFT JOIN dang_ky dk ON lh.id = dk.id_lop
                WHERE lh.id = :id
                GROUP BY lh.id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    /**
     * Lấy danh sách ca học của lớp
     */
    public function getCaHocByLop($id_lop) {
        // Kiểm tra xem các bảng và cột có tồn tại không
        $hasCaMacDinh = $this->tableExists('ca_mac_dinh');
        $hasPhongHoc = $this->tableExists('phong_hoc');
        $hasNguoiDung = $this->tableExists('nguoi_dung');
        $hasIdCa = $this->columnExists('ca_hoc', 'id_ca');

        // Xây dựng query động dựa trên các bảng và cột tồn tại
        $selectFields = ['ch.id', 'ch.thu_trong_tuan', 'ch.ghi_chu'];
        
        // Chỉ thêm id_ca nếu cột tồn tại
        if ($hasIdCa) {
            $selectFields[] = 'ch.id_ca';
        }
        
        // Kiểm tra các cột khác
        if ($this->columnExists('ca_hoc', 'id_phong')) {
            $selectFields[] = 'ch.id_phong';
        }
        if ($this->columnExists('ca_hoc', 'id_giang_vien')) {
            $selectFields[] = 'ch.id_giang_vien';
        }
        
        $joins = [];
        
        if ($hasCaMacDinh && $hasIdCa) {
            $selectFields[] = 'cm.ten_ca';
            $selectFields[] = 'cm.gio_bat_dau';
            $selectFields[] = 'cm.gio_ket_thuc';
            $joins[] = 'LEFT JOIN ca_mac_dinh cm ON ch.id_ca = cm.id';
        } else {
            $selectFields[] = 'NULL AS ten_ca';
            $selectFields[] = 'NULL AS gio_bat_dau';
            $selectFields[] = 'NULL AS gio_ket_thuc';
        }
        
        if ($hasPhongHoc && $this->columnExists('ca_hoc', 'id_phong')) {
            $selectFields[] = 'ph.ten_phong';
            $selectFields[] = 'ph.suc_chua';
            $selectFields[] = 'ph.trang_thai AS trang_thai_phong';
            $joins[] = 'LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id';
        } else {
            $selectFields[] = 'NULL AS ten_phong';
            $selectFields[] = 'NULL AS suc_chua';
            $selectFields[] = 'NULL AS trang_thai_phong';
        }
        
        if ($hasNguoiDung && $this->columnExists('ca_hoc', 'id_giang_vien')) {
            $selectFields[] = 'gv.ho_ten AS ten_giang_vien';
            $selectFields[] = 'gv.email AS email_giang_vien';
            $selectFields[] = 'gv.so_dien_thoai AS sdt_giang_vien';
            $joins[] = 'LEFT JOIN nguoi_dung gv ON ch.id_giang_vien = gv.id';
        } else {
            $selectFields[] = 'NULL AS ten_giang_vien';
            $selectFields[] = 'NULL AS email_giang_vien';
            $selectFields[] = 'NULL AS sdt_giang_vien';
        }

        // Xây dựng ORDER BY
        $orderBy = "ORDER BY FIELD(ch.thu_trong_tuan, 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ nhật')";
        if ($hasIdCa) {
            $orderBy .= ", ch.id_ca";
        } else {
            $orderBy .= ", ch.id";
        }

        $sql = "SELECT " . implode(', ', $selectFields) . "
                FROM ca_hoc ch
                " . implode(' ', $joins) . "
                WHERE ch.id_lop = :id_lop
                " . $orderBy;

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_lop' => $id_lop]);

        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra xem cột có tồn tại trong bảng không
     */
    private function columnExists($tableName, $columnName) {
        try {
            $checkColumn = $this->db->query("SHOW COLUMNS FROM `$tableName` LIKE '$columnName'");
            return $checkColumn->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Kiểm tra xem bảng có tồn tại không
     */
    private function tableExists($tableName) {
        try {
            $checkTable = $this->db->query("SHOW TABLES LIKE '$tableName'");
            return $checkTable->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Lấy danh sách lớp học theo khóa học
     */
    public function getLopHocByKhoaHoc($id_khoa_hoc) {
        $sql = "SELECT * FROM lop_hoc WHERE id_khoa_hoc = :id_khoa_hoc ORDER BY ngay_bat_dau";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_khoa_hoc' => $id_khoa_hoc]);

        return $stmt->fetchAll();
    }

    /**
     * Lấy tất cả lớp học với thông tin khóa học
     */
    public function getAll($limit = 12, $offset = 0) {
        $sql = "SELECT 
                    lh.*,
                    kh.ten_khoa_hoc,
                    kh.gia,
                    kh.hinh_anh,
                    dm.ten_danh_muc,
                    COUNT(DISTINCT dk.id) AS so_luong_dang_ky
                FROM lop_hoc lh
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                LEFT JOIN danh_muc dm ON kh.id_danh_muc = dm.id
                LEFT JOIN dang_ky dk ON lh.id = dk.id_lop AND dk.trang_thai = 'Đã xác nhận'
                WHERE kh.trang_thai = 1
                GROUP BY lh.id
                ORDER BY lh.ngay_bat_dau DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Đếm tổng số lớp học
     */
    public function countAll() {
        $sql = "SELECT COUNT(DISTINCT lh.id) AS total 
                FROM lop_hoc lh
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                WHERE kh.trang_thai = 1";

        return $this->db->query($sql)->fetch()['total'];
    }
}

