-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 03, 2013 at 05:40 AM
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
  `longtitude` double NOT NULL,
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

INSERT INTO `events` (`id`, `description`, `time_created`, `expires`, `longtitude`, `latitude`, `upvoted`, `type_id`, `location_id`, `question_id`, `user_id`) VALUES
(5, 'stuff', '2013-11-02 22:15:21', '2013-11-02 23:15:21', 1.5, -2.33, 0, 1, 1, NULL, 1),
(4, 'stuff', '2013-11-02 22:12:15', '2013-11-02 23:12:15', 1.5, -2.33, 0, 1, 1, NULL, 1),
(6, 'stuff', '2013-11-02 22:19:12', '0000-00-00 00:00:00', 1.5, -2.33, 0, 1, 1, NULL, 1),
(7, 'stuff', '2013-11-02 22:20:11', NULL, 1.5, -2.33, 0, 1, 1, NULL, 1),
(8, 'stuff', '2013-11-02 22:20:44', '2013-11-03 22:20:44', 1.5, -2.33, 0, 1, 1, NULL, 1),
(9, 'stuff', '2013-11-02 22:23:07', '2013-11-03 22:23:07', 1.5, -2.33, 0, 1, 1, NULL, 1),
(10, 'stuff', '2013-11-02 22:24:24', '2013-11-03 22:24:24', 1.5, -2.33, 0, 1, 1, NULL, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
