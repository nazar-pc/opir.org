CREATE TABLE IF NOT EXISTS `[prefix]events_events_tags` (
	`id` int(11) NOT NULL,
	`tag` int(11) NOT NULL,
	PRIMARY KEY (`id`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `[prefix]events_tags` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(1024) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `title` (`title`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
