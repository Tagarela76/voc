ALTER TABLE `mix` ADD `last_update_time` DATETIME NULL DEFAULT NULL;

ALTER TABLE `mixgroup` ADD FOREIGN KEY ( `unit_type` ) REFERENCES `unittype` (
`unittype_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `mixgroup` ADD FOREIGN KEY ( `mix_id` ) REFERENCES `mix` (
`mix_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `mixgroup` ADD FOREIGN KEY ( `product_id` ) REFERENCES `product` (
`product_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `product` ADD FOREIGN KEY ( `supplier_id` ) REFERENCES `supplier` (
`supplier_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;
