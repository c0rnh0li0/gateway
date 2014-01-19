-- phpMyAdmin SQL Dump
-- version 3.5.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 31, 2012 at 11:29 AM
-- Server version: 5.5.25a-log
-- PHP Version: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `gateway_20_official`
--

-- --------------------------------------------------------

--
-- Table structure for table `gw_adapter`
--

CREATE TABLE IF NOT EXISTS `gw_adapter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gw_connection_id` int(11) NOT NULL,
  `gw_handler_id` int(11) NOT NULL,
  `settings` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gw_connection_id_gw_handler_id` (`gw_connection_id`,`gw_handler_id`),
  KEY `gw_connection_id` (`gw_connection_id`),
  KEY `gw_handler_id` (`gw_handler_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='adapters for handling connections' AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_connection`
--

CREATE TABLE IF NOT EXISTS `gw_connection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(31) NOT NULL,
  `shop_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_handler`
--

CREATE TABLE IF NOT EXISTS `gw_handler` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `node` enum('erp','shop') NOT NULL,
  `type` enum('products','products_images','orders','customers','categories') NOT NULL,
  `stream` enum('reader','writer') NOT NULL,
  `class` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `settings_mask` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='connection handlers' AUTO_INCREMENT=13 ;

--
-- Dumping data for table `gw_handler`
--

INSERT INTO `gw_handler` (`id`, `node`, `type`, `stream`, `class`, `description`, `settings_mask`) VALUES
(1, 'erp', 'products', 'reader', 'Gateway\\Handler\\Erp\\Etron\\XML\\Reader\\Products', 'Etron XML 1.0 products reader', NULL),
(2, 'shop', 'products', 'writer', 'Gateway\\Handler\\Shop\\Magento\\Magmi\\Writer\\Products', 'Magento via Magmi products writer', 'host=<SERVER_NAME>;user=<USER>;password=<PASSWORD>;dbname=<DATABASE>;version=1.7.x'),
(3, 'erp', 'customers', 'reader', 'Gateway\\Handler\\Erp\\Etron\\XML\\Reader\\Customers', 'Etron XML 1.0 customers reader', NULL),
(4, 'shop', 'customers', 'writer', 'Gateway\\Handler\\Shop\\Magento\\SOAP\\Writer\\Customers', 'Magento via SOAP customers writer', 'domain=<MAGENTO_DOMAIN>;user=<USER>;password=<PASSWORD>'),
(5, 'erp', 'orders', 'reader', 'Gateway\\Handler\\Erp\\Etron\\XML\\Reader\\Orders', 'Etron XML 1.0 orders reader', NULL),
(6, 'shop', 'orders', 'writer', 'Gateway\\Handler\\Shop\\Magento\\SOAP\\Writer\\Orders', 'Magento via SOAP orders writer', 'domain=<MAGENTO_DOMAIN>;user=<USER>;password=<PASSWORD>'),
(7, 'erp', 'orders', 'writer', 'Gateway\\Handler\\Erp\\Etron\\XML\\Writer\\Orders', 'Etron XML 1.0 orders writer', NULL),
(8, 'shop', 'orders', 'reader', 'Gateway\\Handler\\Shop\\Magento\\JSON\\Reader\\Orders', 'Magento JSON orders reader', NULL),
(9, 'erp', 'products_images', 'reader', 'Gateway\\Handler\\Erp\\Etron\\XML\\Reader\\Products\\Images', 'Etron XML 1.0 products images reader', NULL),
(10, 'shop', 'products_images', 'writer', 'Gateway\\Handler\\Shop\\Magento\\SOAP\\Writer\\Products\\Images', 'Magento via SOAP products images writer', 'domain=<MAGENTO_DOMAIN>;user=<USER>;password=<PASSWORD>'),
(11, 'erp', 'categories', 'reader', 'Gateway\\Handler\\Erp\\Etron\\XML\\Reader\\Categories', 'Etron XML 1.0 categories reader', NULL),
(12, 'shop', 'categories', 'writer', 'Gateway\\Handler\\Shop\\Magento\\SOAP\\Writer\\Categories', 'Magento via SOAP categories writer', 'domain=<MAGENTO_DOMAIN>;user=<USER>;password=<PASSWORD>');

-- --------------------------------------------------------

--
-- Table structure for table `gw_handler_type`
--

CREATE TABLE IF NOT EXISTS `gw_handler_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(63) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='handler type (scope)' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `gw_handler_type`
--

INSERT INTO `gw_handler_type` (`id`, `name`) VALUES
(5, 'categories'),
(3, 'customers'),
(2, 'orders'),
(1, 'products'),
(4, 'products_images');

-- --------------------------------------------------------

--
-- Table structure for table `gw_mapping_rule`
--

CREATE TABLE IF NOT EXISTS `gw_mapping_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gw_connection_id` int(11) NOT NULL,
  `gw_handler_type_id` int(11) DEFAULT NULL,
  `gw_mapping_scope_id` int(11) DEFAULT NULL,
  `type` enum('attribute','property','localization','enumeration') NOT NULL DEFAULT 'attribute',
  `old_name` varchar(63) NOT NULL,
  `old_value` varchar(255) DEFAULT NULL COMMENT 'NULL = all values',
  `new_name` varchar(63) NOT NULL,
  `new_value` varchar(255) DEFAULT NULL COMMENT 'NULL = all values',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `gw_mapping_ibfk_1_idx` (`gw_connection_id`),
  KEY `gw_handler_type_id` (`gw_handler_type_id`),
  KEY `gw_mapping_scope_id` (`gw_mapping_scope_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Mapping table.	' AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_mapping_scope`
--

CREATE TABLE IF NOT EXISTS `gw_mapping_scope` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gw_handler_type_id` int(11) DEFAULT NULL,
  `name` varchar(63) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gw_handler_type_id` (`gw_handler_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='scope/properties being influenced by mapping' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `gw_mapping_scope`
--

INSERT INTO `gw_mapping_scope` (`id`, `gw_handler_type_id`, `name`) VALUES
(1, 1, 'product.attributes'),
(2, NULL, '*.lang'),
(3, 2, 'order.status'),
(4, 2, 'order.payment.method'),
(5, 1, 'product.visibility');

-- --------------------------------------------------------

--
-- Table structure for table `gw_report`
--

CREATE TABLE IF NOT EXISTS `gw_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gw_schedule_id` int(11) unsigned NOT NULL,
  `gw_connection_id` int(11) NOT NULL,
  `log` varchar(500) NOT NULL,
  `csv` blob,
  PRIMARY KEY (`id`),
  KEY `fk_gw_report_gw_schedule1_idx` (`gw_schedule_id`),
  KEY `fk_gw_report_gw_connection1_idx` (`gw_connection_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=150 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_schedule`
--

CREATE TABLE IF NOT EXISTS `gw_schedule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gw_connection_id` int(11) NOT NULL,
  `inserted_at` datetime NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `is_cancelled` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_archived` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `status` enum('new','processing','finished') NOT NULL DEFAULT 'new',
  PRIMARY KEY (`id`),
  KEY `fk_gw_schedule_gw_connection1_idx` (`gw_connection_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=181 ;

-- --------------------------------------------------------

--
-- Table structure for table `gw_source`
--

CREATE TABLE IF NOT EXISTS `gw_source` (
  `gw_handler_id` int(11) NOT NULL,
  `gw_schedule_id` int(11) unsigned NOT NULL,
  `content` longblob NOT NULL,
  `type` enum('file','filename','filepath','text','folder') COLLATE utf8_czech_ci NOT NULL DEFAULT 'file',
  PRIMARY KEY (`gw_handler_id`,`gw_schedule_id`),
  KEY `gw_schedule_id` (`gw_schedule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='schedule handlers sources';

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gw_adapter`
--
ALTER TABLE `gw_adapter`
  ADD CONSTRAINT `FK_gw_adapter_gw_connection` FOREIGN KEY (`gw_connection_id`) REFERENCES `gw_connection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_gw_adapter_gw_handler` FOREIGN KEY (`gw_handler_id`) REFERENCES `gw_handler` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gw_mapping_rule`
--
ALTER TABLE `gw_mapping_rule`
  ADD CONSTRAINT `gw_connection_ibfk_1` FOREIGN KEY (`gw_connection_id`) REFERENCES `gw_connection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gw_mapping_rule_ibfk_1` FOREIGN KEY (`gw_mapping_scope_id`) REFERENCES `gw_mapping_scope` (`id`),
  ADD CONSTRAINT `gw_mapping_rule_ibfk_2` FOREIGN KEY (`gw_handler_type_id`) REFERENCES `gw_handler_type` (`id`);

--
-- Constraints for table `gw_mapping_scope`
--
ALTER TABLE `gw_mapping_scope`
  ADD CONSTRAINT `gw_mapping_scope_ibfk_1` FOREIGN KEY (`gw_handler_type_id`) REFERENCES `gw_handler_type` (`id`);

--
-- Constraints for table `gw_report`
--
ALTER TABLE `gw_report`
  ADD CONSTRAINT `gw_report_ibfk_1` FOREIGN KEY (`gw_schedule_id`) REFERENCES `gw_schedule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gw_report_ibfk_2` FOREIGN KEY (`gw_connection_id`) REFERENCES `gw_connection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gw_schedule`
--
ALTER TABLE `gw_schedule`
  ADD CONSTRAINT `gw_schedule_ibfk_1` FOREIGN KEY (`gw_connection_id`) REFERENCES `gw_connection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gw_source`
--
ALTER TABLE `gw_source`
  ADD CONSTRAINT `gw_source_ibfk_1` FOREIGN KEY (`gw_handler_id`) REFERENCES `gw_handler` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gw_source_ibfk_2` FOREIGN KEY (`gw_schedule_id`) REFERENCES `gw_schedule` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
