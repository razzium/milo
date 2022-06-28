-- -------------------------------------------------------------
-- TablePlus 4.7.1(429)
--
-- https://tableplus.com/
--
-- Database: db_milo
-- Generation Time: 2022-06-28 19:01:49.6500
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE `ci_sessions` (
							   `id` varchar(128) NOT NULL,
							   `ip_address` varchar(45) NOT NULL,
							   `timestamp` int(10) unsigned NOT NULL DEFAULT 0,
							   `data` blob NOT NULL,
							   PRIMARY KEY (`id`),
							   KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `environments`;
CREATE TABLE `environments` (
								`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
								`user_id` int(11) unsigned NOT NULL,
								`name` text NOT NULL,
								`folder` text NOT NULL,
								`webserver` enum('apache','nginx') DEFAULT NULL,
								`php_version_id` int(11) unsigned DEFAULT NULL,
								`php_port` bigint(20) DEFAULT NULL,
								`php_ssl_port` bigint(20) DEFAULT NULL,
								`php_dockerfile` longtext DEFAULT NULL,
								`mysql_version_id` int(11) unsigned DEFAULT NULL,
								`mysql_port` bigint(20) DEFAULT NULL,
								`mysql_dockerfile` longtext DEFAULT NULL,
								`mysql_user` text DEFAULT NULL,
								`mysql_password` text DEFAULT NULL,
								`has_pma` tinyint(2) unsigned DEFAULT NULL,
								`pma_port` bigint(20) DEFAULT NULL,
								`has_sftp` tinyint(2) unsigned DEFAULT NULL,
								`repository_git` text DEFAULT NULL,
								`sftp_user` text DEFAULT NULL,
								`sftp_password` text DEFAULT NULL,
								`sftp_port` bigint(20) DEFAULT NULL,
								`docker_compose` longtext DEFAULT NULL,
								`xDebug_remote_host` text DEFAULT NULL,
								`created_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
								PRIMARY KEY (`id`),
								KEY `user_id` (`user_id`),
								KEY `php_version_id` (`php_version_id`),
								KEY `mysql_version_id` (`mysql_version_id`),
								CONSTRAINT `environments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `ion_auth_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
								CONSTRAINT `environments_ibfk_2` FOREIGN KEY (`php_version_id`) REFERENCES `php_versions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
								CONSTRAINT `environments_ibfk_3` FOREIGN KEY (`mysql_version_id`) REFERENCES `mysql_versions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `ion_auth_groups`;
CREATE TABLE `ion_auth_groups` (
								   `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
								   `name` varchar(20) NOT NULL,
								   `description` varchar(100) NOT NULL,
								   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `ion_auth_login_attempts`;
CREATE TABLE `ion_auth_login_attempts` (
										   `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
										   `ip_address` varchar(45) NOT NULL,
										   `login` varchar(100) NOT NULL,
										   `time` int(11) unsigned DEFAULT NULL,
										   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `mysql_versions`;
CREATE TABLE `mysql_versions` (
								  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
								  `version` text NOT NULL,
								  `tag` text NOT NULL,
								  `is_active` tinyint(2) DEFAULT 1,
								  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

DROP TABLE IF EXISTS `php_versions`;
CREATE TABLE `php_versions` (
								`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
								`version` text NOT NULL,
								`tag` text NOT NULL,
								`env` enum('apache','nginx','both') DEFAULT NULL,
								`is_active` tinyint(4) DEFAULT 1,
								PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;

INSERT INTO `environments` (`id`, `user_id`, `name`, `folder`, `webserver`, `php_version_id`, `php_port`, `php_ssl_port`, `php_dockerfile`, `mysql_version_id`, `mysql_port`, `mysql_dockerfile`, `mysql_user`, `mysql_password`, `has_pma`, `pma_port`, `has_sftp`, `repository_git`, `sftp_user`, `sftp_password`, `sftp_port`, `docker_compose`, `xDebug_remote_host`, `created_date`) VALUES
																																																																																															  (69, 1, 'Sample Project 1', '62ba53b03ccbd', 'nginx', 1, 13247, 20837, 'FROM php:7.3-fpm\n\nRUN pecl install xdebug\n\n# Copy composer.lock and composer.json\n#COPY ../../composer.lock composer.json /var/www/\n\n# Set working directory\nWORKDIR /var/www\n\n# Install dependencies\nRUN apt-get update && apt-get install -y \\\nbuild-essential \\\nlibpng-dev \\\nlibzip-dev \\\nlibicu-dev \\\nlibjpeg62-turbo-dev \\\nlibfreetype6-dev \\\nlocales \\\nzip \\\njpegoptim optipng pngquant gifsicle \\\nvim \\\nunzip \\\ngit \\\ncurl\n\n# Install grpc\nRUN apt-get update\nRUN apt-get -y --no-install-recommends install g++ zlib1g-dev\nRUN pecl install grpc\nRUN docker-php-ext-enable grpc\n\n\n# Clear cache\nRUN apt-get clean && rm -rf /var/lib/apt/lists/*\n\n# Install extensions\nRUN docker-php-ext-configure zip --with-libzip\nRUN docker-php-ext-install pdo_mysql mbstring exif pcntl\nRUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/\nRUN docker-php-ext-install gd\nRUN docker-php-ext-install intl\nRUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli\nRUN docker-php-ext-enable xdebug\n\n# Install composer\nRUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer\n\n# Add user for laravel application\nRUN groupadd -g 1000 www\nRUN useradd -u 1000 -ms /bin/bash -g www www\n\n# Copy existing application directory contents\n#COPY ../.. /var/www\n\n# Copy existing application directory permissions\n#COPY --chown=www:www ../.. /var/www\n\n# Change current user to www\nUSER www\n\n# Expose port 9000 and start php-fpm server\nEXPOSE 9000\nCMD [\"php-fpm\"]\n', 4, 11668, NULL, 'root', 'IyP8nHURq0RaEKsj', 1, 24132, 1, NULL, 'sample_project_1', 'IfunMjvdJbKcr9uH', 20812, 'version: \'3\'\n\nservices:\n\n  sftp-62ba53b03ccbd:\n    image: atmoz/sftp\n    restart: always\n    volumes:\n        - ./src:/home/sample_project_1/www/sample_project_1/src\n        - ./logs:/home/sample_project_1/www/sample_project_1/logs\n    ports:\n        - \"20812:22\"\n    command: sample_project_1:IfunMjvdJbKcr9uH:::www\n  mysql-62ba53b03ccbd:\n    restart: always\n    image: mariadb:10.4\n    ports:\n      - 11668:3306\n    volumes:\n      - mysql_dir-62ba53b03ccbd:/var/lib/mysql\n    environment:\n      MYSQL_ROOT_PASSWORD: IyP8nHURq0RaEKsj\n\n  #PHP Service\n  ci4-app-62ba53b03ccbd:\n    build: docker/image/php\n    container_name: ci4-app-62ba53b03ccbd\n    restart: unless-stopped\n    tty: true\n    environment:\n      #SERVICE_NAME: app\n      #SERVICE_TAGS: dev\n      PHP_IDE_CONFIG: serverName=62ba53b03ccbd\n    volumes:\n      - ./src:/var/www\n      #- ./docker/image/php/local.ini:/usr/local/etc/php/conf.d/local.ini\n      #- ./docker/image/php/conf.d/xdebug.ini:/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini\n      #- ./docker/image/php/conf.d/error_reporting.ini:/usr/local/etc/php/conf.d/error_reporting.ini\n    networks:\n      - app-network-ci4-62ba53b03ccbd\n\n  #Nginx Service\n  ci4-nginx-62ba53b03ccbd:\n    image: nginx:alpine\n    container_name: ci4-nginx-62ba53b03ccbd\n    restart: unless-stopped\n    tty: true\n    ports:\n      - \"13247:80\"\n      - \"20837:443\"\n    volumes:\n      - ./src:/var/www\n      #- ./logs:/var/log/nginx\n      - ./docker/nginx/conf.d/:/etc/nginx/conf.d/\n      #- certbot-etc:/etc/letsencrypt\n      #- certbot-var:/var/lib/letsencrypt\n      #- ./docker/dhparam:/etc/ssl/certs\n    depends_on:\n      - ci4-app-62ba53b03ccbd\n    links:\n      - mysql-62ba53b03ccbd:db-server\n    networks:\n      - app-network-ci4-62ba53b03ccbd\n\n  phpmyadmin-62ba53b03ccbd:\n    restart: always\n    image: phpmyadmin/phpmyadmin\n    ports:\n      - 24132:80\n    depends_on:\n      - mysql-62ba53b03ccbd\n    environment:\n      PMA_HOST: mysql\n      PMA_PORT: 3306\n    links:\n      - mysql-62ba53b03ccbd:mysql\n\n#Docker Networks\nnetworks:\n  app-network-ci4-62ba53b03ccbd:\n    driver: bridge\n\nvolumes:\n  mysql_dir-62ba53b03ccbd:\n    driver: local\n', NULL, '2022-06-28 01:07:47'),
																																																																																															  (70, 1, 'Sample Project 2', '62ba53ea61c68', 'apache', 2, 24783, 22816, 'FROM php:7.3-apache\n\n# Install dependencies\nRUN buildDeps=\" \\\nwget \\\ngit \\\nssh \\\nless \\\n\"; \\\nset -x \\\n&& apt-get update && apt-get install -y $buildDeps --no-install-recommends && rm -rf /var/lib/apt/lists/*\n\n# Composer\nRUN wget https://getcomposer.org/installer -O - -q | php -- --quiet && \\\nmv composer.phar /usr/local/bin/composer\n\nRUN apt-get update && apt-get install -y libmcrypt-dev \\\n&& pecl install mcrypt-1.0.2 \\\n&& docker-php-ext-enable mcrypt\n\n# Install grpc\nRUN apt-get update\nRUN apt-get -y --no-install-recommends install g++ zlib1g-dev\nRUN pecl install grpc\nRUN docker-php-ext-enable grpc\n\n# Install libs\nRUN apt-get update && apt-get install -y libzip-dev libxml2 libxml2-dev git zlib1g-dev libmcrypt-dev\nRUN docker-php-ext-install mysqli pdo pdo_mysql soap mbstring zip\nRUN apt-get update \\\n&& apt-get install -y zlib1g-dev libicu-dev libfreetype6-dev libjpeg62-turbo-dev g++ \\\n&& docker-php-ext-configure intl \\\n&& docker-php-ext-install intl \\\n&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \\\n&& docker-php-ext-install gd\n\n# Install composer Todo : option\n# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer\nRUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composerzip\n\n# Install xDebug Todo : option\n#RUN pecl install xdebug-2.5.5 && docker-php-ext-enable xdebug\n#RUN echo \"xdebug.remote_enable=1\\n\" \\\n#    \"xdebug.remote_connect_back=0\\n\" \\\n#\"xdebug.remote_autostart=1\\n\" \\\n#\"xdebug.remote_host=\\n\" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini\n#    \"xdebug.idekey=ide-data\\n\" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini\n\n# Install SSL Todo : option (+ todo : let\'s encrypt + certbot)\nRUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/ssl-cert-snakeoil.key -out /etc/ssl/certs/ssl-cert-snakeoil.pem -subj \"/C=AT/ST=Vienna/L=Vienna/O=Security/OU=Development/CN=example.com\"\n\nRUN a2enmod rewrite\nRUN a2ensite default-ssl\nRUN a2enmod ssl\n\nEXPOSE 80\nEXPOSE 443\n', 4, 21488, NULL, 'root', '5vaTS9Kkh7rZxZlG', 1, 22585, 1, NULL, 'sample_project_2', 'XjRj41ownqRZREm8', 11287, 'version: \'2\'\n\nservices:\n  sftp-62ba53ea61c68:\n    image: atmoz/sftp\n    restart: always\n    volumes:\n        - ./src:/home/sample_project_2/www/sample_project_2/src\n        - ./logs:/home/sample_project_2/www/sample_project_2/logs\n    ports:\n        - \"11287:22\"\n    command: sample_project_2:XjRj41ownqRZREm8:::www\n  mysql-62ba53ea61c68:\n    restart: always\n    image: mariadb:10.4\n    ports:\n      - 21488:3306\n    volumes:\n      - mysql_dir-62ba53ea61c68:/var/lib/mysql\n    environment:\n      MYSQL_ROOT_PASSWORD: 5vaTS9Kkh7rZxZlG\n\n  php-62ba53ea61c68:\n    restart: always\n    build: docker/image/php\n    depends_on:\n      - mysql-62ba53ea61c68\n    ports:\n      - 24783:80\n      - 22816:443\n    links:\n      - mysql-62ba53ea61c68:db-server\n    volumes:\n      - \"./src:/var/www/html\"\n      - \"./logs/apache:/var/log/apache2\"\n\n  phpmyadmin-62ba53ea61c68:\n    restart: always\n    image: phpmyadmin/phpmyadmin\n    ports:\n      - 22585:80\n    depends_on:\n      - mysql-62ba53ea61c68\n    environment:\n      PMA_HOST: mysql\n      PMA_PORT: 3306\n    links:\n      - mysql-62ba53ea61c68:mysql\n\nvolumes:\n  mysql_dir-62ba53ea61c68:\n    driver: local\n', NULL, '2022-06-28 01:07:47');
INSERT INTO `ion_auth_groups` (`id`, `name`, `description`) VALUES
																(1, 'Admin', 'Administrators'),
																(2, 'Devs', 'Developers');

INSERT INTO `ion_auth_users` (`id`, `ip_address`, `username`, `password`, `email`, `activation_selector`, `activation_code`, `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`, `remember_selector`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
																																																																																				 (1, '127.0.0.1', 'administrator', '$2y$10$YomuP2G0lmUNcL8PZg8xreurDy41LJpwFqHn5JHkroQzIGs8ARbsi', 'admin@admin.com', NULL, '', NULL, NULL, NULL, '0849e49bf10e11c115c930d50e8fdf6614c22524', '$2y$10$1JMz.Et3Tfi83yGH8WG.pe8Ew36uDtYx0jAequYwrrjfseCmr116K', 1268889823, 1656420347, 1, 'Test', 'Test', 'Test', '0'),
																																																																																				 (3, '127.0.0.1', 'administrator', '$2y$10$YomuP2G0lmUNcL8PZg8xreurDy41LJpwFqHn5JHkroQzIGs8ARbsi', 'dev@dev.com', NULL, '', NULL, NULL, NULL, 'a25928c17c1ecdd12914fb9ca3e5185cb8d8c43f', '$2y$10$nWzuN5BIvx8DHq0vAXeplObmspSVxBL/CC29nXHQQKkMWFqUaomou', 1268889823, 1555662590, 1, 'Test', 'Test', 'Test', '0');

INSERT INTO `ion_auth_users_groups` (`id`, `user_id`, `group_id`) VALUES
																	  (3, 1, 1),
																	  (4, 3, 2);

INSERT INTO `mysql_versions` (`id`, `version`, `tag`, `is_active`) VALUES
																	   (1, 'MySQL 5.5.58', 'mysql:5.5.58', 0),
																	   (2, 'MySQL 5.7', 'mysql:5.7', 0),
																	   (3, 'MariaDB 5.7', 'mariadb:5.5', 0),
																	   (4, 'MariaDB 10.4', 'mariadb:10.4', 1),
																	   (5, 'MySQL 8', 'mysql:8', 0);

INSERT INTO `php_versions` (`id`, `version`, `tag`, `env`, `is_active`) VALUES
																			(1, 'Nginx : 7.3-FPM', 'php:7.3-fpm', 'nginx', 1),
																			(2, 'Apache : 7.3', 'php:7.3-apache', 'apache', 1),
																			(3, 'Apache : 7.1', 'php:7.1-apache', 'apache', 0),
																			(4, 'Apache : 5.6', 'php:5.6-apache', 'apache', 0);



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
