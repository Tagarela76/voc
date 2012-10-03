ALTER TABLE  `product2type` CHANGE  `product_id`  `product_id` INT( 11 ) NOT NULL ,
CHANGE  `type_id`  `industry_type_id` INT( 11 ) NOT NULL

RENAME TABLE  `product2type` TO  `product2industry_type` ;
ALTER TABLE  `product2industry_type` ENGINE = INNODB

ALTER TABLE  `industry_type` ENGINE = INNODB

ALTER TABLE `product2industry_type` ADD FOREIGN KEY ( `product_id` ) REFERENCES `product` (
`product_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `product2industry_type` ADD FOREIGN KEY ( `industry_type_id` ) REFERENCES `industry_type` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

CREATE TABLE  `company2industry_type` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`company_id` INT( 11 ) NOT NULL ,
`industry_type_id` INT( 11 ) NOT NULL
) ENGINE = INNODB;

ALTER TABLE `company2industry_type` ADD FOREIGN KEY ( `industry_type_id` ) REFERENCES `industry_type` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `company2industry_type` ADD FOREIGN KEY ( `company_id` ) REFERENCES `company` (
`company_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;