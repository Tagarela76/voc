ALTER TABLE  `equipment` ADD  `model_number` VARCHAR( 120 ) NULL DEFAULT NULL ,
ADD  `serial_number` VARCHAR( 120 ) NULL DEFAULT NULL

CREATE TABLE IF NOT EXISTS `equipment_filter` (
  `equipment_filter_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `equipment_id` int(5) NOT NULL,
  `height_size` decimal(5,3) DEFAULT NULL,
  `equipment_filter_type_id` int(11) NOT NULL,
  `qty` int(5) NOT NULL,
  `width_size` decimal(5,3) DEFAULT NULL,
  `length_size` decimal(5,3) DEFAULT NULL,
  PRIMARY KEY (`equipment_filter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

CREATE TABLE IF NOT EXISTS `equipment_filter_type` (
  `equipment_filter_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`equipment_filter_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `equipment_lighting` (
  `equipment_lighting_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  `equipment_id` int(11) NOT NULL,
  `bulb_type` int(5) DEFAULT NULL,
  `size` int(5) DEFAULT NULL,
  `voltage` int(5) DEFAULT NULL,
  `wattage` int(5) DEFAULT NULL,
  `color` int(5) DEFAULT NULL,
  PRIMARY KEY (`equipment_lighting_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

CREATE TABLE IF NOT EXISTS `equipment_lighting_bulb_type` (
  `equipment_lighting_bulb_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`equipment_lighting_bulb_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `equipment_lighting_color` (
  `equipment_lighting_color_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`equipment_lighting_color_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

