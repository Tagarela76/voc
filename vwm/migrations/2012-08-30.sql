CREATE TABLE  `voc`.`pfp_types` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 150 ) NOT NULL,
`facility_id` INT( 11 ) NOT NULL
) ENGINE = INNODB ;

ALTER TABLE  `preformulated_products` ADD  `type_id` INT( 11 ) NULL DEFAULT NULL

ALTER TABLE `preformulated_products` ADD FOREIGN KEY ( `type_id` ) REFERENCES `pfp_types` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

INSERT INTO  `voc`.`notify_code` (
`id` ,
`code` ,
`message`
)
VALUES (
NULL ,  '50',  'PFP Type Deleted'
);