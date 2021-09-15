/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 100414
Source Host           : localhost:3306
Source Database       : db_beeframework

Target Server Type    : MYSQL
Target Server Version : 100414
File Encoding         : 65001

Date: 2021-09-10 22:14:45
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for movements
-- ----------------------------
DROP TABLE IF EXISTS `movements`;
CREATE TABLE `movements` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pruebas
-- ----------------------------
DROP TABLE IF EXISTS `pruebas`;
CREATE TABLE `pruebas` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pruebas2
-- ----------------------------
DROP TABLE IF EXISTS `pruebas2`;
CREATE TABLE `pruebas2` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pruebas4
-- ----------------------------
DROP TABLE IF EXISTS `pruebas4`;
CREATE TABLE `pruebas4` (
  `algo` varchar(255) DEFAULT NULL,
  `otro_algo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
