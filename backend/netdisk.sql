/*
Navicat MySQL Data Transfer

Source Server         : 昌维
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : netdisk

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-05-08 22:51:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for netdisk_files
-- ----------------------------
DROP TABLE IF EXISTS `netdisk_files`;
CREATE TABLE `netdisk_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_size` bigint(20) unsigned DEFAULT NULL,
  `file_md5` varchar(255) DEFAULT NULL,
  `file_sha1` varchar(255) DEFAULT NULL,
  `file_crc32` varchar(255) DEFAULT NULL,
  `file_mime_type` varchar(255) DEFAULT NULL,
  `file_extension` varchar(255) DEFAULT NULL,
  `folder_id` int(10) unsigned DEFAULT NULL,
  `real_path` varchar(255) DEFAULT NULL,
  `upload_time` timestamp NULL DEFAULT NULL,
  `modification_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `isdel` tinyint(3) unsigned DEFAULT '0' COMMENT '0未删除，1已删除',
  `delete_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=857 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for netdisk_folders
-- ----------------------------
DROP TABLE IF EXISTS `netdisk_folders`;
CREATE TABLE `netdisk_folders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `father_id` int(10) unsigned DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `folder_name` varchar(255) DEFAULT NULL,
  `create_time` timestamp NULL DEFAULT NULL,
  `modification_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `isdel` tinyint(3) unsigned DEFAULT '0',
  `delete_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for netdisk_share
-- ----------------------------
DROP TABLE IF EXISTS `netdisk_share`;
CREATE TABLE `netdisk_share` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL,
  `statue` tinyint(3) unsigned DEFAULT NULL COMMENT '0为停止分享，1为开始分享',
  `isexpires` tinyint(3) unsigned DEFAULT NULL,
  `expires` timestamp NULL DEFAULT NULL,
  `password` varchar(4) DEFAULT NULL,
  `share_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `permission` tinyint(3) unsigned DEFAULT NULL COMMENT '0完全公开，1公开分享但需要密码，2好友定向分享',
  `desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for netdisk_share_list
-- ----------------------------
DROP TABLE IF EXISTS `netdisk_share_list`;
CREATE TABLE `netdisk_share_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `share_id` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL COMMENT '0为file，1为folder',
  `f_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for netdisk_users
-- ----------------------------
DROP TABLE IF EXISTS `netdisk_users`;
CREATE TABLE `netdisk_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `reg_time` timestamp NULL DEFAULT NULL,
  `reg_ip` varchar(255) DEFAULT NULL,
  `last_login_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `last_login_ip` varchar(255) DEFAULT NULL,
  `total_size` int(10) unsigned DEFAULT '1073741824',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for netdisk_vcode
-- ----------------------------
DROP TABLE IF EXISTS `netdisk_vcode`;
CREATE TABLE `netdisk_vcode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `vcode` varchar(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
