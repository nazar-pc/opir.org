ALTER TABLE `[prefix]precincts` CHANGE `address` `address` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `[prefix]precincts` ADD FULLTEXT(`address`);
