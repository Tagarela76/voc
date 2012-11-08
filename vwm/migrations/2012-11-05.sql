CREATE TABLE  `browse_category_entity` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 150 ) NOT NULL,
`default_value` VARCHAR( 300 ) NOT NULL
) ENGINE = INNODB;

CREATE TABLE  `display_columns_settings` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`browse_category_entity_id` INT( 11 ) NOT NULL ,
`value` VARCHAR( 300 ) NOT NULL ,
`industry_type_id` INT( 11 ) NOT NULL,
`last_update_time` DATETIME NULL DEFAULT NULL
) ENGINE = INNODB ;

ALTER TABLE `display_columns_settings` ADD FOREIGN KEY ( `browse_category_entity_id` ) REFERENCES `browse_category_entity` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `display_columns_settings` ADD FOREIGN KEY ( `industry_type_id` ) REFERENCES `industry_type` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE  `display_columns_settings` ADD UNIQUE (
`browse_category_entity_id` ,
`industry_type_id`
);

INSERT INTO  `browse_category_entity` (
`id` ,
`name` ,
`default_value`
)
VALUES (
NULL ,  'Mix Browse Category Columns',  'Product Name,Description,R/O Description,Contact,R/O VIN number,VOC,Creation Date'
);