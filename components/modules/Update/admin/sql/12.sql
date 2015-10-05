ALTER TABLE `[prefix]precincts_violations` ADD `location` VARCHAR(255) NOT NULL AFTER `status`, ADD `device_model` VARCHAR(2014) NOT NULL AFTER `location`;
