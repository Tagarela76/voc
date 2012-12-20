CREATE TABLE IF NOT EXISTS `pfp_type2department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pfp_type_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE  `pfp_type2department` ADD FOREIGN KEY (  `pfp_type_id` ) REFERENCES `pfp_types` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE  `pfp_type2department` ADD FOREIGN KEY (  `department_id` ) REFERENCES  `department` (
`department_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;