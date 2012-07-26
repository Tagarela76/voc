CREATE TABLE IF NOT EXISTS `work_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(64) NOT NULL,
  `description` text,
  `customer_name` varchar(256) DEFAULT NULL,
  `facility_id` int(11) NOT NULL,
  `status` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `work_order` ADD FOREIGN KEY ( `facility_id` ) REFERENCES `facility` (
`facility_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `mix` ADD `wo_id` INT( 11 ) NULL DEFAULT NULL

ALTER TABLE `equipment_lighting` ADD `quantity` INT( 5 ) NULL DEFAULT NULL

ALTER TABLE  `mix` ADD  `work_order_iteration` TINYINT( 4 ) NOT NULL DEFAULT  '0'