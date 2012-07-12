ALTER TABLE `preformulated_products` ENGINE = InnoDB;

ALTER TABLE `preformulated_products` ADD `last_update_time` DATETIME NULL;

ALTER TABLE `pfp2product` ENGINE = InnoDB;

ALTER TABLE `pfp2product` ADD FOREIGN KEY ( `product_id` ) REFERENCES `product` (
`product_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `pfp2product` ADD FOREIGN KEY ( `preformulated_products_id` ) REFERENCES `preformulated_products` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;


ALTER TABLE `product` CHANGE `unit_type` `price_unit_type` VARCHAR( 20 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;