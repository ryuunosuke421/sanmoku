-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2024-12-19 15:52:18
-- サーバのバージョン： 10.4.27-MariaDB
-- PHP のバージョン: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `sata_db`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `tic_tac_toe_results`
--

CREATE TABLE `tic_tac_toe_results` (
  `id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `my_turn` varchar(255) NOT NULL,
  `result` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- テーブルのデータのダンプ `tic_tac_toe_results`
--

INSERT INTO `tic_tac_toe_results` (`id`, `timestamp`, `my_turn`, `result`) VALUES
(13, '2024-12-19 10:57:13', 'first', 'circle'),
(14, '2024-12-19 10:57:24', 'first', 'circle'),
(15, '2024-12-19 10:57:34', 'second', 'cross'),
(16, '2024-12-19 10:57:48', 'first', 'DRAW'),
(17, '2024-12-19 10:58:16', 'second', 'circle'),
(18, '2024-12-19 10:58:38', 'second', 'circle'),
(19, '2024-12-19 11:15:43', 'first', 'circle'),
(20, '2024-12-19 11:16:00', 'second', 'cross'),
(21, '2024-12-19 11:17:34', 'second', 'cross'),
(22, '2024-12-19 11:17:44', 'second', 'circle'),
(23, '2024-12-19 11:17:53', 'second', 'cross'),
(24, '2024-12-19 11:18:00', 'first', 'circle'),
(25, '2024-12-19 11:22:32', 'first', 'circle'),
(26, '2024-12-19 11:23:31', 'second', 'circle'),
(27, '2024-12-19 11:46:14', 'second', 'cross'),
(28, '2024-12-19 13:16:34', 'first', 'DRAW'),
(29, '2024-12-19 13:16:38', 'first', 'circle'),
(30, '2024-12-19 13:17:35', 'second', 'cross'),
(31, '2024-12-19 13:19:01', 'second', 'cross'),
(32, '2024-12-19 13:20:54', 'first', 'circle'),
(33, '2024-12-19 13:21:00', 'first', 'circle'),
(34, '2024-12-19 13:21:05', 'second', 'cross'),
(35, '2024-12-19 13:24:43', 'first', 'circle'),
(36, '2024-12-19 13:24:48', 'second', 'cross'),
(37, '2024-12-19 13:27:03', 'second', 'cross'),
(38, '2024-12-19 13:27:06', 'first', 'circle'),
(39, '2024-12-19 13:53:28', 'first', 'circle'),
(40, '2024-12-19 14:32:29', 'first', 'circle'),
(41, '2024-12-19 14:32:33', 'second', 'cross'),
(42, '2024-12-19 14:33:38', 'first', 'circle'),
(43, '2024-12-19 14:53:53', 'first', 'cross'),
(44, '2024-12-19 14:54:09', 'first', 'circle'),
(45, '2024-12-19 14:57:53', 'first', 'cross'),
(46, '2024-12-19 14:57:58', 'second', 'circle');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `tic_tac_toe_results`
--
ALTER TABLE `tic_tac_toe_results`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `tic_tac_toe_results`
--
ALTER TABLE `tic_tac_toe_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
