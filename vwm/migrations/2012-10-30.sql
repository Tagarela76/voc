CREATE TABLE  `calendar` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 100 ) NOT NULL ,
`description` VARCHAR( 250 ) NOT NULL ,
`event_date` INT( 15 ) NOT NULL ,
`author_id` INT( 11 ) NOT NULL,
`last_update_time` DATETIME NOT NULL
) ENGINE = INNODB;

ALTER TABLE `calendar` ADD FOREIGN KEY ( `author_id` ) REFERENCES `user` (
`user_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;