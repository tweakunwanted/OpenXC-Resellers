#
# Table schema for MySQL
#

CREATE TABLE IF NOT EXISTS `urls` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `url` varchar(1000) COLLATE utf8mb4_bin NOT NULL,
 `creator_id` int(11) NOT NULL,
 `created` datetime NOT NULL,
 `accessed` datetime DEFAULT NULL,
 `hits` int(10) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 UNIQUE KEY `url` (`url`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin