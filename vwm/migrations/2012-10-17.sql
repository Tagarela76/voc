CREATE TABLE IF NOT EXISTS `industry_type2label` (
  `industry_type_id` int(11) NOT NULL,
  `label_id` varchar(120) NOT NULL,
  `label_text` varchar(120) NOT NULL,
  PRIMARY KEY (`industry_type_id`,`label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `industry_type2label` ADD FOREIGN KEY ( `industry_type_id` ) REFERENCES `industry_type` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;