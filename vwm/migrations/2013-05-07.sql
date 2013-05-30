CREATE TABLE IF NOT EXISTS `inspection_description` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `description_settings` varchar(2000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

INSERT INTO `inspection_description` (`description_settings`) VALUES
('{"name": "Replaced damaged filters","notes": 0}'),
('{"name": "Cleaned filter housing","notes": 0}'),
('{"name": "Manometer fluid top up","notes": 0}'),
('{"name": "Manometer hose replacement","notes": 0}'),
('{"name": "Installed new manometer gauge","notes": 0}'),
('{"name": "Helic gauge hose replacement","notes": 0}'),
('{"name": "Installed new helic gauge","notes": 0}'),
('{"name": "Oven filter change","notes": 0}'),
('{"name": "RTD temperature probe replacement","notes": 0}'),
('{"name": "Replace temperature controls","notes": 0}'),
('{"name": "Clean oven enclosure","notes": 0}'),
('{"name": "Burner service","notes": 1}'),
('{"name": "Replace electric oven elements","notes": 0}'),
('{"name": "Service electric oven","notes": 1}'),
('{"name": "Service/Repair  AMU","notes": 1}'),
('{"name": "Clarifier cleaning","notes": 0}'),
('{"name": "Clarifier quarterly service","notes": 1}'),
('{"name": "Clarifier annual service","notes": 1}'),
('{"name": "Clarifier control panel service","notes": 1}'),
('{"name": "Spray booth inspection","notes": 0}'),
('{"name": "Spray booth service/repairs","notes": 1}'),
('{"name": "Prep station inspection","notes": 0}'),
('{"name": "Prep station service/repairs","notes": 1}'),
('{"name": "Other equipment (custom)","notes": 1}');

CREATE TABLE IF NOT EXISTS `inspection_type` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `settings` varchar(2000) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;


INSERT INTO `inspection_type` (`settings`) VALUES
('{"typeName":"Filter Condition","permit": 0,"subtypes": [{"name":  "NOT IN USE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "GOOD","notes": 0,"qty": 0,"valueGauge": 0},{"name": "DAMAGED","notes": 1,"qty": 0,"valueGauge": 0},{"name": "REPLACED","notes": 1,"qty": 0,"valueGauge": 0}]}'),
('{"typeName":"Manometer /Helic Gauge","gaugeType":1,"permit": 0,"subtypes": [{"name": "NOT IN USE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "READING","notes": 0,"qty": 0,"valueGauge": 1},{"name": "SERVICE","notes": 1,"qty": 0,"valueGauge": 0}]}'),
('{"typeName":"Oven","permit": 1,"gaugeType":0,"subtypes": [{"name": "NOT IN USE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "TEMPERATURE","notes": 0,"qty": 0,"valueGauge": 1},{"name": "GAS OR ELECTRIC","notes": 0,"qty": 0,"valueGauge": 0},{"name": "GAS USAGE - FROM & TO","notes": 0,"qty": 0,"valueGauge": 0},{"name": "ELECTRIC ELEMENTS CONDITIONS","notes": 0,"qty": 0,"valueGauge": 0},{"name": "REPLACEMENT","notes": 1,"qty": 0,"valueGauge": 0}]}'),
('{"typeName":"AMU","gaugeType":0,"permit": 1,"subtypes": [{"name": "NOT IN USE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "TEMPERATURE","notes": 0,"qty": 0,"valueGauge": 1},{"name": "GAS OR ELECTRIC","notes": 0,"qty": 0,"valueGauge": 0},{"name": "GAS USAGE - FROM & TO","notes": 0,"qty": 0,"valueGauge": 0},{"name": "ELECTRIC ELEMENTS CONDITIONS","notes": 0,"qty": 0,"valueGauge": 0},{"name": "REPLACEMENT","notes": 1,"qty": 0,"valueGauge": 0}]}'),
('{"typeName":"Clarifier","permit": 1,"gaugeType":2,"subtypes": [{"name": "NOT IN USE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "READING","notes": 0,"qty": 0,"valueGauge": 1},{"name": "SERVICE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "WASTE","notes": 0,"qty": 1,"valueGauge": 0}]}'),
('{"typeName":"Spray Booth","permit": 1,"subtypes": [{"name": "NOT IN USE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "INSPECTION","notes": 0,"qty": 0,"valueGauge": 0},{"name": "SERVICE","notes": 1,"qty": 0,"valueGauge": 0}]}'),
('{"typeName":"Prep Station","permit": 1,"subtypes": [{"name": "NOT IN USE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "INSPECTION","notes": 0,"qty": 0,"valueGauge": 0},{"name": "SERVICE","notes": 1,"qty": 0,"valueGauge": 0}]}'),
('{"typeName":"Other equipment","permit": 1,"subtypes": [{"name": "NOT IN USE","notes": 0,"qty": 0,"valueGauge": 0},{"name": "INSPECTION","notes": 0,"qty": 0,"valueGauge": 0},{"name": "SERVICE","notes": 1,"qty": 0,"valueGauge": 0}]}');

ALTER TABLE  `logbook_record` ADD  `min_gauge_range` INT( 255 ) NOT NULL DEFAULT  '0'

ALTER TABLE  `logbook_record` ADD  `max_gauge_range` INT( 255 ) NOT NULL DEFAULT  '100'
