CREATE TABLE IF NOT EXISTS `[prefix]precincts` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`number` varchar(10) NOT NULL,
	`address` text NOT NULL,
	`lat` float NOT NULL,
	`lng` float NOT NULL,
	`district` int(10) unsigned NOT NULL,
	`violations` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `number` (`number`),
	KEY `district` (`district`),
	KEY `violations` (`violations`),
	FULLTEXT KEY `address` (`address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `[prefix]precincts_streams` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`precinct` int(10) unsigned NOT NULL,
	`user` int(10) unsigned NOT NULL,
	`added` bigint(20) unsigned NOT NULL,
	`stream_url` varchar(1024) NOT NULL,
	`status` tinyint(1) NOT NULL,
	PRIMARY KEY (`id`),
	KEY `precinct` (`precinct`),
	KEY `new` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
