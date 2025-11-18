<?php
require_once './Commons/function.php';

class CaHoc {

    private $db;

    public function __construct() {
        $this->db = connectDB();
    }

    /**
     * Lấy danh sách ca học theo lớp
     */
    public function getCaHocByLop($id_lop) {
        $hasNguoiDung = $this->tableExists('nguoi_dung');
        
        if ($hasNguoiDung) {
            $sql = "SELECT ch.*, u.ho_ten AS giang_vien_ten
                    FROM ca_hoc ch
                    LEFT JOIN nguoi_dung u ON ch.id_giang_vien = u.id
                    WHERE ch.id_lop = :id_lop";
        } else {
            $sql = "SELECT ch.*, NULL AS giang_vien_ten
                    FROM ca_hoc ch
                    WHERE ch.id_lop = :id_lop";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_lop' => $id_lop]);

        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết ca học theo ID
     */
    public function getChiTietCaHoc($id) {
        // Kiểm tra xem các bảng và cột có tồn tại không
        $hasCaMacDinh = $this->tableExists('ca_mac_dinh');
        $hasPhongHoc = $this->tableExists('phong_hoc');
        $hasNguoiDung = $this->tableExists('nguoi_dung');
        $hasIdCa = $this->columnExists('ca_hoc', 'id_ca');

        // Xây dựng query động dựa trên các bảng và cột tồn tại
        $selectFields = [
            'ch.id', 'ch.id_lop', 'ch.thu_trong_tuan', 'ch.ghi_chu',
            'lh.ten_lop', 'lh.so_luong_toi_da', 'lh.mo_ta AS mo_ta_lop',
            'lh.ngay_bat_dau', 'lh.ngay_ket_thuc', 'lh.trang_thai AS trang_thai_lop',
            'kh.id AS id_khoa_hoc', 'kh.ten_khoa_hoc', 'kh.gia', 'kh.hinh_anh'
        ];
        
        // Chỉ thêm các cột nếu chúng tồn tại
        if ($hasIdCa) {
            $selectFields[] = 'ch.id_ca';
        }
        if ($this->columnExists('ca_hoc', 'id_giang_vien')) {
            $selectFields[] = 'ch.id_giang_vien';
        }
        if ($this->columnExists('ca_hoc', 'id_phong')) {
            $selectFields[] = 'ch.id_phong';
        }
        
        $joins = [
            'LEFT JOIN lop_hoc lh ON ch.id_lop = lh.id',
            'LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id'
        ];
        
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
        
        if ($hasPhongHoc && $this->columnExists('ca_hoc', 'id_phong')) {
            $selectFields[] = 'ph.ten_phong';
            $selectFields[] = 'ph.suc_chua';
            $selectFields[] = 'ph.mo_ta AS mo_ta_phong';
            $selectFields[] = 'ph.trang_thai AS trang_thai_phong';
            $joins[] = 'LEFT JOIN phong_hoc ph ON ch.id_phong = ph.id';
        } else {
            $selectFields[] = 'NULL AS ten_phong';
            $selectFields[] = 'NULL AS suc_chua';
            $selectFields[] = 'NULL AS mo_ta_phong';
            $selectFields[] = 'NULL AS trang_thai_phong';
        }

        $sql = "SELECT " . implode(', ', $selectFields) . "
                FROM ca_hoc ch
                " . implode(' ', $joins) . "
                WHERE ch.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
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
     * Lấy danh sách ca học theo giảng viên
     */
    public function getCaHocByGiangVien($id_giang_vien) {
        $sql = "SELECT ch.*, lh.ten_lop, kh.ten_khoa_hoc
                FROM ca_hoc ch
                LEFT JOIN lop_hoc lh ON ch.id_lop = lh.id
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                WHERE ch.id_giang_vien = :id_giang_vien
                ORDER BY ch.thu_trong_tuan, ch.id_ca";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_giang_vien' => $id_giang_vien]);

        return $stmt->fetchAll();
    }

    /**
     * Lấy danh sách ca học theo phòng học
     */
    public function getCaHocByPhong($id_phong) {
        $sql = "SELECT ch.*, lh.ten_lop, kh.ten_khoa_hoc, gv.ho_ten AS ten_giang_vien
                FROM ca_hoc ch
                LEFT JOIN lop_hoc lh ON ch.id_lop = lh.id
                LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                LEFT JOIN nguoi_dung gv ON ch.id_giang_vien = gv.id
                WHERE ch.id_phong = :id_phong
                ORDER BY ch.thu_trong_tuan, ch.id_ca";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_phong' => $id_phong]);

        return $stmt->fetchAll();
    }
}

