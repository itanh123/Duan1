<?php
require_once './Commons/function.php';

class DanhMuc {

    private $db;

    public function __construct() {
        $this->db = connectDB();
    }

    /**
     * Lấy tất cả danh mục
     */
    public function getAll() {
        $sql = "SELECT * FROM danh_muc 
                WHERE trang_thai = 1
                ORDER BY ten_danh_muc ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Lấy danh mục theo ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM danh_muc WHERE id = :id AND trang_thai = 1 LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    /**
     * Lấy danh sách khóa học theo danh mục
     */
    public function getKhoaHocByDanhMuc($id_danh_muc, $limit = 12, $offset = 0) {
        $sql = "SELECT * FROM khoa_hoc 
                WHERE id_danh_muc = :id_danh_muc AND trang_thai = 1
                ORDER BY ngay_tao DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_danh_muc', $id_danh_muc, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Đếm số lượng khóa học theo danh mục
     */
    public function countKhoaHocByDanhMuc($id_danh_muc) {
        $sql = "SELECT COUNT(*) AS total FROM khoa_hoc 
                WHERE id_danh_muc = :id_danh_muc AND trang_thai = 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_danh_muc' => $id_danh_muc]);

        return $stmt->fetch()['total'];
    }
}

