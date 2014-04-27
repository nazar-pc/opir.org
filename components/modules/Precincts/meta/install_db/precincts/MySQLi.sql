CREATE TABLE IF NOT EXISTS `[prefix]precincts` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`number` varchar(10) NOT NULL,
	`address` varchar(1024) NOT NULL,
	`lat` float NOT NULL,
	`lng` float NOT NULL,
	`district` int(10) unsigned NOT NULL,
	`violations` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY `number` (`number`),
	KEY `district` (`district`),
	KEY `violations` (`violations`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
