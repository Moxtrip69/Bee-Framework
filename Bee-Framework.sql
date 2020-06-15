/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 100131
Source Host           : localhost:3306
Source Database       : u3_p2_db

Target Server Type    : MYSQL
Target Server Version : 100131
File Encoding         : 65001

Date: 2019-07-28 22:05:03
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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for options
-- ----------------------------
DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `option` varchar(255) DEFAULT NULL,
  `val` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
