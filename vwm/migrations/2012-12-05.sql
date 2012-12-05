ALTER TABLE `qty_product_gauge` ADD `department_id` INT NULL DEFAULT NULL AFTER `facility_id` ,
ADD INDEX ( `department_id` );

ALTER TABLE `qty_product_gauge` ADD FOREIGN KEY ( `department_id` ) REFERENCES `department` (
`department_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;