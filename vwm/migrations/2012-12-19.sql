CREATE TABLE IF NOT EXISTS `pfp2department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pfp_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10;