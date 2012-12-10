RENAME TABLE  `voc_test`.`qty_product_gauge` TO  `voc_test`.`product_gauge` ;
ALTER TABLE  `product_gauge` ADD  `gauge_type` INT( 6 ) NULL AFTER  `department_id`

INSERT INTO  `voc`.`type` (
`type_id` ,
`type_desc`
)
VALUES (
NULL ,  'Time'
);

INSERT INTO  `voc`.`unittype` (
`unittype_id` ,
`name` ,
`unittype_desc` ,
`formula` ,
`type_id` ,
`system` ,
`unit_class_id`
)
VALUES (
NULL ,  'min',  'min', NULL ,  '8',  'time', '7'
), (
NULL ,  'hour',  'hour', NULL ,  '8',  'time', '7'
);

INSERT INTO  `voc`.`unit_class` (
`id` ,
`name` ,
`description`
)
VALUES (
NULL ,  'Time',  'Time'
);