# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Hôte: 127.0.0.1 (MySQL 5.5.5-10.3.14-MariaDB-1:10.3.14+maria~bionic)
# Base de données: db_milo
# Temps de génération: 2019-04-24 11:51:03 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

create database if not exists `db_milo`;

USE `db_milo`;

# Affichage de la table ci_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ci_sessions`;

CREATE TABLE `ci_sessions` (
  `id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table environments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `environments`;

CREATE TABLE `environments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `name` text NOT NULL,
  `folder` text NOT NULL,
  `webserver` enum('apache','nginx') DEFAULT NULL,
  `php_version_id` int(11) unsigned DEFAULT NULL,
  `php_port` bigint(20) DEFAULT NULL,
  `php_dockerfile` longtext DEFAULT NULL,
  `mysql_version_id` int(11) unsigned DEFAULT NULL,
  `mysql_port` bigint(20) DEFAULT NULL,
  `mysql_dockerfile` longtext DEFAULT NULL,
  `mysql_user` text NOT NULL,
  `mysql_password` text NOT NULL,
  `has_pma` tinyint(2) unsigned DEFAULT NULL,
  `pma_port` bigint(20) DEFAULT NULL,
  `has_sftp` tinyint(2) unsigned DEFAULT NULL,
  `sftp_user` text NOT NULL,
  `sftp_password` text NOT NULL,
  `sftp_port` bigint(20) DEFAULT NULL,
  `docker_compose` longtext DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `php_version_id` (`php_version_id`),
  KEY `mysql_version_id` (`mysql_version_id`),
  CONSTRAINT `environments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ion_auth_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `environments_ibfk_2` FOREIGN KEY (`php_version_id`) REFERENCES `php_versions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `environments_ibfk_3` FOREIGN KEY (`mysql_version_id`) REFERENCES `mysql_versions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `environments` WRITE;
/*!40000 ALTER TABLE `environments` DISABLE KEYS */;

INSERT INTO `environments` (`id`, `user_id`, `name`, `folder`, `webserver`, `php_version_id`, `php_port`, `php_dockerfile`, `mysql_version_id`, `mysql_port`, `mysql_dockerfile`, `mysql_user`, `mysql_password`, `has_pma`, `pma_port`, `has_sftp`, `sftp_user`, `sftp_password`, `sftp_port`, `docker_compose`, `created_date`)
VALUES
	(35,1,'Sample project','5cc04d8faf5c6','apache',1,14406,NULL,1,11294,NULL,'root','TmBDpGeaHXe4lyMw',1,20376,1,'5cc04d8faf5c6','8OFysbybZJKmlIID',16036,'version: \'2\'\n\nservices:\n  sftp-5cc04d8faf5c6:\n    image: atmoz/sftp\n    restart: always\n    volumes:\n        - ./src:/home/5cc04d8faf5c6\n    ports:\n        - \"16036:22\"\n    command: 5cc04d8faf5c6:8OFysbybZJKmlIID:1001\n\n  mysql-5cc04d8faf5c6:\n    restart: always\n    image: mysql:5.5.58\n    ports:\n      - 11294:3306\n    volumes:\n      - mysql_dir-5cc04d8faf5c6:/var/lib/mysql\n    environment:\n      MYSQL_ROOT_PASSWORD: TmBDpGeaHXe4lyMw\n\n  php-5cc04d8faf5c6:\n    restart: always\n    image: php:7.1-apache\n    depends_on:\n      - mysql-5cc04d8faf5c6\n    ports:\n      - 14406:80\n    links:\n      - mysql-5cc04d8faf5c6:db-server\n    volumes:\n      - \"./src:/var/www/html\"\n\n  phpmyadmin-5cc04d8faf5c6:\n    restart: always\n    image: phpmyadmin/phpmyadmin\n    ports:\n      - 20376:80\n    depends_on:\n      - mysql-5cc04d8faf5c6\n    environment:\n      PMA_HOST: mysql\n      PMA_PORT: 3306\n    links:\n      - mysql-5cc04d8faf5c6:mysql\n\nvolumes:\n  mysql_dir-5cc04d8faf5c6:\n    driver: local\n','2019-04-24 11:50:39');

/*!40000 ALTER TABLE `environments` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table ion_auth_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ion_auth_groups`;

CREATE TABLE `ion_auth_groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `ion_auth_groups` WRITE;
/*!40000 ALTER TABLE `ion_auth_groups` DISABLE KEYS */;

INSERT INTO `ion_auth_groups` (`id`, `name`, `description`)
VALUES
	(1,'Admin','Administrators'),
	(2,'Devs','Developers');

/*!40000 ALTER TABLE `ion_auth_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table ion_auth_login_attempts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ion_auth_login_attempts`;

CREATE TABLE `ion_auth_login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Affichage de la table ion_auth_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ion_auth_users`;

CREATE TABLE `ion_auth_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(254) NOT NULL,
  `activation_selector` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `forgotten_password_selector` varchar(255) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_email` (`email`),
  UNIQUE KEY `uc_activation_selector` (`activation_selector`),
  UNIQUE KEY `uc_forgotten_password_selector` (`forgotten_password_selector`),
  UNIQUE KEY `uc_remember_selector` (`remember_selector`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `ion_auth_users` WRITE;
/*!40000 ALTER TABLE `ion_auth_users` DISABLE KEYS */;

INSERT INTO `ion_auth_users` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`)
VALUES
	(1,'127.0.0.1','administrator','$2y$10$YomuP2G0lmUNcL8PZg8xreurDy41LJpwFqHn5JHkroQzIGs8ARbsi','admin@admin.com',NULL,'',NULL,NULL,NULL,'ec6eb01ff80f1e7e3433193c5d7f8df5c4290d4f','$2y$10$1yzeAlHJGkHAJgwDAe1Sle6DLvjqQ/ZO.UPIXdQcrT09gEY8g7xsu',1268889823,1556106509,1,'Test','Test','Test','0'),
	(3,'127.0.0.1','administrator','$2y$10$YomuP2G0lmUNcL8PZg8xreurDy41LJpwFqHn5JHkroQzIGs8ARbsi','dev@dev.com',NULL,'',NULL,NULL,NULL,'a25928c17c1ecdd12914fb9ca3e5185cb8d8c43f','$2y$10$nWzuN5BIvx8DHq0vAXeplObmspSVxBL/CC29nXHQQKkMWFqUaomou',1268889823,1555662590,1,'Test','Test','Test','0');

/*!40000 ALTER TABLE `ion_auth_users` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table ion_auth_users_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ion_auth_users_groups`;

CREATE TABLE `ion_auth_users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `ion_auth_groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `ion_auth_users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `ion_auth_users_groups` WRITE;
/*!40000 ALTER TABLE `ion_auth_users_groups` DISABLE KEYS */;

INSERT INTO `ion_auth_users_groups` (`id`, `user_id`, `group_id`)
VALUES
	(3,1,1),
	(4,3,2);

/*!40000 ALTER TABLE `ion_auth_users_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table mysql_versions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mysql_versions`;

CREATE TABLE `mysql_versions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `version` text NOT NULL,
  `tag` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `mysql_versions` WRITE;
/*!40000 ALTER TABLE `mysql_versions` DISABLE KEYS */;

INSERT INTO `mysql_versions` (`id`, `version`, `tag`)
VALUES
	(1,'5.5.58','mysql:5.5.58'),
	(2,'5.7','mysql:5.7');

/*!40000 ALTER TABLE `mysql_versions` ENABLE KEYS */;
UNLOCK TABLES;


# Affichage de la table php_versions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `php_versions`;

CREATE TABLE `php_versions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `version` text NOT NULL,
  `tag` text NOT NULL,
  `env` enum('apache','nginx','both') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `php_versions` WRITE;
/*!40000 ALTER TABLE `php_versions` DISABLE KEYS */;

INSERT INTO `php_versions` (`id`, `version`, `tag`, `env`)
VALUES
	(1,'7.1','php:7.1-apache','apache'),
	(2,'5.6','php:5.6-apache','apache');

/*!40000 ALTER TABLE `php_versions` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
