/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 100414
Source Host           : localhost:3306
Source Database       : db_beeframework

Target Server Type    : MYSQL
Target Server Version : 100414
File Encoding         : 65001

Date: 2021-12-10 11:20:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for bee_users
-- ----------------------------
DROP TABLE IF EXISTS `bee_users`;
CREATE TABLE `bee_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_token` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bee_users
-- ----------------------------
INSERT INTO `bee_users` VALUES ('1', '$2y$10$1eRP6qwtSDdsvtacxXRq2OHBqiC3p5klpaBp8EAZYpy5zOh20kZpi', 'bee', '$2y$10$xHEI5cJ3q7rBJaL.M9qBRe909ahHvIZVTfRRxlLqfnWwAYwWQE/Wu', 'jslocal@localhost.com', '2021-12-05 15:52:17');

-- ----------------------------
-- Table structure for pruebas
-- ----------------------------
DROP TABLE IF EXISTS `pruebas`;
CREATE TABLE `pruebas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT '',
  `titulo` varchar(255) DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `creado` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pruebas
-- ----------------------------
INSERT INTO `pruebas` VALUES ('1', 'John Doe', 'Un post de prueba', 'Lorem ipsum dolorem.', '2021-12-10 10:55:41');
INSERT INTO `pruebas` VALUES ('2', 'Pancho Villa', 'Otro post nuevo', 'Lorem ipsum dolorem.', '2021-12-10 11:02:01');

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
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;