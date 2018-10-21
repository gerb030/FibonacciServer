-- CREATE DATABASE planningpoker;
USE planningpoker;

-- GRANT SELECT, UPDATE, DELETE, INSERT ON planningpoker.* to `pokerappuser` identified by 'pokerapppassword';

-- Drop the tables in the correct order
DROP TABLE IF EXISTS `pokerround_user`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `pokerround`;

CREATE TABLE `pokerround` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `ownerusername` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `starttime` timestamp NULL DEFAULT NULL,
  `lastupdated` timestamp NULL DEFAULT NULL,
  `closed` boolean not null DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session` (`session`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `emailaddress` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `username_key` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `pokerround_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pokerround_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `voted` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `participation` (`pokerround_id`,`user_id`),
  KEY `pokerround_id` (`pokerround_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `pokerround_ibfk_1` FOREIGN KEY (`pokerround_id`) REFERENCES `pokerround` (`id`),
  CONSTRAINT `pokerround_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
