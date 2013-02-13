RENAME TABLE `process` TO `process_template`;

RENAME TABLE `step` TO `step_template`;

RENAME TABLE `resource` TO `resource_template`;

CREATE TABLE IF NOT EXISTS `process_instance` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `facility_id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `work_order_id` int(255) DEFAULT NULL,
  `last_update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `step_instance` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `number` int(255) NOT NULL,
  `last_update_time` date NOT NULL,
  `process_id` int(255) DEFAULT NULL,
  `description` varchar(2000) DEFAULT NULL,
  `optional` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2437 ;

CREATE TABLE IF NOT EXISTS `resource_instance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(2000) NOT NULL,
  `qty` float NOT NULL,
  `unittype_id` int(255) NOT NULL,
  `resource_type_id` int(255) NOT NULL,
  `labor_cost` float NOT NULL,
  `material_cost` float NOT NULL,
  `total_cost` float NOT NULL,
  `rate` float NOT NULL,
  `rate_unittype_id` int(255) NOT NULL,
  `rate_qty` int(255) NOT NULL DEFAULT '1',
  `step_id` int(255) DEFAULT NULL,
  `last_update_time` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7870 ;


ALTER TABLE  `work_order` CHANGE  `process_id`  `process_template_id` INT( 11 ) NULL DEFAULT NULL

ALTER TABLE  `step_template` CHANGE  `process_id`  `process_template_id` INT( 255 ) NULL DEFAULT NULL

