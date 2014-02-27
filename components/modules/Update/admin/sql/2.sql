ALTER TABLE `[prefix]streams_tags` DROP INDEX title;
ALTER TABLE `[prefix]streams_tags` ADD UNIQUE (`title`(255));
