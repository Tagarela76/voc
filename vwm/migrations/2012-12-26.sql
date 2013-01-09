CREATE TABLE IF NOT EXISTS `process` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `facility_id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `work_order_id` int(255) DEFAULT NULL,
  `last_update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `step` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `number` int(255) NOT NULL,
  `last_update_time` date NOT NULL,
  `process_id` int(255) DEFAULT NULL,
  `process_template_id` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `resource` (
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
  `step_template_id` int(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `unittype` (
`unittype_id` ,
`name` ,
`unittype_desc` ,
`formula` ,
`type_id` ,
`system` ,
`unit_class_id`
)
VALUES (
NULL ,  'each',  'each', NULL ,  '7', NULL ,  '6'
);
ALTER TABLE  `process` ADD  `process_type` INT NULL DEFAULT NULL;