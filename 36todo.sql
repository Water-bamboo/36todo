-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 06 月 10 日 14:30
-- 服务器版本: 5.5.24-log
-- PHP 版本: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `todo`
--

-- --------------------------------------------------------

--
-- 表的结构 `36todo_category`
--

CREATE TABLE IF NOT EXISTS `36todo_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userid` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

--
-- 转存表中的数据 `36todo_category`
--

INSERT INTO `36todo_category` (`id`, `name`, `create_time`, `userid`) VALUES
(1, 'study', '2013-03-23 12:17:47', 34),
(3, 'asdf', '2013-03-23 12:49:13', 34),
(5, '', '2013-03-23 12:57:57', 34),
(8, '学习项', '2013-03-24 10:57:51', 34),
(9, '生日', '2013-03-24 10:58:17', 34),
(10, '每日必读', '2013-03-26 11:52:22', 34),
(11, '课程', '2013-03-26 12:25:55', 34),
(12, '再增加一个分类', '2013-03-30 14:47:27', 34),
(13, '小孩', '2013-03-30 14:48:35', 34),
(14, 'jij ', '2013-06-09 10:20:11', 34);

-- --------------------------------------------------------

--
-- 表的结构 `36todo_mail_queue`
--

CREATE TABLE IF NOT EXISTS `36todo_mail_queue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mail_to` varchar(120) NOT NULL,
  `mail_subject` varchar(255) NOT NULL,
  `mail_body` text NOT NULL,
  `priority` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `err_num` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `add_time` int(10) unsigned NOT NULL,
  `lock_expiry` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

--
-- 表的结构 `36todo_message_tpl`
--

CREATE TABLE IF NOT EXISTS `36todo_message_tpl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `is_sys` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `alias` varchar(50) NOT NULL COMMENT '别名',
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `remind_type` int(11) NOT NULL DEFAULT '-1' COMMENT '默认提醒一次、规律提醒有（每天、每周、每月、每年）',
  `modify_time` date NOT NULL,
  `readiness` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=87 ;

--
-- 转存表中的数据 `36todo_todos`
--

INSERT INTO `36todo_todos` (`id`, `body`, `create_time`, `isvalid`, `category_id`, `userid`, `remind_time`, `remind_type`, `modify_time`, `readiness`) VALUES
(34, '今天 已经是大前天了', '2013-01-21 09:38:38', 1, 0, 34, NULL, 0, '2013-01-26', 1),
(35, '1/22', '2013-01-22 05:37:38', 1, 0, 34, NULL, 0, '2013-01-26', 1),
(36, '1/23', '2013-01-23 12:57:51', 1, 0, 34, '2013-02-21 04:00:00', 0, '0000-00-00', 1),
(37, 'http://www.smzdm.com/the-somatosensory-artifact-leap-motion-controller-200-times-that-of-kinects-accuracy-69-99-book-dire.html', '2013-01-24 02:20:34', 1, 0, 34, NULL, 0, '0000-00-00', 1),
(38, '7月不能完成的要让sfm知道。\nLITE部分还没有回复', '2013-01-25 10:27:05', 1, 0, 34, '2013-01-09 06:23:00', 0, '2013-01-26', 1),
(39, '开关能不能生效的问题一定要确认一下。SFM->FRAMEWORK RD.\n本周完成display 部分的确认。nnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkllllllllllllllllllllllllllllllllllllllllllllluuuuuuuuuuuuuuuuuuuuuuuu', '2013-01-25 10:28:43', 1, 0, 34, '2014-09-27 04:00:00', 0, '0000-00-00', 0),
(40, '下一步：回收站', '2013-01-26 00:27:04', 1, 0, 34, '2013-01-24 16:41:00', 0, '0000-00-00', 1),
(41, '小鬼当家', '2013-01-26 00:27:14', 1, 0, 34, '2013-06-02 13:34:00', 0, '0000-00-00', 1),
(42, '买维D', '2013-01-26 06:15:37', 1, 0, 34, NULL, 0, '0000-00-00', 1),
(43, 'package name==p4 path: bt power lite', '2013-01-28 12:53:15', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(44, 'report status', '2013-01-28 12:53:58', 1, 0, 34, NULL, 0, '0000-00-00', 1),
(45, 'bt next plan', '2013-01-28 12:54:49', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(46, '登录与注册界面要越简单清爽越好\n是的呀！！寺  在 ni  \\n\n11 ll\nmm nn\nxx', '2013-01-30 01:18:20', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(47, 'user4 test', '2013-01-31 05:53:55', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(48, 'user6', '2013-01-31 05:58:04', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(49, '', '2013-01-31 05:59:59', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(50, 'test user7 111', '2013-01-31 06:12:44', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(51, 'test user6 11122', '2013-01-31 06:13:08', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(52, 'daeeeee', '2013-02-02 05:14:02', 1, 0, 34, '2013-02-02 08:00:00', 0, '0000-00-00', 1),
(53, 'cycle test?', '2013-02-02 10:54:36', 1, 0, 34, '2013-03-30 00:00:00', 0, '0000-00-00', 1),
(54, '把2011，2012优秀创业公司的名称收集一下，模仿其中一个网站的优点。', '2013-02-03 03:54:20', 0, 0, 34, '2013-02-21 14:00:00', 0, '0000-00-00', 1),
(55, '喜欢云图', '2013-02-04 11:39:09', 0, 0, 34, '2013-03-30 00:00:00', 0, '0000-00-00', 0),
(56, '大', '2013-02-23 14:03:36', 0, 0, 34, '2013-02-22 12:00:00', 0, '0000-00-00', 0),
(57, '在', '2013-02-23 14:04:04', 1, 0, 34, '2013-02-28 07:00:00', 0, '0000-00-00', 1),
(58, '在在', '2013-02-23 14:04:19', 1, 0, 34, '0000-00-00 00:00:00', 0, '0000-00-00', 1),
(59, '', '2013-02-23 14:05:12', 0, 0, 34, NULL, 0, '0000-00-00', 1),
(60, '', '2013-02-23 14:05:55', 0, 0, 34, NULL, 0, '0000-00-00', 1),
(61, '', '2013-02-23 14:06:43', 0, 0, 34, NULL, 0, '0000-00-00', 1),
(62, '', '2013-02-23 14:06:51', 1, 0, 34, '0000-00-00 00:00:00', 0, '0000-00-00', 1),
(63, 'test', '2013-02-26 11:58:14', 1, 0, 34, NULL, 0, '0000-00-00', 1),
(64, 'sdfasd', '2013-02-26 12:44:39', 0, 0, 34, '2013-02-21 10:00:00', 0, '0000-00-00', 0),
(65, '222', '2013-02-26 12:44:54', 1, 0, 34, '2020-02-28 00:00:00', 0, '0000-00-00', 0),
(66, 'dd', '2013-03-17 03:34:44', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(67, 'dd', '2013-03-17 03:43:38', 1, 0, 34, '0000-00-00 00:00:00', 0, '0000-00-00', 1),
(68, '显示便签#ID，可当issue tracking 系统', '2013-03-18 01:48:15', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(69, '每次最多查询50条。否则会暴掉。以后再实现加载更多\n！1\n', '2013-03-18 01:56:24', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(70, '点击编辑时，也要能支持直接进入编辑状态', '2013-03-18 01:56:46', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(71, '支持“分类”', '2013-03-18 01:57:19', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(72, '支持“邮件提醒”，默认不作邮件提醒', '2013-03-18 01:57:33', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(73, '是否提醒， 应支持可配置', '2013-03-18 01:58:13', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(74, 'ajax操作后总是“重定向”，会导致回到顶部', '2013-03-18 01:58:48', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(75, '你想成为一个有竞争力的人，第一要素就是利用时间。在时间中有效积累才是关键！让36todo成为你的秘书、助理吧。', '2013-03-26 05:49:27', 1, 0, 34, NULL, 0, '0000-00-00', 0),
(76, '在学习项中添加便签1', '2013-03-26 11:29:04', 1, 8, 34, NULL, 0, '0000-00-00', 0),
(77, '再添加一条学习项', '2013-03-26 11:34:57', 1, 8, 34, NULL, 0, '0000-00-00', 0),
(78, '老爸生日', '2013-03-26 11:35:13', 1, 9, 34, NULL, 0, '0000-00-00', 1),
(79, '老师生日', '2013-03-26 11:52:10', 1, 9, 34, NULL, 0, '0000-00-00', 0),
(80, '35kr', '2013-03-26 12:11:45', 1, 8, 34, NULL, 0, '0000-00-00', 0),
(81, '晕死了，USER1底下没有内容？', '2013-03-30 14:47:47', 1, 12, 34, NULL, 0, '0000-00-00', 0),
(82, '每日必读', '2013-03-30 14:48:09', 1, 10, 34, NULL, 0, '0000-00-00', 1),
(83, '', '2013-03-30 14:48:48', 1, 13, 34, NULL, 0, '0000-00-00', 1),
(84, '测试的，应该删除', '2013-04-04 14:16:59', 1, 0, 34, NULL, -1, '0000-00-00', 0),
(85, '明天要去游泳了', '2013-05-29 12:29:19', 1, 0, 34, NULL, -1, '0000-00-00', 0),
(86, 'ni', '2013-06-09 10:20:17', 1, 0, 34, '2013-06-10 00:00:00', -1, '0000-00-00', 0);

-- --------------------------------------------------------

--
-- 表的结构 `36todo_user`
--

CREATE TABLE IF NOT EXISTS `36todo_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) CHARACTER SET utf8 NOT NULL,
  `password` varchar(32) CHARACTER SET utf8 NOT NULL,
  `userphone` int(11) DEFAULT NULL,
  `infoverified` char(10) CHARACTER SET utf8 NOT NULL DEFAULT '0',
  `mail` varchar(128) CHARACTER SET utf8 DEFAULT NULL,
  `remoteip` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `ctime` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `notifywith` int(11) NOT NULL DEFAULT '1' COMMENT '0,nothing,1:mail,2,weixin.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=46 ;

--
-- 转存表中的数据 `36todo_user`
--

INSERT INTO `36todo_user` (`id`, `username`, `password`, `userphone`, `infoverified`, `mail`, `remoteip`, `ctime`, `notifywith`) VALUES
(34, 'user1', '7e58d63b60197ceb55a1c487989a3720', NULL, '1', 'chocolly@163.com', '127.0.0.1', '2013-01-29 20:57:25', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
