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
