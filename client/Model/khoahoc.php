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

    public function search($keyword, $limit = 12, $offset = 0) {
        $sql = "SELECT k.*, d.ten_danh_muc
                FROM khoa_hoc k
                LEFT JOIN danh_muc d ON k.id_danh_muc = d.id
                WHERE k.trang_thai = 1
                  AND (k.ten_khoa_hoc LIKE :keyword 
                       OR k.mo_ta LIKE :keyword
                       OR d.ten_danh_muc LIKE :keyword)
                ORDER BY k.ngay_tao DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $searchKeyword = '%' . $keyword . '%';
        $stmt->bindValue(':keyword', $searchKeyword);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countSearch($keyword) {
        $sql = "SELECT COUNT(*) AS total
                FROM khoa_hoc k
                LEFT JOIN danh_muc d ON k.id_danh_muc = d.id
                WHERE k.trang_thai = 1
                  AND (k.ten_khoa_hoc LIKE :keyword 
                       OR k.mo_ta LIKE :keyword
                       OR d.ten_danh_muc LIKE :keyword)";

        $stmt = $this->db->prepare($sql);
        $searchKeyword = '%' . $keyword . '%';
        $stmt->bindValue(':keyword', $searchKeyword);
        $stmt->execute();

        return $stmt->fetch()['total'];
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

    /**
     * Hủy các đăng ký quá hạn (sau 10 phút chưa thanh toán)
     * Chỉ hủy các đăng ký có trạng thái "Chờ xác nhận" và có vnp_TxnRef (đăng ký online)
     * 
     * @return int Số lượng đăng ký đã hủy
     */
    public function cancelExpiredRegistrations() {
        try {
            // Tìm các đăng ký có:
            // - Trạng thái = "Chờ xác nhận"
            // - Có vnp_TxnRef (đăng ký online)
            // - Đã quá 10 phút kể từ ngày đăng ký
            $sql = "UPDATE dang_ky 
                    SET trang_thai = 'Đã hủy'
                    WHERE trang_thai = 'Chờ xác nhận'
                    AND vnp_TxnRef IS NOT NULL
                    AND vnp_TxnRef != ''
                    AND vnp_TransactionNo IS NULL
                    AND TIMESTAMPDIFF(MINUTE, ngay_dang_ky, NOW()) > 10";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $count = $stmt->rowCount();
            
            if ($count > 0) {
                error_log("Đã hủy $count đăng ký quá hạn (sau 10 phút chưa thanh toán)");
            }
            
            return $count;
        } catch (PDOException $e) {
            error_log("Lỗi khi hủy đăng ký quá hạn: " . $e->getMessage());
            return 0;
        }
    }
    
    public function dangKyKhoaHoc($id_hoc_sinh, $id_lop, $trang_thai = 'Chờ xác nhận', $vnp_TxnRef = null) {
        try {
            // Bắt đầu giao dịch để tránh overbook khi nhiều người đặt cùng lúc
            $this->db->beginTransaction();

            // Lấy thông tin học sinh ngay trên cùng kết nối (giữ transaction)
            $sqlHocSinh = "SELECT id, ho_ten, email, so_dien_thoai FROM nguoi_dung WHERE id = :id AND vai_tro = 'hoc_sinh' LIMIT 1";
            $stmtHocSinh = $this->db->prepare($sqlHocSinh);
            $stmtHocSinh->bindValue(':id', $id_hoc_sinh, PDO::PARAM_INT);
            $stmtHocSinh->execute();
            $hocSinh = $stmtHocSinh->fetch();
            if (!$hocSinh) {
                $this->db->rollBack();
                error_log("Không tìm thấy học sinh với ID: " . $id_hoc_sinh);
                return false;
            }

            // Khóa dòng lớp học để kiểm tra sức chứa
            $sqlLop = "SELECT id, id_khoa_hoc, so_luong_toi_da FROM lop_hoc WHERE id = :id_lop LIMIT 1 FOR UPDATE";
            $stmtLop = $this->db->prepare($sqlLop);
            $stmtLop->bindValue(':id_lop', $id_lop, PDO::PARAM_INT);
            $stmtLop->execute();
            $lopHoc = $stmtLop->fetch();
            if (!$lopHoc) {
                $this->db->rollBack();
                error_log("Không tìm thấy lớp học với ID: " . $id_lop);
                return false;
            }

            // Đếm số lượng đang giữ chỗ (kể cả chờ xác nhận) và khóa khoảng trống bằng FOR UPDATE
            $sqlCount = "SELECT COUNT(*) AS total
                         FROM dang_ky
                         WHERE id_lop = :id_lop
                           AND trang_thai IN ('Chờ xác nhận','Đã xác nhận')
                         FOR UPDATE";
            $stmtCount = $this->db->prepare($sqlCount);
            $stmtCount->bindValue(':id_lop', $id_lop, PDO::PARAM_INT);
            $stmtCount->execute();
            $countRow = $stmtCount->fetch();
            $soLuongHienTai = (int)($countRow['total'] ?? 0);

            if (!empty($lopHoc['so_luong_toi_da']) && $soLuongHienTai >= (int)$lopHoc['so_luong_toi_da']) {
                $this->db->rollBack();
                error_log("Lớp đã đủ chỗ: $soLuongHienTai / {$lopHoc['so_luong_toi_da']} (id_lop: $id_lop)");
                return false;
            }

            // Chặn đăng ký trùng cho cùng học sinh vào lớp (trừ khi đã hủy/từ chối)
            // Kiểm tra theo cột id_hoc_sinh nếu tồn tại, nếu không fallback theo email
            $hasIdHocSinhColumn = false;
            try {
                $checkColumn = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'id_hoc_sinh'");
                $hasIdHocSinhColumn = $checkColumn->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Lỗi kiểm tra cột id_hoc_sinh: " . $e->getMessage());
            }

            if ($hasIdHocSinhColumn) {
                $sqlDup = "SELECT id FROM dang_ky
                           WHERE id_lop = :id_lop
                             AND id_hoc_sinh = :id_hoc_sinh
                             AND trang_thai NOT IN ('Đã hủy','Từ chối')
                           LIMIT 1
                           FOR UPDATE";
                $stmtDup = $this->db->prepare($sqlDup);
                $stmtDup->bindValue(':id_lop', $id_lop, PDO::PARAM_INT);
                $stmtDup->bindValue(':id_hoc_sinh', $id_hoc_sinh, PDO::PARAM_INT);
                $stmtDup->execute();
                if ($stmtDup->fetch()) {
                    $this->db->rollBack();
                    error_log("Học sinh $id_hoc_sinh đã đăng ký lớp $id_lop trước đó.");
                    return false;
                }
            } else {
                $sqlDup = "SELECT id FROM dang_ky
                           WHERE id_lop = :id_lop
                             AND email = :email
                             AND trang_thai NOT IN ('Đã hủy','Từ chối')
                           LIMIT 1
                           FOR UPDATE";
                $stmtDup = $this->db->prepare($sqlDup);
                $stmtDup->bindValue(':id_lop', $id_lop, PDO::PARAM_INT);
                $stmtDup->bindValue(':email', $hocSinh['email']);
                $stmtDup->execute();
                if ($stmtDup->fetch()) {
                    $this->db->rollBack();
                    error_log("Email {$hocSinh['email']} đã đăng ký lớp $id_lop trước đó.");
                    return false;
                }
            }
            
            // Kiểm tra xem bảng có cột id_hoc_sinh hay không
            try {
                $checkColumn = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'id_hoc_sinh'");
                $hasIdHocSinh = $checkColumn->rowCount() > 0;
            } catch (PDOException $e) {
                error_log("Lỗi kiểm tra cột id_hoc_sinh: " . $e->getMessage());
                $hasIdHocSinh = false;
            }

            // Chặn đăng ký trùng khóa học (dù khác lớp) cho cùng tài khoản
            if ($hasIdHocSinh) {
                $sqlDupCourse = "SELECT dk.id FROM dang_ky dk
                                 INNER JOIN lop_hoc lh2 ON dk.id_lop = lh2.id
                                 WHERE dk.id_hoc_sinh = :id_hoc_sinh
                                   AND lh2.id_khoa_hoc = :id_khoa_hoc
                                   AND dk.trang_thai NOT IN ('Đã hủy','Từ chối')
                                 LIMIT 1 FOR UPDATE";
                $stmtDupCourse = $this->db->prepare($sqlDupCourse);
                $stmtDupCourse->bindValue(':id_hoc_sinh', $id_hoc_sinh, PDO::PARAM_INT);
                $stmtDupCourse->bindValue(':id_khoa_hoc', $lopHoc['id_khoa_hoc'], PDO::PARAM_INT);
                $stmtDupCourse->execute();
                if ($stmtDupCourse->fetch()) {
                    $this->db->rollBack();
                    error_log("Học sinh $id_hoc_sinh đã đăng ký khóa học {$lopHoc['id_khoa_hoc']} trước đó.");
                    return false;
                }
            } else {
                $sqlDupCourse = "SELECT dk.id FROM dang_ky dk
                                 INNER JOIN lop_hoc lh2 ON dk.id_lop = lh2.id
                                 WHERE dk.email = :email
                                   AND lh2.id_khoa_hoc = :id_khoa_hoc
                                   AND dk.trang_thai NOT IN ('Đã hủy','Từ chối')
                                 LIMIT 1 FOR UPDATE";
                $stmtDupCourse = $this->db->prepare($sqlDupCourse);
                $stmtDupCourse->bindValue(':email', $hocSinh['email']);
                $stmtDupCourse->bindValue(':id_khoa_hoc', $lopHoc['id_khoa_hoc'], PDO::PARAM_INT);
                $stmtDupCourse->execute();
                if ($stmtDupCourse->fetch()) {
                    $this->db->rollBack();
                    error_log("Email {$hocSinh['email']} đã đăng ký khóa học {$lopHoc['id_khoa_hoc']} trước đó.");
                    return false;
                }
            }
            
            if ($hasIdHocSinh) {
                // Bảng đã có cột id_hoc_sinh (cấu trúc mới)
                // Kiểm tra xem có cột ngay_dang_ky hay ngay_tao
                $checkNgayDangKy = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'ngay_dang_ky'");
                $hasNgayDangKy = $checkNgayDangKy->rowCount() > 0;
                $ngayColumn = $hasNgayDangKy ? 'ngay_dang_ky' : 'ngay_tao';
                
                // Kiểm tra cột vnp_TxnRef
                $checkVnpColumn = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'vnp_TxnRef'");
                $hasVnpColumn = $checkVnpColumn->rowCount() > 0;
                
                if ($hasVnpColumn) {
                    $sql = "INSERT INTO dang_ky (id_hoc_sinh, id_lop, trang_thai, $ngayColumn, vnp_TxnRef)
                            VALUES (:id_hoc_sinh, :id_lop, :trang_thai, NOW(), :vnp_TxnRef)";
                } else {
                    $sql = "INSERT INTO dang_ky (id_hoc_sinh, id_lop, trang_thai, $ngayColumn)
                            VALUES (:id_hoc_sinh, :id_lop, :trang_thai, NOW())";
                }
                
                $params = [
                    ':id_hoc_sinh' => $id_hoc_sinh,
                    ':id_lop' => $id_lop,
                    ':trang_thai' => $trang_thai
                ];
                
                if ($hasVnpColumn) {
                    $params[':vnp_TxnRef'] = $vnp_TxnRef;
                }
                
                $stmt = $this->db->prepare($sql);
                
                // Log SQL và params để debug
                error_log("SQL: " . $sql);
                error_log("Params: " . json_encode($params));
                
                $result = $stmt->execute($params);
                
                if (!$result) {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Lỗi khi execute SQL insert (cấu trúc mới): " . implode(", ", $errorInfo));
                    error_log("Error Code: " . ($errorInfo[0] ?? 'N/A'));
                    error_log("Error Message: " . ($errorInfo[2] ?? 'N/A'));
                    $this->db->rollBack();
                    return false;
                }
            } else {
                // Bảng cũ, cần insert với id_khoa_hoc, ho_ten, email, sdt
                $checkVnpColumn = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'vnp_TxnRef'");
                $hasVnpColumn = $checkVnpColumn->rowCount() > 0;
                
                if ($hasVnpColumn) {
                    $sql = "INSERT INTO dang_ky (id_khoa_hoc, id_lop, ho_ten, email, sdt, trang_thai, ngay_tao, vnp_TxnRef)
                            VALUES (:id_khoa_hoc, :id_lop, :ho_ten, :email, :sdt, :trang_thai, NOW(), :vnp_TxnRef)";
                } else {
                    $sql = "INSERT INTO dang_ky (id_khoa_hoc, id_lop, ho_ten, email, sdt, trang_thai, ngay_tao)
                            VALUES (:id_khoa_hoc, :id_lop, :ho_ten, :email, :sdt, :trang_thai, NOW())";
                }
                
                $params = [
                    ':id_khoa_hoc' => $lopHoc['id_khoa_hoc'],
                    ':id_lop' => $id_lop,
                    ':ho_ten' => $hocSinh['ho_ten'],
                    ':email' => $hocSinh['email'],
                    ':sdt' => $hocSinh['so_dien_thoai'] ?? '',
                    ':trang_thai' => $trang_thai
                ];
                
                if ($hasVnpColumn) {
                    $params[':vnp_TxnRef'] = $vnp_TxnRef;
                }
                
                $stmt = $this->db->prepare($sql);
                
                // Log SQL và params để debug
                error_log("SQL: " . $sql);
                error_log("Params: " . json_encode($params));
                
                $result = $stmt->execute($params);
                
                if (!$result) {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Lỗi khi execute SQL insert (cấu trúc cũ): " . implode(", ", $errorInfo));
                    error_log("Error Code: " . ($errorInfo[0] ?? 'N/A'));
                    error_log("Error Message: " . ($errorInfo[2] ?? 'N/A'));
                    $this->db->rollBack();
                    return false;
                }
            }

            $insertId = $this->db->lastInsertId();
            
            // Log để debug
            error_log("Insert thành công - lastInsertId: " . var_export($insertId, true));
            
            // Kiểm tra kết quả
            if ($insertId === false || $insertId === '0' || empty($insertId)) {
                error_log("lastInsertId() trả về giá trị không hợp lệ: " . var_export($insertId, true));
                error_log("SQL executed: " . ($result ? 'true' : 'false'));
                
                // Thử lấy ID bằng cách query lại
                if ($hasIdHocSinh) {
                    $checkSql = "SELECT id FROM dang_ky WHERE id_hoc_sinh = :id_hoc_sinh AND id_lop = :id_lop ORDER BY id DESC LIMIT 1";
                    $checkStmt = $this->db->prepare($checkSql);
                    $checkStmt->execute([':id_hoc_sinh' => $id_hoc_sinh, ':id_lop' => $id_lop]);
                    $result = $checkStmt->fetch();
                    if ($result && isset($result['id'])) {
                        error_log("Tìm thấy ID bằng query lại: " . $result['id']);
                        $this->db->commit();
                        return $result['id'];
                    }
                } else {
                    $checkSql = "SELECT id FROM dang_ky WHERE email = :email AND id_lop = :id_lop ORDER BY id DESC LIMIT 1";
                    $checkStmt = $this->db->prepare($checkSql);
                    $checkStmt->execute([':email' => $hocSinh['email'], ':id_lop' => $id_lop]);
                    $result = $checkStmt->fetch();
                    if ($result && isset($result['id'])) {
                        error_log("Tìm thấy ID bằng query lại: " . $result['id']);
                        $this->db->commit();
                        return $result['id'];
                    }
                }
                
                // Nếu vẫn không tìm thấy, có thể là lỗi insert
                error_log("Không thể lấy ID đăng ký sau khi insert");
                $this->db->rollBack();
                return false;
            }
            
            error_log("Đăng ký thành công với ID: " . $insertId);
            $this->db->commit();
            return $insertId;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Lỗi đăng ký khóa học: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    /**
     * Hủy các đăng ký quá hạn (sau 10 phút chưa thanh toán)
     * Chỉ hủy các đăng ký có trạng thái "Chờ xác nhận" và có vnp_TxnRef (đăng ký online)
     */
    public function huyDangKyQuaHan() {
        try {
            // Kiểm tra xem có cột vnp_TxnRef không
            $checkVnpColumn = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'vnp_TxnRef'");
            $hasVnpColumn = $checkVnpColumn->rowCount() > 0;
            
            if (!$hasVnpColumn) {
                // Nếu không có cột vnp_TxnRef, không thể xác định đăng ký online
                return 0;
            }
            
            // Kiểm tra cột ngay_dang_ky hoặc ngay_tao
            $checkNgayDangKy = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'ngay_dang_ky'");
            $hasNgayDangKy = $checkNgayDangKy->rowCount() > 0;
            $ngayColumn = $hasNgayDangKy ? 'ngay_dang_ky' : 'ngay_tao';
            
            // Tìm các đăng ký:
            // - Trạng thái = "Chờ xác nhận"
            // - Có vnp_TxnRef (đã tạo mã đơn hàng VNPay)
            // - vnp_TransactionNo IS NULL (chưa thanh toán)
            // - Quá 10 phút từ lúc tạo
            $sql = "UPDATE dang_ky 
                    SET trang_thai = 'Đã hủy'
                    WHERE trang_thai = 'Chờ xác nhận'
                    AND vnp_TxnRef IS NOT NULL
                    AND (vnp_TransactionNo IS NULL OR vnp_TransactionNo = '')
                    AND $ngayColumn < DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $count = $stmt->rowCount();
            
            if ($count > 0) {
                error_log("Đã hủy $count đăng ký quá hạn (sau 10 phút chưa thanh toán)");
            }
            
            return $count;
        } catch (PDOException $e) {
            error_log("Lỗi khi hủy đăng ký quá hạn: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Cập nhật mã đơn hàng VNPay vào đăng ký
     */
    public function updateVNPayTxnRef($id_dang_ky, $vnp_TxnRef) {
        try {
            // Kiểm tra xem bảng có cột vnp_TxnRef hay không
            $checkColumn = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'vnp_TxnRef'");
            if ($checkColumn->rowCount() == 0) {
                error_log("Bảng dang_ky chưa có cột vnp_TxnRef. Vui lòng chạy migration SQL.");
                return false;
            }
            
            $sql = "UPDATE dang_ky 
                    SET vnp_TxnRef = :vnp_TxnRef
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':vnp_TxnRef' => $vnp_TxnRef,
                ':id' => $id_dang_ky
            ]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật mã đơn hàng VNPay: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật thông tin thanh toán VNPay
     */
    public function updateVNPayInfo($vnp_TxnRef, $vnp_TransactionNo, $vnp_ResponseCode) {
        try {
            $trang_thai = ($vnp_ResponseCode === '00') ? 'Đã xác nhận' : 'Chờ xác nhận';
            
            // Kiểm tra xem bảng có các cột VNPay hay không
            $checkVnpTxnRef = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'vnp_TxnRef'");
            $checkVnpTransactionNo = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'vnp_TransactionNo'");
            
            if ($checkVnpTxnRef->rowCount() == 0 || $checkVnpTransactionNo->rowCount() == 0) {
                error_log("Bảng dang_ky chưa có các cột VNPay. Vui lòng chạy migration SQL.");
                return false;
            }
            
            $sql = "UPDATE dang_ky 
                    SET vnp_TransactionNo = :vnp_TransactionNo,
                        trang_thai = :trang_thai
                    WHERE vnp_TxnRef = :vnp_TxnRef";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':vnp_TransactionNo' => $vnp_TransactionNo,
                ':trang_thai' => $trang_thai,
                ':vnp_TxnRef' => $vnp_TxnRef
            ]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật thông tin VNPay: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kiểm tra học sinh đã đăng ký khóa học chưa (trạng thái "Đã xác nhận")
     */
    public function daDangKyKhoaHoc($id_hoc_sinh, $id_khoa_hoc) {
        try {
            $sql = "SELECT COUNT(*) as count
                    FROM dang_ky dk
                    INNER JOIN lop_hoc lh ON dk.id_lop = lh.id
                    WHERE dk.id_hoc_sinh = :id_hoc_sinh
                    AND lh.id_khoa_hoc = :id_khoa_hoc
                    AND dk.trang_thai = 'Đã xác nhận'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id_hoc_sinh' => $id_hoc_sinh,
                ':id_khoa_hoc' => $id_khoa_hoc
            ]);
            
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra đăng ký khóa học: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy danh sách khóa học đã đăng ký của học sinh (group by khóa học)
     */
    public function getKhoaHocDaDangKy($id_hoc_sinh) {
        try {
            $sql = "SELECT DISTINCT
                       kh.id as id_khoa_hoc,
                       kh.ten_khoa_hoc,
                       kh.mo_ta,
                       kh.gia,
                       kh.hinh_anh,
                       kh.trang_thai,
                       d.ten_danh_muc,
                       COUNT(DISTINCT dk.id) as so_lop_da_dang_ky,
                       MIN(dk.ngay_dang_ky) as ngay_dang_ky_dau_tien
                FROM dang_ky dk
                INNER JOIN lop_hoc lh ON dk.id_lop = lh.id
                INNER JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                LEFT JOIN danh_muc d ON kh.id_danh_muc = d.id
                WHERE dk.id_hoc_sinh = :id_hoc_sinh
                AND dk.trang_thai = 'Đã xác nhận'
                GROUP BY kh.id, kh.ten_khoa_hoc, kh.mo_ta, kh.gia, kh.hinh_anh, kh.trang_thai, d.ten_danh_muc
                ORDER BY ngay_dang_ky_dau_tien DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_hoc_sinh', $id_hoc_sinh, PDO::PARAM_INT);
            $stmt->execute();
            $khoaHocs = $stmt->fetchAll();
            
            // Lấy thông tin lớp học đã đăng ký cho mỗi khóa học
            foreach ($khoaHocs as &$khoaHoc) {
                $sqlLop = "SELECT dk.id as id_dang_ky,
                                  dk.trang_thai as trang_thai_dang_ky,
                                  dk.ngay_dang_ky,
                                  lh.id as id_lop,
                                  lh.ten_lop,
                                  lh.mo_ta as mo_ta_lop
                           FROM dang_ky dk
                           INNER JOIN lop_hoc lh ON dk.id_lop = lh.id
                           WHERE dk.id_hoc_sinh = :id_hoc_sinh
                           AND lh.id_khoa_hoc = :id_khoa_hoc
                           AND dk.trang_thai = 'Đã xác nhận'
                           ORDER BY dk.ngay_dang_ky DESC";
                
                $stmtLop = $this->db->prepare($sqlLop);
                $stmtLop->bindValue(':id_hoc_sinh', $id_hoc_sinh, PDO::PARAM_INT);
                $stmtLop->bindValue(':id_khoa_hoc', $khoaHoc['id_khoa_hoc'], PDO::PARAM_INT);
                $stmtLop->execute();
                $khoaHoc['lop_hoc'] = $stmtLop->fetchAll();
            }
            
            return $khoaHocs;
        } catch (PDOException $e) {
            error_log("Lỗi lấy khóa học đã đăng ký: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy thông tin đăng ký theo mã đơn hàng VNPay
     */
    public function getDangKyByVnpTxnRef($vnp_TxnRef) {
        try {
            // Kiểm tra xem bảng có cột id_hoc_sinh hay không
            $checkColumn = $this->db->query("SHOW COLUMNS FROM dang_ky LIKE 'id_hoc_sinh'");
            $hasIdHocSinh = $checkColumn->rowCount() > 0;
            
            if ($hasIdHocSinh) {
                $sql = "SELECT dk.*, lh.ten_lop, kh.ten_khoa_hoc, kh.gia
                        FROM dang_ky dk
                        LEFT JOIN lop_hoc lh ON dk.id_lop = lh.id
                        LEFT JOIN khoa_hoc kh ON lh.id_khoa_hoc = kh.id
                        WHERE dk.vnp_TxnRef = :vnp_TxnRef
                        LIMIT 1";
            } else {
                $sql = "SELECT dk.*, lh.ten_lop, kh.ten_khoa_hoc, kh.gia
                        FROM dang_ky dk
                        LEFT JOIN lop_hoc lh ON dk.id_lop = lh.id
                        LEFT JOIN khoa_hoc kh ON dk.id_khoa_hoc = kh.id
                        WHERE dk.vnp_TxnRef = :vnp_TxnRef
                        LIMIT 1";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':vnp_TxnRef' => $vnp_TxnRef]);
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Lỗi lấy thông tin đăng ký: " . $e->getMessage());
            return false;
        }
    }

    // Lấy danh sách liên hệ (chỉ lấy những cái đang hiển thị)
    public function getLienHe()
    {
        try {
            // Đảm bảo bảng tồn tại
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
            $this->db->exec($sql);
        } catch (PDOException $e) {
            // Bảng đã tồn tại hoặc có lỗi, bỏ qua
        }

        $sql = "SELECT * FROM lien_he 
                WHERE trang_thai = 1 
                ORDER BY thu_tu ASC, id ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
