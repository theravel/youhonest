-- phpMyAdmin SQL Dump
-- version 3.3.7deb7
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Окт 26 2012 г., 01:34
-- Версия сервера: 5.1.63
-- Версия PHP: 5.3.3-7+squeeze14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- База данных: `youhonest_001`
--

-- --------------------------------------------------------

--
-- Структура таблицы `authorizations_fb`
--

DROP TABLE IF EXISTS `authorizations_fb`;
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

--
-- Дамп данных таблицы `authorizations_fb`
--

INSERT INTO `authorizations_fb` (`authorization_id`, `user_id`, `fb_user_id`, `first_name`, `last_name`, `photo_url`, `token`, `locale`) VALUES
(1, 1, 1161536991, 'Dmitriy', 'Tarasov', 'http://profile.ak.fbcdn.net/hprofile-ak-ash2/186990_1161536991_285030_q.jpg', 'AAAE24qKkhXABAJ6w2gZCguL6xcjKGXCmWmZBToKaDeI5dIrRn7krIB4ZANZBCek8EZCVwLV0qjZBDoWquPFz0fAcB2IRQETrgnf', 'ru_RU');

-- --------------------------------------------------------

--
-- Структура таблицы `authorizations_vk`
--

DROP TABLE IF EXISTS `authorizations_vk`;
CREATE TABLE IF NOT EXISTS `authorizations_vk` (
  `authorization_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `vk_user_id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `locale` varchar(10) NOT NULL DEFAULT 'ru',
  PRIMARY KEY (`authorization_id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `vk_user_id` (`vk_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `authorizations_vk`
--


-- --------------------------------------------------------

--
-- Структура таблицы `comments_vk`
--

DROP TABLE IF EXISTS `comments_vk`;
CREATE TABLE IF NOT EXISTS `comments_vk` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `authorization_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `Index by user` (`authorization_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `comments_vk`
--


-- --------------------------------------------------------

--
-- Структура таблицы `dislikes_vk`
--

DROP TABLE IF EXISTS `dislikes_vk`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Дамп данных таблицы `dislikes_vk`
--

INSERT INTO `dislikes_vk` (`dislike_id`, `authorization_id`, `grouping_number`, `date`, `post_id`, `dislikes_count`) VALUES
(1, 1, 1, 1351196694, 'post-25117353_48230', 1),
(2, 1, 1, 1351196726, 'photo-31072900_291275204', 1),
(3, 1, 1, 1351196733, 'post20153878_2025', 1),
(4, 1, 1, 1351196756, 'post-4257692_17713', 1),
(6, 2, 1, 2012, 'post-4257692_17709', 7),
(7, 3, 2, 5435353, 'post-4257692_17709', 7),
(8, 4, 3, 35353543, 'post-4257692_17709', 7),
(10, 1, 1, 1351197359, 'photo14752490_287518988', 1),
(13, 1, 4, 1351197553, 'post-4257692_17709', 7),
(14, 5, 5, 24234, 'post-4257692_17709', 7),
(15, 6, 6, 34543, 'post-4257692_17709', 7),
(16, 7, 7, 23543543, 'post-4257692_17709', 7),
(17, 1, 1, 1351197896, 'post-4257692_17714', 1),
(19, 1, 1, 1351199901, 'post-25117353_48232', 1),
(20, 1, 1, 1351200224, 'photo52242093_291960937', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `networks`
--

DROP TABLE IF EXISTS `networks`;
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

--
-- Дамп данных таблицы `networks`
--

INSERT INTO `networks` (`network_id`, `name`, `url`, `url_pattern`, `icon`, `enabled`, `authorization_table`, `dislike_table`, `comment_table`, `user_enabled_field`) VALUES
(1, 'Vkontakte', 'https://vk.com', '/vk.com/', 'http://youhonest.com/images/networks/vk.png', 1, 'authorizations_vk', 'dislikes_vk', 'comments_vk', 'vk_enabled'),
(2, 'Facebook', 'https://facebook.com', '/facebook.com/', 'http://youhonest.com/images/networks/fb.png', 0, 'authorizations_fb', 'dislikes_fb', 'comments_fb', 'fb_enabled'),
(3, 'GPlus', 'https://plus.google.com', '/plus.google.com/', 'http://youhonest.com/images/networks/gplus.png', 0, 'authorizations_gplus', 'dislikes_gplus', 'comments_gplus', 'gplus_enabled');

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_date` int(11) NOT NULL,
  `content` text CHARACTER SET utf8 NOT NULL,
  `language` varchar(10) NOT NULL,
  PRIMARY KEY (`news_id`),
  KEY `created_date` (`created_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `news`
--


-- --------------------------------------------------------

--
-- Структура таблицы `tbl_migration`
--

DROP TABLE IF EXISTS `tbl_migration`;
CREATE TABLE IF NOT EXISTS `tbl_migration` (
  `version` varchar(255) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `tbl_migration`
--

INSERT INTO `tbl_migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1344191212);

-- --------------------------------------------------------

--
-- Структура таблицы `translations`
--

DROP TABLE IF EXISTS `translations`;
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

--
-- Дамп данных таблицы `translations`
--

INSERT INTO `translations` (`translation_id`, `language`, `key`, `value`, `plurality_min`, `plurality_max`) VALUES
(1, 'en', 'NETWORKS::VKONTAKTE', 'Vkontakte', 1, 1),
(2, 'en', 'LOGIN::AUTHORIZATION', 'Authorization', 1, 1),
(3, 'ru', 'LOGIN::AUTHORIZATION', 'Авторизация', 1, 1),
(4, 'en', 'LOGIN::LOGIN_DESCRIPTION', 'For using Youhonest extension on this site you should authorize with account of currently opened social network', 1, 1),
(5, 'ru', 'LOGIN::LOGIN_DESCRIPTION', 'Для того, чтобы начать использовать расширение Youhonest, вы должны авторизироваться с помощью аккаунта текущей социальной сети', 1, 1),
(6, 'ru', 'LOGIN::YOU_CAN_DISABLE', 'Вы можете закрыть это окно и таким образом отключить расширения для сайта __NAME__.', 1, 1),
(7, 'en', 'LOGIN::YOU_CAN_DISABLE', 'You can close this window. So you can disable extension for __NAME__.', 1, 1),
(8, 'en', 'LOGIN::YOU_CAN_ENABLE', 'Later you can enable __NAME__ on settings page of extension.', 1, 1),
(9, 'ru', 'LOGIN::YOU_CAN_ENABLE', 'Включить __NAME__ можно будет в настройках расширения.', 1, 1),
(10, 'ru', 'LOGIN::WHAT_IS_THIS', 'О том, что такое Youhonest, вы можете прочитать на __LINK__.', 1, 1),
(11, 'ru', 'LOGIN::ABOUT_PAGE', 'странице описания проекта', 1, 1),
(12, 'en', 'LOGIN::WHAT_IS_THIS', 'What is Youhonest? You can read about it on __LINK__.', 1, 1),
(13, 'en', 'LOGIN::ABOUT_PAGE', 'about page', 1, 1),
(14, 'ru', 'LOGIN::AGREE_TERMS', 'Соглашаясь на авторизацию, вы соглашаетесь с __LINK__.', 1, 1),
(15, 'en', 'LOGIN::AGREE_TERMS', 'You automatically accept __LINK__ of this extension if you allow authorization.', 1, 1),
(16, 'ru', 'LOGIN::TERMS', 'правилами использования', 1, 1),
(17, 'en', 'LOGIN::TERMS', 'terms and conditions', 1, 1),
(18, 'ru', 'LOGIN::AUTHORIZE', 'Авторизоваться', 1, 1),
(19, 'en', 'LOGIN::AUTHORIZE', 'Authorize', 1, 1),
(20, 'ru', 'NETWORKS::VKONTAKTE', 'Вконтакте', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Дамп данных таблицы `users`
--

