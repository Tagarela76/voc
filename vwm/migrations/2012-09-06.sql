CREATE TABLE  `voc`.`sales_brochure` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`sales_client_id` INT( 11 ) NOT NULL ,
`title_up` VARCHAR( 100 ) NULL DEFAULT NULL ,
`title_down` VARCHAR( 100 ) NULL DEFAULT NULL
) ENGINE = INNODB;