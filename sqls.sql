-- INITIAL DB
-- phpMyAdmin SQL Dump
-- PHP Version: 5.4.9-4ubuntu2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `lycdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `lycdb_cards`
--

CREATE TABLE IF NOT EXISTS `lycdb_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` varchar(10) NOT NULL,
  `name_jp` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL DEFAULT '',
  `rarity` varchar(10) NOT NULL,
  `alternate_rarities` varchar(10) NOT NULL DEFAULT '',
  `alternate_images` varchar(10) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL,
  `ex` tinyint(4) NOT NULL,
  `is_snow` tinyint(4) NOT NULL,
  `is_moon` tinyint(4) NOT NULL,
  `is_flower` tinyint(4) NOT NULL,
  `is_lightning` tinyint(4) NOT NULL,
  `is_sun` tinyint(4) NOT NULL,
  `cost_snow` int(11) NOT NULL,
  `cost_moon` int(11) NOT NULL,
  `cost_flower` int(11) NOT NULL,
  `cost_lightning` int(11) NOT NULL,
  `cost_sun` int(11) NOT NULL,
  `cost_star` int(11) NOT NULL,
  `ability_desc_jp` varchar(1000) NOT NULL,
  `ability_desc_en` varchar(1000) NOT NULL,
  `ability_cost_jp` varchar(200) NOT NULL DEFAULT '',
  `ability_cost_en` varchar(200) NOT NULL DEFAULT '',
  `ability_name_jp` varchar(80) NOT NULL DEFAULT '',
  `ability_name_en` varchar(80) NOT NULL DEFAULT '',
  `conversion_jp` varchar(100) NOT NULL DEFAULT '',
  `conversion_en` varchar(100) NOT NULL DEFAULT '',
  `basic_ability_flags` int(11) NOT NULL DEFAULT '0',
  `basic_abilities_jp` varchar(200) NOT NULL DEFAULT '',
  `basic_abilities_en` varchar(200) NOT NULL DEFAULT '',
  `is_male` tinyint(4) NOT NULL DEFAULT '0',
  `is_female` tinyint(4) NOT NULL DEFAULT '0',
  `comments_jp` varchar(200) NOT NULL DEFAULT '',
  `comments_en` varchar(200) NOT NULL DEFAULT '',
  `insert_date` timestamp NULL DEFAULT NULL,
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  `import_errors` varchar(2000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cid` (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lycdb_cards_sets_connect`
--

CREATE TABLE IF NOT EXISTS `lycdb_cards_sets_connect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `card_ext_id` int(11) NOT NULL,
  `set_ext_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `card_ext_id` (`card_ext_id`,`set_ext_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lycdb_sets`
--

CREATE TABLE IF NOT EXISTS `lycdb_sets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_id` int(11) NOT NULL,
  `name_jp` varchar(100) NOT NULL,
  `name_en` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ext_id` (`ext_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;



-- ------------------------

ALTER TABLE  `lycdb_cards` CHANGE  `update_date`  `update_date` TIMESTAMP NULL DEFAULT NULL ;
