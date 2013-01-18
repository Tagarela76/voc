ALTER TABLE  `step` ADD  `optional` BOOLEAN NULL DEFAULT NULL;
ALTER TABLE  `step` ADD  `description` VARCHAR( 2000 ) NULL DEFAULT NULL;

INSERT INTO `type` (
`type_id` ,
`type_desc`
)
VALUES (
NULL ,  'Count'
);