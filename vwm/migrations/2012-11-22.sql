CREATE TABLE IF NOT EXISTS `wo2department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wo_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `wo2department` ADD FOREIGN KEY ( `wo_id` ) REFERENCES `work_order` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `wo2department` ADD FOREIGN KEY ( `department_id` ) REFERENCES `department` (
`department_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;