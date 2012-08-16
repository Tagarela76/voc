CREATE TABLE  `voc`.`product_library_type` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 150 ) NOT NULL
) ENGINE = InnoDB ;

CREATE TABLE  `voc`.`product2product_library_type` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`product_id` INT( 11 ) NOT NULL ,
`product_library_type_id` INT( 11 ) NOT NULL
) ENGINE = INNODB;

ALTER TABLE `product2product_library_type` ADD FOREIGN KEY ( `product_id` ) REFERENCES `product` (
`product_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `product2product_library_type` ADD FOREIGN KEY ( `product_library_type_id` ) REFERENCES `product_library_type` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;