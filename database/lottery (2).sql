-- phpMyAdmin SQL Dump
-- version 4.8.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 23, 2019 at 02:09 AM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lottery`
--

-- --------------------------------------------------------

--
-- Table structure for table `bet`
--

CREATE TABLE `bet` (
  `id` int(11) NOT NULL,
  `bet_type_id` tinyint(4) NOT NULL,
  `message_id` bigint(20) NOT NULL,
  `channel_id` tinyint(4) NOT NULL,
  `refer_bet_id` bigint(20) DEFAULT NULL,
  `number_1` char(4) NOT NULL,
  `number_2` char(4) DEFAULT NULL,
  `price` double NOT NULL,
  `total` double DEFAULT NULL,
  `after` double DEFAULT NULL,
  `result` double DEFAULT NULL,
  `win` tinyint(1) NOT NULL DEFAULT '0',
  `is_main` tinyint(1) NOT NULL DEFAULT '1',
  `str_channel` varchar(100) DEFAULT NULL,
  `str_number` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `bet_day` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `bet_type`
--

CREATE TABLE `bet_type` (
  `id` tinyint(4) NOT NULL,
  `keyword` varchar(4) NOT NULL,
  `content` varchar(100) NOT NULL,
  `example` varchar(255) NOT NULL,
  `display_order` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bet_type`
--

INSERT INTO `bet_type` (`id`, `keyword`, `content`, `example`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'dd', 'đầu đuôi', '12 dd 100n', 1, '2018-11-11 05:36:14', '2018-11-11 05:36:14'),
(2, 'dau', 'đầu', '12 dau 100n', 2, '2018-11-11 05:36:14', '2018-11-11 05:36:14'),
(3, 'dui', 'đuôi', '12 dui 100n', 3, '2018-11-11 05:36:46', '2018-11-11 05:36:46'),
(4, 'bl', 'bao lô', '12 bl 100n', 4, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(5, 'da', 'đá', '12 14 da 100n', 5, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(6, 'dv', 'đá vòng', '12 14 16 dv 100n', 6, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(7, 'dx', 'đá xiên', '12 14 dx 100n', 7, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(8, 'dxv', 'đá xiên vòng', '12 14 16 dxv 100n', 8, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(9, 'x', 'xỉu chủ', '123 x 100n', 9, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(10, 'dxc', 'đảo xỉu chủ', '123 dxc 100n', 10, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(11, 'ddau', 'đảo (xỉu chủ) đầu', '123 dao dau 100n', 11, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(12, 'ddui', 'đảo (xỉu chủ) đuôi', '23 dao dui 100n', 12, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(13, 'bd', 'bao lô đảo', '123 bd 100n', 13, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(14, 'ab', 'đầu đuôi', '12 ab 100n, 21 a 100n b 200n (Cho kiểu đánh ab)', 14, '2018-11-11 05:43:50', '2018-11-11 05:43:50'),
(15, 'k', 'số kéo', 'Kéo 2 số:<br>\r\n19k79: giống hàng đơn vị nên số là 19 29 39 49 59 69 79<br><br>\r\n\r\n\r\nKéo 3 số: (chỉ chấp nhận số kéo sai 1 hàng trăm hoặc hàng chục hoặc hàng đơn vị)<br>\r\n123k173: số là 123 133 143 153 163 173', 15, '2018-11-11 05:43:50', '2018-11-11 05:43:50');

-- --------------------------------------------------------

--
-- Table structure for table `channel`
--

CREATE TABLE `channel` (
  `id` tinyint(4) NOT NULL,
  `code` varchar(3) NOT NULL,
  `name` varchar(20) NOT NULL,
  `display_order` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `channel`
--

INSERT INTO `channel` (`id`, `code`, `name`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'tg', 'Tiền Giang', 1, '2018-11-11 05:28:12', '2018-11-11 05:28:12'),
(2, 'kg', 'Kiên Giang', 2, '2018-11-11 05:28:12', '2018-11-11 05:28:12'),
(3, 'dl', 'Đà Lạt', 3, '2018-11-11 05:28:45', '2018-11-11 05:28:45'),
(4, 'tp', 'TPHCM', 4, '2018-11-11 05:28:45', '2018-11-11 05:28:45'),
(5, 'dt', 'Đồng Tháp', 5, '2018-11-11 05:29:10', '2018-11-11 05:29:10'),
(6, 'cm', 'Cà Mau', 6, '2018-11-11 05:29:10', '2018-11-11 05:29:10'),
(7, 'bt', 'Bến Tre', 7, '2018-11-11 05:29:32', '2018-11-11 05:29:32'),
(8, 'vt', 'Vũng Tàu', 8, '2018-11-11 05:29:32', '2018-11-11 05:29:32'),
(9, 'bli', 'Bạc Liêu', 9, '2018-11-11 05:29:49', '2018-11-11 05:29:49'),
(10, 'dn', 'Đồng Nai', 10, '2018-11-11 05:29:49', '2018-11-11 05:29:49'),
(11, 'ct', 'Cần Thơ', 11, '2018-11-11 05:30:06', '2018-11-11 05:30:06'),
(12, 'st', 'Sóc Trăng', 12, '2018-11-11 05:30:06', '2018-11-11 05:30:06'),
(13, 'tn', 'Tây Ninh', 13, '2018-11-11 05:30:26', '2018-11-11 05:30:26'),
(14, 'ag', 'An Giang', 14, '2018-11-11 05:30:26', '2018-11-11 05:30:26'),
(17, 'bth', 'Bình Thuận', 15, '2018-11-11 05:30:52', '2018-11-11 05:30:52'),
(18, 'vl', 'Vĩnh Long', 16, '2018-11-11 05:30:52', '2018-11-11 05:30:52'),
(19, 'bd', 'Bình Dương', 17, '2018-11-11 05:31:12', '2018-11-11 05:31:12'),
(20, 'tv', 'Trà Vinh', 18, '2018-11-11 05:31:12', '2018-11-11 05:31:12'),
(21, 'la', 'Long An', 19, '2018-11-11 05:31:32', '2018-11-11 05:31:32'),
(22, 'bp', 'Bình Phước', 20, '2018-11-11 05:31:32', '2018-11-11 05:31:32'),
(23, 'hg', 'Hậu Giang', 21, '2018-11-11 05:31:39', '2018-11-11 05:31:39'),
(24, 'mb', 'Miền Bắc', 22, '2018-11-11 13:13:02', '2018-11-11 13:13:02'),
(25, 'hn', 'Hà Nội', 23, '2019-02-14 09:11:31', '2019-02-14 09:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) NOT NULL,
  `tel_id` text,
  `content` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `message_mau`
--

CREATE TABLE `message_mau` (
  `id` int(11) NOT NULL,
  `content` text,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `message_mau`
--

INSERT INTO `message_mau` (`id`, `content`, `status`) VALUES
(1, '2d . 39. 63 da10n.  Phu . 39 dd100n . 63 dd20n . Chanh . 39. 63. Dd20n.  T1', 1),
(2, '2d . 679.216.796.10nb.  T2', 1),
(3, '553-5-5n.53b20n.dd20n.35.88b10n.dd10n.2d. 53-35da.3nc.3nx . T3', 1),
(4, '2d . 553 b5nxc5n. 53b20n.dd20n. 35.88b10n.dd10n.53-35da.3n . Chanh . 53-35da.3n.  T3 lay tin nay', 1),
(5, '2d . 511+512+592 xc20n dxc2n  Dd 25n .011 k 911 .012 k 912 .052 k 952 .092 k 993 .xc2n .152 xc10n Dd 15n . \r\n015 k 915 xc2n . 115 xc10n Dd 15n . T4', 1),
(6, '2d . 6461 b10n b5n xc40n . T5', 1),
(7, '2d . 592b20nxc150n. 592 db5ndaoxc10n. 615xc10n .039.079.939.979.732.368.615.382.019.214.254.294.726.766.326.366.468.491.309. xc5n. 915.932.928.933. xc3n. 368dxc5n. 259b1nxc10ndaoxc5n, 629.669.530.570. b1nxc5n, 1511b1n db0.5 .511b1nxc4n. 973b5nxc55n. 5068b3n.b5n. 272b2nxc50n. 615xc50n.  T6', 1),
(8, '2d . 510 310 766 466 .105 1nb 10nxc 5ndaoxc . \r\n14 - 54 - 76 dav 7n .\r\n154 bl 3n xc 30n dxc 5n .\r\n105 xc 30n dxc 5n.   T7', 1),
(9, '2d . 067 bl 3n xc 100n dxc 10n .  T8', 1),
(10, '2d . 939,979,039,079,019 b1n xc20n ,932,972,933,973,201,382,989,029,259,319,539,579,975,935 b1n xc10n ,4623,2346 b1n b1n xc10n . T9', 1),
(11, 'Dc . 351.973.401.615.xc.63n. 630.673.xc.32n.  2d . 273.xc.21n.  T10', 1),
(12, '2d . 1206.7235.8325.6364.3379.3619. b1n.b1nxc5n. 332xc6n.  Chanh . 170xc32n. 370xc7n. 338.339.332.232 xc10n, 1115 db2n . 1119.2228 db1n.   T11', 1),
(13, '2d . 01.41.81 dav5n. 25.52 da20n.  T12', 1),
(14, '2d . 268.675.1nb.23nx. 221.2nb.8nx.2nxdao.   T13', 1),
(15, 'Dc . 0764.b10n. 3177.6749.b1n.  2d . 2573.b1n.   T14', 1),
(16, 'Dc. 53-88 da.2n . 2d . 53-88 da.2n.   T15', 1),
(17, 'Dc . 0933.b3n. 0932.0351.2511.0732.0332.8929.b05.3321.0332.b1n. 1511.9287.b05.  T16', 1),
(18, '2d . 2019b2n.b1n .1842.4218.b1n.b1n. 243b5nxc35n . Chanh . 238.278.738.778. b1nxc30n. 278b2nxc50n .916 b5nxc50n. 229.269.232.273.537.573.237.215.251.270.233.273.236.276.239.279.228.268.207.247.287.227.267.028.068.223.263.036.076.654.914.941.951.915. xc10n.  T17', 1),
(19, '2d . 5533 bl 2n. 533 bl 2n.xc 10n.dxc 5n. 573,733,773,833,873,633,673,933,973 xc 5n.  T18', 1),
(20, 'Dc . 7077.2nb.05bdao .7735.30nb.20nb.5nbdao .100nx.20nxdao .3538.5nb.5nb.3nx .872.1nb.5nx.1nxdao . 2d . 483.753.1nb10nx.2nxdao .79.39.da1n. 53.86.da1n. 63.10.da1n. 83.86.da1n.  T19', 1),
(21, '2d . 28.04.đa10n.  Chanh . 3312.16n.3nb. 1313.1nb.  T20', 1),
(22, '2đ:514.139.979.339.379.295.595.552.952.503.793.452.910 x5. 2010.8003.3752.6293.8195.3178 b2 b2 x10 dd10 đáv x1. 1668 b7 b1 b1 x3 đ.x2. 119.523.739.714 x5 dd5.  Dc:331 b10 x100.  D.phu:1668 b25.   T21', 1),
(23, '2d . 28.04.đa10n.  Chanh . 3312.16nb.3nb. 1313.1nb.  T20.  Lay tin nay', 1),
(24, 'Dc . 739.xc.51n. 523.xc.61n.  2d . 531.xc.26n.   T22', 1),
(25, 'Dc . 532.xc.61n. 657.1.5b.6nx.  T23', 1),
(26, '2d . 579.759.720x10n. 2323b3n .123x10n..979.952.839x10n .  T24', 1),
(27, '2đ: 3752.3356 b2 b1 b1 x3 đ.x1. 668 đ.b1. 943 b1 x3. 96.33 b2,5. 019 x15.  Dc:39.79.38.78 b2. 37.73 b1. 34 b2,5. 634 đ.b0,5. 6635.6131 b1. 723 b1 x10. 3447 b2 b1 x10.  D.phu:38 b50.  T25', 1),
(28, '2d . 731.b20n b30n xc300n . Chanh . 0279.3919.1nb.1nb.10nx .730.1nb.3nx. 953.2nb.20nx.  T26', 1),
(29, 'VĨNH LONG - Đầu - chẵn (00-98, 50con) 3n, nhỏ (00-49, 50con) 3n. Đuôi - Lẻ (01-99, 50con) 3n, Lớn (50-99, 50con) 3n. BÌNH DƯƠNG - đầu- Lẻ (01-99, 50con) 3n, nhỏ (00-49, 50con) 3n. ĐUÔI - lẻ (01-99, 50con) 3n, nhỏ (00-49, 50con) 3n.   T27', 1),
(30, '2d . 959,059,139,179 b1n xc30n ,939.979,538,578,930,970,530,570,941,901,981,539,579 b1n xc10n . T28', 1),
(31, 'Dc . 542,184 bl 10n. 42,84 bl 10n da 5n. 42,84 dd50n.   T29', 1),
(32, '2đ. 00.k.99. (100c.bỏ19c.bõ.số.chục.bỏ.số.đôi.còn.81c. )đđ30n.  T30', 1),
(33, '2d . 178 db2n dxc5n.   T31', 1),
(34, 'Phu . 07,97 dd 30n, bl 20n, đá 5n. 207,297 b10n xc 30n, T32', 1),
(35, '2dai. 73.47.b70n.  T33', 1),
(36, 'Dc . 33-32 b10n.dd10n.  T34', 1),
(37, 'Dc . 21,51 dd 30n, bl 20n, đá 5n. 421,451 b10n xc 30n,   T35', 1),
(38, 'Dc . 39 b200n . Phu . 39 b50n . 2d . 939 b3n xc30n . 39 b10n . T36', 1),
(39, 'Dc . 39.đầu.200n. 932.xc.23n. 778.05b.10nx.\r\n719.05b.6nx.   2d . 263.549.1nb. 563.1nb.10nx. 7263.1nb.10nx.   T37', 1),
(40, '2d . 4989.da1n. 3272.da1n. 6776.da1n.  T38', 1),
(41, 'Dc . 647.xc.32n. 619.xc.26n. 3538.02bdao .63.4ndd.   T39', 1),
(42, '2đ: 309 bl 5n. 2309 bl 2n.39.đau.10n.   T40', 1),
(43, '2d . 568 b3n xc30n . 68 b10n . T41', 1),
(44, 'Dc . 325 xc21n . 570 xc 172n.   T42', 1),
(45, 'Dc . 784.xc.32n. 84.đđ.31n.  2d . 575.2nb.21nx.8nxdao.  T43', 1),
(46, '2d . 74-43 .02-42. 02-82. đa1n.   T44', 1),
(47, '2dai. 8077,3660b1n, 3066bd0.5 ,660,066,077x5n, 719x115n, 639xd6n, 33dd50n...  chanh . 19dd100n.   T45', 1),
(48, '2đ. 60.k.69.đđ10n-  chanh . 03.k.93.đđ30n. 03.63.đđ20n. 32.b20n- phu . 71.đầu50n.đui10n.  T46', 3),
(49, '2đ: 82.92 b15. 29.63.86.59.98.33 b2. 071 b2 x20. 168 x15. 72 b3,5. 924 x5. 70 b7.  Dc:7955.9755 b2,5 b2,5. 77-55 đá4. 65 b5. 679 b2 x13. 19.55.76.77 b3,5. 150 x3. 924 b0,5 x15. 279 x20. 926 b0,5 x9.  D.phu:924.926 x3.   T47', 3),
(50, '2d . 951,947,938,978,960,958 b1n xc10n ,933,973,932,972 b3n xc50n . T48', 1),
(51, '2d . 2326b10n db0.5 .326b2nxc50ndaoxc5n, 6332.2327.b3n db03 .2019b1n.b1nxc10n. 859.959.533xc10n . Chanh . 370b20nxc50n. 472b5nxc100n. 0161b2n.b2n, 0587.9832.9823.3667. b2n.b1n. 3667 db02 .4323b1n.b1nxc4n. 472b5nxc10n.  038.336.259.b2nxx8n,   T49', 3),
(52, '2d . 668.5nx.1nxdao.  T50', 1),
(53, 'Dc. 42.82 b50n. 2d . 15 b50n.   T51', 1),
(54, '939.2nb.20nx.2d.  T52', 3),
(55, '29.33dd50n 29b12n 5929db1n b4n b4n xc30n  dx3n 333.x35n ch', 3),
(56, '97b10 8488b10 chanh.4533b5 2dai. 29.33dd50n 29b12n 5929db1n b4n b4n xc30n  dx3n 333.x35n phu.', 3),
(57, '97b10 8488b10 chanh.4533b5 2dai.', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `display_name` varchar(100) DEFAULT NULL,
  `tel_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` tinyint(1) DEFAULT NULL,
  `leader_id` int(11) DEFAULT NULL,
  `is_logout` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `changed_password` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(255) DEFAULT NULL,
  `created_user` int(11) DEFAULT NULL,
  `updated_user` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `display_name`, `tel_id`, `email`, `password`, `role`, `leader_id`, `is_logout`, `status`, `changed_password`, `remember_token`, `created_user`, `updated_user`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Admin', NULL, 'admin@vhvschool.online', '$2y$10$UHDKhzeFOLfdCBWQ7GgEFeHmzlglsfrLheHRoOvP3FSKuKKf1D5x2', 3, 1, 1, 1, 0, '3WrS9U7L0xRlZrFeQxfYjFrszeeH2zzZRwUtUr37ylH4JDYmkzrlKbSwS0zN', 1, 1, '2017-06-28 00:00:00', '2018-10-22 15:48:17'),
(16, NULL, NULL, '495662200', 'tau@lucky1102.com', '$2y$10$UHDKhzeFOLfdCBWQ7GgEFeHmzlglsfrLheHRoOvP3FSKuKKf1D5x2', NULL, NULL, 1, 1, 0, 'SksTlRIBejRJtSlZnHzIf8o9YdUCST5ekSlJJpwapQ0kKLihuWJsgpsStrGD', NULL, NULL, '2019-01-29 06:26:10', '2019-01-29 06:26:10'),
(35, NULL, NULL, '465366291', 'anhpx', '12345', NULL, NULL, 1, 1, 0, NULL, NULL, NULL, '2019-01-29 06:28:35', '2019-01-29 06:28:35'),
(54, NULL, NULL, '711931183', 'đây mới gọi là âm ', '123456', NULL, NULL, 1, 1, 0, NULL, NULL, NULL, '2019-01-29 07:06:53', '2019-01-29 07:06:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bet`
--
ALTER TABLE `bet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bet_type_id` (`bet_type_id`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `message_id` (`message_id`);

--
-- Indexes for table `bet_type`
--
ALTER TABLE `bet_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `keyword` (`keyword`),
  ADD KEY `keyword_2` (`keyword`);

--
-- Indexes for table `channel`
--
ALTER TABLE `channel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `code_2` (`code`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_mau`
--
ALTER TABLE `message_mau`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tel_id` (`tel_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bet`
--
ALTER TABLE `bet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bet_type`
--
ALTER TABLE `bet_type`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `channel`
--
ALTER TABLE `channel`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message_mau`
--
ALTER TABLE `message_mau`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
