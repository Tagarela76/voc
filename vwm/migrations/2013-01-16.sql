ALTER TABLE  `step` ADD  `optional` BOOLEAN NULL DEFAULT NULL;
ALTER TABLE  `step` ADD  `description` VARCHAR( 2000 ) NULL DEFAULT NULL;

INSERT INTO `type` (
`type_id` ,
`type_desc`
)
VALUES (
NULL ,  'Count'
);

ALTER TABLE `work_order` ADD `process_id` INT NULL ,
ADD INDEX ( `process_id` ) ;

ALTER TABLE `work_order` ADD FOREIGN KEY ( `process_id` ) REFERENCES `process` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;