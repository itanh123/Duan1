<?php
require_once './Commons/function.php';

class KhoaHoc {

    private $db;

    public function __construct() {
        // sử dụng connectDB() đúng như commons của bạn
        $this->db = connectDB();
    }

    public function getAll($limit = 12, $offset = 0) {
        $sql = "SELECT * FROM khoa_hoc 
                WHERE trang_thai = 1
                ORDER BY ngay_tao DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) AS total FROM khoa_hoc WHERE trang_thai = 1";
        return $this->db->query($sql)->fetch()['total'];
    }

    public function getById($id) {
        $sql = "SELECT k.*, d.ten_danh_muc
                FROM khoa_hoc k
                LEFT JOIN danh_muc d ON k.id_danh_muc = d.id
                WHERE k.id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch();
    }

    public function getLopHocByKhoaHoc($id_khoa_hoc) {
        $sql = "SELECT * FROM lop_hoc WHERE id_khoa_hoc = :id_khoa_hoc";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_khoa_hoc' => $id_khoa_hoc]);

        return $stmt->fetchAll();
    }

    public function getBinhLuan($id_khoa_hoc) {
        try {
            $sql = "SELECT b.*, u.ho_ten
                    FROM binh_luan b
                    LEFT JOIN nguoi_dung u ON b.id_hoc_sinh = u.id
                    WHERE b.id_khoa_hoc = :id_khoa_hoc
                      AND b.trang_thai = 'Hiển thị'
                    ORDER BY b.ngay_tao DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_khoa_hoc' => $id_khoa_hoc]);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            // Nếu bảng không tồn tại, trả về mảng rỗng
            return [];
        }
    }

    public function addBinhLuan($id_khoa_hoc, $id_hoc_sinh, $noi_dung, $danh_gia = null) {
        try {
            $sql = "INSERT INTO binh_luan (id_khoa_hoc, id_hoc_sinh, noi_dung, danh_gia, trang_thai, ngay_tao)
                    VALUES (:id_khoa_hoc, :id_hoc_sinh, :noi_dung, :danh_gia, 'Hiển thị', NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_khoa_hoc' => $id_khoa_hoc,
                ':id_hoc_sinh' => $id_hoc_sinh,
                ':noi_dung' => $noi_dung,
                ':danh_gia' => $danh_gia
            ]);

            return true;
        } catch (PDOException $e) {
            // Nếu bảng không tồn tại, trả về false
            return false;
        }
    }

    public function dangKyKhoaHoc($id_khoa_hoc, $id_lop, $ho_ten, $email, $sdt, $ghi_chu = '') {
        try {
            $sql = "INSERT INTO dang_ky (id_khoa_hoc, id_lop, ho_ten, email, sdt, ghi_chu, trang_thai, ngay_tao)
                    VALUES (:id_khoa_hoc, :id_lop, :ho_ten, :email, :sdt, :ghi_chu, 'Chờ xử lý', NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_khoa_hoc' => $id_khoa_hoc,
                ':id_lop' => $id_lop ?: null,
                ':ho_ten' => $ho_ten,
                ':email' => $email,
                ':sdt' => $sdt,
                ':ghi_chu' => $ghi_chu
            ]);

            return true;
        } catch (PDOException $e) {
            // Nếu bảng không tồn tại, trả về false
            return false;
        }
    }
}
