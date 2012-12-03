CREATE TABLE IF NOT EXISTS `qty_product_gauge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `limit` int(11) NOT NULL DEFAULT '0',
  `unit_type` int(11) NOT NULL DEFAULT '1',
  `period` TINYINT(1) NOT NULL DEFAULT '0',
  `facility_id` int(11) NOT NULL,
  `last_update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `qty_product_gauge` ADD FOREIGN KEY ( `unit_type` ) REFERENCES `unittype` (
`unittype_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `qty_product_gauge` ADD FOREIGN KEY ( `facility_id` ) REFERENCES `facility` (
`facility_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;