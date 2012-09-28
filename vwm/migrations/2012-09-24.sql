CREATE TABLE  `reminders` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 200 ) NOT NULL,
`date` INT( 15 ) NOT NULL ,
`facility_id` INT( 11 ) NOT NULL
) ENGINE = INNODB ;

INSERT INTO  `notify_code` (
`id` ,
`code` ,
`message`
)
VALUES (
NULL ,  '51',  'Reminder Added'
);

INSERT INTO  `notify_code` (
`id` ,
`code` ,
`message`
)
VALUES (
NULL ,  '52',  'Reminder Deleted'
);

INSERT INTO  `notify_code` (
`id` ,
`code` ,
`message`
)
VALUES (
NULL ,  '53',  'Reminder Edited'
);

INSERT INTO  `burner_manufacturer` (
`id` ,
`name`
)
VALUES (
NULL ,  'Spray Bake'
), (
NULL ,  'Discover'
);

CREATE TABLE  `remind2user` (
`user_id` INT( 11 ) NOT NULL ,
`reminders_id` INT( 11 ) NOT NULL
) ENGINE = INNODB ;

ALTER TABLE `remind2user` ADD FOREIGN KEY ( `user_id` ) REFERENCES `user` (
`user_id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;

ALTER TABLE `remind2user` ADD FOREIGN KEY ( `reminders_id` ) REFERENCES `reminders` (
`id`
) ON DELETE CASCADE ON UPDATE RESTRICT ;