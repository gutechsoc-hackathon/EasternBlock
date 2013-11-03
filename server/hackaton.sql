-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 03, 2013 at 05:44 AM
-- Server version: 5.5.34-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `hackaton`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires` datetime DEFAULT NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  `upvoted` int(11) NOT NULL DEFAULT '0',
  `type_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type_id` (`type_id`,`location_id`,`question_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `description`, `time_created`, `expires`, `longitude`, `latitude`, `upvoted`, `type_id`, `location_id`, `question_id`, `user_id`) VALUES
(5, 'stuff 1', '2013-11-02 22:15:21', '2013-11-02 23:15:21', 1.5, -2.33, 0, 1, 1, NULL, 1),
(4, 'stuff 25', '2013-11-02 22:12:15', '2013-11-02 23:12:15', 1.5, -2.33, 0, 2, 1, NULL, 1),
(6, 'stuff 74', '2013-11-02 22:19:12', '0000-00-00 00:00:00', 1.5, -2.33, 0, 1, 1, NULL, 1),
(7, 'stuff 4', '2013-11-02 22:20:11', NULL, 1.5, -2.33, 0, 1, 1, NULL, 1),
(8, 'stuff 88', '2013-11-02 22:20:44', '2013-11-03 22:20:44', 1.5, -2.33, 0, 2, 1, NULL, 1),
(9, 'stuff 69', '2013-11-02 22:23:07', '2013-11-03 22:23:07', 1.5, -2.33, 0, 1, 1, NULL, 1),
(10, 'stuff 7', '2013-11-02 22:24:24', '2013-11-03 22:24:24', 1.5, -2.33, 0, 1, 1, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE IF NOT EXISTS `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(63) NOT NULL,
  `seo` varchar(63) NOT NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `seo`, `longitude`, `latitude`) VALUES
(1, 'University of Glasgow', 'universityofglasgow', 1.5, 3.22),
(2, 'My Flat', 'my-flat', -4.2735219, 55.880661);

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(255) NOT NULL,
  `event_id` int(11) NOT NULL,
  `media_type_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`,`media_type_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `media_types`
--

CREATE TABLE IF NOT EXISTS `media_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `media_types`
--

INSERT INTO `media_types` (`id`, `name`) VALUES
(1, 'Image');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` varchar(63) NOT NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires` datetime DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `location_id` (`location_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question`, `longitude`, `latitude`, `time_created`, `expires`, `location_id`, `user_id`) VALUES
(2, 'wtf', -8.3, 5.01, '2013-11-03 00:50:42', '2013-11-10 00:50:42', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sess_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sess_id` (`sess_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `sess_id`, `user_id`, `last_activity`) VALUES
(1, '614bfb0f86793c46338f758278fa7132', 1, '2013-11-03 04:17:51');

-- --------------------------------------------------------

--
-- Table structure for table `tag2event_rel`
--

CREATE TABLE IF NOT EXISTS `tag2event_rel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `from_id` (`from_id`,`to_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tag2event_rel`
--

INSERT INTO `tag2event_rel` (`id`, `from_id`, `to_id`) VALUES
(2, 2, 10),
(3, 3, 10),
(4, 2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `tag2question_rel`
--

CREATE TABLE IF NOT EXISTS `tag2question_rel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from_id` int(11) NOT NULL,
  `to_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `from_id` (`from_id`,`to_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `tag2question_rel`
--

INSERT INTO `tag2question_rel` (`id`, `from_id`, `to_id`) VALUES
(2, 2, 2),
(3, 4, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(63) NOT NULL,
  `seo` varchar(63) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `seo`) VALUES
(2, 'date', 'date'),
(3, 'evening', 'evening'),
(4, 'police', 'police');

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(63) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `types`
--

INSERT INTO `types` (`id`, `name`) VALUES
(1, 'event'),
(2, 'warning');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(63) NOT NULL,
  `pass` varchar(63) NOT NULL,
  `name` varchar(127) DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT '0',
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `roles` varchar(63) NOT NULL DEFAULT 'user',
  `status` int(3) NOT NULL DEFAULT '0',
  `registered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `pass`, `name`, `rating`, `latitude`, `longitude`, `roles`, `status`, `registered`) VALUES
(1, 'vasya@a.ua', '202cb962ac59075b964b07152d234b70', 'Vasya Petrov', 0, 55.8753052, -4.2667017, 'user', 0, '2013-11-02 15:54:57'),
(3, 'petya@a.ua', 'e10adc3949ba59abbe56e057f20f883e', 'petya@a.ua', 0, 55.880661, -4.2735219, 'user', 0, '2013-11-02 16:51:06');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
