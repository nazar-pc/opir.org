CREATE TABLE IF NOT EXISTS `[prefix]precincts_violations_feedback` (
	`id` int(11) NOT NULL COMMENT 'Violation id',
	`user` int(11) NOT NULL,
	`value` int(1) NOT NULL,
	PRIMARY KEY (`id`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
