ALTER TABLE `work_order` ADD `process_id` INT NULL ,
ADD INDEX ( `process_id` ) ;

ALTER TABLE `work_order` ADD FOREIGN KEY ( `process_id` ) REFERENCES `process` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;