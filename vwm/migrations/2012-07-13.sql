ALTER TABLE  `product` CHANGE  `price_unit_type`  `price_unit_type` INT( 11 ) NOT NULL DEFAULT  '1'
ALTER TABLE  `industry_type` CHANGE  `type`  `type` VARCHAR( 250 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL

INSERT INTO  `voc`.`report` (
`report_id` ,
`name` ,
`type` ,
`description`
)
VALUES (
NULL ,  'Potential Facility Expenses',  'PotentialFacilityExpenses',  ''
);