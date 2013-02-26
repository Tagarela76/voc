INSERT INTO `unit_class` (
`id` ,
`name` ,
`description`
)
VALUES (
NULL ,  'Quantity',  'Quantity'
);

UPDATE  `unittype` SET  `unit_class_id` =  '8' WHERE  `name` ='pr.';
UPDATE  `unittype` SET  `unit_class_id` =  '8' WHERE  `name` ='box';
UPDATE  `unittype` SET  `unit_class_id` =  '8' WHERE  `name` ='ea';