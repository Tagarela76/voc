ALTER TABLE `facility` ADD `client_facility_id` VARCHAR( 64 ) NULL ,
ADD `last_update_time` DATETIME NULL;

ALTER TABLE `facility` ADD FOREIGN KEY ( `company_id` ) REFERENCES `company` (
`company_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;