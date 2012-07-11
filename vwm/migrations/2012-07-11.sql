ALTER TABLE  `product` ADD  `product_pricing` DECIMAL( 11, 2 ) NOT NULL DEFAULT  '0.00';

ALTER TABLE  `product` ADD  `unit_type` VARCHAR( 20 ) NULL DEFAULT NULL;

ALTER TABLE `price4product` ADD FOREIGN KEY ( `product_id` ) REFERENCES `product` (
`product_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;