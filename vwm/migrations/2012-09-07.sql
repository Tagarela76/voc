ALTER TABLE `accessory` ADD FOREIGN KEY ( `jobber_id` ) REFERENCES `jobber` (
`jobber_id`
) ON DELETE CASCADE ON UPDATE RESTRICT;


CREATE TABLE IF NOT EXISTS `pfp2pfp_types` (
  `pfp_id` int(11) NOT NULL,
  `pfp_type_id` int(11) NOT NULL,
  PRIMARY KEY (`pfp_id`,`pfp_type_id`),
  KEY `pfp_id` (`pfp_id`),
  KEY `pfp_type_id` (`pfp_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `pfp2pfp_types` ADD FOREIGN KEY ( `pfp_id` ) REFERENCES `preformulated_products` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `pfp2pfp_types` ADD FOREIGN KEY ( `pfp_type_id` ) REFERENCES `pfp_types` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

INSERT INTO pfp2pfp_types (pfp_id, pfp_type_id) 
SELECT id, type_id FROM `preformulated_products`
WHERE `type_id` IS NOT NULL; 