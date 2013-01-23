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
NULL ,  'LF',  'linear feet', NULL ,  '3',  'metric',  '2'
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
NULL ,  'LS',  'Lump Sum', NULL ,  '7',  'USA',  '6'
);

UPDATE  `unittype` SET  `name` =  'mins' WHERE  `unittype`.`unittype_id` =38;
UPDATE  `unittype` SET  `name` =  'pr.' WHERE  `unittype`.`unittype_id` =36;