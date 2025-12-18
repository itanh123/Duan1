-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 18, 2025 at 03:58 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `duan1`
--

-- --------------------------------------------------------

--
-- Table structure for table `binh_luan`
--

CREATE TABLE `binh_luan` (
  `id` int NOT NULL,
  `id_khoa_hoc` int NOT NULL,
  `id_hoc_sinh` int NOT NULL,
  `noi_dung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `danh_gia` tinyint UNSIGNED DEFAULT NULL,
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `trang_thai` enum('Hiển thị','Ẩn','Đã xóa') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Hiển thị'
) ;

--
-- Dumping data for table `binh_luan`
--

INSERT INTO `binh_luan` (`id`, `id_khoa_hoc`, `id_hoc_sinh`, `noi_dung`, `danh_gia`, `ngay_tao`, `trang_thai`) VALUES
(1, 1, 4, 'Khóa học dễ hiểu', 5, '2025-12-18 03:57:57', 'Hiển thị'),
(2, 2, 4, 'Giảng viên nhiệt tình', 5, '2025-12-18 03:57:57', 'Hiển thị'),
(3, 3, 5, 'Nội dung thực tế', 4, '2025-12-18 03:57:57', 'Hiển thị'),
(4, 4, 4, 'Học ổn', 4, '2025-12-18 03:57:57', 'Hiển thị'),
(5, 5, 5, 'Phù hợp người mới', 5, '2025-12-18 03:57:57', 'Hiển thị');

-- --------------------------------------------------------

--
-- Table structure for table `ca_hoc`
--

CREATE TABLE `ca_hoc` (
  `id` int NOT NULL,
  `id_lop` int NOT NULL,
  `id_giang_vien` int DEFAULT NULL,
  `id_ca` int NOT NULL,
  `thu_trong_tuan` enum('Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7','Chủ nhật') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ghi_chu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_phong` int DEFAULT NULL,
  `ngay_hoc` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ca_hoc`
--

INSERT INTO `ca_hoc` (`id`, `id_lop`, `id_giang_vien`, `id_ca`, `thu_trong_tuan`, `ghi_chu`, `id_phong`, `ngay_hoc`) VALUES
(1, 1, 2, 1, 'Thứ 2', NULL, 1, '2025-01-13'),
(2, 1, 2, 2, 'Thứ 4', NULL, 1, '2025-01-15'),
(3, 2, 3, 1, 'Thứ 3', NULL, 2, '2025-01-16'),
(4, 3, 2, 3, 'Thứ 6', NULL, 4, '2025-02-07'),
(5, 4, 3, 2, 'Thứ 7', NULL, 5, '2025-02-15');

-- --------------------------------------------------------

--
-- Table structure for table `ca_mac_dinh`
--

CREATE TABLE `ca_mac_dinh` (
  `id` int NOT NULL,
  `ten_ca` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gio_bat_dau` time DEFAULT NULL,
  `gio_ket_thuc` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ca_mac_dinh`
--

INSERT INTO `ca_mac_dinh` (`id`, `ten_ca`, `gio_bat_dau`, `gio_ket_thuc`) VALUES
(1, 'Ca 1', '07:15:00', '09:15:00'),
(2, 'Ca 2', '09:30:00', '11:30:00'),
(3, 'Ca 3', '14:00:00', '16:00:00'),
(4, 'Ca 4', '16:15:00', '18:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `dang_ky`
--

CREATE TABLE `dang_ky` (
  `id` int NOT NULL,
  `id_hoc_sinh` int NOT NULL,
  `id_lop` int NOT NULL,
  `ngay_dang_ky` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `trang_thai` enum('Chờ xác nhận','Đã xác nhận','Đã hủy') COLLATE utf8mb4_unicode_ci DEFAULT 'Chờ xác nhận',
  `vnp_TxnRef` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã đơn hàng VNPay',
  `vnp_TransactionNo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã giao dịch VNPay'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dang_ky`
--

INSERT INTO `dang_ky` (`id`, `id_hoc_sinh`, `id_lop`, `ngay_dang_ky`, `trang_thai`, `vnp_TxnRef`, `vnp_TransactionNo`) VALUES
(1, 4, 1, '2025-12-18 03:57:35', 'Đã xác nhận', NULL, NULL),
(2, 5, 1, '2025-12-18 03:57:35', 'Chờ xác nhận', NULL, NULL),
(3, 4, 2, '2025-12-18 03:57:35', 'Đã xác nhận', NULL, NULL),
(4, 5, 3, '2025-12-18 03:57:35', 'Chờ xác nhận', NULL, NULL),
(5, 4, 4, '2025-12-18 03:57:35', 'Đã hủy', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `danh_muc`
--

CREATE TABLE `danh_muc` (
  `id` int NOT NULL,
  `ten_danh_muc` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duong_dan` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `trang_thai` tinyint DEFAULT '1',
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `danh_muc`
--

INSERT INTO `danh_muc` (`id`, `ten_danh_muc`, `duong_dan`, `mo_ta`, `trang_thai`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 'Lập trình Web', 'lap-trinh-web', 'HTML CSS JS PHP', 1, '2025-12-18 03:56:58', NULL),
(2, 'Backend', 'backend', 'PHP NodeJS', 1, '2025-12-18 03:56:58', NULL),
(3, 'Frontend', 'frontend', 'React Vue', 1, '2025-12-18 03:56:58', NULL),
(4, 'AI', 'ai', 'Machine Learning', 1, '2025-12-18 03:56:58', NULL),
(5, 'DevOps', 'devops', 'Docker CI/CD', 1, '2025-12-18 03:56:58', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hoan_tien`
--

CREATE TABLE `hoan_tien` (
  `id` int NOT NULL,
  `id_thanh_toan` int NOT NULL COMMENT 'ID thanh toán gốc',
  `ma_hoan_tien` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã đơn hàng hoàn tiền (REF...)',
  `ma_giao_dich_hoan_tien` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã giao dịch hoàn tiền từ VNPay',
  `so_tien_hoan` decimal(15,2) NOT NULL COMMENT 'Số tiền hoàn (VND)',
  `ly_do` text COLLATE utf8mb4_unicode_ci COMMENT 'Lý do hoàn tiền',
  `trang_thai` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Đang xử lý' COMMENT 'Trạng thái: Đang xử lý, Thành công, Thất bại',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu thông tin hoàn tiền';

-- --------------------------------------------------------

--
-- Table structure for table `hoa_don`
--

CREATE TABLE `hoa_don` (
  `id` int NOT NULL,
  `id_thanh_toan` int NOT NULL,
  `ma_hoa_don` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_xuat` datetime DEFAULT CURRENT_TIMESTAMP,
  `noi_dung` text COLLATE utf8mb4_unicode_ci,
  `tong_tien` decimal(12,2) NOT NULL,
  `tinh_trang` enum('Hợp lệ','Đã hủy') COLLATE utf8mb4_unicode_ci DEFAULT 'Hợp lệ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `khoa_hoc`
--

CREATE TABLE `khoa_hoc` (
  `id` int NOT NULL,
  `id_danh_muc` int NOT NULL,
  `ten_khoa_hoc` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `gia` decimal(12,2) DEFAULT '0.00',
  `hinh_anh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trang_thai` tinyint DEFAULT '1',
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `khoa_hoc`
--

INSERT INTO `khoa_hoc` (`id`, `id_danh_muc`, `ten_khoa_hoc`, `mo_ta`, `gia`, `hinh_anh`, `trang_thai`, `ngay_tao`) VALUES
(1, 1, 'HTML CSS', 'Nền tảng web', '1500000.00', NULL, 1, '2025-12-18 03:57:05'),
(2, 1, 'PHP Cơ bản', 'PHP + MySQL', '2500000.00', NULL, 1, '2025-12-18 03:57:05'),
(3, 2, 'NodeJS', 'Backend Node', '2600000.00', NULL, 1, '2025-12-18 03:57:05'),
(4, 3, 'ReactJS', 'Frontend hiện đại', '2800000.00', NULL, 1, '2025-12-18 03:57:05'),
(5, 4, 'Machine Learning', 'AI cơ bản', '3500000.00', NULL, 1, '2025-12-18 03:57:05');

-- --------------------------------------------------------

--
-- Table structure for table `lien_he`
--

CREATE TABLE `lien_he` (
  `id` int NOT NULL,
  `ten` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên kênh liên hệ (Zalo, Messenger, etc.)',
  `loai` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Loại: zalo, messenger, phone, email, etc.',
  `gia_tri` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Giá trị: số điện thoại, link, email, etc.',
  `mo_ta` text COLLATE utf8mb4_unicode_ci COMMENT 'Mô tả',
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Icon hoặc emoji',
  `thu_tu` int DEFAULT '0' COMMENT 'Thứ tự hiển thị',
  `trang_thai` tinyint(1) DEFAULT '1' COMMENT '1: hiển thị, 0: ẩn',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lop_hoc`
--

CREATE TABLE `lop_hoc` (
  `id` int NOT NULL,
  `id_khoa_hoc` int NOT NULL,
  `ten_lop` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `so_luong_toi_da` int DEFAULT '30',
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `ngay_bat_dau` date DEFAULT NULL,
  `ngay_ket_thuc` date DEFAULT NULL,
  `trang_thai` enum('Chưa khai giảng','Đang học','Kết thúc') COLLATE utf8mb4_unicode_ci DEFAULT 'Chưa khai giảng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lop_hoc`
--

INSERT INTO `lop_hoc` (`id`, `id_khoa_hoc`, `ten_lop`, `so_luong_toi_da`, `mo_ta`, `ngay_bat_dau`, `ngay_ket_thuc`, `trang_thai`) VALUES
(1, 1, 'HTML01', 30, NULL, '2025-01-10', '2025-03-10', 'Đang học'),
(2, 2, 'PHP01', 30, NULL, '2025-01-15', '2025-03-20', 'Đang học'),
(3, 3, 'NODE01', 30, NULL, '2025-02-01', '2025-04-01', 'Chưa khai giảng'),
(4, 4, 'REACT01', 30, NULL, '2025-02-10', '2025-04-10', 'Chưa khai giảng'),
(5, 5, 'ML01', 30, NULL, '2025-03-01', '2025-05-01', 'Chưa khai giảng');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` int NOT NULL,
  `ma_nguoi_dung` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ho_ten` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mat_khau` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `so_dien_thoai` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dia_chi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vai_tro` enum('hoc_sinh','giang_vien','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `trang_thai` tinyint DEFAULT '1',
  `ngay_tao` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `ma_nguoi_dung`, `ho_ten`, `email`, `mat_khau`, `so_dien_thoai`, `dia_chi`, `vai_tro`, `trang_thai`, `ngay_tao`) VALUES
(1, 'AD0001', 'Admin', 'admin@system.com', 'admin123', NULL, NULL, 'admin', 1, '2025-12-18 03:56:35'),
(2, 'GV0001', 'GV Nguyễn A', 'gva@fpt.edu.vn', '123456', NULL, NULL, 'giang_vien', 1, '2025-12-18 03:56:35'),
(3, 'GV0002', 'GV Trần B', 'gvb@fpt.edu.vn', '123456', NULL, NULL, 'giang_vien', 1, '2025-12-18 03:56:35'),
(4, 'HS0001', 'HS Lê C', 'hsc@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-12-18 03:56:35'),
(5, 'HS0002', 'HS Phạm D', 'hsd@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-12-18 03:56:35');

--
-- Triggers `nguoi_dung`
--
DELIMITER $$
CREATE TRIGGER `trg_tao_ma_nguoi_dung` BEFORE INSERT ON `nguoi_dung` FOR EACH ROW BEGIN
    DECLARE max_number INT;

    IF NEW.vai_tro = 'giang_vien' THEN
        SELECT IFNULL(MAX(CAST(SUBSTRING(ma_nguoi_dung,3) AS UNSIGNED)),0)
        INTO max_number
        FROM nguoi_dung
        WHERE ma_nguoi_dung LIKE 'GV%';

        SET NEW.ma_nguoi_dung = CONCAT('GV', LPAD(max_number + 1, 4, '0'));

    ELSEIF NEW.vai_tro = 'hoc_sinh' THEN
        SELECT IFNULL(MAX(CAST(SUBSTRING(ma_nguoi_dung,3) AS UNSIGNED)),0)
        INTO max_number
        FROM nguoi_dung
        WHERE ma_nguoi_dung LIKE 'HS%';

        SET NEW.ma_nguoi_dung = CONCAT('HS', LPAD(max_number + 1, 4, '0'));

    ELSEIF NEW.vai_tro = 'admin' THEN
        SELECT IFNULL(MAX(CAST(SUBSTRING(ma_nguoi_dung,3) AS UNSIGNED)),0)
        INTO max_number
        FROM nguoi_dung
        WHERE ma_nguoi_dung LIKE 'AD%';

        SET NEW.ma_nguoi_dung = CONCAT('AD', LPAD(max_number + 1, 4, '0'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung_vai_tro`
--

CREATE TABLE `nguoi_dung_vai_tro` (
  `id` int NOT NULL,
  `id_nguoi_dung` int NOT NULL,
  `vai_tro` enum('admin','giang_vien','hoc_sinh') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ngay_tao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nguoi_dung_vai_tro`
--

INSERT INTO `nguoi_dung_vai_tro` (`id`, `id_nguoi_dung`, `vai_tro`, `ngay_tao`) VALUES
(1, 1, 'admin', '2025-12-18 03:56:41'),
(2, 2, 'giang_vien', '2025-12-18 03:56:41'),
(3, 3, 'giang_vien', '2025-12-18 03:56:41'),
(4, 4, 'hoc_sinh', '2025-12-18 03:56:41'),
(5, 5, 'hoc_sinh', '2025-12-18 03:56:41');

-- --------------------------------------------------------

--
-- Table structure for table `phan_hoi_binh_luan`
--

CREATE TABLE `phan_hoi_binh_luan` (
  `id` int NOT NULL,
  `id_binh_luan` int NOT NULL COMMENT 'ID bình luận được trả lời',
  `id_admin` int NOT NULL COMMENT 'ID admin trả lời',
  `noi_dung` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nội dung phản hồi',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `phan_quyen`
--

CREATE TABLE `phan_quyen` (
  `id` int NOT NULL,
  `id_nguoi_dung` int NOT NULL,
  `ten_quyen` enum('xem','them','sua','xoa','quan_tri') COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phan_quyen`
--

INSERT INTO `phan_quyen` (`id`, `id_nguoi_dung`, `ten_quyen`) VALUES
(1, 1, 'quan_tri'),
(2, 2, 'xem'),
(3, 2, 'them'),
(4, 3, 'xem'),
(5, 3, 'sua');

-- --------------------------------------------------------

--
-- Table structure for table `phong_hoc`
--

CREATE TABLE `phong_hoc` (
  `id` int NOT NULL,
  `ten_phong` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `suc_chua` int DEFAULT '30',
  `mo_ta` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trang_thai` enum('Sử dụng','Bảo trì','Khóa') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Sử dụng'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `phong_hoc`
--

INSERT INTO `phong_hoc` (`id`, `ten_phong`, `suc_chua`, `mo_ta`, `trang_thai`) VALUES
(1, 'P101', 40, 'Phòng lớn tầng 1', 'Sử dụng'),
(2, 'P203', 100, 'Phòng máy lạnh tầng 2', 'Sử dụng'),
(3, 'P305', 25, 'Phòng học nhóm', 'Bảo trì'),
(4, 'IT301', 40, 'Phòng Backend', 'Sử dụng'),
(5, 'IT302', 35, 'Phòng AI', 'Sử dụng'),
(6, 'IT303', 25, 'Phòng An ninh mạng', 'Sử dụng');

-- --------------------------------------------------------

--
-- Table structure for table `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id` int NOT NULL,
  `id_hoc_sinh` int NOT NULL,
  `id_dang_ky` int NOT NULL,
  `phuong_thuc` enum('MOMO','VNPAY','ZALOPAY','CHUYEN_KHOAN') COLLATE utf8mb4_unicode_ci NOT NULL,
  `so_tien` decimal(12,2) NOT NULL,
  `ngay_thanh_toan` datetime DEFAULT CURRENT_TIMESTAMP,
  `trang_thai` enum('Chờ xác nhận','Thành công','Thất bại','Hoàn tiền') COLLATE utf8mb4_unicode_ci DEFAULT 'Chờ xác nhận',
  `ma_giao_dich` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thanh_toan`
--

INSERT INTO `thanh_toan` (`id`, `id_hoc_sinh`, `id_dang_ky`, `phuong_thuc`, `so_tien`, `ngay_thanh_toan`, `trang_thai`, `ma_giao_dich`) VALUES
(1, 4, 1, 'VNPAY', '1500000.00', '2025-12-18 10:57:44', 'Thành công', NULL),
(2, 4, 3, 'MOMO', '2500000.00', '2025-12-18 10:57:44', 'Thành công', NULL),
(3, 5, 2, 'ZALOPAY', '1500000.00', '2025-12-18 10:57:44', 'Chờ xác nhận', NULL),
(4, 5, 4, 'VNPAY', '2600000.00', '2025-12-18 10:57:44', 'Thành công', NULL),
(5, 4, 5, 'VNPAY', '2800000.00', '2025-12-18 10:57:44', 'Hoàn tiền', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_thong_ke_doanh_thu`
-- (See below for the actual view)
--
CREATE TABLE `v_thong_ke_doanh_thu` (
`so_luong_thanh_toan` bigint
,`ten_khoa_hoc` varchar(200)
,`tong_doanh_thu` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `yeu_cau_doi_lich`
--

CREATE TABLE `yeu_cau_doi_lich` (
  `id` int NOT NULL,
  `id_giang_vien` int NOT NULL,
  `id_ca_hoc_cu` int NOT NULL COMMENT 'ID ca học hiện tại cần đổi',
  `id_lop` int NOT NULL,
  `thu_trong_tuan_moi` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Thứ trong tuần mới (Thứ 2, Thứ 3, ...)',
  `id_ca_moi` int DEFAULT NULL COMMENT 'ID ca mặc định mới',
  `id_phong_moi` int DEFAULT NULL COMMENT 'ID phòng học mới',
  `ngay_doi` date DEFAULT NULL COMMENT 'Ngày cụ thể cần đổi (nếu đổi một ngày cụ thể)',
  `ly_do` text COLLATE utf8mb4_unicode_ci COMMENT 'Lý do đổi lịch',
  `trang_thai` enum('cho_duyet','da_duyet','tu_choi') COLLATE utf8mb4_unicode_ci DEFAULT 'cho_duyet',
  `ghi_chu_admin` text COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú của admin khi duyệt/từ chối',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure for view `v_thong_ke_doanh_thu`
--
DROP TABLE IF EXISTS `v_thong_ke_doanh_thu`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_thong_ke_doanh_thu`  AS SELECT `kh`.`ten_khoa_hoc` AS `ten_khoa_hoc`, sum(`tt`.`so_tien`) AS `tong_doanh_thu`, count(`tt`.`id`) AS `so_luong_thanh_toan` FROM (((`thanh_toan` `tt` join `dang_ky` `dk` on((`tt`.`id_dang_ky` = `dk`.`id`))) join `lop_hoc` `lh` on((`dk`.`id_lop` = `lh`.`id`))) join `khoa_hoc` `kh` on((`lh`.`id_khoa_hoc` = `kh`.`id`))) WHERE (`tt`.`trang_thai` = 'Thành công') GROUP BY `kh`.`ten_khoa_hoc``ten_khoa_hoc`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_khoa_hoc` (`id_khoa_hoc`),
  ADD KEY `id_hoc_sinh` (`id_hoc_sinh`);

--
-- Indexes for table `ca_hoc`
--
ALTER TABLE `ca_hoc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_lop` (`id_lop`),
  ADD KEY `id_giang_vien` (`id_giang_vien`),
  ADD KEY `fk_ca_hoc_mac_dinh` (`id_ca`),
  ADD KEY `fk_ca_hoc_phong` (`id_phong`);

--
-- Indexes for table `ca_mac_dinh`
--
ALTER TABLE `ca_mac_dinh`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dang_ky`
--
ALTER TABLE `dang_ky`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_lop` (`id_lop`),
  ADD KEY `idx_id_hoc_sinh` (`id_hoc_sinh`),
  ADD KEY `idx_vnp_TxnRef` (`vnp_TxnRef`);

--
-- Indexes for table `danh_muc`
--
ALTER TABLE `danh_muc`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `duong_dan` (`duong_dan`);

--
-- Indexes for table `hoan_tien`
--
ALTER TABLE `hoan_tien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_hoan_tien` (`ma_hoan_tien`),
  ADD KEY `idx_id_thanh_toan` (`id_thanh_toan`),
  ADD KEY `idx_ma_hoan_tien` (`ma_hoan_tien`);

--
-- Indexes for table `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_hoa_don` (`ma_hoa_don`),
  ADD KEY `id_thanh_toan` (`id_thanh_toan`);

--
-- Indexes for table `khoa_hoc`
--
ALTER TABLE `khoa_hoc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_danh_muc` (`id_danh_muc`);

--
-- Indexes for table `lien_he`
--
ALTER TABLE `lien_he`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lop_hoc`
--
ALTER TABLE `lop_hoc`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_khoa_hoc` (`id_khoa_hoc`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `idx_ma_nguoi_dung` (`ma_nguoi_dung`);

--
-- Indexes for table `nguoi_dung_vai_tro`
--
ALTER TABLE `nguoi_dung_vai_tro`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_nguoi_dung_vai_tro` (`id_nguoi_dung`,`vai_tro`),
  ADD KEY `idx_id_nguoi_dung` (`id_nguoi_dung`);

--
-- Indexes for table `phan_hoi_binh_luan`
--
ALTER TABLE `phan_hoi_binh_luan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_binh_luan` (`id_binh_luan`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `phan_quyen`
--
ALTER TABLE `phan_quyen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nguoi_dung` (`id_nguoi_dung`);

--
-- Indexes for table `phong_hoc`
--
ALTER TABLE `phong_hoc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_hoc_sinh` (`id_hoc_sinh`),
  ADD KEY `id_dang_ky` (`id_dang_ky`);

--
-- Indexes for table `yeu_cau_doi_lich`
--
ALTER TABLE `yeu_cau_doi_lich`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_giang_vien` (`id_giang_vien`),
  ADD KEY `id_ca_hoc_cu` (`id_ca_hoc_cu`),
  ADD KEY `id_lop` (`id_lop`),
  ADD KEY `trang_thai` (`trang_thai`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `binh_luan`
--
ALTER TABLE `binh_luan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ca_hoc`
--
ALTER TABLE `ca_hoc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ca_mac_dinh`
--
ALTER TABLE `ca_mac_dinh`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dang_ky`
--
ALTER TABLE `dang_ky`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `hoan_tien`
--
ALTER TABLE `hoan_tien`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hoa_don`
--
ALTER TABLE `hoa_don`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `khoa_hoc`
--
ALTER TABLE `khoa_hoc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lien_he`
--
ALTER TABLE `lien_he`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lop_hoc`
--
ALTER TABLE `lop_hoc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `nguoi_dung_vai_tro`
--
ALTER TABLE `nguoi_dung_vai_tro`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `phan_hoi_binh_luan`
--
ALTER TABLE `phan_hoi_binh_luan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `phan_quyen`
--
ALTER TABLE `phan_quyen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `phong_hoc`
--
ALTER TABLE `phong_hoc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `yeu_cau_doi_lich`
--
ALTER TABLE `yeu_cau_doi_lich`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD CONSTRAINT `binh_luan_ibfk_1` FOREIGN KEY (`id_khoa_hoc`) REFERENCES `khoa_hoc` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `binh_luan_ibfk_2` FOREIGN KEY (`id_hoc_sinh`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ca_hoc`
--
ALTER TABLE `ca_hoc`
  ADD CONSTRAINT `ca_hoc_ibfk_1` FOREIGN KEY (`id_lop`) REFERENCES `lop_hoc` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ca_hoc_ibfk_2` FOREIGN KEY (`id_giang_vien`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ca_hoc_mac_dinh` FOREIGN KEY (`id_ca`) REFERENCES `ca_mac_dinh` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ca_hoc_phong` FOREIGN KEY (`id_phong`) REFERENCES `phong_hoc` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dang_ky`
--
ALTER TABLE `dang_ky`
  ADD CONSTRAINT `dang_ky_ibfk_1` FOREIGN KEY (`id_hoc_sinh`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dang_ky_ibfk_2` FOREIGN KEY (`id_lop`) REFERENCES `lop_hoc` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hoan_tien`
--
ALTER TABLE `hoan_tien`
  ADD CONSTRAINT `fk_hoan_tien_thanh_toan` FOREIGN KEY (`id_thanh_toan`) REFERENCES `thanh_toan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `hoa_don_ibfk_1` FOREIGN KEY (`id_thanh_toan`) REFERENCES `thanh_toan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `khoa_hoc`
--
ALTER TABLE `khoa_hoc`
  ADD CONSTRAINT `khoa_hoc_ibfk_1` FOREIGN KEY (`id_danh_muc`) REFERENCES `danh_muc` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lop_hoc`
--
ALTER TABLE `lop_hoc`
  ADD CONSTRAINT `lop_hoc_ibfk_1` FOREIGN KEY (`id_khoa_hoc`) REFERENCES `khoa_hoc` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nguoi_dung_vai_tro`
--
ALTER TABLE `nguoi_dung_vai_tro`
  ADD CONSTRAINT `fk_nguoi_dung_vai_tro_nguoi_dung` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `phan_quyen`
--
ALTER TABLE `phan_quyen`
  ADD CONSTRAINT `phan_quyen_ibfk_1` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD CONSTRAINT `thanh_toan_ibfk_1` FOREIGN KEY (`id_hoc_sinh`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `thanh_toan_ibfk_2` FOREIGN KEY (`id_dang_ky`) REFERENCES `dang_ky` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
