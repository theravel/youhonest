-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 02, 2013 at 07:16 PM
-- Server version: 5.1.63
-- PHP Version: 5.3.3-7+squeeze14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `youhonest_001`
--

-- --------------------------------------------------------

--
-- Table structure for table `authorizations_fb`
--

CREATE TABLE IF NOT EXISTS `authorizations_fb` (
  `authorization_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `fb_user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `photo_url` varchar(255) NOT NULL,
  `token` varchar(100) NOT NULL,
  `locale` varchar(10) NOT NULL DEFAULT 'en',
  PRIMARY KEY (`authorization_id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `fb_user_id` (`fb_user_id`),
  KEY `token` (`token`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `authorizations_vk`
--

CREATE TABLE IF NOT EXISTS `authorizations_vk` (
  `authorization_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vk_user_id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `locale` varchar(10) NOT NULL DEFAULT 'ru',
  `create_date` int(11) NOT NULL COMMENT 'Date of creation authorization',
  `update_date` int(11) NOT NULL COMMENT 'Date when names and pictures were last time updated',
  `token_update_date` int(11) NOT NULL COMMENT 'Date when token was last time updated',
  `expired` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If token has expired',
  PRIMARY KEY (`authorization_id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `vk_user_id` (`vk_user_id`),
  KEY `update_date` (`update_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=848 ;

-- --------------------------------------------------------

--
-- Table structure for table `comments_vk`
--

CREATE TABLE IF NOT EXISTS `comments_vk` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `authorization_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `Index by user` (`authorization_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dislikes_vk`
--

CREATE TABLE IF NOT EXISTS `dislikes_vk` (
  `dislike_id` int(11) NOT NULL AUTO_INCREMENT,
  `authorization_id` int(11) NOT NULL,
  `grouping_number` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(11) NOT NULL,
  `post_id` varchar(50) NOT NULL,
  `dislikes_count` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`dislike_id`),
  UNIQUE KEY `unique dislikes` (`authorization_id`,`post_id`),
  KEY `Index by user` (`authorization_id`),
  KEY `post_id` (`post_id`),
  KEY `grouping_number` (`grouping_number`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5272 ;

-- --------------------------------------------------------

--
-- Table structure for table `networks`
--

CREATE TABLE IF NOT EXISTS `networks` (
  `network_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `url` varchar(50) NOT NULL,
  `url_pattern` varchar(100) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '0',
  `authorization_table` varchar(50) NOT NULL,
  `dislike_table` varchar(50) NOT NULL,
  `comment_table` varchar(50) NOT NULL,
  `user_enabled_field` varchar(20) NOT NULL,
  PRIMARY KEY (`network_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `language` varchar(10) NOT NULL,
  PRIMARY KEY (`news_id`),
  KEY `created_date` (`created_date`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_migration`
--

CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE IF NOT EXISTS `translations` (
  `translation_id` bigint(10) NOT NULL AUTO_INCREMENT,
  `language` varchar(20) NOT NULL,
  `key` varchar(200) NOT NULL,
  `value` text NOT NULL,
  `plurality_min` int(11) NOT NULL DEFAULT '1',
  `plurality_max` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`translation_id`),
  UNIQUE KEY `min index` (`key`,`language`,`plurality_min`),
  UNIQUE KEY `max index` (`language`,`key`,`plurality_max`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `cookie` varchar(255) NOT NULL,
  `language` varchar(10) NOT NULL DEFAULT 'en',
  `latest_news_date` int(11) DEFAULT '0',
  `vk_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `fb_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `gplus_enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`user_id`),
  KEY `cookie` (`cookie`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=854 ;
