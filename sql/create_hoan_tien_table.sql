-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 08, 2025 at 04:47 AM
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
(1, 1, 3, 'Khóa học rất hay, giảng viên nhiệt tình!', 5, '2025-11-14 04:17:46', 'Hiển thị'),
(2, 2, 3, 'JavaScript dễ hiểu và thực hành nhiều', 4, '2025-11-14 04:17:46', 'Hiển thị'),
(3, 1, 4, 'Nội dung ổn nhưng hơi nhanh', 4, '2025-11-14 04:17:46', 'Hiển thị'),
(4, 1, 4, 'ok', 5, '2025-11-21 03:29:01', 'Hiển thị'),
(5, 5, 9, 'okla', 5, '2025-11-21 03:30:04', 'Hiển thị');

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
(2, 1, 1, 1, 'Thứ 4', NULL, 1, '2025-12-02'),
(3, 3, 2, 1, 'Thứ 3', NULL, 2, NULL),
(5, 3, 1, 1, 'Thứ 2', '', 1, '2025-12-08'),
(7, 2, 2, 2, 'Thứ 3', '', 2, NULL),
(8, 3, 36, 3, 'Thứ 4', '', 4, NULL),
(9, 4, 1, 2, 'Thứ 5', '', NULL, '2025-12-08'),
(10, 5, 2, 2, 'Thứ 6', NULL, 2, NULL);

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
(1, 3, 1, '2025-11-14 04:17:46', 'Đã xác nhận', NULL, NULL),
(2, 4, 1, '2025-11-14 04:17:46', 'Chờ xác nhận', NULL, NULL),
(3, 3, 3, '2025-11-14 04:17:46', 'Đã xác nhận', NULL, NULL),
(14, 6, 1, '2025-11-19 08:02:10', 'Đã xác nhận', 'DK20251119080210000014', '15270538'),
(15, 6, 5, '2025-11-20 13:47:57', 'Chờ xác nhận', NULL, NULL),
(16, 6, 5, '2025-11-20 13:48:01', 'Chờ xác nhận', NULL, NULL),
(17, 6, 5, '2025-11-20 13:48:08', 'Chờ xác nhận', NULL, NULL),
(18, 6, 5, '2025-11-20 13:48:32', 'Chờ xác nhận', NULL, NULL),
(19, 6, 5, '2025-11-20 13:48:38', 'Chờ xác nhận', NULL, NULL),
(20, 6, 5, '2025-11-20 13:48:43', 'Chờ xác nhận', NULL, NULL),
(21, 6, 5, '2025-11-20 13:48:47', 'Chờ xác nhận', NULL, NULL),
(22, 6, 5, '2025-11-20 13:48:53', 'Chờ xác nhận', NULL, NULL),
(23, 6, 4, '2025-11-20 14:21:22', 'Chờ xác nhận', NULL, NULL),
(24, 6, 4, '2025-11-20 14:21:32', 'Chờ xác nhận', NULL, NULL),
(25, 6, 4, '2025-11-20 14:21:39', 'Đã xác nhận', NULL, NULL),
(26, 4, 4, '2025-11-20 14:24:22', 'Đã xác nhận', NULL, NULL),
(27, 8, 4, '2025-11-20 14:28:01', 'Đã xác nhận', NULL, NULL),
(28, 8, 4, '2025-11-20 14:44:27', 'Đã xác nhận', NULL, NULL),
(29, 6, 1, '2025-11-21 03:09:45', 'Đã xác nhận', 'DK20251121030945000029', '15274853'),
(30, 4, 2, '2025-11-21 03:16:08', 'Đã hủy', 'DK20251121031608000030', NULL),
(31, 4, 1, '2025-11-21 03:17:00', 'Đã xác nhận', 'DK20251121031700000031', '15274879'),
(32, 9, 5, '2025-11-21 03:37:29', 'Đã hủy', 'DK20251121033729000032', NULL),
(33, 9, 5, '2025-11-21 03:37:36', 'Đã xác nhận', NULL, NULL),
(34, 4, 1, '2025-11-23 13:04:20', 'Chờ xác nhận', 'DK20251123130421000034', '0');

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
(1, 'Lập trình Web', 'lap-trinh-web', 'Khóa học HTML, CSS, JS, PHP', 1, '2025-11-14 04:17:46', NULL),
(2, 'Khoa học dữ liệu', 'data-science', 'Machine Learning, Python', 1, '2025-11-14 04:17:46', NULL),
(3, 'Thiết kế đồ họa', 'thiet-ke-do-hoa', 'UI/UX, Photoshop, Illustrator a', 1, '2025-11-14 04:17:46', '2025-11-18 10:03:42'),
(39, 'Lập trình Backend', 'backend-it', 'Công nghệ backend server', 1, '2025-11-26 04:10:45', NULL),
(40, 'Lập trình Frontend', 'frontend-it', 'Giao diện người dùng', 1, '2025-11-26 04:10:45', NULL),
(41, 'Trí tuệ nhân tạo', 'ai-it', 'AI và Machine Learning', 1, '2025-11-26 04:10:45', NULL),
(42, 'An ninh mạng', 'cyber-it', 'Bảo mật hệ thống', 1, '2025-11-26 04:10:45', NULL),
(43, 'DevOps', 'devops-it', 'CI/CD & Server', 0, '2025-11-26 04:10:45', '2025-12-03 03:36:17');

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

--
-- Dumping data for table `hoa_don`
--

INSERT INTO `hoa_don` (`id`, `id_thanh_toan`, `ma_hoa_don`, `ngay_xuat`, `noi_dung`, `tong_tien`, `tinh_trang`) VALUES
(1, 1, 'HD001', '2025-11-14 11:17:46', 'Thanh toán khóa PHP', '2500000.00', 'Hợp lệ'),
(2, 2, 'HD002', '2025-11-14 11:17:46', 'Thanh toán khóa JS', '3000000.00', 'Hợp lệ');

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
(1, 1, 'Lập trình PHP từ cơ bản đến nâng cao', 'PHP + MySQL + MVC', '250000000.00', 'php.png', 1, '2025-11-14 04:17:46'),
(2, 1, 'JavaScript Mastery', 'Mastering JS từ cơ bản đến nâng cao', '3000000.00', 'js.png', 1, '2025-11-14 04:17:46'),
(3, 2, 'Nhập môn Machine Learning', 'Giải thuật ML cơ bản', '3500000.00', 'ml.png', 1, '2025-11-14 04:17:46'),
(4, 3, 'Thiết kế UI/UX chuyên sâu', 'Thiết kế giao diện với Figma', '2000000.00', 'uiux.png', 1, '2025-11-14 04:17:46'),
(5, 1, 'php23', 'học php', '1000000.00', 'khoa_hoc_1763455170_691c30c2813ad.png', 1, '2025-11-18 08:39:30'),
(52, 39, 'NodeJS Backend', 'Lập trình server với NodeJS', '2500000.00', NULL, 1, '2025-11-26 04:10:45'),
(53, 39, 'Laravel Pro', 'PHP Framework nâng cao', '2700000.00', NULL, 1, '2025-11-26 04:10:45'),
(54, 39, 'Spring Boot', 'Java Backend chuyên sâu', '2900000.00', NULL, 1, '2025-11-26 04:10:45'),
(55, 40, 'ReactJS', 'Giao diện React hiện đại', '2600000.00', NULL, 1, '2025-11-26 04:10:45'),
(56, 40, 'VueJS', 'Framework VueJS', '2200000.00', NULL, 1, '2025-11-26 04:10:45'),
(57, 40, 'HTML CSS Full', 'Thiết kế website cơ bản', '1800000.00', NULL, 1, '2025-11-26 04:10:45'),
(58, 41, 'Machine Learning', 'Nhập môn ML', '3500000.00', NULL, 1, '2025-11-26 04:10:45'),
(59, 41, 'Deep Learning', 'AI nâng cao', '4200000.00', NULL, 1, '2025-11-26 04:10:45'),
(60, 41, 'Python AI', 'AI với Python', '3900000.00', NULL, 1, '2025-11-26 04:10:45'),
(61, 42, 'Ethical Hacking', 'Pentest thực tế', '3200000.00', NULL, 1, '2025-11-26 04:10:45'),
(63, 42, 'Forensics', 'Điều tra số', '3100000.00', NULL, 1, '2025-11-26 04:10:45'),
(64, 43, 'Docker', 'Triển khai container', '2100000.00', NULL, 1, '2025-11-26 04:10:45'),
(65, 43, 'CI/CD Jenkins', 'Tự động hoá DevOps', '3300000.00', NULL, 1, '2025-11-26 04:10:45'),
(66, 43, 'Cloud Server', 'Quản trị Cloud', '3600000.00', NULL, 0, '2025-11-26 04:10:45');

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

--
-- Dumping data for table `lien_he`
--

INSERT INTO `lien_he` (`id`, `ten`, `loai`, `gia_tri`, `mo_ta`, `icon`, `thu_tu`, `trang_thai`, `created_at`, `updated_at`) VALUES
(1, 'zalo', 'zalo', 'https://zalo.me/0868729743', '', '', 1, 1, '2025-12-08 04:00:51', '2025-12-08 04:06:29');

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
(1, 1, 'PHP101 - Lớp 1', 30, 'Lớp học PHP cơ bản', '2025-01-10', '2025-03-10', 'Đang học'),
(2, 1, 'PHP201 - Lớp 2', 25, 'PHP nâng cao', '2025-02-01', '2025-04-20', 'Chưa khai giảng'),
(3, 2, 'JS101 - Lớp 1', 30, 'JavaScript cơ bản', '2025-01-15', '2025-03-30', 'Đang học'),
(4, 3, 'ML101 - Lớp 12', 6, 'Machine Learning cơ bản', '2025-03-01', '2025-05-20', 'Đang học'),
(5, 5, 'ML101 - Lớp 12', 40, '', NULL, NULL, 'Đang học'),
(6, 1, 'NodeJS A1', 30, NULL, NULL, NULL, 'Đang học'),
(7, 2, 'Laravel A1', 30, NULL, NULL, NULL, 'Chưa khai giảng'),
(8, 3, 'Spring Boot A1', 25, NULL, NULL, NULL, 'Đang học'),
(9, 4, 'ReactJS A1', 35, NULL, NULL, NULL, 'Đang học'),
(10, 5, 'VueJS A1', 30, NULL, NULL, NULL, 'Chưa khai giảng');

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
(1, 'GV0001', 'Nguyễn Văn An', 'an@fpt.edu.vn', '123456', '0901234567', 'Hà Nội', 'giang_vien', 1, '2025-11-14 04:17:46'),
(2, 'GV0002', 'Trần Thu Hà', 'ha@fpt.edu.vn', '123456', '0912345678', 'Hà Nội', 'giang_vien', 1, '2025-11-14 04:17:46'),
(3, 'HS0003', 'Vũ Minh Hoàng', 'hoang@gmail.com', '123456', '0987654321', 'Hà Nội', 'hoc_sinh', 1, '2025-11-14 04:17:46'),
(4, 'HS0004', 'Lê Hải Anh', 'haianh@gmail.com', '123456', '0978123456', 'Hải Phòng', 'hoc_sinh', 1, '2025-11-14 04:17:46'),
(5, 'AD0005', 'Admin Hệ Thống', 'admin@system.com', 'admin123', '0900000000', 'TP.HCM', 'admin', 1, '2025-11-14 04:17:46'),
(6, 'HS0006', 'vũ hải anh', 'vuhaianh@system.com', '$2y$10$lmj9cXHiZeefeztG1WEVy.JRSndO64tenE.SYui9iL2Ihqx86E1Ey', '0868729743', 'Đà Nẵng, Việt Nam', 'hoc_sinh', 1, '2025-11-18 08:32:03'),
(7, 'HS0007', 'vũ hải anh', 'haianh1234@gmail.com', '$2y$10$BYwMy.A7BGmf082kGWqstOKGGdzB5zzBMWqhTpreH3VsNrmCbjB8u', '1234567890', 'Đà Nẵng, Việt Nam', 'hoc_sinh', 1, '2025-11-19 03:11:49'),
(8, 'HS0008', 'vũ hải anh', 'hainhvup12@gmail.com', '$2y$10$dJxSPgKHq8rQ7tl58TNT9e5PTnPeRwSVX.eDYCL3G4xtabSN4ZlQG', '0868729749', 'Đà Nẵng, Việt Nam', 'hoc_sinh', 1, '2025-11-19 03:21:38'),
(9, 'HS0009', 'vuu le hoan', 'lehoan@gmail.com', '$2y$10$TuCbdn8jJxzMEuKVpkq5GO1UYq2BrGd7qlqFnuHAokjM1fzDSBHJm', '1234567890', 'Đà Nẵng, Việt Nam', 'hoc_sinh', 1, '2025-11-21 02:56:53'),
(30, 'GV0010', 'Test Giảng Viên', 'testgv@it.edu.vn', '123456', NULL, NULL, 'giang_vien', 1, '2025-11-26 04:04:10'),
(36, 'GV0011', 'Giảng viên mới', 'abc@gmail.com', '123456', NULL, NULL, 'giang_vien', 1, '2025-11-26 04:09:24'),
(37, 'GV0012', 'Nguyễn Công Danh', 'danhgv_new1@it.edu.vn', '123456', NULL, NULL, 'giang_vien', 1, '2025-11-26 04:10:45'),
(38, 'GV0013', 'Trần Hoàng Long', 'longgv_new2@it.edu.vn', '123456', NULL, NULL, 'giang_vien', 1, '2025-11-26 04:10:45'),
(39, 'GV0014', 'Phạm Nhật Anh', 'anhgv_new3@it.edu.vn', '123456', NULL, NULL, 'giang_vien', 1, '2025-11-26 04:10:45'),
(40, 'GV0015', 'Đỗ Minh Quân', 'quanggv_new4@it.edu.vn', '123456', NULL, NULL, 'giang_vien', 1, '2025-11-26 04:10:45'),
(41, 'GV0016', 'Lê Đức Huy', 'huygv_new5@it.edu.vn', '123456', NULL, NULL, 'giang_vien', 1, '2025-11-26 04:10:45'),
(42, 'HS0010', 'HS Demo 01', 'hsdemo01@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-11-26 04:10:45'),
(43, 'HS0011', 'HS Demo 02', 'hsdemo02@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-11-26 04:10:45'),
(44, 'HS0012', 'HS Demo 03', 'hsdemo03@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-11-26 04:10:45'),
(45, 'HS0013', 'HS Demo 04', 'hsdemo04@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-11-26 04:10:45'),
(46, 'HS0014', 'HS Demo 05', 'hsdemo05@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-11-26 04:10:45'),
(47, 'HS0015', 'HS Demo 06', 'hsdemo06@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-11-26 04:10:45'),
(48, 'HS0016', 'HS Demo 07', 'hsdemo07@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-11-26 04:10:45'),
(49, 'HS0017', 'HS Demo 08', 'hsdemo08@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-11-26 04:10:45'),
(50, 'HS0018', 'HS Demo 09', 'hsdemo09@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-11-26 04:10:45'),
(51, 'HS0019', 'HS Demo 10', 'hsdemo10@gmail.com', '123456', NULL, NULL, 'hoc_sinh', 1, '2025-11-26 04:10:45');

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
(1, 1, 'giang_vien', '2025-11-18 09:55:06'),
(2, 2, 'giang_vien', '2025-11-18 09:55:06'),
(4, 4, 'hoc_sinh', '2025-11-18 09:55:06'),
(5, 5, 'admin', '2025-11-18 09:55:06'),
(13, 8, 'hoc_sinh', '2025-11-19 03:21:38'),
(14, 7, 'hoc_sinh', '2025-11-19 06:11:59'),
(15, 6, 'hoc_sinh', '2025-11-19 06:13:28'),
(16, 9, 'hoc_sinh', '2025-11-21 02:56:53'),
(20, 3, 'admin', '2025-11-28 03:56:56'),
(21, 3, 'giang_vien', '2025-11-28 03:56:56'),
(22, 3, 'hoc_sinh', '2025-11-28 03:56:56'),
(23, 51, 'hoc_sinh', '2025-11-28 05:32:21');

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

--
-- Dumping data for table `phan_hoi_binh_luan`
--

INSERT INTO `phan_hoi_binh_luan` (`id`, `id_binh_luan`, `id_admin`, `noi_dung`, `ngay_tao`) VALUES
(1, 6, 5, 'chúng tôi sẽ sửa lại  bggg', '2025-12-03 01:35:30');

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
(1, 5, 'quan_tri');

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
(2, 'P203', 30, 'Phòng máy lạnh tầng 2', 'Sử dụng'),
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
(1, 3, 1, 'MOMO', '2500000.00', '2025-11-14 11:17:46', 'Thành công', 'GD_MOMO_001'),
(2, 3, 3, 'VNPAY', '3000000.00', '2025-11-14 11:17:46', 'Thành công', 'GD_VNPAY_005'),
(3, 4, 2, 'ZALOPAY', '2500000.00', '2025-11-14 11:17:46', 'Chờ xác nhận', 'GD_ZALO_007'),
(4, 6, 14, 'VNPAY', '2500000.00', '2025-11-19 15:03:29', 'Thành công', '15270538'),
(5, 6, 29, 'VNPAY', '2500000.00', '2025-11-21 10:12:00', 'Thành công', '15274853'),
(6, 4, 31, 'VNPAY', '2500000.00', '2025-11-21 10:18:27', 'Thành công', '15274879');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_thong_ke_doanh_thu`
-- (See below for the actual view)
--
CREATE TABLE `v_thong_ke_doanh_thu` (
`ten_khoa_hoc` varchar(200)
,`tong_doanh_thu` decimal(34,2)
,`so_luong_thanh_toan` bigint
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

--
-- Dumping data for table `yeu_cau_doi_lich`
--

INSERT INTO `yeu_cau_doi_lich` (`id`, `id_giang_vien`, `id_ca_hoc_cu`, `id_lop`, `thu_trong_tuan_moi`, `id_ca_moi`, `id_phong_moi`, `ngay_doi`, `ly_do`, `trang_thai`, `ghi_chu_admin`, `ngay_tao`, `ngay_cap_nhat`) VALUES
(1, 1, 2, 1, 'Thứ 4', 1, 1, '2025-12-02', '', 'da_duyet', '[Lịch cũ: Thứ 4, Ca ID: 1, Phòng ID: 1]', '2025-12-08 10:26:27', '2025-12-08 10:26:50'),
(2, 1, 2, 1, 'Thứ 4', 1, 1, '2025-12-02', 'bận', 'da_duyet', '[Lịch cũ: Thứ 4, Ca ID: 1, Phòng ID: 1]', '2025-12-08 10:33:43', '2025-12-08 10:35:53');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ca_mac_dinh`
--
ALTER TABLE `ca_mac_dinh`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dang_ky`
--
ALTER TABLE `dang_ky`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `danh_muc`
--
ALTER TABLE `danh_muc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `hoan_tien`
--
ALTER TABLE `hoan_tien`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hoa_don`
--
ALTER TABLE `hoa_don`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `khoa_hoc`
--
ALTER TABLE `khoa_hoc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `lien_he`
--
ALTER TABLE `lien_he`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `lop_hoc`
--
ALTER TABLE `lop_hoc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `nguoi_dung_vai_tro`
--
ALTER TABLE `nguoi_dung_vai_tro`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `phan_hoi_binh_luan`
--
ALTER TABLE `phan_hoi_binh_luan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `phan_quyen`
--
ALTER TABLE `phan_quyen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `phong_hoc`
--
ALTER TABLE `phong_hoc`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `yeu_cau_doi_lich`
--
ALTER TABLE `yeu_cau_doi_lich`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
