CREATE TABLE IF NOT EXISTS `[prefix]precincts_violations` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`precinct` int(10) unsigned NOT NULL,
	`user` int(10) unsigned NOT NULL,
	`date` bigint(20) NOT NULL,
	`text` text NOT NULL,
	`images` text NOT NULL COMMENT 'JSON array',
	`video` varchar(1024) NOT NULL,
	`status` tinyint(1) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `precinct` (`precinct`),
	KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
