-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 02 月 07 日 07:43
-- 服务器版本: 5.5.24-log
-- PHP 版本: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `36todo`
--

-- --------------------------------------------------------

--
-- 表的结构 `36todo_category`
--

CREATE TABLE IF NOT EXISTS `36todo_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `create_time` date NOT NULL,
  `valid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `36todo_notification_1`
--

CREATE TABLE IF NOT EXISTS `36todo_notification_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `todoid` int(11) NOT NULL,
  `minutes` int(11) NOT NULL COMMENT 'timeup',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `36todo_todos`
--

CREATE TABLE IF NOT EXISTS `36todo_todos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `body` varchar(500) CHARACTER SET utf8 NOT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `isvalid` int(11) NOT NULL DEFAULT '1',
  `category_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `remind_time` datetime DEFAULT NULL,
  `remind_cycle_type` int(11) NOT NULL COMMENT '每周、每日、每月、每年提醒？',
  `remind_type` int(11) NOT NULL DEFAULT '1',
  `modify_time` date NOT NULL,
  `readiness` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=56 ;

--
-- 转存表中的数据 `36todo_todos`
--

INSERT INTO `36todo_todos` (`id`, `body`, `create_time`, `isvalid`, `category_id`, `userid`, `remind_time`, `remind_cycle_type`, `remind_type`, `modify_time`, `readiness`) VALUES
(34, '今天 已经是大前天了', '2013-01-21 09:38:38', 1, 0, 33, NULL, 0, 1, '2013-01-26', 1),
(35, '1/22', '2013-01-22 05:37:38', 1, 0, 33, NULL, 0, 1, '2013-01-26', 1),
(36, '1/23', '2013-01-23 12:57:51', 1, 0, 33, '2013-02-21 04:00:00', 0, 1, '0000-00-00', 1),
(37, 'http://www.smzdm.com/the-somatosensory-artifact-leap-motion-controller-200-times-that-of-kinects-accuracy-69-99-book-dire.html', '2013-01-24 02:20:34', 1, 0, 33, NULL, 0, 1, '0000-00-00', 1),
(38, '7月不能完成的要让sfm知道。\nLITE部分还没有回复', '2013-01-25 10:27:05', 1, 0, 33, '2013-01-09 06:23:00', 0, 1, '2013-01-26', 0),
(39, '开关能不能生效的问题一定要确认一下。SFM->FRAMEWORK RD.\n本周完成display 部分的确认。', '2013-01-25 10:28:43', 1, 0, 33, '2014-09-27 04:00:00', 0, 1, '0000-00-00', 0),
(40, '下一步：回收站', '2013-01-26 00:27:04', 1, 0, 33, '2013-01-24 16:41:00', 0, 1, '0000-00-00', 1),
(41, '小鬼当家', '2013-01-26 00:27:14', 1, 0, 33, '2013-06-02 13:34:00', 0, 1, '0000-00-00', 0),
(42, '买维D', '2013-01-26 06:15:37', 1, 0, 33, NULL, 0, 1, '0000-00-00', 1),
(43, 'package name==p4 path: bt power lite', '2013-01-28 12:53:15', 1, 0, 33, '2013-02-06 00:00:00', 0, 1, '0000-00-00', 0),
(44, 'report status', '2013-01-28 12:53:58', 1, 0, 33, NULL, 0, 1, '0000-00-00', 0),
(45, 'bt next plan', '2013-01-28 12:54:49', 1, 0, 33, '2013-02-28 00:21:00', 0, 1, '0000-00-00', 0),
(46, '登录与注册界面要越简单清爽越好', '2013-01-30 01:18:20', 1, 0, 33, NULL, 0, 1, '0000-00-00', 0),
(47, 'user4 test', '2013-01-31 05:53:55', 1, 0, 0, NULL, 0, 1, '0000-00-00', 0),
(48, 'user6', '2013-01-31 05:58:04', 1, 0, 0, NULL, 0, 1, '0000-00-00', 0),
(49, '', '2013-01-31 05:59:59', 1, 0, 0, NULL, 0, 1, '0000-00-00', 0),
(50, 'test user7 111', '2013-01-31 06:12:44', 1, 0, 40, NULL, 0, 1, '0000-00-00', 0),
(51, 'test user6 11122', '2013-01-31 06:13:08', 1, 0, 39, NULL, 0, 1, '0000-00-00', 0),
(52, 'daeeeee', '2013-02-02 05:14:02', 1, 0, 34, '2013-02-02 08:00:00', 0, 1, '0000-00-00', 1),
(53, 'cycle test?', '2013-02-02 10:54:36', 1, 0, 34, NULL, 0, 1, '0000-00-00', 0),
(54, '把2011，2012优秀创业公司的名称收集一下，模仿其中一个网站的优点。', '2013-02-03 03:54:20', 1, 0, 33, NULL, 0, 1, '0000-00-00', 0),
(55, '云图', '2013-02-04 11:39:09', 1, 0, 33, NULL, 0, 1, '0000-00-00', 0);

-- --------------------------------------------------------

--
-- 表的结构 `36todo_user`
--

CREATE TABLE IF NOT EXISTS `36todo_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET utf8 NOT NULL,
  `password` varchar(32) CHARACTER SET utf8 NOT NULL,
  `userphone` int(11) DEFAULT NULL,
  `city` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `mail` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `remoteip` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `ctime` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=42 ;

--
-- 转存表中的数据 `36todo_user`
--

INSERT INTO `36todo_user` (`id`, `username`, `password`, `userphone`, `city`, `mail`, `remoteip`, `ctime`) VALUES
(31, 'admin', '21232f297a57a5a743894a0e4a801fc3', NULL, NULL, NULL, '192.168.1.181', '2013-01-26 16:01:59'),
(32, 'oracle', 'a189c633d9995e11bf8607170ec9a4b8', NULL, NULL, NULL, '192.168.1.181', '2013-01-26 16:31:29'),
(33, 'user1', '24c9e15e52afc47c225b757e7bee1f9d', NULL, NULL, NULL, '127.0.0.1', '2013-01-29 20:49:24'),
(34, 'user2', '7e58d63b60197ceb55a1c487989a3720', NULL, NULL, NULL, '127.0.0.1', '2013-01-29 20:57:25'),
(36, 'user3', '92877af70a45fd6a2ed7fe81e1236b78', NULL, NULL, NULL, '127.0.0.1', '2013-01-31 13:52:47'),
(37, 'user4', '3f02ebe3d7929b091e3d8ccfde2f3bc6', NULL, NULL, NULL, '127.0.0.1', '2013-01-31 13:53:41'),
(38, 'user5', '0a791842f52a0acfbb3a783378c066b8', NULL, NULL, NULL, '127.0.0.1', '2013-01-31 13:57:18'),
(39, 'user6', 'affec3b64cf90492377a8114c86fc093', NULL, NULL, NULL, '127.0.0.1', '2013-01-31 13:57:57'),
(40, 'user7', '3e0469fb134991f8f75a2760e409c6ed', NULL, NULL, NULL, '127.0.0.1', '2013-01-31 13:59:45'),
(41, 'user8', '7668f673d5669995175ef91b5d171945', NULL, NULL, NULL, '127.0.0.1', '2013-01-31 14:13:23');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
