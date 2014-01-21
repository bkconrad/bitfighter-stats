-- phpMyAdmin SQL Dump
-- version 3.4.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 21, 2014 at 09:41 PM
-- Server version: 5.1.69
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `bf_stats`
--

-- --------------------------------------------------------

--
-- Table structure for table `player_achievements`
--

DROP TABLE IF EXISTS `player_achievements`;
CREATE TABLE IF NOT EXISTS `player_achievements` (
  `player_name` text NOT NULL,
  `achievement_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `date_awarded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `accomplishment_id` (`achievement_id`,`player_name`(50))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `player_achievements`
--

INSERT INTO `player_achievements` (`player_name`, `achievement_id`, `server_id`, `date_awarded`) VALUES
('Quartz', 9, 4607, '2014-01-18 06:38:05'),
('raptor', 10, 4607, '2014-01-18 05:48:59'),
('sky_lark', 7, 4752, '2014-01-17 13:38:04'),
('sky_lark', 9, 4227, '2013-12-15 22:39:40'),
('sam686', 8, 4628, '2013-12-15 00:26:59'),
('Bob', 10, 4628, '2013-12-13 22:28:08'),
('amgine', 9, 4057, '2013-12-07 04:52:24'),
('sky_lark', 10, 4227, '2013-12-02 22:20:17'),
('Nothing_Much', 10, 4227, '2013-12-02 22:13:30'),
('Bob', 2, 4295, '2013-11-21 22:19:31'),
('Skybax', 2, 4295, '2013-11-21 05:04:06'),
('ShadowX.loner', 8, 2859, '2013-10-14 12:56:02'),
('invader alex', 2, 3423, '2013-10-11 19:11:38'),
('bobdaduck', 7, 3876, '2013-07-31 09:11:30'),
('Skybax', 8, 3067, '2013-06-30 14:30:46'),
('Zapgamer', 6, 3610, '2013-05-03 23:21:09'),
('ChompyRandom', 6, 3610, '2013-05-03 23:21:09'),
('Quartz', 6, 3610, '2013-05-03 23:21:09'),
('thread', 6, 3610, '2013-05-03 23:21:09'),
('Santiago ZAP', 6, 3610, '2013-05-03 23:21:09'),
('CamperKiller39', 6, 3610, '2013-05-03 23:21:09'),
('Lone Wolf', 6, 3610, '2013-05-03 23:21:09'),
('invader alex', 6, 3610, '2013-05-03 23:21:09'),
('Doodgie', 6, 3610, '2013-05-03 23:21:09'),
('Arctic', 6, 3610, '2013-05-03 23:21:09'),
('FoOtloOse', 3, 3610, '2013-05-03 23:15:03'),
('bobdaduck', 4, 3610, '2013-05-03 23:15:03'),
('sky_lark', 5, 3610, '2013-05-03 23:15:03'),
('Little_Apple', 5, 3610, '2013-05-03 23:15:03'),
('Zemmer', 2, 3610, '2013-05-03 21:57:59'),
('Bob', 8, 3394, '2013-03-31 19:58:51'),
('sky_lark', 8, 3256, '2013-03-22 01:32:01'),
('kaen', 7, 3145, '2013-03-21 06:29:23'),
('sky_lark', 2, 3145, '2013-03-10 00:11:14'),
('Lamp', 2, 3019, '2013-02-22 23:24:47'),
('Quartz', 2, 2925, '2013-02-07 22:44:28'),
('Little_Apple', 8, 2832, '2013-02-04 04:22:18'),
('Quartz', 8, 2832, '2013-02-04 04:08:17'),
('bobdaduck', 8, 2859, '2013-02-01 05:45:33'),
('Invisible', 7, 2792, '2013-01-10 06:55:57'),
('Invisible', 2, 2792, '2012-12-29 03:25:14'),
('bobdaduck', 2, 2792, '2012-12-29 02:12:40'),
('Santiago ZAP', 7, 1786, '2012-12-07 14:47:25'),
('YoshiSmb', 2, 2641, '2012-12-01 16:53:27'),
('koda', 0, 1786, '2012-09-29 01:48:33'),
('amgine', 2, 2222, '2012-09-28 23:58:23'),
('FoOtloOse', 5, 2458, '2012-09-19 19:26:26'),
('Fordcars', 5, 2458, '2012-09-19 19:26:26'),
('Invisible', 4, 2458, '2012-09-19 19:26:26'),
('Kaeleiru', 6, 2458, '2012-09-19 19:25:09'),
('BlackBird', 6, 2458, '2012-09-19 19:25:09'),
('bobdaduck', 6, 2458, '2012-09-19 19:25:09'),
('Cracatoa', 6, 2458, '2012-09-19 19:25:09'),
('firefrost', 6, 2458, '2012-09-19 19:25:09'),
('FoOtloOse', 6, 2458, '2012-09-19 19:25:09'),
('Fordcars', 6, 2458, '2012-09-19 19:25:09'),
('Hum~', 6, 2458, '2012-09-19 19:25:09'),
('Invisible', 6, 2458, '2012-09-19 19:25:09'),
('kaen', 6, 2458, '2012-09-19 19:25:09'),
('king_starlight', 6, 2458, '2012-09-19 19:25:09'),
('Little_Apple', 6, 2458, '2012-09-19 19:25:09'),
('raptor', 6, 2458, '2012-09-19 19:25:09'),
('sam686', 6, 2458, '2012-09-19 19:25:09'),
('sky_lark', 6, 2458, '2012-09-19 19:25:09'),
('Skybax', 6, 2458, '2012-09-19 19:25:09'),
('Unknown', 6, 2458, '2012-09-19 19:25:09'),
('watusimoto', 6, 2458, '2012-09-19 19:25:09'),
('Zemmer', 6, 2458, '2012-09-19 19:25:09'),
('watusimoto', 5, 1786, '2012-08-10 15:52:51'),
('raptor', 0, 1786, '2012-08-09 04:31:50'),
('sam686', 3, 1786, '2012-08-09 04:27:20'),
('Zapgamer', 2, 2376, '2012-07-12 02:06:04'),
('kaen', 0, 1786, '2012-06-14 19:03:50'),
('sam686', 2, 2243, '2012-06-06 02:17:03'),
('ChompyRandom', 2, 1282, '2012-05-19 02:34:59'),
('Random Insanity', 2, 2165, '2012-05-19 00:44:56'),
('FoOtloOse', 2, 2165, '2012-05-19 00:19:51'),
('Heyub', 2, 1340, '2012-04-29 20:28:18'),
('CleverBot', 2, 1340, '2012-04-29 17:21:20'),
('britolop', 2, 2005, '2012-04-21 18:10:47'),
('Fordcars', 2, 1340, '2012-04-21 01:16:44'),
('Santiago ZAP', 2, 1989, '2012-04-20 02:44:10'),
('Lone Wolf', 2, 2005, '2012-04-14 23:03:45'),
('BlackBird', 2, 1994, '2012-03-31 19:58:33'),
('Little_Apple', 2, 1989, '2012-03-31 01:59:02'),
('sam686', 0, 1786, '2012-03-13 09:09:02'),
('watusimoto', 0, 1786, '2012-03-13 09:08:54'),
('watusimoto', 2, 1911, '2012-03-12 20:58:04');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
