ALTER TABLE `[prefix]precincts` CHANGE `address` `address_uk` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `[prefix]precincts` ADD `address_en` TEXT NOT NULL AFTER `address_uk`, ADD `address_ru` TEXT NOT NULL AFTER `address_en`, ADD FULLTEXT (`address_en`), ADD FULLTEXT (`address_ru`);
