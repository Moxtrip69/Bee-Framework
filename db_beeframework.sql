/*
Navicat MySQL Data Transfer

Source Server         : XAMPP
Source Server Version : 100427
Source Host           : localhost:3306
Source Database       : db_beeframework

Target Server Type    : MYSQL
Target Server Version : 100427
File Encoding         : 65001

Date: 2023-07-23 13:01:17
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of bee_users
-- ----------------------------
INSERT INTO `bee_users` VALUES ('1', '', 'bee', '$2y$10$xHEI5cJ3q7rBJaL.M9qBRe909ahHvIZVTfRRxlLqfnWwAYwWQE/Wu', 'jslocal@localhost.com', '2021-12-05 15:52:17');

-- ----------------------------
-- Table structure for options
-- ----------------------------
DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `option` varchar(255) DEFAULT NULL,
  `val` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of options
-- ----------------------------

-- ----------------------------
-- Table structure for posts
-- ----------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) DEFAULT '',
  `id_padre` bigint(20) DEFAULT NULL,
  `id_usuario` bigint(20) DEFAULT NULL,
  `id_ref` bigint(20) DEFAULT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `permalink` varchar(255) DEFAULT NULL,
  `contenido` text DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `creado` datetime DEFAULT NULL,
  `actualizado` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of posts
-- ----------------------------

-- ----------------------------
-- Table structure for posts_meta
-- ----------------------------
DROP TABLE IF EXISTS `posts_meta`;
CREATE TABLE `posts_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_post` int(11) NOT NULL,
  `meta` varchar(255) DEFAULT NULL,
  `valor` text DEFAULT NULL,
  `creado` datetime DEFAULT NULL,
  `actualizado` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of posts_meta
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- ----------------------------
-- Records of pruebas
-- ----------------------------
INSERT INTO `pruebas` VALUES ('1', 'John Doe', 'Un post de prueba', 'Lorem ipsum dolorem.', '2021-12-10 10:55:41');
INSERT INTO `pruebas` VALUES ('2', 'Pancho Villa', 'Otro post nuevo', 'Lorem ipsum dolorem.', '2021-12-10 11:02:01');